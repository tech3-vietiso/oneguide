<?php

use Vietiso\OneGuide\Province\Province;
use Vietiso\OneGuide\Client;

require '../vendor/autoload.php';

/*
 * Ví dụ: Lấy danh sách tỉnh/thành (Province) có phân trang.
 *
 * API trả về dữ liệu theo kiểu con trỏ (cursor). Mỗi lần gọi list() trả về
 * một trang (Collection); nếu còn dữ liệu, Collection sẽ có next_cursor để
 * lấy trang tiếp theo. File này minh họa 2 cách gom hết dữ liệu về mảng.
 */

// Khởi tạo Client với thông tin xác thực và địa chỉ API.
$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxx',
    'url' => 'http://localhost:9095/api'
]);

/*
 * Cách 1: Tự lặp qua từng trang bằng con trỏ (thủ công).
 * Phù hợp khi bạn cần kiểm soát rõ ràng việc lấy trang, xử lý giữa các trang...
 */
$provinces = [];
$province = new Province($client);

// Lấy trang đầu tiên và gom item của trang này vào mảng kết quả.
$page = $province->list();
$provinces = array_merge($provinces, $page->getItems());

// Chừng nào còn trang tiếp theo thì lấy tiếp bằng next_cursor và gom thêm.
while ($page->hasMore()) {
    $nextCursor = $page->getNextCursor();
    // Truyền theo thứ tự tham số: list($countryCode, $limit, $nextCursor)
    $page = $province->list(null, 10, $nextCursor);
    $provinces = array_merge($provinces, $page->getItems());
}
var_dump($provinces);


/*
 * Cách 2: Duyệt trực tiếp bằng foreach (khuyến nghị vì gọn hơn).
 * Collection tự động lấy các trang tiếp theo trong lúc lặp, nên bạn chỉ cần
 * foreach một lần là duyệt hết toàn bộ tỉnh/thành.
 */
$provinces = [];
foreach ($province->list() as $item) {
    $provinces[] = $item;
}
var_dump($provinces);
