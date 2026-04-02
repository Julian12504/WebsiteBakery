const USER_STORAGE_KEY = "client_user_data";
const AUTH_LINK_ID = "client-auth-link"; // Bây giờ chỉ là link Đăng Nhập/Đăng Xuất

/**
 * Lấy thông tin người dùng hiện tại (ID, Tên) từ localStorage.
 * @returns {object|null} Thông tin người dùng hoặc null.
 */
function getCurrentUser() {
  const userData = localStorage.getItem(USER_STORAGE_KEY);
  return userData ? JSON.parse(userData) : null;
}

/**
 * Kiểm tra xem khách hàng đã đăng nhập chưa.
 * @returns {boolean} True nếu có client user data trong localStorage.
 */
window.isClientLoggedIn = () => {
  return getCurrentUser() !== null;
};

// Hàm đăng nhập (Mô phỏng)
window.clientLogin = (username, password) => {
  // 💡 ĐIỀU KIỆN XÁC THỰC: user@gmail.com / 123
  if (username === "user@gmail.com" && password === "123") {
    const userData = {
      id: "user_123",
      name: "Lê Thanh Hùng",
      email: username,
      token: "fake_jwt_token_12345",
      // THÔNG TIN MỚI ĐƯỢC THÊM VÀO LOCAL STORAGE
      phone: "0987654321", // Số điện thoại thật
      address: "1 An Dương Vương, Quận 5, TP.HCM", // Địa chỉ thật
    };
    localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(userData));
    return true;
  }
  return false;
};

/**
 * Hàm cập nhật giao diện liên kết Đăng Nhập/Đăng Xuất trên Header.
 * ⚠️ Sử dụng hai ID riêng biệt (#client-welcome-msg và #client-auth-link)
 */
window.updateAuthLink = () => {
  const authLink = document.getElementById(AUTH_LINK_ID); // Link Đăng Nhập/Đăng Xuất
  const welcomeMsg = document.getElementById("client-welcome-msg"); // Lời chào
  const user = getCurrentUser();

  if (authLink && welcomeMsg) {
    if (user) {
      // Đã đăng nhập

      // 1. Cập nhật lời chào (SPAN)
      welcomeMsg.textContent = ` Xin chào, ${user.name}`;
      welcomeMsg.style.display = "inline-block"; // Hiển thị lời chào

      // 2. Biến link Đăng Nhập thành link Đăng Xuất (A)
      authLink.textContent = "Đăng Xuất";
      authLink.href = "#";
      authLink.style.color = "#c9302c";

      // 3. Gán sự kiện click chỉ cho link Đăng Xuất
      authLink.removeEventListener("click", window.clientLogout); // Xóa listener cũ (nếu có)
      authLink.addEventListener("click", window.clientLogout);
    } else {
      // Chưa đăng nhập

      // 1. Ẩn lời chào (SPAN)
      welcomeMsg.textContent = "";
      welcomeMsg.style.display = "none";

      // 2. Khôi phục link về trạng thái Đăng Nhập (A)
      authLink.textContent = "Đăng Nhập";
      authLink.href = "login.html";
      authLink.style.color = "";

      // 3. Xóa sự kiện Đăng Xuất (quan trọng)
      authLink.removeEventListener("click", window.clientLogout);
    }
  }

  // Gọi hàm cập nhật giỏ hàng
  if (typeof window.updateCartIconCount === "function") {
    window.updateCartIconCount();
  }
};

// Hàm đăng xuất (Giữ nguyên)
window.clientLogout = (event) => {
  if (event) {
    event.preventDefault();
  }

  if (confirm("Bạn có chắc chắn muốn Đăng Xuất?")) {
    localStorage.removeItem(USER_STORAGE_KEY);
    localStorage.removeItem("clientCart");

    window.updateAuthLink();

    window.location.href = "index.html";
  }
};

// ----------------------------------------------------
// Tích hợp và chạy khi tải trang
// ----------------------------------------------------

document.addEventListener("DOMContentLoaded", () => {
  // 1. Cập nhật trạng thái Đăng nhập/Đăng xuất khi tải trang
  window.updateAuthLink();

  // 2. Xử lý logic Form Đăng nhập
  const loginForm = document.getElementById("client-login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const username = loginForm.querySelector("#client-username").value;
      const password = loginForm.querySelector("#client-password").value;

      const errorMessageElement = document.getElementById(
        "login-error-message",
      );
      if (errorMessageElement) {
        errorMessageElement.style.display = "none";
      }

      if (window.clientLogin(username, password)) {
        window.updateAuthLink();
        window.location.href = "index2.html";
      } else {
        if (errorMessageElement) {
          errorMessageElement.textContent = "Email hoặc Mật khẩu không đúng.";
          errorMessageElement.style.display = "block";
        } else {
          alert("Tên đăng nhập hoặc mật khẩu không đúng.");
        }
      }
    });
  }
});
