/* client/assets/client_script.js */

// ... (Các hàm khác như formatCurrency, populateUserProfileForm) ...

// --- HÀM HỖ TRỢ ĐỌC THAM SỐ URL ---
function getQueryParams() {
    const params = {};
    const urlParams = new URLSearchParams(window.location.search);

    // Lấy các tham số từ form index.html
    params.searchTerm = urlParams.get('name') || '';
    params.category = urlParams.get('category') || 'all';
    params.priceMin = urlParams.get('min_price');
    params.priceMax = urlParams.get('max_price');
    
    // Chuyển đổi giá sang số và xử lý null/rỗng
    params.priceMin = params.priceMin ? parseFloat(params.priceMin) : null;
    params.priceMax = params.priceMax ? parseFloat(params.priceMax) : null;
    
    return params;
}

// --- HÀM LỌC SẢN PHẨM CHÍNH (filterProducts) ---
// (Giữ nguyên logic filterProducts đã cung cấp ở câu trả lời trước,
// nó là hàm chịu trách nhiệm ẩn/hiện các thẻ sản phẩm HTML)
function filterProducts(category, searchTerm = '', priceMin = null, priceMax = null) {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        // ... (Logic đọc dữ liệu từ card và so sánh) ...
        const productCategoryText = card.querySelector('.category')
            ? card.querySelector('.category').textContent.replace('Loại: ', '').trim()
            : ''; 
        const productName = card.querySelector('h3 a').textContent.toLowerCase();
        const productPrice = parseFloat(card.querySelector('.price').textContent.replace(/[^0-9]/g, '')) || Infinity; 
        
        // Áp dụng các tiêu chí lọc
        const matchesName = searchTerm === '' || productName.includes(searchTerm.toLowerCase().trim());
        const matchesCategory = (category === 'all') || (productCategoryText === category);
        const matchesPriceMin = priceMin === null || isNaN(priceMin) || productPrice >= priceMin;
        const matchesPriceMax = priceMax === null || isNaN(priceMax) || productPrice <= priceMax;
        
        const isMatch = matchesName && matchesCategory && matchesPriceMin && matchesPriceMax;

        card.style.display = isMatch ? 'block' : 'none';
    });
}


document.addEventListener('DOMContentLoaded', () => {
    
    // ... (Các biến và logic chung) ...
    const categoryLinks = document.querySelectorAll('.category-list a');
    const searchForm = document.getElementById('search-form'); 
    
    // 🎯 3. KHỞI TẠO LỌC BẰNG THAM SỐ URL
    const urlParams = getQueryParams();
    let { category, searchTerm, priceMin, priceMax } = urlParams;
    
    // Nếu có tham số tìm kiếm từ URL (tức là vừa chuyển từ index.html sang)
    if (searchTerm || category !== 'all' || priceMin !== null || priceMax !== null) {
        
        // 3.1. Cập nhật Form Tìm kiếm trên product_list.html để hiển thị tiêu chí
        if (searchForm) {
            document.getElementById('search-name').value = searchTerm;
            document.getElementById('search-category').value = category;
            
            if (priceMin !== null) document.getElementById('search-price-min').value = priceMin;
            if (priceMax !== null) document.getElementById('search-price-max').value = priceMax;
        }
        
        // 3.2. Cập nhật trạng thái active trên Sidebar
        categoryLinks.forEach(link => {
            link.classList.remove('active-category');
            if (link.getAttribute('data-filter') === category) {
                link.classList.add('active-category');
            }
        });
    }

    // 4. GÁN SỰ KIỆN FORM TÌM KIẾM NÂNG CAO (để lọc trên trang này)
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            // Lấy giá trị từ form (đã được người dùng thay đổi)
            const currentSearchTerm = document.getElementById('search-name').value;
            const currentCategory = document.getElementById('search-category').value;
            const currentPriceMin = document.getElementById('search-price-min').value ? parseFloat(document.getElementById('search-price-min').value) : null;
            const currentPriceMax = document.getElementById('search-price-max').value ? parseFloat(document.getElementById('search-price-max').value) : null;
            
            categoryLinks.forEach(item => item.classList.remove('active-category'));
            
            filterProducts(currentCategory, currentSearchTerm, currentPriceMin, currentPriceMax);
        });
    }

    // 5. GÁN SỰ KIỆN SIDEBAR (để lọc trên trang này)
    categoryLinks.forEach(link => {
        // ... (Logic click sidebar như cũ: reset form và gọi filterProducts) ...
        link.addEventListener('click', (e) => {
            e.preventDefault(); 
            const selectedCategory = link.getAttribute('data-filter');

            categoryLinks.forEach(item => item.classList.remove('active-category'));
            link.classList.add('active-category');

            if (searchForm) {
                searchForm.reset();
            }

            filterProducts(selectedCategory);
        });
    });

    // 6. KHỞI TẠO CUỐI CÙNG
    // Áp dụng bộ lọc ban đầu (từ URL hoặc mặc định)
    filterProducts(category, searchTerm, priceMin, priceMax); 
});