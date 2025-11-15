// client/assets/cart_script.js

const CART_STORAGE_KEY = 'clientCart';
const SHIPPING_FEE = 30000; // Định nghĩa phí vận chuyển cố định

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
 * Thêm sản phẩm vào giỏ hàng.
 * @param {string} productId
 * @param {string} productName
 * @param {number} price
 * @param {number} quantity
 */
window.addToCart = (productId, productName, price, quantity = 1) => {
    // 1. Kiểm tra đăng nhập
    if (!window.isClientLoggedIn()) {
        alert("Vui lòng đăng nhập để sử dụng chức năng giỏ hàng.");
        // Chuyển hướng đến trang đăng nhập khách hàng (client_login.html)
        // window.location.href = 'client_login.html'; 
        return;
    }

    let cart = getCart();
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        // Nếu sản phẩm đã tồn tại, tăng số lượng
        existingItem.quantity += quantity;
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm mới
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: quantity
        });
    }

    saveCart(cart);
    alert(`${productName} đã được thêm vào giỏ hàng. Số lượng: ${existingItem ? existingItem.quantity : quantity}`);
    updateCartIconCount(cart);
};

/**
 * Cập nhật số lượng sản phẩm trong giỏ hàng (Áp dụng cho trang giỏ hàng).
 * @param {string} productId
 * @param {number} newQuantity
 */
window.updateQuantity = (productId, newQuantity) => {
    let cart = getCart();
    const itemIndex = cart.findIndex(item => item.id === productId);

    if (itemIndex > -1) {
        if (newQuantity > 0) {
            cart[itemIndex].quantity = newQuantity;
        } else {
            // Nếu số lượng là 0, xóa sản phẩm
            cart.splice(itemIndex, 1);
        }
    }
    saveCart(cart);
    renderCart(); // Gọi hàm render lại giao diện giỏ hàng
    updateCartIconCount(cart);
};

/**
 * Xóa hoàn toàn một sản phẩm khỏi giỏ hàng.
 * @param {string} productId
 */
window.removeFromCart = (productId) => {
    let cart = getCart();
    const initialLength = cart.length;
    cart = cart.filter(item => item.id !== productId);
    
    if (cart.length < initialLength) {
        saveCart(cart);
        alert("Đã xóa sản phẩm khỏi giỏ hàng.");
        renderCart(); 
        updateCartIconCount(cart);
    }
};

/**
 * Cập nhật số lượng sản phẩm trên icon giỏ hàng (Header).
 */
const updateCartIconCount = (cart = getCart()) => {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCountElement.textContent = totalItems;
        // Hiển thị hoặc ẩn icon giỏ hàng dựa trên totalItems
        // Bỏ đoạn này để luôn hiển thị số 0 nếu giỏ hàng rỗng
        // cartCountElement.style.display = totalItems > 0 ? 'inline-block' : 'none'; 
    }
};

// Hàm hiển thị giỏ hàng (Chỉ chạy trên trang giỏ hàng)
const renderCart = () => {
    if (!document.getElementById('cart-items-container')) return;
    
    const cart = getCart();
    const container = document.getElementById('cart-items-container');
    container.innerHTML = ''; // Xóa nội dung cũ
    let totalAmount = 0;
    
    if (cart.length === 0) {
        container.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 50px;">Giỏ hàng của bạn đang trống.</td></tr>';
        document.getElementById('cart-total-amount').textContent = '0 VNĐ';
        // Thêm logic ẩn nút Thanh toán nếu cần
        return;
    }

    // Hàm format tiền tệ (được sử dụng lại)
    const formatCurrency = (amount) => {
        return amount.toLocaleString('vi-VN') + ' VNĐ';
    };

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        totalAmount += itemTotal;

        const row = document.createElement('tr');
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

    document.getElementById('cart-total-amount').textContent = formatCurrency(totalAmount);
};

/**
 * Tính toán tổng tiền trước thuế/phí.
 * @param {Array} cart - Mảng giỏ hàng.
 * @returns {number} Tổng tiền.
 */
const calculateSubtotal = (cart) => {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    return subtotal;
}

// ----------------------------------------------------------------------
// ⚠️ HÀM QUAN TRỌNG: Cập nhật tóm tắt đơn hàng trên trang Thanh Toán (checkout.html)
// ----------------------------------------------------------------------
window.renderCheckoutSummary = () => {
    if (!document.getElementById('checkout-form')) return; // Chỉ chạy trên trang checkout

    const cart = getCart();
    
    // Kiểm tra giỏ hàng rỗng (bảo vệ checkout)
    if (cart.length === 0) {
        alert('Giỏ hàng trống. Không thể thanh toán.');
        window.location.href = 'index2.html'; 
        return;
    }

    // 1. Tính toán giá trị
    const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
    const subtotalAmount = calculateSubtotal(cart);
    const totalFinal = subtotalAmount + SHIPPING_FEE;
    
    // 2. Định dạng tiền tệ
    const formatCurrency = (amount) => {
        return amount.toLocaleString('vi-VN') + ' VNĐ';
    };

    // 3. Lấy các phần tử DOM theo ID trên checkout.html
    const itemCountEl = document.getElementById('summary-item-count');
    const subtotalEl = document.getElementById('summary-subtotal');
    const shippingEl = document.getElementById('summary-shipping');
    const totalEl = document.getElementById('summary-total');

    // 4. Cập nhật nội dung
    if (itemCountEl) itemCountEl.textContent = itemCount;
    if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotalAmount);
    // Phí vận chuyển
    if (shippingEl) shippingEl.textContent = formatCurrency(SHIPPING_FEE);
    // Tổng cuối cùng
    if (totalEl) totalEl.textContent = formatCurrency(totalFinal);
};


// Khởi tạo: Cập nhật số lượng giỏ hàng và render giỏ hàng/checkout khi trang tải
document.addEventListener('DOMContentLoaded', () => {
    updateCartIconCount();
    
    // Chỉ render giỏ hàng nếu đang ở trang giỏ hàng
    if (document.getElementById('cart-items-container')) {
        renderCart();
    }
    
    // Chỉ render tóm tắt checkout nếu đang ở trang checkout
    if (document.getElementById('checkout-form')) {
        window.renderCheckoutSummary();
    }
});