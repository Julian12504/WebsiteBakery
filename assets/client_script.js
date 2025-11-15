// client/assets/client_script.js

// --- 0. DỮ LIỆU SẢN PHẨM GIẢ LẬP VÀ BIẾN LỌC ---

// client/assets/client_script.js

// --- 0. DỮ LIỆU SẢN PHẨM GIẢ LẬP VÀ BIẾN LỌC ---

const ALL_PRODUCTS = [
    // ⚠️ ĐÃ SỬA: Thêm "assets/" vào đầu đường dẫn hình ảnh
    { id: '1', name: 'Tiramisu Ý Truyền Thống', price: 180000, categoryId: 'mousse', categoryName: 'Mousse & Pudding', img: './assets/img/IPOS-03.jpg', stock: 10 },
    { id: '2', name: 'Bánh Kem Dâu Tây Thượng Hạng', price: 350000, categoryId: 'kem', categoryName: 'Bánh Kem', img: './assets/img/IPOS-03.jpg', stock: 5 },
    { id: '3', name: 'Mousse Matcha Trà Xanh', price: 220000, categoryId: 'mousse', categoryName: 'Mousse & Pudding', img: './assets/img/IPOS-03.jpg', stock: 20 },
    { id: '4', name: 'Tart Trái Cây Mix', price: 150000, categoryId: 'tart', categoryName: 'Tart & Pie', img: './assets/img/IPOS-03.jpg', stock: 0 },
    { id: '5', name: 'Bánh Chocolate Lava', price: 280000, categoryId: 'kem', categoryName: 'Bánh Kem', img: './assets/img/IPOS-03.jpg', stock: 8 },
    { id: '6', name: 'Apple Pie Truyền Thống', price: 120000, categoryId: 'tart', categoryName: 'Tart & Pie', img: './assets/img/IPOS-03.jpg', stock: 12 },
    { id: '7', name: 'Bánh Kem Táo Xanh', price: 250000, categoryId: 'kem', categoryName: 'Bánh Kem', img: './assets/img/IPOS-03.jpg', stock: 3 },
    { id: '8', name: 'Mousse Dừa Lạnh', price: 190000, categoryId: 'mousse', categoryName: 'Mousse & Pudding', img: './assets/img/IPOS-03.jpg', stock: 15 },
    // Thêm các sản phẩm khác để kiểm tra phân trang
    { id: '9', name: 'Bánh Mì Bơ Tỏi', price: 55000, categoryId: 'mi', categoryName: 'Bánh Mì Ngọt', img: './assets/img/IPOS-03.jpg', stock: 10 },
    { id: '10', name: 'Cookie Socola Chip', price: 35000, categoryId: 'kho', categoryName: 'Bánh Khô & Cookies', img: './assets/img/IPOS-03.jpg', stock: 30 },
    { id: '11', name: 'Bánh Mì Phô Mai', price: 65000, categoryId: 'mi', categoryName: 'Bánh Mì Ngọt', img: './assets/img/IPOS-03.jpg', stock: 15 },
    { id: '12', name: 'Tart Chanh Dây', price: 140000, categoryId: 'tart', categoryName: 'Tart & Pie', img: './assets/img/IPOS-03.jpg', stock: 18 },
    { id: '13', name: 'Bánh Kem Sầu Riêng', price: 400000, categoryId: 'kem', categoryName: 'Bánh Kem', img: './assets/img/IPOS-03.jpg', stock: 2 },
    { id: '14', name: 'Cookie Yến Mạch', price: 40000, categoryId: 'kho', categoryName: 'Bánh Khô & Cookies', img: './assets/img/IPOS-03.jpg', stock: 25 },
];
// ... (các đoạn code JS còn lại giữ nguyên) ...
let currentFilters = {
    keyword: '',
    category: 'all', // Mặc định hiển thị tất cả
    minPrice: 0,
    maxPrice: 500000 // Giả định thanh trượt giá tối đa 500k VNĐ
};

// ⚠️ BIẾN PHÂN TRANG
const PRODUCTS_PER_PAGE = 8;
let currentPage = 1;

// Hàm định dạng tiền tệ (sử dụng lại trong toàn bộ file)
const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';

// -------------------------------------------------------------------------------------
// ⚠️ HÀM TẠO VÀ XỬ LÝ GIAO DIỆN PHÂN TRANG (TÍCH HỢP TỪ client_pagination.js)
// -------------------------------------------------------------------------------------

/**
 * Hàm cập nhật giao diện thanh phân trang và gán sự kiện Prev/Next/Numbers
 * @param {number} totalPages - Tổng số trang
 * @param {number} currentPage - Trang hiện tại
 */
