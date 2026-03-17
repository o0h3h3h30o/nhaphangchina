// Store product data from content scripts
let productData = {};

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === 'PRODUCT_DATA') {
    const tabId = sender.tab?.id;
    if (tabId) {
      productData[tabId] = message.data;
    }
    sendResponse({ success: true });
  }

  if (message.type === 'GET_PRODUCT_DATA') {
    chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
      const tabId = tabs[0]?.id;
      sendResponse({ data: tabId ? productData[tabId] : null });
    });
    return true; // async response
  }
});

// Clean up when tab is closed
chrome.tabs.onRemoved.addListener((tabId) => {
  delete productData[tabId];
});
