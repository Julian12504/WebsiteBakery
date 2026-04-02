// client/assets/cart_script.js

const CART_STORAGE_KEY = "clientCart";
const SHIPPING_FEE = 30000; // Định nghĩa phí vận chuyển cố định

// ----------------------------------------------------------------------
// 🛠️ HÀM HỖ TRỢ CHUNG
// ----------------------------------------------------------------------

/**
 * Lấy giỏ hàng từ Local Storage.
 * @returns {Array} Mảng các sản phẩm trong giỏ hàng.
 */
const getCart = () => {
  const cart = localStorage.getItem(CART_STORAGE_KEY);
  return cart ? JSON.parse(cart) : [];
};

/**
 * Lưu giỏ hàng vào Local Storage.
 * @param {Array} cart - Mảng giỏ hàng mới.
 */
const saveCart = (cart) => {
  localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
};

/**
 * Cập nhật số lượng sản phẩm trên icon giỏ hàng (Header).
 */
const updateCartIconCount = (cart = getCart()) => {
  const cartCountElement = document.getElementById("cart-count");
  if (cartCountElement) {
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    cartCountElement.textContent = totalItems;
  }
};

/**
 * Tính toán tổng tiền trước thuế/phí.
 * @param {Array} cart - Mảng giỏ hàng.
 * @returns {number} Tổng tiền.
 */
const calculateSubtotal = (cart) => {
  let subtotal = 0;
  cart.forEach((item) => {
    subtotal += item.price * item.quantity;
  });
  return subtotal;
};

// Hàm format tiền tệ (được sử dụng lại)
const formatCurrency = (amount) => {
  return amount.toLocaleString("vi-VN") + " VNĐ";
};

// ----------------------------------------------------------------------
// 🛒 LOGIC GIỎ HÀNG (CRUD)
// ----------------------------------------------------------------------

/**
 * Thêm sản phẩm vào giỏ hàng.
 * @param {string} productId
 * @param {string} productName
 * @param {number} price
 * @param {number} quantity
 */
window.addToCart = (productId, productName, price, quantity = 1) => {
  // 1. Kiểm tra đăng nhập
  if (typeof window.isClientLoggedIn !== "function") {
    // Đây là lỗi nếu client_auth.js không load đúng
    console.error(
      "LỖI AUTH: Hàm window.isClientLoggedIn không tồn tại. Đã tải client_auth.js chưa?",
    );
    alert("Lỗi hệ thống: Vui lòng tải lại trang.");
    return;
  }

  if (!window.isClientLoggedIn()) {
    console.warn("Chặn Giỏ hàng: Người dùng chưa đăng nhập.");
    alert("Vui lòng đăng nhập để sử dụng chức năng giỏ hàng.");
    return;
  }

  // 2. Kiểm tra dữ liệu sản phẩm
  if (!productId || !productName || typeof price !== "number" || price <= 0) {
    console.error("LỖI DATA: Dữ liệu sản phẩm không hợp lệ:", {
      productId,
      productName,
      price,
    });
    alert("Lỗi dữ liệu sản phẩm. Vui lòng thử lại.");
    return;
  }

  let cart = getCart();
  const existingItem = cart.find((item) => item.id === productId);

  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({
      id: productId,
      name: productName,
      price: price,
      quantity: quantity,
    });
  }

  saveCart(cart);
  console.log(
    `Đã thêm ${productName} (ID: ${productId}) vào giỏ. Số lượng mới: ${existingItem ? existingItem.quantity : quantity}`,
  );
  // alert(`${productName} đã được thêm vào giỏ hàng. Số lượng: ${existingItem ? existingItem.quantity : quantity}`);
  updateCartIconCount(cart);
  return true; // Trả về true nếu thêm thành công
};

/**
 * Xử lý chức năng Mua Ngay: Thêm sản phẩm vào Giỏ và chuyển đến trang Checkout.
 * @param {Event} event - Đối tượng sự kiện (bắt buộc phải có).
 * @param {string} productId
 * @param {string} productName
 * @param {number} price
 * @param {number} quantity
 */
