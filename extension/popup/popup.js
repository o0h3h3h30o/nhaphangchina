const DEFAULT_API = 'http://localhost:8080';

const statusLabels = {
  draft: 'Nháp',
  submitted: 'Đã gửi',
  received_cn: 'Đã nhận TQ',
  packed_for_truck: 'Đã đóng gói',
  in_transit_cn_vn: 'Đang vận chuyển',
  received_vn: 'Đã nhận VN',
  fee_calculated: 'Đã tính phí',
  waiting_payment: 'Chờ thanh toán',
  ready_for_delivery: 'Sẵn sàng giao',
  ready_for_pickup: 'Sẵn sàng lấy',
  delivering: 'Đang giao',
  completed: 'Hoàn thành',
  cancelled: 'Đã hủy',
  lost_issue: 'Thất lạc',
};

const $ = (id) => document.getElementById(id);

let currentProduct = null;
let selectedSkus = {}; // { groupName: { name, image, id } }

// Init
document.addEventListener('DOMContentLoaded', async () => {
  await loadSettings();
  await checkAuth();
  setupTabs();
  setupEventListeners();
  loadProductData();
});

async function loadSettings() {
  const data = await chrome.storage.local.get(['apiUrl']);
  $('apiUrl').value = data.apiUrl || DEFAULT_API;
}

function getApiUrl() {
  return $('apiUrl').value.replace(/\/$/, '') || DEFAULT_API;
}

async function checkAuth() {
  const data = await chrome.storage.local.get(['token', 'user']);
  if (data.token && data.user) {
    showMainSection(data.user);
  } else {
    showLoginSection();
  }
}

function showLoginSection() {
  $('loginSection').style.display = 'block';
  $('mainSection').style.display = 'none';
  $('userInfo').style.display = 'none';
  $('settingsBar').style.display = 'flex';
}

function showMainSection(user) {
  $('loginSection').style.display = 'none';
  $('mainSection').style.display = 'block';
  $('userInfo').style.display = 'flex';
  $('userName').textContent = user.username || user.email;
  $('settingsBar').style.display = 'flex';
}

function setupTabs() {
  document.querySelectorAll('.tab').forEach((tab) => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach((t) => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach((c) => c.classList.remove('active'));
      tab.classList.add('active');
      const target = tab.dataset.tab;
      $('tab' + target.charAt(0).toUpperCase() + target.slice(1)).classList.add('active');
      if (target === 'orders') loadOrders();
    });
  });
}

function setupEventListeners() {
  $('btnLogin').addEventListener('click', handleLogin);
  $('btnLogout').addEventListener('click', handleLogout);
  $('btnOrder').addEventListener('click', handleOrder);
  $('btnSaveSettings').addEventListener('click', async () => {
    await chrome.storage.local.set({ apiUrl: $('apiUrl').value });
    $('btnSaveSettings').textContent = '✓';
    setTimeout(() => ($('btnSaveSettings').textContent = 'Lưu'), 1000);
  });
  $('password').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') handleLogin();
  });
}

async function handleLogin() {
  const email = $('email').value.trim();
  const password = $('password').value;
  $('loginError').style.display = 'none';

  if (!email || !password) {
    showError('loginError', 'Vui lòng nhập email và mật khẩu');
    return;
  }

  $('btnLogin').disabled = true;
  $('btnLogin').textContent = 'Đang đăng nhập...';

  try {
    const res = await apiCall('POST', '/api/auth/login', { email, password });
    if (res.success) {
      await chrome.storage.local.set({ token: res.token, user: res.user });
      showMainSection(res.user);
      loadProductData();
    } else {
      showError('loginError', res.message || 'Đăng nhập thất bại');
    }
  } catch (err) {
    showError('loginError', 'Không kết nối được server. Kiểm tra API URL.');
  }

  $('btnLogin').disabled = false;
  $('btnLogin').textContent = 'Đăng nhập';
}

async function handleLogout() {
  await chrome.storage.local.remove(['token', 'user']);
  showLoginSection();
}

function loadProductData() {
  chrome.runtime.sendMessage({ type: 'GET_PRODUCT_DATA' }, (response) => {
    if (response?.data) {
      currentProduct = response.data;
      displayProduct(currentProduct);
    } else {
      chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
        if (tabs[0]) {
          chrome.tabs.sendMessage(tabs[0].id, { type: 'EXTRACT_NOW' }, () => {
            if (chrome.runtime.lastError) return;
          });
          setTimeout(() => {
            chrome.runtime.sendMessage({ type: 'GET_PRODUCT_DATA' }, (res) => {
              if (res?.data) {
                currentProduct = res.data;
                displayProduct(currentProduct);
              }
            });
          }, 500);
        }
      });
    }
  });
}

function displayProduct(product) {
  $('noProduct').style.display = 'none';
  $('productInfo').style.display = 'block';

  $('productTitle').textContent = product.title;
  $('productPrice').textContent = product.price || '—';

  if (product.image) {
    $('productImage').src = product.image;
    $('productImage').style.display = 'block';
  } else {
    $('productImage').style.display = 'none';
  }

  const source = product.source || '1688';
  $('productSource').textContent = source.toUpperCase();
  $('productSource').className = 'badge badge-' + source;

  // Render SKU groups
  renderSkuGroups(product.skuGroups || []);
}

