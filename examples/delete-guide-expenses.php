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

// Danh sách external_expense_id cần xoá — chính là giá trị đã gửi ở setId() lúc
// đồng bộ tạm ứng / chi bổ sung / hoàn tạm ứng.
// Hướng dẫn viên sẽ nhận email báo các phiếu này đã bị huỷ.
$tour->deleteExpenses([1001, 2001]);
