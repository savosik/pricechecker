/**
 * MP Parser Worker - Popup Script
 */

document.addEventListener('DOMContentLoaded', async () => {
    const apiUrlInput = document.getElementById('apiUrl');
    const apiTokenInput = document.getElementById('apiToken');
    const toggleBtn = document.getElementById('toggleBtn');
    const statusIndicator = document.getElementById('statusIndicator');
    const statusText = document.getElementById('statusText');
    const tasksCompleted = document.getElementById('tasksCompleted');
    const tasksFailed = document.getElementById('tasksFailed');
    const lastActivity = document.getElementById('lastActivity');
    const workerIdEl = document.getElementById('workerId');

    // Load saved settings
    const settings = await chrome.storage.local.get(['apiUrl', 'apiToken', 'workerId', 'isRunning', 'stats']);

    if (settings.apiUrl) {
        apiUrlInput.value = settings.apiUrl;
    }
    if (settings.apiToken) {
        apiTokenInput.value = settings.apiToken;
    }
    if (settings.workerId) {
        workerIdEl.textContent = `ID: ${settings.workerId}`;
    }

    // Save settings on change
    apiUrlInput.addEventListener('change', () => {
        chrome.storage.local.set({ apiUrl: apiUrlInput.value.replace(/\/$/, '') });
    });

    apiTokenInput.addEventListener('change', () => {
        chrome.storage.local.set({ apiToken: apiTokenInput.value });
    });

    // Update UI based on status
    function updateUI(isRunning, stats) {
        if (isRunning) {
            statusIndicator.className = 'status-indicator running';
            statusText.textContent = 'Работает';
            toggleBtn.className = 'btn btn-stop';
            toggleBtn.textContent = 'Остановить';
        } else {
            statusIndicator.className = 'status-indicator stopped';
            statusText.textContent = 'Остановлен';
            toggleBtn.className = 'btn btn-start';
            toggleBtn.textContent = 'Запустить';
        }

        if (stats) {
            tasksCompleted.textContent = stats.tasksCompleted || 0;
            tasksFailed.textContent = stats.tasksFailed || 0;

            if (stats.lastActivity) {
                const date = new Date(stats.lastActivity);
                lastActivity.textContent = `Последняя активность: ${date.toLocaleTimeString()}`;
            }
        }
    }

    // Get current status from background
    async function refreshStatus() {
        try {
            const response = await chrome.runtime.sendMessage({ action: 'getStatus' });
            updateUI(response.isRunning, response.stats);
        } catch (e) {
            console.error('Failed to get status:', e);
        }
    }

    // Toggle worker on/off
    toggleBtn.addEventListener('click', async () => {
        const settings = await chrome.storage.local.get(['isRunning']);
        const action = settings.isRunning ? 'stop' : 'start';

        await chrome.runtime.sendMessage({ action });

        // Refresh status after a short delay
        setTimeout(refreshStatus, 500);
    });

    // Initial status update
    updateUI(settings.isRunning, settings.stats);
    refreshStatus();

    // Refresh status periodically
    setInterval(refreshStatus, 2000);
});
