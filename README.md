# Nhóm 20 - WebsiteBakery

Người thực hiện: Lê Thanh Hùng - 3122411059

tài khoản khách hàng: user@gmail.com
mật khẩu:123456
tài khoản admin: admin
mật khẩu: 123456


project/
│
├── public/
│   ├── index.php          # user site
│   ├── admin.php          # entry cho admin
│   │
│   ├── css/
│   ├── js/
│   └── images/
│
├── app/
│   ├── controllers/
│   │   ├── HomeController.php
│   │   └── admin/
│   │       ├── DashboardController.php
│   │       └── ProductController.php
│   │
│   ├── models/
│   │   └── Product.php
│   │
│   └── views/
│       ├── user/
│       │   ├── home.php
│       │   └── product.php
│       │
│       └── admin/
│           ├── dashboard.php
│           └── product.php
│
├── routes/
│   ├── web.php        # user routes
│   └── admin.php      # admin routes
│
├── config/
│   └── database.php
│
└── includes/
    ├── header.php
    ├── footer.php