function renderSkuGroups(groups) {
  const container = $('skuSection');
  selectedSkus = {};

  if (!groups || groups.length === 0) {
    container.style.display = 'none';
    return;
  }

  container.style.display = 'block';
  let html = '';

  groups.forEach((group, gi) => {
    html += `<div class="sku-group" data-group="${gi}">`;
    html += `<div class="sku-group-label">${escapeHtml(group.name)}</div>`;
    html += `<div class="sku-options">`;

    group.values.forEach((val, vi) => {
      const hasImg = val.image && !val.image.includes('data:');
      html += `<div class="sku-option" data-group="${gi}" data-index="${vi}"
                    data-name="${escapeAttr(val.name)}" data-gname="${escapeAttr(group.name)}"
                    data-image="${escapeAttr(val.image || '')}">`;
      if (hasImg) {
        html += `<img src="${escapeAttr(val.image)}" alt="">`;
      }
      html += `<span class="sku-option-text" title="${escapeAttr(val.name)}">${escapeHtml(val.name)}</span>`;
      html += `</div>`;
    });

    html += `</div></div>`;
  });

  html += `<div id="skuSummary" class="sku-selected-summary"></div>`;
  container.innerHTML = html;

  // Add click listeners
  container.querySelectorAll('.sku-option').forEach((opt) => {
    opt.addEventListener('click', () => {
      const groupIndex = opt.dataset.group;
      const groupName = opt.dataset.gname;

      // Toggle selection in same group
      container.querySelectorAll(`.sku-option[data-group="${groupIndex}"]`).forEach((o) => {
        o.classList.remove('selected');
      });
      opt.classList.add('selected');

      selectedSkus[groupName] = {
        name: opt.dataset.name,
        image: opt.dataset.image,
      };

      // Update product image if SKU has image
      if (opt.dataset.image) {
        $('productImage').src = opt.dataset.image;
        $('productImage').style.display = 'block';
      }

      updateSkuSummary();
    });
  });
}

function updateSkuSummary() {
  const summary = document.getElementById('skuSummary');
  const parts = Object.entries(selectedSkus).map(
    ([group, val]) => `${group}: ${val.name}`
  );

  if (parts.length > 0) {
    summary.textContent = 'Đã chọn: ' + parts.join(' | ');
    summary.classList.add('visible');
  } else {
    summary.classList.remove('visible');
  }
}

function getSelectedSkuText() {
  const parts = Object.entries(selectedSkus).map(
    ([group, val]) => `${group}: ${val.name}`
  );
  return parts.join(', ');
}

async function handleOrder() {
  if (!currentProduct) return;
  $('orderError').style.display = 'none';
  $('orderSuccess').style.display = 'none';

  $('btnOrder').disabled = true;
  $('btnOrder').textContent = 'Đang đặt hàng...';

  // Build description with SKU selections
  const skuText = getSelectedSkuText();
  const noteText = $('note').value.trim();
  const descParts = [currentProduct.title];
  if (skuText) descParts.push('Tùy chọn: ' + skuText);

  try {
    const res = await apiCall('POST', '/api/orders', {
      product_name: currentProduct.title,
      product_price: currentProduct.price,
      product_image: currentProduct.image,
      source_url: currentProduct.url,
      quantity: parseInt($('quantity').value) || 1,
      cn_tracking_code: $('trackingCode').value.trim(),
      note: noteText,
      product_description: descParts.join('\n'),
      sku_selections: selectedSkus,
    });

    if (res.success) {
      $('orderSuccess').textContent = `Đặt hàng thành công! Mã: ${res.order.order_code}`;
      $('orderSuccess').style.display = 'block';
      $('note').value = '';
      $('trackingCode').value = '';
      $('quantity').value = '1';
    } else {
      showError('orderError', res.message || 'Đặt hàng thất bại');
    }
  } catch (err) {
    showError('orderError', 'Không kết nối được server');
  }

  $('btnOrder').disabled = false;
  $('btnOrder').textContent = '🛒 Đặt hàng';
}

async function loadOrders() {
  $('ordersList').innerHTML = '<div class="loading">Đang tải...</div>';

  try {
    const res = await apiCall('GET', '/api/orders');
    if (res.success && res.orders.length > 0) {
      $('ordersList').innerHTML = res.orders
        .map(
          (o) => `
        <div class="order-item">
          <div class="order-code">${o.order_code}</div>
          <div class="order-name">${escapeHtml(o.product_name || '—')}</div>
          <div class="order-meta">
            <span>${o.created_at?.substring(0, 10) || ''}</span>
            <span class="status-badge status-${o.status}">${statusLabels[o.status] || o.status}</span>
          </div>
        </div>
      `
        )
        .join('');
    } else if (res.success) {
      $('ordersList').innerHTML = '<div class="empty-state"><p>Chưa có đơn hàng nào</p></div>';
    } else {
      $('ordersList').innerHTML = '<div class="error">Lỗi tải đơn hàng</div>';
    }
  } catch (err) {
    $('ordersList').innerHTML = '<div class="error">Không kết nối được server</div>';
  }
}

async function apiCall(method, path, body = null) {
  const url = getApiUrl() + path;
  const headers = { 'Content-Type': 'application/json' };

  const data = await chrome.storage.local.get(['token']);
  if (data.token) {
    headers['Authorization'] = 'Bearer ' + data.token;
  }

  const opts = { method, headers };
  if (body) opts.body = JSON.stringify(body);

  const res = await fetch(url, opts);

  if (res.status === 401) {
    await chrome.storage.local.remove(['token', 'user']);
    showLoginSection();
    throw new Error('Unauthorized');
  }

  return res.json();
}

function showError(elementId, message) {
  const el = $(elementId);
  el.textContent = message;
  el.style.display = 'block';
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function escapeAttr(text) {
  return (text || '')
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}
