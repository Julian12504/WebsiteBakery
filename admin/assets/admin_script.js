// admin/assets/admin_script.js (Chỉ còn các logic chức năng quản trị)

document.addEventListener('DOMContentLoaded', () => {
    console.log("Admin script loaded. Dashboard interactions are ready.");

    // Lưu ý: Logic Đăng nhập (0, 5) và Đăng xuất đã được chuyển sang admin_login.js

    // --- 1. LOGIC QUẢN LÝ LOẠI SẢN PHẨM (admin/category_manage.html) ---
    
    const categoryForm = document.getElementById('category-form');
    const btnAddCategory = document.querySelector('.action-bar .btn-primary');
    
    if (categoryForm && btnAddCategory) {
        // Thêm hàm vào scope global để HTML có thể gọi
        window.showForm = () => {
            categoryForm.style.display = 'block';
            categoryForm.scrollIntoView({ behavior: 'smooth' });
        };
        window.hideForm = () => {
            categoryForm.style.display = 'none';
        };
        
        // Gắn sự kiện cho nút Thêm Loại Sản Phẩm Mới (Nếu chưa có onclick trong HTML)
        btnAddCategory.addEventListener('click', window.showForm);

        // Lắng nghe nút Hủy trong form
        categoryForm.querySelector('.btn-secondary')?.addEventListener('click', window.hideForm);
    }
    
    // --- 2. LOGIC CẢNH BÁO TỒN KHO (admin/stock_manage.html) ---
    
    const stockTable = document.querySelector('table');
    if (stockTable && document.title.includes("Tồn Kho")) {
        // Giả lập mức cảnh báo
        const ALERT_LEVEL = 15; 
        
        stockTable.querySelectorAll('tbody tr').forEach(row => {
            // Giả sử cột số lượng tồn là cột thứ 4 (index 3)
            const stockCell = row.children[3];
            const stockQuantity = parseInt(stockCell.textContent) || 0;
            
            if (stockQuantity < ALERT_LEVEL) {
                // Thêm class highlight vào hàng và cột để cảnh báo
                row.style.backgroundColor = '#fff3cd'; // Màu vàng nhạt
                stockCell.style.color = '#dc3545';
                stockCell.style.fontWeight = 'bold';
            }
        });
    }

    // --- 3. LOGIC CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (admin/order_detail.html) ---
    
    const orderStatusBox = document.querySelector('.order-status-box');
    if (orderStatusBox) {
        const statusDisplay = orderStatusBox.querySelector('span');
        const selectStatus = orderStatusBox.querySelector('#update_status');
        const btnUpdate = orderStatusBox.querySelector('.btn-success');
        
        btnUpdate.addEventListener('click', (e) => {
            e.preventDefault(); // Ngăn form submit
            const newStatusValue = selectStatus.value;
            let newStatusText = '';

            // Cập nhật text hiển thị dựa trên giá trị được chọn
            if (newStatusValue === 'processing') {
                newStatusText = 'Đã xử lý';
            } else if (newStatusValue === 'shipped') {
                newStatusText = 'Đã giao';
            } else if (newStatusValue === 'cancelled') {
                newStatusText = 'Hủy';
            }
            
            if (newStatusText) {
                statusDisplay.textContent = newStatusText;
                statusDisplay.className = `status-${newStatusValue}`; // Cập nhật class CSS
                alert(`Đã cập nhật trạng thái đơn hàng thành: ${newStatusText}`);
            }
        });
    }

    // --- 4. LOGIC QUẢN LÝ NHẬP HÀNG (admin/import_form.html) ---

    const importForm = document.querySelector('.import-form');
    if (importForm) {
        importForm.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-add')) {
                e.preventDefault();
                alert("Mô phỏng: Thêm dòng sản phẩm mới vào phiếu nhập.");
                // Trong thực tế, cần tạo một dòng <tr> mới và chèn vào <tbody>
            } else if (e.target.classList.contains('btn-remove')) {
                e.preventDefault();
                e.target.closest('tr').remove();
            } else if (e.target.classList.contains('btn-success')) {
                e.preventDefault();
                alert("Phiếu nhập đã được HOÀN THÀNH và cập nhật số lượng tồn kho!");
                window.location.href = 'import_list.html'; // Chuyển hướng về trang danh sách
            }
        });
    }
});