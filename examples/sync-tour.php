<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Tour\Operator;
use Vietiso\OneGuide\Tour\Itinerary;
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Tour\TourType;
use Vietiso\OneGuide\Service\Service;
use Vietiso\OneGuide\Service\ServiceType;

require '../vendor/autoload.php';

/*
 * Ví dụ: Đẩy (đồng bộ) một TOUR ĐIỀU HÀNH đầy đủ lên OneGuide.
 *
 * Lưu ý: đây là tour ĐÃ chuyển sang điều hành (có điều hành viên phụ trách),
 * KHÔNG PHẢI tour chưa chuyển điều hành. Vì vậy tour bắt buộc phải có ít nhất
 * một Operator (điều hành viên) thì mới đồng bộ được.
 *
 * Một tour điều hành gồm các thành phần:
 *   - Thông tin chung (mã, tên, loại, ngày khởi hành, số ngày/đêm, số khách...).
 *   - Hành trình theo ngày (Itinerary).
 *   - Dịch vụ đi kèm (Service).
 *   - Điều hành viên phụ trách (Operator).
 * Cuối cùng gọi sync() để validate và gửi toàn bộ lên API.
 */

// Khởi tạo Client với thông tin xác thực và địa chỉ API.
$client = new Client([
    'api_key' => 'ogk_uKrDG0SBVpm9686JgNJvzzaHfrEWzhvG',
    'secret' => 'EMOrEdtUAurgTRmp6d1h6MTurmvHm3HYXniKu4gb1emwH9jN',
    'url' => 'http://localhost:9095/api'
]);

// --- Thông tin chung của tour ---
// Các setter trả về chính đối tượng Tour nên có thể gọi nối chuỗi (fluent).
// - setId: ID tour bên hệ thống của bạn (external id, bắt buộc).
// - setStartDate: ngày khởi hành (đối tượng DateTime).
// - setType: loại tour, dùng hằng số trong TourType (PRIVATE | SIC | OUTBOUND).
// - setNumberAdult: số khách người lớn (phải > 0).
// - setCode / setTitle: mã và tên tour (bắt buộc).
// - setNumberDay / setNumberNight: số ngày và số đêm.
$tour = new Tour($client);
$tour
    ->setId(111)
    ->setStartDate(new DateTime('2026-01-01'))
    ->setType(TourType::PRIVATE)
    ->setNumberAdult(1)
    ->setNumberChildren(2)
    ->setCode('HNHP2DAY3NIGHT')
    ->setTitle('Hà Nội Hải Phòng 2 ngày 3 đêm')
    ->setNumberDay(3)
    ->setNumberNight(2)
    ->setImage('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7ZKKCrxGI3_okyphTf2K1lOmWRlOVzBycsYgu3aX7rQ&s=10');

// --- Hành trình theo từng ngày ---
// Mỗi Itinerary là chương trình của một ngày:
// - setTitle: tiêu đề ngày.
// - setDayNumber: ngày thứ mấy trong tour.
// - setImage: ảnh minh họa (phải là URL hợp lệ).
// (Tour phải có ít nhất một hành trình thì mới đồng bộ được.)
foreach (range(1, 10) as $day) {
    $itinerary = (new Itinerary())
        ->setTitle("Ngày {$day}")
        ->setDayNumber($day)
        ->setImage('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7ZKKCrxGI3_okyphTf2K1lOmWRlOVzBycsYgu3aX7rQ&s=10');
    $tour->addItinerary($itinerary);
}

// --- Dịch vụ đi kèm tour ---
// - setType: loại dịch vụ, dùng hằng số trong ServiceType.
// - setTitle: tên dịch vụ.
// - setTourDays: các ngày trong tour áp dụng dịch vụ này.
$service = new Service();
$service
    ->setType(ServiceType::HOTEL)
    ->setId(123)
    ->setTitle('Dịch vụ test')
    ->setAddress('Hà Nội')
    ->setTourDays([1, 2, 3]);
$tour->addService($service);

$service = new Service();
$service
    ->setType(ServiceType::RESTAURANT)
    ->setId(234)
    ->setTitle('Nhà hàng gì đó')
    ->setAddress('Hà Nội')
    ->setTourDays([1, 2, 3]);
$tour->addService($service);

// --- Điều hành viên phụ trách tour ---
// - setName: tên (bắt buộc).
// - setEmail / setPhone: thông tin liên hệ (phone tối đa 20 ký tự).
// - setAvatar: ảnh đại diện (phải là URL hợp lệ).
// (Tour phải có ít nhất một operator thì mới đồng bộ được.)
$operator = (new Operator())
    ->setName('Nguyễn Hoàng Thắng Thuận')
    ->setEmail('thuanvp012van@gmail.com')
    ->setPhone('0325305738')
    ->setAvatar('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7ZKKCrxGI3_okyphTf2K1lOmWRlOVzBycsYgu3aX7rQ&s=10');
$tour->addOperator($operator);

// Đẩy toàn bộ tour lên OneGuide.
// sync() sẽ validate dữ liệu trước (bắt buộc, đúng định dạng...) rồi mới gửi;
// nếu thiếu/sai sẽ ném ValidationException.
$tour->sync();
