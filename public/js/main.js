function addToCart(productId) {
    // Giả sử bạn dùng biến session trong PHP để check login
    const isLoggedIn = false; // Thay bằng giá trị thực tế từ server

    if (!isLoggedIn) {
        alert("Vui lòng đăng nhập để sử dụng giỏ hàng!");
        window.location.href = "index.php?action=login";
        return;
    }

    // Gửi yêu cầu AJAX thêm sản phẩm
    console.log("Thêm sản phẩm ID: " + productId + " vào giỏ hàng");
    alert("Đã thêm bánh vào giỏ thành công!");
}

// Hiệu ứng cuộn header
window.onscroll = function() {
    let header = document.querySelector("header");
    if (window.pageYOffset > 50) {
        header.style.padding = "5px 0";
    } else {
        header.style.padding = "15px 0";
    }
};