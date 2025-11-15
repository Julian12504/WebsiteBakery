// client/assets/client_pagination.js

document.addEventListener('DOMContentLoaded', () => {
    
    // --- PHÂN TRANG LOGIC ---

    // 1. Lấy các phần tử cần thiết
    const productGrid = document.querySelector('.product-grid'); 
    const items = productGrid ? productGrid.querySelectorAll('.product-card') : [];
    const paginationContainer = document.querySelector('.pagination-container');

    if (items.length === 0 || !paginationContainer) {
        console.log("Không tìm thấy sản phẩm hoặc container phân trang.");
        return; // Dừng nếu không có sản phẩm hoặc không có container phân trang
    }

    const itemsPerPage = 8; // Số sản phẩm trên mỗi trang (Cài đặt mặc định)
    const totalItems = items.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    let currentPage = 1;

    const paginationNumbers = document.getElementById('page-numbers');
    const prevButton = document.getElementById('prev-btn');
    const nextButton = document.getElementById('next-btn');
    
    // Nếu tổng số sản phẩm ít hơn hoặc bằng số lượng trên mỗi trang, ẩn phân trang
    if (totalItems <= itemsPerPage) {
        paginationContainer.style.display = 'none';
        items.forEach(item => item.style.display = 'block'); // Đảm bảo tất cả đều hiển thị
        return;
    }


    // 2. Hàm hiển thị sản phẩm của trang hiện tại
    function displayPage(page) {
        // Cập nhật trang hiện tại
        currentPage = page;
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Ẩn tất cả trước
        items.forEach(item => item.classList.remove('active-page-item'));

        // Chỉ hiện các sản phẩm trong phạm vi trang hiện tại
        for (let i = start; i < end && i < totalItems; i++) {
            items[i].classList.add('active-page-item');
        }
        
        // Cuộn lên đầu danh sách (Tùy chọn)
        productGrid.scrollIntoView({ behavior: 'smooth' });
        
        // Cập nhật trạng thái nút
        updatePaginationControls();
    }

    // 3. Hàm tạo các nút số trang
    function setupPagination() {
        paginationNumbers.innerHTML = ''; // Xóa các nút cũ
        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.addEventListener('click', () => displayPage(i));
            paginationNumbers.appendChild(button);
        }
    }

    // 4. Hàm cập nhật trạng thái nút (Active, Disabled)
    function updatePaginationControls() {
        // Cập nhật nút Previous/Next
        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage === totalPages;

        // Cập nhật class 'active' cho nút số trang
        paginationNumbers.querySelectorAll('button').forEach(btn => {
            if (parseInt(btn.textContent) === currentPage) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // 5. Thêm sự kiện cho nút Prev/Next
    prevButton.addEventListener('click', () => {
        if (currentPage > 1) {
            displayPage(currentPage - 1);
        }
    });

    nextButton.addEventListener('click', () => {
        if (currentPage < totalPages) {
            displayPage(currentPage + 1);
        }
    });

    // 6. Khởi tạo
    setupPagination();
    displayPage(1); // Mở trang đầu tiên
});