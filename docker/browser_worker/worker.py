#!/usr/bin/env python3
"""
DOM Parser Worker using undetected-chromedriver
Polls API for tasks, fetches DOM from URLs, and submits results
"""

import os
import sys
import time
import requests
import undetected_chromedriver as uc
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Configuration from environment
API_URL = os.getenv('API_URL', 'http://nginx')
API_TOKEN = os.getenv('API_TOKEN', '')
WORKER_ID = os.getenv('WORKER_ID', f'uc_worker_{int(time.time())}')
POLL_INTERVAL = int(os.getenv('POLL_INTERVAL', '10'))
PAGE_LOAD_TIMEOUT = int(os.getenv('PAGE_LOAD_TIMEOUT', '60'))

def log(msg):
    print(f"[{time.strftime('%Y-%m-%d %H:%M:%S')}] {msg}", flush=True)

def get_driver():
    """Initialize undetected Chrome driver with virtual display"""
    options = uc.ChromeOptions()
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--disable-gpu')
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--lang=ru-RU')
    options.add_argument('--start-maximized')
    
    # Anti-detection settings
    options.add_argument('--disable-blink-features=AutomationControlled')
    
    # Use non-headless mode - undetected_chromedriver will use xvfb automatically
    driver = uc.Chrome(options=options, headless=False, use_subprocess=True)
    driver.set_page_load_timeout(PAGE_LOAD_TIMEOUT)
    
    return driver

def fetch_task():
    """Fetch next task from API"""
    try:
        url = f"{API_URL}/api/dom-parser/task"
        headers = {
            'Authorization': f'Bearer {API_TOKEN}',
            'Accept': 'application/json'
        }
        params = {'worker_id': WORKER_ID}
        
        response = requests.get(url, headers=headers, params=params, timeout=30)
        
        if response.status_code == 200:
            data = response.json()
            return data.get('task')
        else:
            log(f"API error: {response.status_code}")
            return None
            
    except Exception as e:
        log(f"Error fetching task: {e}")
        return None

def submit_result(task_id, success, dom_content=None, error_message=None):
    """Submit result to API"""
    try:
        url = f"{API_URL}/api/dom-parser/result"
        headers = {
            'Authorization': f'Bearer {API_TOKEN}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        data = {
            'task_id': task_id,
            'worker_id': WORKER_ID,
            'success': success,
            'dom_content': dom_content,
            'error_message': error_message
        }
        
        response = requests.post(url, headers=headers, json=data, timeout=60)
        return response.status_code == 200
        
    except Exception as e:
        log(f"Error submitting result: {e}")
        return False

def handle_ozon_age_verification(driver):
    """Handle Ozon age verification modal if present"""
    try:
        # Wait a bit for modal to appear
        time.sleep(2)
        
        # Try to find and click age confirmation button
        selectors = [
            '[data-widget="confirm18"] button',
            'button[class*="confirm"]',
            '//button[contains(text(), "Подтвердить")]',
            '//button[contains(text(), "Да, мне")]'
        ]
        
        for selector in selectors:
            try:
                if selector.startswith('//'):
                    elements = driver.find_elements(By.XPATH, selector)
                else:
                    elements = driver.find_elements(By.CSS_SELECTOR, selector)
                    
                for elem in elements:
                    if elem.is_displayed():
                        elem.click()
                        log("Clicked age verification button")
                        time.sleep(2)
                        return True
            except:
                continue
                
    except Exception as e:
        log(f"Age verification check: {e}")
    
    return False

def process_task(driver, task):
    """Process a single task - navigate to URL and get DOM"""
    task_id = task['id']
    url = task['url']
    marketplace = task.get('marketplace_code', 'unknown')
    
    log(f"Processing task {task_id}: {url[:80]}...")
    
    try:
        # Navigate to URL
        driver.get(url)
        
        # Wait for page to load
        time.sleep(5)
        
        # Handle age verification for Ozon
        if marketplace == 'ozon':
            handle_ozon_age_verification(driver)
            time.sleep(2)
        
        # Wait for main content to load
        try:
            WebDriverWait(driver, 30).until(
                EC.presence_of_element_located((By.TAG_NAME, "body"))
            )
        except:
            pass
        
        # Additional wait for dynamic content
        time.sleep(3)
        
        # Get DOM
        dom = driver.page_source
        
        if not dom or len(dom) < 1000:
            raise Exception("DOM too short, page may not have loaded")
        
        # Check for anti-bot blocking
        if 'доступ ограничен' in dom.lower() or 'access denied' in dom.lower():
            raise Exception("Anti-bot protection detected")
        
        # Submit success
        if submit_result(task_id, True, dom):
            log(f"Task {task_id} completed, DOM length: {len(dom)}")
            return True
        else:
            log(f"Task {task_id} - failed to submit result")
            return False
            
    except Exception as e:
        error_msg = str(e)
        log(f"Task {task_id} failed: {error_msg}")
        submit_result(task_id, False, error_message=error_msg)
        return False

def main():
    log(f"Starting DOM Parser Worker: {WORKER_ID}")
    log(f"API URL: {API_URL}")
    log(f"Poll interval: {POLL_INTERVAL}s")
    
    if not API_TOKEN:
        log("ERROR: API_TOKEN not set!")
        sys.exit(1)
    
    driver = None
    consecutive_errors = 0
    max_consecutive_errors = 5
    
    try:
        log("Initializing Chrome driver...")
        driver = get_driver()
        log("Chrome driver ready")
        
        while True:
            try:
                task = fetch_task()
                
                if task:
                    consecutive_errors = 0
                    success = process_task(driver, task)
                    
                    if not success:
                        consecutive_errors += 1
                        
                    # Small delay between tasks
                    time.sleep(2)
                else:
                    # No tasks available
                    time.sleep(POLL_INTERVAL)
                    
            except Exception as e:
                log(f"Loop error: {e}")
                consecutive_errors += 1
                time.sleep(POLL_INTERVAL)
            
            # Restart driver if too many errors
            if consecutive_errors >= max_consecutive_errors:
                log("Too many consecutive errors, restarting driver...")
                try:
                    driver.quit()
                except:
                    pass
                time.sleep(5)
                driver = get_driver()
                consecutive_errors = 0
                
    except KeyboardInterrupt:
        log("Shutting down...")
    finally:
        if driver:
            try:
                driver.quit()
            except:
                pass

if __name__ == '__main__':
    main()
