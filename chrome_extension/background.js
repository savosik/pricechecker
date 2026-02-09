/**
 * MP Parser Worker - Background Service Worker
 * Polls the API for tasks, opens tabs, collects DOM, and submits results
 */

// Configuration
const CONFIG = {
    POLL_INTERVAL: 5000, // 5 seconds
    PAGE_LOAD_TIMEOUT: 60000, // 60 seconds
    DOM_WAIT_DELAY: 5000, // Wait for dynamic content
    DOM_RETRY_COUNT: 3, // Number of retries for DOM extraction
    DOM_RETRY_DELAY: 2000, // Delay between retries
    RETRY_DELAY: 10000, // 10 seconds on error
};

// State
let isRunning = false;
let stats = {
    tasksCompleted: 0,
    tasksFailed: 0,
    lastActivity: null,
};

// Generate unique worker ID
async function getWorkerId() {
    const result = await chrome.storage.local.get(['workerId']);
    if (result.workerId) {
        return result.workerId;
    }
    const workerId = 'worker_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    await chrome.storage.local.set({ workerId });
    return workerId;
}

// Get settings from storage
async function getSettings() {
    const result = await chrome.storage.local.get(['apiUrl', 'apiToken', 'isRunning']);
    return {
        apiUrl: result.apiUrl || '',
        apiToken: result.apiToken || '',
        isRunning: result.isRunning || false,
    };
}

