<?php

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Tour\Gender;
use Vietiso\OneGuide\Tour\Member;
use Vietiso\OneGuide\Tour\Operator;
use Vietiso\OneGuide\Tour\Itinerary;
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Tour\TourType;
use Vietiso\OneGuide\Service\Service;
use Vietiso\OneGuide\Service\ServiceType;
use Vietiso\OneGuide\Service\BookingStatus;

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
 *   - Thành viên trong đoàn (Member).
 * Cuối cùng gọi sync() để validate và gửi toàn bộ lên API.
 */

// Khởi tạo Client với thông tin xác thực và địa chỉ API.
$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxx',
    'url' => 'https://xxxxxxxxxxxxx'
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
// - setContent: nội dung chi tiết của ngày (tùy chọn).
// - setImage: ảnh minh họa (phải là URL hợp lệ).
// (Tour phải có ít nhất một hành trình thì mới đồng bộ được.)
foreach (range(1, 10) as $day) {
    $itinerary = (new Itinerary())
        ->setTitle("Ngày {$day}")
        ->setDayNumber($day)
        ->setContent("Nội dung chương trình ngày {$day}.")
        ->setImage('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7ZKKCrxGI3_okyphTf2K1lOmWRlOVzBycsYgu3aX7rQ&s=10');
    $tour->addItinerary($itinerary);
}

// --- Dịch vụ đi kèm tour ---
// - setType: loại dịch vụ, dùng hằng số trong ServiceType.
// - setTitle: tên dịch vụ.
// - setTourDays: các ngày trong tour áp dụng dịch vụ này.
// - setQuantity: số lượng dịch vụ (tùy chọn, phải > 0).
// - setAmount: số tiền của dịch vụ (tùy chọn, phải >= 0).
// - setBookingStatus: tình trạng đặt dịch vụ với nhà cung cấp, dùng hằng số trong
//   BookingStatus (BOOKED = 1: đã đặt, NOT_BOOKED = 0: chưa đặt).
// - setAddress / setNote: địa chỉ và ghi chú của dịch vụ (tùy chọn).
// - setCompanyName / setCompanyPhone / setCompanyEmail: thông tin nhà cung cấp (tùy chọn).
$service = new Service();
$service
    ->setType(ServiceType::HOTEL)
    ->setId(123)
    ->setTitle('Dịch vụ test')
    ->setAddress('Hà Nội')
    ->setQuantity(2)
    ->setAmount(1500000)
    ->setBookingStatus(BookingStatus::BOOKED)
    ->setNote('Phòng đôi, view hồ')
    ->setCompanyName('Mường Thanh Hospitality')
    ->setCompanyPhone('02438220099')
    ->setCompanyEmail('booking@muongthanh.com')
    ->setTourDays([1, 2, 3]);
$tour->addService($service);

$service = new Service();
$service
    ->setType(ServiceType::RESTAURANT)
    ->setId(234)
    ->setTitle('Nhà hàng gì đó')
    ->setAddress('Hà Nội')
    ->setQuantity(10)
    ->setAmount(3000000)
    ->setBookingStatus(BookingStatus::NOT_BOOKED)
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

// --- Thành viên trong đoàn ---
// (Tour không bắt buộc phải có thành viên; không thêm ai thì vẫn đồng bộ bình thường.)
// Nếu có thêm, bắt buộc: setId (ID thành viên bên hệ thống của bạn) và setFullName (họ tên).
// Tùy chọn: setBirthday, setPhone, setEmail, setPassportNumber, setPassportExpiryDate,
//           setIdentityCardNumber (CCCD),
//           setGender (Gender::MALE | FEMALE | OTHER),
//           setCountryId (ID quốc gia bên OneGuide), setNote.
// Trường tùy chọn nào không set sẽ được lược khỏi dữ liệu gửi đi.
$tour->addMember(
    (new Member())
        ->setId(9001)
        ->setFullName('Nguyễn Văn A')
        ->setBirthday(new DateTime('1990-05-20'))
        ->setPhone('0325305738')
        ->setEmail('a@example.com')
        ->setPassportNumber('C1234567')
        ->setPassportExpiryDate(new DateTime('2030-12-31'))
        ->setIdentityCardNumber('001090012345')
        ->setGender(Gender::MALE)
        ->setCountryId(1)
        ->setNote('Trưởng đoàn')
);

// Đẩy toàn bộ tour lên OneGuide.
// sync() sẽ validate dữ liệu trước (bắt buộc, đúng định dạng...) rồi mới gửi;
// nếu thiếu/sai sẽ ném ValidationException.
$tour->sync();
