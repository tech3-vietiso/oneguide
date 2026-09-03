<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Guide\AdditionalExpense;
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

$guide->addAdditionalExpense(
    (new AdditionalExpense())
        ->setId(2001)                      // bắt buộc
        ->setCode('EXP001')                // bắt buộc
        ->setTitle('Vé tham quan phát sinh') // bắt buộc
        ->setAmount(1200000)               // bắt buộc
        ->setNote('Khách yêu cầu thêm điểm tham quan')
);

$tour->addGuide($guide);
$tour->syncAdditionalExpenses();
