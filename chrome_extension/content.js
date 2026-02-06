/**
 * MP Parser Worker - Content Script
 * Injected into Ozon and Wildberries pages
 */

// Handle age verification for Ozon
function handleOzonAgeVerification() {
    // Check for age verification modal
    const observer = new MutationObserver((mutations, obs) => {
        // Look for age verification elements
        const yearInput = document.querySelector('input[placeholder*="Год"]');
        const confirmButton = document.querySelector('[data-widget="confirm18"] button');

        if (yearInput) {
            yearInput.value = '1990';
            yearInput.dispatchEvent(new Event('input', { bubbles: true }));
            yearInput.dispatchEvent(new Event('change', { bubbles: true }));

            // Wait a bit and click confirm
            setTimeout(() => {
                const buttons = document.querySelectorAll('button');
                for (const btn of buttons) {
                    if (btn.textContent.includes('Подтвердить') ||
                        btn.textContent.includes('Продолжить') ||
                        btn.textContent.includes('Да')) {
                        btn.click();
                        break;
                    }
                }
            }, 500);

            obs.disconnect();
            return;
        }

        if (confirmButton) {
            confirmButton.click();
            obs.disconnect();
            return;
        }

        // Check for simple confirm button
        const buttons = document.querySelectorAll('button');
        for (const btn of buttons) {
            const text = btn.textContent.toLowerCase();
            if (text.includes('мне') && (text.includes('18') || text.includes('исполнилось'))) {
                btn.click();
                obs.disconnect();
                return;
            }
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });

    // Initial check
    setTimeout(() => {
        const buttons = document.querySelectorAll('button');
        for (const btn of buttons) {
            const text = btn.textContent.toLowerCase();
            if (text.includes('мне') && (text.includes('18') || text.includes('исполнилось'))) {
                btn.click();
                break;
            }
        }
    }, 1000);

    // Stop observing after 10 seconds
    setTimeout(() => observer.disconnect(), 10000);
}

// Run on page load
if (window.location.hostname.includes('ozon.ru')) {
    handleOzonAgeVerification();
}

// Listen for messages from background script
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === 'getDOM') {
        sendResponse({
            success: true,
            dom: document.documentElement.outerHTML,
        });
    } else if (message.action === 'handleAge') {
        handleOzonAgeVerification();
        sendResponse({ success: true });
    }
    return true;
});
