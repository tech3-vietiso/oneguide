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

$guide = (new Guide('101153183'));

$guide->addAdvance(
    (new Advance())
        ->setId(1001)
        ->setAmount(5000000)
        ->setNote('Tạm ứng đợt 1')
);

$tour->addGuide($guide);
$tour->syncAdvances();