// Fetch next task from API
async function fetchTask() {
    const settings = await getSettings();
    if (!settings.apiUrl || !settings.apiToken) {
        console.log('API not configured');
        return null;
    }

    const workerId = await getWorkerId();
    const url = `${settings.apiUrl}/api/dom-parser/task?worker_id=${encodeURIComponent(workerId)}`;

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${settings.apiToken}`,
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            console.error('Failed to fetch task:', response.status);
            return null;
        }

        const data = await response.json();
        return data.task;
    } catch (error) {
        console.error('Error fetching task:', error);
        return null;
    }
}

// Submit result to API
async function submitResult(taskId, success, domContent, errorMessage) {
    const settings = await getSettings();
    if (!settings.apiUrl || !settings.apiToken) {
        return false;
    }

    const workerId = await getWorkerId();
    const url = `${settings.apiUrl}/api/dom-parser/result`;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${settings.apiToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                task_id: taskId,
                worker_id: workerId,
                success: success,
                dom_content: domContent,
                error_message: errorMessage,
            }),
        });

        if (!response.ok) {
            console.error('Failed to submit result:', response.status);
            return false;
        }

        return true;
    } catch (error) {
        console.error('Error submitting result:', error);
        return false;
    }
}

// Process a single task
async function processTask(task) {
    console.log('Processing task:', task.id, task.url);
    stats.lastActivity = new Date().toISOString();
    await updateStats();

    let tab = null;

    try {
        // Create a new tab (active so we can execute scripts)
        tab = await chrome.tabs.create({
            url: task.url,
            active: true,
        });

        console.log('Tab created:', tab.id);

        // Wait for page to load
        await waitForTabLoad(tab.id, CONFIG.PAGE_LOAD_TIMEOUT);

        // Wait for dynamic content
        await sleep(CONFIG.DOM_WAIT_DELAY);

        // Check and handle age verification for Ozon
        if (task.marketplace_code === 'ozon') {
            await handleOzonAgeVerification(tab.id);
            await sleep(2000);
        }

        // Get DOM from the page
        const dom = await getDomFromTab(tab.id);

        if (!dom) {
            throw new Error('Failed to get DOM content');
        }

        // Submit result
        await submitResult(task.id, true, dom, null);
        stats.tasksCompleted++;
        console.log('Task completed:', task.id);

    } catch (error) {
        console.error('Task failed:', task.id, error);
        await submitResult(task.id, false, null, error.message);
        stats.tasksFailed++;
    } finally {
        // Close the tab
        if (tab) {
            try {
                await chrome.tabs.remove(tab.id);
            } catch (e) {
                // Tab may already be closed
            }
        }
        await updateStats();
    }
}

// Wait for tab to finish loading
function waitForTabLoad(tabId, timeout) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();

        const checkLoad = async () => {
            try {
                const tab = await chrome.tabs.get(tabId);

                if (tab.status === 'complete') {
                    resolve();
                    return;
                }

                if (Date.now() - startTime > timeout) {
                    reject(new Error('Page load timeout'));
                    return;
                }

                setTimeout(checkLoad, 500);
            } catch (error) {
                reject(error);
            }
        };

        checkLoad();
    });
}

// Get DOM content from a tab with retries
async function getDomFromTab(tabId) {
    for (let attempt = 1; attempt <= CONFIG.DOM_RETRY_COUNT; attempt++) {
        try {
            console.log(`Getting DOM attempt ${attempt}/${CONFIG.DOM_RETRY_COUNT}`);

            const results = await chrome.scripting.executeScript({
                target: { tabId },
                func: () => document.documentElement.outerHTML,
            });

            if (results && results[0] && results[0].result) {
                const dom = results[0].result;
                console.log(`Got DOM, length: ${dom.length}`);
                return dom;
            }

            console.warn(`Attempt ${attempt}: No result from executeScript`);
        } catch (error) {
            console.error(`Attempt ${attempt} error:`, error.message);
        }

        if (attempt < CONFIG.DOM_RETRY_COUNT) {
            await sleep(CONFIG.DOM_RETRY_DELAY);
        }
    }

    return null;
}

// Handle Ozon age verification modal
async function handleOzonAgeVerification(tabId) {
    try {
        await chrome.scripting.executeScript({
            target: { tabId },
            func: () => {
                // Look for age verification modal
                const confirmButton = document.querySelector('[data-widget="confirm18"] button');
                if (confirmButton) {
                    confirmButton.click();
                    return true;
                }

                // Alternative: look for "Подтвердить" button in modal
                const buttons = document.querySelectorAll('button');
                for (const btn of buttons) {
                    if (btn.textContent.includes('Подтвердить') || btn.textContent.includes('Да, мне')) {
                        btn.click();
                        return true;
                    }
                }

                // Try year input method
                const yearInput = document.querySelector('input[placeholder*="Год"]');
                if (yearInput) {
                    yearInput.value = '1990';
                    yearInput.dispatchEvent(new Event('input', { bubbles: true }));

                    // Find and click confirm button
                    setTimeout(() => {
                        const confirmBtns = document.querySelectorAll('button');
                        for (const btn of confirmBtns) {
                            if (btn.textContent.includes('Подтвердить')) {
                                btn.click();
                                break;
                            }
                        }
                    }, 500);
                    return true;
                }

                return false;
            },
        });
    } catch (error) {
        console.log('Age verification check failed (may not be needed):', error);
    }
}

// Update stats in storage
async function updateStats() {
    await chrome.storage.local.set({ stats });
}

// Sleep helper
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Main polling loop
async function pollLoop() {
    const settings = await getSettings();

    if (!settings.isRunning) {
        console.log('Worker stopped');
        isRunning = false;
        return;
    }

    isRunning = true;

    try {
        const task = await fetchTask();

        if (task) {
            await processTask(task);
            // Immediately check for next task
            setTimeout(pollLoop, 100);
        } else {
            // No tasks, wait before polling again
            setTimeout(pollLoop, CONFIG.POLL_INTERVAL);
        }
    } catch (error) {
        console.error('Poll loop error:', error);
        setTimeout(pollLoop, CONFIG.RETRY_DELAY);
    }
}

// Start the worker
async function startWorker() {
    console.log('Starting worker...');
    await chrome.storage.local.set({ isRunning: true });

    if (!isRunning) {
        pollLoop();
    }
}

// Stop the worker
async function stopWorker() {
    console.log('Stopping worker...');
    await chrome.storage.local.set({ isRunning: false });
    isRunning = false;
}

// Listen for messages from popup
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === 'start') {
        startWorker();
        sendResponse({ success: true });
    } else if (message.action === 'stop') {
        stopWorker();
        sendResponse({ success: true });
    } else if (message.action === 'getStatus') {
        sendResponse({
            isRunning,
            stats,
        });
    }
    return true;
});

// Load configuration from config.json if available
async function loadConfigFromFile() {
    try {
        const configUrl = chrome.runtime.getURL('config.json');
        const response = await fetch(configUrl);
        if (!response.ok) {
            console.log('No config.json found or not readable');
            return;
        }
        const config = await response.json();
        console.log('Config.json loaded:', { apiUrl: config.apiUrl, autoStart: config.autoStart });

        const updates = {};
        if (config.apiUrl) updates.apiUrl = config.apiUrl;
        if (config.apiToken) updates.apiToken = config.apiToken;

        if (Object.keys(updates).length > 0) {
            await chrome.storage.local.set(updates);
            console.log('Config applied from config.json');
        }

        // Auto-start if configured
        if (config.autoStart === true || config.autoStart === 'true') {
            console.log('Auto-starting worker from config.json');
            await startWorker();
        }
    } catch (error) {
        console.log('Could not load config.json:', error.message);
    }
}

// Initialize on install
chrome.runtime.onInstalled.addListener(async () => {
    console.log('MP Parser Worker installed');
    await chrome.storage.local.set({
        stats: {
            tasksCompleted: 0,
            tasksFailed: 0,
            lastActivity: null,
        },
    });
    // Load config on install
    await loadConfigFromFile();
});

// Also load config on service worker startup (covers restarts)
loadConfigFromFile();
