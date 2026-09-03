<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Guide\Refund;
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

// card_number của hướng dẫn viên là bắt buộc.
$guide = (new Guide('101153183'));

$guide->addRefund(
    (new Refund())
        ->setId(3001)                      // bắt buộc
        ->setCode('REF001')                // bắt buộc
        ->setTitle('Hoàn tạm ứng ăn trưa') // bắt buộc
        ->setAmount(500000)                // bắt buộc, số tiền hoàn lại
        ->setNote('Hoàn phần chưa dùng hết') // Tùy chọn
);

$tour->addGuide($guide);
$tour->syncRefunds();
