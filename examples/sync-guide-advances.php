<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Guide\Advance;
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

$guide->addAdvance(
    (new Advance())
        ->setId(1001)                 // bắt buộc
        ->setCode('ADV001')           // bắt buộc
        ->setServiceId(222)           // bắt buộc
        ->setAmount(5000000)          // bắt buộc
        ->setCurrency('VND')          // Tùy chọn, mặc định VND
        ->setNote('Tạm ứng đợt 1')   // Tùy chọn
);

$tour->addGuide($guide);
$tour->syncAdvances();
