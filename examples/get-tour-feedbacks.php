<?php

use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Client;

require '../vendor/autoload.php';

/*
 * Ví dụ: Lấy danh sách feedback (ảnh/video) của một tour, có phân trang.
 *
 * Giống như danh mục tỉnh/thành, API trả về dữ liệu theo kiểu con trỏ (cursor).
 * Mỗi lần gọi listFeedbacks() trả về một trang (Collection); nếu còn dữ liệu,
 * Collection sẽ có next_cursor để lấy trang tiếp theo.
 */

// Khởi tạo Client với thông tin xác thực và địa chỉ API.
$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxx',
    'url' => 'https://xxxxxxxxxxxxx'
]);

// Trỏ tới tour cần lấy feedback bằng ID. Bắt buộc phải setId() trước khi gọi.
$tour = new Tour($client);
$tour->setId(111);

/*
 * Cách 1: Tự lặp qua từng trang bằng con trỏ (thủ công).
 * Phù hợp khi bạn cần kiểm soát rõ ràng việc lấy trang, xử lý giữa các trang...
 */

// Lấy trang đầu tiên (20 feedback) và gom item của trang này vào mảng kết quả.
$page = $tour->listFeedbacks(20);
$feedbacks = $page->getItems();

// Chừng nào còn trang tiếp theo thì lấy tiếp bằng next_cursor và gom thêm.
while ($page->hasMore()) {
    $nextCursor = $page->getNextCursor();
    // Truyền theo thứ tự tham số: listFeedbacks($limit, $nextCursor)
    $page = $tour->listFeedbacks(20, $nextCursor);
    $feedbacks = array_merge($feedbacks, $page->getItems());
}
var_dump($feedbacks);


/*
 * Cách 2: Duyệt trực tiếp bằng foreach (khuyến nghị vì gọn hơn).
 * Collection tự động lấy các trang tiếp theo trong lúc lặp, nên bạn chỉ cần
 * foreach một lần là duyệt hết toàn bộ feedback của tour.
 */
$feedbacks = [];
foreach ($tour->listFeedbacks() as $feedback) {
    // Mỗi $feedback là mảng dữ liệu thô do API trả về (ảnh hoặc video).
    $feedbacks[] = $feedback;
}
var_dump($feedbacks);
