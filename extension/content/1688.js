// Content script for 1688.com product pages
(function () {
  function extractProduct() {
    const data = {
      source: '1688',
      title: '',
      price: '',
      price_raw: '',
      price_range: '',
      image: '',
      images: [],
      url: window.location.href,
      skus: [],       // Individual SKU combinations with price/stock
      skuGroups: [],   // Groups of options (color, size, etc.)
    };

    // === Method 1: Try to get data from global JS variables ===
    try {
      const initData = extractFromScripts();
      if (initData) {
        Object.assign(data, initData);
      }
    } catch (e) {
      console.log('[NHC] Script extraction failed:', e);
    }

    // === Method 2: Fallback to DOM parsing ===
    if (!data.title) {
      data.title =
        document.querySelector('.title-text')?.textContent?.trim() ||
        document.querySelector('.mod-detail-title h1')?.textContent?.trim() ||
        document.querySelector('[class*="DetailTitle"] [class*="title"]')?.textContent?.trim() ||
        document.querySelector('h1')?.textContent?.trim() ||
        '';
    }

    if (!data.price) {
      data.price_raw =
        document.querySelector('.price-text')?.textContent?.trim() ||
        document.querySelector('.price .value')?.textContent?.trim() ||
        document.querySelector('[class*="price"] [class*="value"]')?.textContent?.trim() ||
        '';
      data.price = data.price_raw.replace(/[^\d.]/g, '');
    }

    if (!data.image) {
      data.image =
        document.querySelector('.detail-gallery-img img')?.src ||
        document.querySelector('[class*="gallery"] img')?.src ||
        document.querySelector('.main-img img')?.src ||
        '';
    }

    // === Extract SKU groups from DOM ===
    if (data.skuGroups.length === 0) {
      extractSkuGroupsFromDom(data);
    }

    if (data.title) {
      chrome.runtime.sendMessage({ type: 'PRODUCT_DATA', data });
    }
  }

  function extractFromScripts() {
    // 1688 stores product data in various global variables
    const scripts = document.querySelectorAll('script');
    for (const script of scripts) {
      const text = script.textContent || '';

      // Try window.__INIT_DATA or similar
      const patterns = [
        /window\.__INIT_DATA\s*=\s*(\{[\s\S]*?\});/,
        /var\s+iDetailData\s*=\s*(\{[\s\S]*?\});/,
        /window\.detailData\s*=\s*(\{[\s\S]*?\});/,
        /window\._GATEWAY_DETAIL_DATA\s*=\s*(\{[\s\S]*?\});/,
      ];

      for (const pattern of patterns) {
        const match = text.match(pattern);
        if (match) {
          try {
            const parsed = JSON.parse(match[1]);
            return parseInitData(parsed);
          } catch (e) {
            // JSON parse failed, try eval-safe extraction
          }
        }
      }
    }

    // Try globalData on window
    if (window.iDetailData) {
      return parseInitData(window.iDetailData);
    }
    if (window.__INIT_DATA) {
      return parseInitData(window.__INIT_DATA);
    }

    return null;
  }

  function parseInitData(raw) {
    const result = {};

    // Navigate nested structures to find product info
    const detail =
      raw?.data?.offerDetail ||
      raw?.data?.detail ||
      raw?.offerDetail ||
      raw?.detail ||
      raw?.globalData?.offerDetail ||
      raw;

    // Title
    result.title =
      detail?.subject ||
      detail?.title ||
      detail?.productInfo?.subject ||
      '';

    // Price
    const priceInfo =
      detail?.priceInfo ||
      detail?.tradePrice ||
      detail?.price ||
      {};

    if (priceInfo?.price) {
      result.price = String(priceInfo.price);
      result.price_raw = '¥' + priceInfo.price;
    }

    if (priceInfo?.priceRange) {
      result.price_range = priceInfo.priceRange;
    }

    // Images
    const imgList =
      detail?.imgList ||
      detail?.images ||
      detail?.productImage?.images ||
      [];

    if (imgList.length > 0) {
      result.image = imgList[0]?.startsWith('//') ? 'https:' + imgList[0] : imgList[0];
      result.images = imgList.map((img) =>
        img?.startsWith('//') ? 'https:' + img : img
      );
    }

    // SKU data
    const skuModel =
      detail?.skuModel ||
      detail?.skuInfo ||
      detail?.productInfo?.skuModel ||
      {};

    if (skuModel?.skuProps) {
      result.skuGroups = skuModel.skuProps.map((prop) => ({
        name: prop.prop || prop.propName || prop.fid || '',
        values: (prop.value || prop.values || []).map((v) => ({
          name: v.name || v.text || '',
          image: v.imageUrl
            ? v.imageUrl.startsWith('//')
              ? 'https:' + v.imageUrl
              : v.imageUrl
            : '',
          id: v.specId || v.id || '',
        })),
      }));
    }

    if (skuModel?.skuInfoMap || skuModel?.skuMap) {
      const skuMap = skuModel.skuInfoMap || skuModel.skuMap || {};
      result.skus = Object.entries(skuMap).map(([key, val]) => ({
        specKey: key,
        price: val.price || val.discountPrice || '',
        stock: val.canBookCount || val.stock || 0,
        specAttrs: key,
      }));
    }

    return result;
  }

  function extractSkuGroupsFromDom(data) {
    // 1688 SKU selector DOM structure
    const skuWrappers = document.querySelectorAll(
      '.sku-wrapper, .obj-sku, [class*="SkuSelect"], [class*="offer-attr"], [class*="obj-content"]'
    );

    skuWrappers.forEach((wrapper) => {
      // Each group has a name and options
      const nameEl = wrapper.querySelector(
        '.obj-header, .sku-title, [class*="title"], dt, label'
      );
      const groupName = nameEl?.textContent?.trim()?.replace(/[:：]/g, '') || '';

      const options = [];
      const optionEls = wrapper.querySelectorAll(
        '.obj-item, .sku-item, [class*="item"], li a, dd a'
      );

      optionEls.forEach((opt) => {
        const name =
          opt.querySelector('span, [class*="text"]')?.textContent?.trim() ||
          opt.textContent?.trim() ||
          '';
        const image =
          opt.querySelector('img')?.src || '';

        if (name && name.length < 100) {
          options.push({
            name,
            image: image.startsWith('//') ? 'https:' + image : image,
            id: opt.getAttribute('data-value') || opt.getAttribute('data-sku-id') || '',
          });
        }
      });

      if (groupName && options.length > 0) {
        data.skuGroups.push({ name: groupName, values: options });
      }
    });
  }

  // Run extraction
  function run() {
    setTimeout(extractProduct, 2000);
  }

  if (document.readyState === 'complete') {
    run();
  } else {
    window.addEventListener('load', run);
  }

  // Re-extract on SPA navigation
  let lastUrl = location.href;
  new MutationObserver(() => {
    if (location.href !== lastUrl) {
      lastUrl = location.href;
      setTimeout(extractProduct, 2500);
    }
  }).observe(document.body, { subtree: true, childList: true });

  // Listen for manual extract request from popup
  chrome.runtime.onMessage.addListener((msg, sender, sendResponse) => {
    if (msg.type === 'EXTRACT_NOW') {
      extractProduct();
      sendResponse({ ok: true });
    }
  });
})();
