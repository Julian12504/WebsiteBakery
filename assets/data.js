// client/assets/product_data.js (Tạo file mới hoặc đặt trong client_script.js)

const ALL_PRODUCTS = [
    { id: '1', name: 'Tiramisu Ý Truyền Thống', price: 180000, categoryId: 'mousse', categoryName: 'Mousse & Pudding', img: './img/IPOS-03.jpg', stock: 10 },
    { id: '2', name: 'Bánh Kem Dâu Tây Thượng Hạng', price: 350000, categoryId: 'kem', categoryName: 'Bánh Kem', img: './img/IPOS-03.jpg', stock: 5 },
    { id: '3', name: 'Mousse Matcha Trà Xanh', price: 220000, categoryId: 'mousse', categoryName: 'Mousse & Pudding', img: './img/IPOS-03.jpg', stock: 20 },
    { id: '4', name: 'Tart Trái Cây Mix', price: 150000, categoryId: 'tart', categoryName: 'Tart & Pie', img: './img/IPOS-03.jpg', stock: 0 }, // Hết hàng
    { id: '5', name: 'Bánh Chocolate Lava', price: 280000, categoryId: 'kem', categoryName: 'Bánh Kem', img: './img/IPOS-03.jpg', stock: 8 },
    { id: '6', name: 'Apple Pie Truyền Thống', price: 120000, categoryId: 'tart', categoryName: 'Tart & Pie', img: './img/IPOS-03.jpg', stock: 12 },
    // Thêm các sản phẩm khác để kiểm tra tính năng lọc...
];