window.renderPagination = (totalPages, currentPage) => {
    const pageNumbersContainer = document.getElementById('page-numbers');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const paginationContainer = document.querySelector('.pagination-container');

    if (!pageNumbersContainer || !paginationContainer) return;
    
    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
    } else {
        paginationContainer.style.display = 'flex'; // Hiển thị lại nếu có nhiều hơn 1 trang
    }


    pageNumbersContainer.innerHTML = '';

    // Tạo nút số trang
    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = i;
        pageBtn.className = 'page-number-item';
        
        if (i === currentPage) {
            pageBtn.classList.add('active');
        }
        
        // Gán sự kiện click: Gọi hàm applyFiltersAndRender với trang mới
        pageBtn.addEventListener('click', () => {
            window.applyFiltersAndRender(i);
        });
        pageNumbersContainer.appendChild(pageBtn);
    }

    // Cập nhật trạng thái nút Previous/Next
    if (prevBtn) {
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                window.applyFiltersAndRender(currentPage - 1);
            }
        };
    }

    if (nextBtn) {
        nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                window.applyFiltersAndRender(currentPage + 1);
            }
        };
    }
};

document.addEventListener('DOMContentLoaded', () => {
    console.log("Client script loaded. JS/DOM basic interactions are ready.");

    // --- 1. MÔ PHỎNG GIỎ HÀNG VÀ CẬP NHẬT HEADER (Giữ nguyên) ---

    // ... (Phần 1 Giỏ hàng và Header) ...

    // --- 2. LOGIC TRANG CHECKOUT (Giữ nguyên) ---

    const addressOptions = document.querySelectorAll('input[name="address_option"]');
    const newAddressFields = document.getElementById('new-address-fields');
    const newAddressInputs = newAddressFields ? newAddressFields.querySelectorAll('input, textarea') : [];

    if (addressOptions.length > 0 && newAddressFields) {
        const toggleRequired = (isRequired) => {
            newAddressInputs.forEach(input => {
                input.required = isRequired;
            });
        };
        
        const toggleAddressFields = (isNew) => {
            newAddressFields.style.display = isNew ? 'block' : 'none';
            toggleRequired(isNew);
        };

        addressOptions.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleAddressFields(this.value === 'new');
            });
        });
        toggleAddressFields(document.querySelector('input[name="address_option"]:checked')?.value === 'new');
    }

    // --- 3. LOGIC TRANG GIỎ HÀNG (Giữ nguyên) ---

    const cartTable = document.querySelector('.cart-table');
    if (cartTable) {
        const updateCartTotals = () => {
            // Giả lập logic tính tổng
            let subtotal = 0; 
            const summaryBox = document.getElementById('cart-total-amount'); 
            if (summaryBox) {
                // Giả định tổng tiền là 100k
                summaryBox.textContent = formatCurrency(100000); 
            }
        };
        updateCartTotals();
    }

    // --- 4. HIỆU ỨNG PHÓNG TO ẢNH TOÀN MÀN HÌNH (Giữ nguyên) ---
    // ... (Phần logic modal của bạn) ...

    // -------------------------------------------------------------------------------------
    // ⚠️ --- 5. LOGIC LỌC SẢN PHẨM & RENDER --- 
    // -------------------------------------------------------------------------------------

    /**
     * Hàm lọc danh sách sản phẩm dựa trên tiêu chí
     */
    const filterProducts = (products) => {
        return products.filter(product => {
            const nameMatches = product.name.toLowerCase().includes(currentFilters.keyword.toLowerCase());
            const categoryMatches = currentFilters.category === 'all' || product.categoryId === currentFilters.category;
            const priceMatches = product.price >= currentFilters.minPrice && product.price <= currentFilters.maxPrice;
            
            return nameMatches && categoryMatches && priceMatches;
        });
    };

    /**
     * Hàm hiển thị sản phẩm ra giao diện (ĐÃ SỬA VÀ TÍCH HỢP PHÂN TRANG)
     */
    const renderProductGrid = (productList) => {
        const container = document.getElementById('product-grid');
        
        // KIỂM TRA ĐỂ TRÁNH LỖI NULL
        if (!container) {
            console.error("Lỗi: Không tìm thấy phần tử #product-grid trong HTML!"); 
            return;
        }
        
        container.innerHTML = '';
        
        if (productList.length === 0) {
            container.innerHTML = '<p style="text-align: center; width: 100%; padding: 40px;">Không tìm thấy sản phẩm nào phù hợp với tiêu chí lọc.</p>';
            window.renderPagination(0, 1); // Cập nhật phân trang
            return;
        }

        // LOGIC PHÂN TRANG: Cắt mảng để chỉ hiển thị sản phẩm của trang hiện tại
        const totalPages = Math.ceil(productList.length / PRODUCTS_PER_PAGE);
        const start = (currentPage - 1) * PRODUCTS_PER_PAGE;
        const end = start + PRODUCTS_PER_PAGE;
        const productsToRender = productList.slice(start, end);

        productsToRender.forEach(product => {
            const isOutOfStock = product.stock <= 0;
            const priceDisplay = formatCurrency(product.price);
            
            // Hàm checkAndRedirectToAuth phải được định nghĩa trong client_auth.js
            const addToCartCall = `checkAndRedirectToAuth(event, 'cart-add', {id: '${product.id}', name: '${product.name}', price: ${product.price}, quantity: 1})`;
            
            const card = document.createElement('div');
            card.className = 'product-card';
            if (isOutOfStock) {
                card.classList.add('out-of-stock');
            }
            
            card.innerHTML = `
                <img src="${product.img}" alt="${product.name}">
                <h3><a href="product_detail.html?id=${product.id}">${product.name}</a></h3>
                <p class="category">Loại: ${product.categoryName}</p>
                <p class="price">Giá: **${priceDisplay}**</p>
                ${isOutOfStock 
                    ? `<span class="btn-oos">Hết hàng</span>` 
                    : `<button class="btn-add-to-cart" onclick="${addToCartCall}">Thêm vào Giỏ</button>`
                }
                <a href="#" class="btn-secondary" onclick="checkAndRedirectToAuth(event, 'checkout', {id: '${product.id}'})">Mua Ngay</a>
            `;
            container.appendChild(card);
        });
        
        // GỌI HÀM RENDER PHÂN TRANG
        window.renderPagination(totalPages, currentPage);
    };

    /**
     * Hàm chính để áp dụng lọc và hiển thị
     * @param {number} page - Trang muốn chuyển đến (mặc định là 1 nếu là lọc mới)
     */
    window.applyFiltersAndRender = (page = 1) => {
        currentPage = page; // Cập nhật trang hiện tại
        const filteredList = filterProducts(ALL_PRODUCTS);
        renderProductGrid(filteredList);
    };

    // ----------------------------------------------------------------
    // 5.1. GÁN SỰ KIỆN LỌC THEO DANH MỤC
    // ----------------------------------------------------------------

    const categoryLinks = document.querySelectorAll('.category-list a');
    categoryLinks.forEach(link => {
        const categoryId = link.getAttribute('data-category-id'); 
        if (categoryId) {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                currentFilters.category = categoryId;
                
                // Cập nhật trạng thái active cho link
                categoryLinks.forEach(l => l.classList.remove('active-category'));
                link.classList.add('active-category'); // Sửa lỗi class: dùng active-category

                // Khi lọc mới, luôn reset về trang 1
                window.applyFiltersAndRender(1);
            });
        }
    });

    // ----------------------------------------------------------------
    // 5.2. GÁN SỰ KIỆN LỌC THEO GIÁ (Thanh trượt)
    // ----------------------------------------------------------------
    const priceRangeSlider = document.getElementById('price-range-slider');
    const priceValueDisplay = document.getElementById('price-value-display');
    // ⚠️ Sửa lỗi: Dùng bộ chọn CSS chính xác cho nút Áp dụng trong price-filter-group
    const priceApplyButton = document.querySelector('.price-filter-group .btn-primary'); 

    if (priceRangeSlider && priceValueDisplay && priceApplyButton) {
        // Cập nhật giá trị hiển thị khi thanh trượt thay đổi
        priceRangeSlider.addEventListener('input', () => {
            const value = parseInt(priceRangeSlider.value);
            priceValueDisplay.textContent = formatCurrency(value);
        });
        
        // Áp dụng bộ lọc khi nhấn nút
        priceApplyButton.addEventListener('click', () => {
            currentFilters.maxPrice = parseInt(priceRangeSlider.value);
            // Khi lọc mới, luôn reset về trang 1
            window.applyFiltersAndRender(1);
        });
        
        // Cập nhật giá trị hiển thị ban đầu
        priceValueDisplay.textContent = formatCurrency(parseInt(priceRangeSlider.value));
    }


    // ----------------------------------------------------------------
    // 5.3. GÁN SỰ KIỆN TÌM KIẾM NÂNG CAO (Header)
    // ----------------------------------------------------------------
    const advancedSearchForm = document.querySelector('.advanced-search-form');
    if (advancedSearchForm) {
        advancedSearchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            currentFilters.keyword = advancedSearchForm.querySelector('input[type="text"]').value.trim();
            const minPriceInput = advancedSearchForm.querySelector('input[placeholder="Giá từ"]').value;
            const maxPriceInput = advancedSearchForm.querySelector('input[placeholder="Giá đến"]').value;
            
            currentFilters.minPrice = minPriceInput ? parseInt(minPriceInput.replace(/\./g, '')) : 0;
            // Nếu MaxPriceInput rỗng, sử dụng giá trị MaxPrice hiện tại từ thanh trượt (500k mặc định)
            currentFilters.maxPrice = maxPriceInput ? parseInt(maxPriceInput.replace(/\./g, '')) : currentFilters.maxPrice; 
            
            window.applyFiltersAndRender(1); // Luôn reset về trang 1
        });
    }

    // ----------------------------------------------------------------
    // 5.4. LẦN ĐẦU TẢI TRANG
    // ----------------------------------------------------------------
    window.applyFiltersAndRender();
});