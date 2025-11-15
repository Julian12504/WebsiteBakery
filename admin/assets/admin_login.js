// admin/assets/admin_login.js

document.addEventListener('DOMContentLoaded', () => {
    // --- CẤU HÌNH CHUNG ---
    const LOGIN_PAGE = 'admin_login.html';
    const DASHBOARD_URL = 'admin_dashboard.html';
    
    // Kiểm tra xem URL hiện tại có phải là trang đăng nhập không
    const isLoginPage = document.URL.includes(LOGIN_PAGE);
    const adminLoggedIn = localStorage.getItem('adminLoggedIn');

    // --- LOGIC KIỂM TRA QUYỀN TRUY CẬP (Áp dụng cho mọi trang trừ login) ---
    if (!isLoginPage) {
        if (adminLoggedIn !== 'true') {
            console.warn('Truy cập bị từ chối. Chuyển hướng về trang đăng nhập.');
            window.location.href = LOGIN_PAGE;
            return; 
        }
    }
    
    // --- LOGIC CHUYỂN HƯỚNG KHI ĐÃ ĐĂNG NHẬP (Áp dụng cho trang login) ---
    if (isLoginPage && adminLoggedIn === 'true') {
        console.log('Đã đăng nhập. Chuyển hướng đến Dashboard.');
        window.location.href = DASHBOARD_URL;
        return; 
    }

    // --- LOGIC ĐĂNG NHẬP (Chỉ chạy trên trang admin_login.html) ---
    const loginForm = document.getElementById('admin-login-form');
    if (loginForm) {
        const ADMIN_USERNAME = 'admin';
        const ADMIN_PASSWORD = '123';
        
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            const usernameInput = loginForm.querySelector('#username');
            const passwordInput = loginForm.querySelector('#password');
            const errorMessage = loginForm.querySelector('.error-message');
            
            if (errorMessage) {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
            }

            const enteredUsername = usernameInput.value.trim();
            const enteredPassword = passwordInput.value;

            if (enteredUsername === ADMIN_USERNAME && enteredPassword === ADMIN_PASSWORD) {
                alert('Đăng nhập thành công! Chuyển hướng đến Dashboard...');
                localStorage.setItem('adminLoggedIn', 'true');
                window.location.href = DASHBOARD_URL; 
            } else {
                const msg = 'Tên đăng nhập hoặc mật khẩu không đúng. Vui lòng thử lại.';
                
                if (errorMessage) {
                    errorMessage.textContent = msg;
                    errorMessage.style.display = 'block';
                } else {
                    alert(msg);
                }
                passwordInput.value = '';
            }
        });
    }

    // --- LOGIC ĐĂNG XUẤT (Chạy trên trang Dashboard/Quản lý khác) ---
    const logoutBtn = document.getElementById('logout-btn'); // Sử dụng ID thống nhất 'logout-btn'
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('adminLoggedIn');
            alert('Đã đăng xuất thành công.');
            window.location.href = LOGIN_PAGE;
        });
    }
});