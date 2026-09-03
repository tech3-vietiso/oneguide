<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Tour\Tour;

require '../vendor/autoload.php';

$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret'  => 'xxxxxxxxxxxxx',
    'url'     => 'https://xxxxxxxxxxxxx'
]);

/**
 * TOUR
 */
$tour = new Tour($client);
$tour->setId(111);

// Mỗi loại phiếu có hàm xoá riêng; tham số là các external_expense_id — chính là
// giá trị đã gửi ở setId() lúc đồng bộ.
// Hướng dẫn viên sẽ nhận email báo các phiếu này đã bị huỷ.

// Xoá tạm ứng
$tour->deleteAdvances([1001, 1002]);

// Xoá chi bổ sung
$tour->deleteAdditionalExpenses([2001]);

// Xoá hoàn tạm ứng
$tour->deleteRefunds([3001]);
