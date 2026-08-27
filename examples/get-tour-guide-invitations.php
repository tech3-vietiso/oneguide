<?php

use Vietiso\OneGuide\Guide\InvitationStatus;
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Client;

require '../vendor/autoload.php';

/*
 * Ví dụ: Xem tình trạng xác nhận của các hướng dẫn viên đã được mời vào một tour.
 *
 * Sau khi gọi inviteGuides(), mỗi hướng dẫn viên nhận được thư mời và tự nhận
 * hoặc từ chối tour. Hàm này cho biết ai đã phản hồi, ai còn đang chờ.
 * Danh sách không phân trang vì một tour chỉ có vài hướng dẫn viên.
 */

// Khởi tạo Client với thông tin xác thực và địa chỉ API.
$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxx',
    'url' => 'https://xxxxxxxxxxxxx'
]);

// Trỏ tới tour cần xem bằng ID. Bắt buộc phải setId() trước khi gọi.
$tour = new Tour($client);
$tour->setId(111);

// Mỗi phần tử là mảng thô do API trả về, gồm thông tin hướng dẫn viên và trạng thái.
$invitations = $tour->listGuideInvitations();

foreach ($invitations as $invitation) {
    // So sánh với hằng số InvitationStatus thay vì viết số trực tiếp.
    switch ($invitation['status']) {
        case InvitationStatus::ACCEPTED:
            $note = 'đã nhận tour';
            break;
        case InvitationStatus::DECLINED:
            // decline_reason là lý do hướng dẫn viên đưa ra khi từ chối.
            $note = 'đã từ chối: ' . $invitation['decline_reason'];
            break;
        default:
            $note = 'chưa phản hồi';
    }

    echo "{$invitation['card_number']} - {$invitation['name']}: {$note}" . PHP_EOL;
}
