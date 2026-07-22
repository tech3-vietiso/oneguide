<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Tour\Tour;

require '../vendor/autoload.php';

/*
 * Ví dụ: Gán (đồng bộ) danh sách hướng dẫn viên cho một tour đã tồn tại.
 *
 * Luồng xử lý:
 *   1. Khởi tạo Client với thông tin xác thực.
 *   2. Tạo đối tượng Tour và trỏ tới tour cần gán bằng ID.
 *   3. Thêm từng hướng dẫn viên (Guide) kèm những ngày họ phụ trách.
 *   4. Gọi syncGuides() để đẩy dữ liệu lên OneGuide.
 */

// Bước 1: Khởi tạo Client.
// - api_key / secret: khóa xác thực do OneGuide cấp.
// - url: địa chỉ gốc của API.
$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxx',
    'url' => 'https://xxxxxxxxxxxxx'
]);

// Danh sách hướng dẫn viên cần gán cho tour.
// - card_number: số thẻ hướng dẫn viên (bắt buộc, dùng để định danh).
// - phone / email: thông tin liên hệ (email là bắt buộc khi đồng bộ).
$guides = [
    [
        'card_number' => 148235149,
        'phone' => '0327145495',
        'email' => 'nguyenhonghanh@gmail.com'
    ],
    [
        'card_number' => 201265735,
        'phone' => '0945385168',
        'email' => 'nguyenhonghanh1@gmail.com'
    ]
];

// Bước 2: Tạo đối tượng Tour và chỉ định ID của tour cần gán hướng dẫn viên.
$tour = new Tour($client);
$tour->setId(111);

// Bước 3: Duyệt danh sách và thêm từng hướng dẫn viên vào tour.
foreach ($guides as $item) {
    // Khởi tạo Guide với số thẻ.
    $guide = new Guide($item['card_number']);

    // Chỉ gán số điện thoại khi có dữ liệu.
    if (!empty($item['phone'])) {
        $guide->setPhone($item['phone']);
    }

    // Chỉ gán email khi có dữ liệu (email dùng để định danh, bắt buộc).
    if (!empty($item['email'])) {
        $guide->setEmail($item['email']);
    }

    // Tham số thứ hai [1, 2, 3, 4] là các ngày trong tour mà hướng dẫn viên này phụ trách.
    $tour->addGuide($guide, [
        1, 2, 3, 4
    ]);
}

// Bước 4: Đẩy toàn bộ hướng dẫn viên đã thêm lên OneGuide.
// Trước khi gửi, SDK sẽ tự validate (ví dụ: email bắt buộc và hợp lệ).
$tour->syncGuides();
