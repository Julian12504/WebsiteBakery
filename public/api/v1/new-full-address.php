<?php
header('Content-Type: application/json');

// Giả lập dữ liệu địa chỉ Việt Nam
$data = [
    "provinces" => [
        ["id" => 1, "name" => "Hà Nội"],
        ["id" => 2, "name" => "TP.HCM"],
        ["id" => 3, "name" => "Đà Nẵng"]
    ],
    "districts" => [
        1 => [
            ["id" => 10, "name" => "Ba Đình"],
            ["id" => 11, "name" => "Hoàn Kiếm"]
        ],
        2 => [
            ["id" => 20, "name" => "Quận 1"],
            ["id" => 21, "name" => "Quận 2"]
        ],
        3 => [
            ["id" => 30, "name" => "Hải Châu"],
            ["id" => 31, "name" => "Thanh Khê"]
        ]
    ],
    "wards" => [
        10 => [
            ["id" => 100, "name" => "Phúc Xá"],
            ["id" => 101, "name" => "Trúc Bạch"]
        ],
        11 => [
            ["id" => 110, "name" => "Phan Chu Trinh"],
            ["id" => 111, "name" => "Hàng Bài"]
        ],
        20 => [
            ["id" => 200, "name" => "Bến Nghé"],
            ["id" => 201, "name" => "Đa Kao"]
        ],
        21 => [
            ["id" => 210, "name" => "Thảo Điền"],
            ["id" => 211, "name" => "An Phú"]
        ],
        30 => [
            ["id" => 300, "name" => "Hải Châu 1"],
            ["id" => 301, "name" => "Hải Châu 2"]
        ],
        31 => [
            ["id" => 310, "name" => "Thanh Khê Tây"],
            ["id" => 311, "name" => "Thanh Khê Đông"]
        ]
    ]
];

echo json_encode($data);
?>