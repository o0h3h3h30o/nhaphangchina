// Content script for taobao.com and tmall.com product pages
(function () {
  function extractProduct() {
    const host = window.location.hostname;
    const isTmall = host.includes('tmall.com');

    const data = {
      source: isTmall ? 'tmall' : 'taobao',
      title: '',
      price: '',
      price_raw: '',
      price_range: '',
      image: '',
      images: [],
      url: window.location.href,
      skus: [],
      skuGroups: [],
    };

    // === Method 1: Extract from global JS data ===
    try {
      const initData = extractFromGlobalData();
      if (initData) {
        Object.assign(data, initData);
        if (isTmall) data.source = 'tmall';
      }
    } catch (e) {
      console.log('[NHC] Global data extraction failed:', e);
    }

    // === Method 2: Fallback DOM parsing ===
    if (!data.title) {
      data.title =
        document.querySelector('[class*="mainTitle"]')?.textContent?.trim() ||
        document.querySelector('.ItemHeader--mainTitle')?.textContent?.trim() ||
        document.querySelector('#J_Title .tb-main-title')?.textContent?.trim() ||
        document.querySelector('h1')?.textContent?.trim() ||
        '';
    }

    if (!data.price) {
      data.price_raw =
        document.querySelector('[class*="priceText"]')?.textContent?.trim() ||
        document.querySelector('.Price--priceText')?.textContent?.trim() ||
        document.querySelector('#J_StrPrice .tb-rmb-num')?.textContent?.trim() ||
        '';
      data.price = data.price_raw.replace(/[^\d.]/g, '');
    }

    if (!data.image) {
      data.image =
        document.querySelector('[class*="mainPic"] img')?.src ||
        document.querySelector('.PicGallery--mainPic img')?.src ||
        document.querySelector('#J_ImgBooth')?.src ||
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

  function extractFromGlobalData() {
    const result = {};

    // Taobao/Tmall new pages use window.__INIT_DATA__ or similar
    const scripts = document.querySelectorAll('script');
    for (const script of scripts) {
      const text = script.textContent || '';
      const patterns = [
        /window\.__INIT_DATA__\s*=\s*(\{[\s\S]*?\});\s*(?:window\.|<\/script>)/,
        /window\.rawData\s*=\s*(\{[\s\S]*?\});/,
        /g_page_config\s*=\s*(\{[\s\S]*?\});/,
        /TShop\.Setup\(\s*(\{[\s\S]*?\})\s*\)/,
      ];

      for (const pattern of patterns) {
        const match = text.match(pattern);
        if (match) {
          try {
            const parsed = JSON.parse(match[1]);
            return parseTaobaoData(parsed);
          } catch (e) {
            // continue trying
          }
        }
      }
    }

    return null;
  }

  function parseTaobaoData(raw) {
    const result = {};

    // Navigate to item data
    const itemData =
      raw?.data?.item ||
      raw?.item ||
      raw?.data?.itemInfoModel ||
      raw?.itemInfoModel ||
      raw?.data?.itemDO ||
      raw;

    result.title = itemData?.title || itemData?.name || '';

    // Price
    const priceData =
      itemData?.price ||
      raw?.data?.price ||
      raw?.price ||
      {};
    if (typeof priceData === 'string') {
      result.price = priceData;
      result.price_raw = '¥' + priceData;
    } else if (priceData?.price) {
      result.price = String(priceData.price);
      result.price_raw = '¥' + priceData.price;
    }

    if (priceData?.priceRange || priceData?.extraPrices) {
      result.price_range = priceData.priceRange || '';
    }

    // Images
    const images =
      itemData?.images ||
      itemData?.imgs ||
      raw?.data?.images ||
      [];
    if (images.length > 0) {
      result.images = images.map((img) => {
        if (typeof img === 'string') return img.startsWith('//') ? 'https:' + img : img;
        return (img.url || img.src || '').replace(/^\/\//, 'https://');
      });
      result.image = result.images[0] || '';
    }

    // SKU props
    const skuData =
      itemData?.skuBase ||
      itemData?.skuInfo ||
      raw?.data?.skuBase ||
      raw?.skuBase ||
      {};

    const props = skuData?.props || skuData?.skuProps || [];
    if (props.length > 0) {
      result.skuGroups = props.map((prop) => ({
        name: prop.name || prop.propName || '',
        values: (prop.values || prop.value || []).map((v) => ({
          name: v.name || v.text || v.valueName || '',
          image: v.image
            ? v.image.startsWith('//')
              ? 'https:' + v.image
              : v.image
            : '',
          id: v.vid || v.valueId || v.id || '',
        })),
      }));
    }

    // SKU list with prices
    const skuList = skuData?.skus || skuData?.skuList || [];
    if (skuList.length > 0) {
      result.skus = skuList.map((sku) => ({
        specKey: sku.propPath || sku.specKey || '',
        price: sku.price?.priceText || sku.price?.price || sku.priceMoney || '',
        stock: sku.quantity || sku.stock || 0,
        specAttrs: sku.propPath || '',
      }));
    }

    return result;
  }

  function extractSkuGroupsFromDom(data) {
    // Taobao/Tmall SKU selectors
    const skuContainers = document.querySelectorAll(
      '[class*="SkuContent"], [class*="skuContent"], .J_SKU, #J_iSKU, .tb-sku, [class*="Sku--"] '
    );

    skuContainers.forEach((container) => {
      // Each prop row
      const propRows = container.querySelectorAll(
        '[class*="skuProp"], [class*="Prop--"], .J_Prop, .tb-prop, dl'
      );

      propRows.forEach((row) => {
        const nameEl = row.querySelector(
          '[class*="propTitle"], [class*="title"], .tb-property-type, dt, label'
        );
        const groupName = nameEl?.textContent?.trim()?.replace(/[:：]/g, '') || '';

        const options = [];
        const optionEls = row.querySelectorAll(
          '[class*="valueItem"], [class*="Item--"], .J_TSaleProp a, li a, dd a'
        );

        optionEls.forEach((opt) => {
          const name =
            opt.querySelector('[class*="valueText"], span')?.textContent?.trim() ||
            opt.getAttribute('title') ||
            opt.textContent?.trim() ||
            '';
          const img =
            opt.querySelector('img')?.src || '';

          if (name && name.length < 100) {
            options.push({
              name,
              image: img.startsWith('//') ? 'https:' + img : img,
              id: opt.getAttribute('data-value') || opt.getAttribute('data-pv') || '',
            });
          }
        });

        if (groupName && options.length > 0) {
          data.skuGroups.push({ name: groupName, values: options });
        }
      });
    });

    // Alternative: flat structure without dl/dt
    if (data.skuGroups.length === 0) {
      const groups = document.querySelectorAll('[class*="sku-item-wrapper"], [class*="SkuGroup"]');
      groups.forEach((group) => {
        const label = group.querySelector('[class*="label"], [class*="name"]')?.textContent?.trim()?.replace(/[:：]/g, '') || '';
        const items = [];
        group.querySelectorAll('[class*="sku-item"], [class*="value"] button, [class*="value"] a').forEach((item) => {
          const txt = item.textContent?.trim();
          const img = item.querySelector('img')?.src || '';
          if (txt && txt.length < 100) {
            items.push({ name: txt, image: img, id: '' });
          }
        });
        if (label && items.length > 0) {
          data.skuGroups.push({ name: label, values: items });
        }
      });
    }
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

  // Listen for manual extract request
  chrome.runtime.onMessage.addListener((msg, sender, sendResponse) => {
    if (msg.type === 'EXTRACT_NOW') {
      extractProduct();
      sendResponse({ ok: true });
    }
  });
})();