window.buyNowAndCheckout = (
  event,
  productId,
  productName,
  price,
  quantity = 1,
) => {
  // Ngăn chặn hành vi mặc định nếu là thẻ <a> (dù bạn dùng <button> nhưng nên giữ lại)
  if (event) event.preventDefault();

  // BƯỚC 1: THÊM VÀO GIỎ HÀNG
  const isAdded = window.addToCart(productId, productName, price, quantity);

  // BƯỚC 2: CHUYỂN HƯỚNG ĐẾN TRANG THANH TOÁN
  // Chỉ chuyển hướng nếu sản phẩm được thêm vào giỏ thành công VÀ giỏ hàng hiện tại có sản phẩm.
  if (isAdded && getCart().length > 0) {
    window.location.href = "checkout.html";
  } else {
    console.warn(
      "Chặn chuyển hướng: Thao tác thêm vào giỏ hàng không thành công (có thể do chưa đăng nhập hoặc lỗi dữ liệu).",
    );
  }
};

/**
 * Cập nhật số lượng sản phẩm trong giỏ hàng (Áp dụng cho trang giỏ hàng).
 * @param {string} productId
 * @param {number} newQuantity
 */
window.updateQuantity = (productId, newQuantity) => {
  let cart = getCart();
  const itemIndex = cart.findIndex((item) => item.id === productId);
  //... (logic update quantity)
  if (itemIndex > -1) {
    if (newQuantity > 0) {
      cart[itemIndex].quantity = newQuantity;
    } else {
      cart.splice(itemIndex, 1);
    }
  }
  saveCart(cart);
  renderCart();
  updateCartIconCount(cart);
};

/**
 * Xóa hoàn toàn một sản phẩm khỏi giỏ hàng.
 * @param {string} productId
 */
window.removeFromCart = (productId) => {
  let cart = getCart();
  const initialLength = cart.length;
  cart = cart.filter((item) => item.id !== productId);
  //... (logic remove from cart)
  if (cart.length < initialLength) {
    saveCart(cart);
    alert("Đã xóa sản phẩm khỏi giỏ hàng.");
    renderCart();
    updateCartIconCount(cart);
  }
};

// ----------------------------------------------------------------------
// 🖼️ LOGIC RENDER
// ----------------------------------------------------------------------

// Hàm hiển thị giỏ hàng (Chỉ chạy trên trang giỏ hàng)
const renderCart = () => {
  if (!document.getElementById("cart-items-container")) return;

  const cart = getCart();
  const container = document.getElementById("cart-items-container");
  container.innerHTML = "";
  let totalAmount = 0;

  if (cart.length === 0) {
    container.innerHTML =
      '<tr><td colspan="5" style="text-align: center; padding: 50px;">Giỏ hàng của bạn đang trống.</td></tr>';
    document.getElementById("cart-total-amount").textContent = "0 VNĐ";
    return;
  }

  cart.forEach((item) => {
    const itemTotal = item.price * item.quantity;
    totalAmount += itemTotal;

    const row = document.createElement("tr");
    row.innerHTML = `
            <td>${item.name}</td>
            <td>${formatCurrency(item.price)}</td>
            <td>
                <input type="number" value="${item.quantity}" min="1" 
                    onchange="updateQuantity('${item.id}', parseInt(this.value))" 
                    style="width: 60px;">
            </td>
            <td>${formatCurrency(itemTotal)}</td>
            <td>
                <button onclick="removeFromCart('${item.id}')" class="btn-danger">Xóa</button>
            </td>
        `;
    container.appendChild(row);
  });

  document.getElementById("cart-total-amount").textContent =
    formatCurrency(totalAmount);
};

// ----------------------------------------------------------------------
// ⚠️ Cập nhật tóm tắt đơn hàng trên trang Thanh Toán (checkout.html)
// ----------------------------------------------------------------------
window.renderCheckoutSummary = () => {
  if (!document.getElementById("checkout-form")) return;

  const cart = getCart();

  if (cart.length === 0) {
    alert("Giỏ hàng trống. Không thể thanh toán.");
    window.location.href = "index2.html";
    return;
  }

  const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
  const subtotalAmount = calculateSubtotal(cart);
  const totalFinal = subtotalAmount + SHIPPING_FEE;

  const itemCountEl = document.getElementById("summary-item-count");
  const subtotalEl = document.getElementById("summary-subtotal");
  const shippingEl = document.getElementById("summary-shipping");
  const totalEl = document.getElementById("summary-total");

  if (itemCountEl) itemCountEl.textContent = itemCount;
  if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotalAmount);
  if (shippingEl) shippingEl.textContent = formatCurrency(SHIPPING_FEE);
  if (totalEl) totalEl.textContent = formatCurrency(totalFinal);
};

// ----------------------------------------------------------------------
// 🚀 KHỞI TẠO
// ----------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", () => {
  updateCartIconCount();

  if (document.getElementById("cart-items-container")) {
    renderCart();
  }

  if (document.getElementById("checkout-form")) {
    window.renderCheckoutSummary();
  }
});
