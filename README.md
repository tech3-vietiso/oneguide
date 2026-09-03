# OneGuide SDK

SDK PHP giúp tích hợp với hệ thống **OneGuide**: đồng bộ tour điều hành, mời hướng dẫn viên vào tour và truy vấn danh mục (tỉnh/thành...).

## Yêu cầu

- PHP 7.4 trở lên.
- Extension `curl` và `json` (đi kèm mặc định trong hầu hết bản PHP).

## Cài đặt

Cài qua Composer:

```bash
composer require vietiso/oneguide
```

Hoặc nếu dùng trực tiếp từ mã nguồn, chỉ cần nạp autoload của Composer:

```php
require 'vendor/autoload.php';
```

## Khởi tạo Client

Mọi thao tác đều bắt đầu từ một `Client`. Thông tin xác thực (`api_key`, `secret`) do OneGuide cấp, `url` là địa chỉ gốc của API.

```php
use Vietiso\OneGuide\Client;

$client = new Client([
    'api_key' => 'xxxxxxxxx',
    'secret'  => 'xxxxxxxxxxxxx',
    'url'     => 'https://api.oneguide.example/api',
]);
```

Client tự thêm header `X-Api-Key` và `X-Api-Secret` vào mỗi request.

## Sử dụng

### 1. Đồng bộ tour điều hành

> **Lưu ý:** đây là tour **đã chuyển sang điều hành** (bắt buộc có ít nhất một điều hành viên), không phải tour chưa chuyển điều hành.

Một tour điều hành gồm: thông tin chung, hành trình theo ngày (`Itinerary`), dịch vụ (`Service`), điều hành viên (`Operator`) và thành viên trong đoàn (`Member`).

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Tour\TourType;
use Vietiso\OneGuide\Tour\Itinerary;
use Vietiso\OneGuide\Tour\Operator;
use Vietiso\OneGuide\Tour\Member;
use Vietiso\OneGuide\Tour\Gender;
use Vietiso\OneGuide\Service\Service;
use Vietiso\OneGuide\Service\ServiceType;
use Vietiso\OneGuide\Service\BookingStatus;

// Thông tin chung
$tour = new Tour($client);
$tour
    ->setId(111)                              // ID tour bên hệ thống của bạn (external id)
    ->setStartDate(new DateTime('2026-01-01'))
    ->setType(TourType::PRIVATE)              // PRIVATE | SIC | OUTBOUND
    ->setNumberAdult(1)                       // Phải > 0
    ->setCode('HNHP2DAY3NIGHT')
    ->setTitle('Hà Nội Hải Phòng 2 ngày 3 đêm')
    ->setNumberDay(3)
    ->setNumberNight(2);

// Hành trình theo ngày (bắt buộc có ít nhất một)
$tour->addItinerary(
    (new Itinerary())
        ->setTitle('Ngày 1')
        ->setDayNumber(1)
        ->setContent('Đón khách tại sân bay, ăn trưa, tham quan phố cổ.') // Tùy chọn, nội dung chi tiết
        ->setImage('https://example.com/day1.jpg') // Phải là URL hợp lệ
);

// Dịch vụ đi kèm (không bắt buộc; nếu có thì bắt buộc: setId, setTitle, setType, setTourDays)
$tour->addService(
    (new Service())
        ->setId(123)                              // ID dịch vụ bên hệ thống của bạn (bắt buộc)
        ->setTitle('Khách sạn Mường Thanh')       // Bắt buộc
        ->setType(ServiceType::HOTEL)             // Bắt buộc, xem bảng ServiceType bên dưới
        ->setTourDays([1, 2, 3])                  // Bắt buộc, không ngày nào được vượt quá setNumberDay
        ->setQuantity(2)                          // Tùy chọn, số lượng (> 0)
        ->setAmount(1500000)                      // Tùy chọn, số tiền (>= 0)
        ->setBookingStatus(BookingStatus::BOOKED) // Tùy chọn, tình trạng đặt với nhà cung cấp: BOOKED (1) | NOT_BOOKED (0)
        ->setAddress('Hà Nội')                    // Tùy chọn
        ->setNote('Phòng đôi, view hồ')           // Tùy chọn
        ->setCompanyName('Mường Thanh Hospitality')   // Tùy chọn, thông tin nhà cung cấp
        ->setCompanyPhone('02438220099')              // Tùy chọn
        ->setCompanyEmail('booking@muongthanh.com')   // Tùy chọn
);

// Điều hành viên (bắt buộc có ít nhất một)
$tour->addOperator(
    (new Operator())
        ->setName('Nguyễn Văn A')
        ->setEmail('a@example.com')
        ->setPhone('0325305738')              // Tối đa 20 ký tự
        ->setAvatar('https://example.com/avatar.jpg')
);

// Thành viên trong đoàn (không bắt buộc; đồng bộ chung trong sync())
// Nếu có thêm thành viên thì bắt buộc: setId (ID bên hệ thống của bạn) và setFullName.
$tour->addMember(
    (new Member())
        ->setId(9001)
        ->setFullName('Nguyễn Văn A')
        ->setBirthday(new DateTime('1990-05-20')) // Tùy chọn, đối tượng DateTime
        ->setPhone('0325305738')                  // Tùy chọn, tối đa 20 ký tự
        ->setEmail('a@example.com')               // Tùy chọn, phải hợp lệ nếu có
        ->setPassportNumber('C1234567')                     // Tùy chọn, số hộ chiếu
        ->setPassportExpiryDate(new DateTime('2030-12-31')) // Tùy chọn, ngày hết hạn hộ chiếu
        ->setIdentityCardNumber('001090012345')             // Tùy chọn, số CCCD
        ->setGender(Gender::MALE)                 // Tùy chọn: MALE | FEMALE | OTHER
        ->setCountryId(1)                         // Tùy chọn, ID quốc gia bên OneGuide
        ->setNote('Trưởng đoàn')                  // Tùy chọn
);

// Validate rồi đẩy lên OneGuide (ném ValidationException nếu dữ liệu sai/thiếu)
$tour->sync();
```

Dịch vụ và thành viên đều được gửi kèm trong chính payload của `sync()` (khóa `services` và `guests`), không có endpoint riêng. Cả hai danh sách này **không bắt buộc** — tour không có dịch vụ/thành viên nào vẫn đồng bộ được (gửi mảng rỗng). Riêng với thành viên, trường tùy chọn nào không set sẽ được lược khỏi payload; với dịch vụ thì trường không set gửi lên `null`.

Xem đầy đủ tại [examples/sync-tour.php](examples/sync-tour.php).

### 2. Mời hướng dẫn viên vào tour

Mời danh sách hướng dẫn viên vào một tour đã tồn tại, kèm những ngày mỗi người phụ trách.

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Guide\Guide;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần mời

$guide = new Guide('148235149'); // card_number: số thẻ hướng dẫn viên
$guide->setEmail('guide@example.com'); // Email bắt buộc & phải hợp lệ
$guide->setPhone('0327145495');

// Tham số thứ hai là các ngày trong tour mà hướng dẫn viên phụ trách
$tour->addGuide($guide, [1, 2, 3, 4]);

$tour->inviteGuides();
```

Xem đầy đủ tại [examples/invite-tour-guides.php](examples/invite-tour-guides.php).

### 3. Đồng bộ tạm ứng cho hướng dẫn viên

Đồng bộ danh sách tạm ứng (chi phí ứng trước) của hướng dẫn viên cho một tour đã tồn tại. Mỗi hướng dẫn viên có thể có nhiều khoản tạm ứng.

Các trường **bắt buộc** khi gọi `syncAdvances()`:

- `card_number` của hướng dẫn viên (`new Guide(...)`).
- Mỗi khoản tạm ứng (`Advance`): `external_expense_id` (`setId`), `expense_code` (`setCode`), `title` (`setTitle`), `tour_service_id` (`setServiceId`) và `amount` (`setAmount`).

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Guide\Advance;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần đồng bộ

$guide = new Guide('101153183'); // card_number: số thẻ hướng dẫn viên (bắt buộc)

// Thêm một hoặc nhiều khoản tạm ứng cho hướng dẫn viên
$guide->addAdvance(
    (new Advance())
        ->setId(1001)                 // ID tạm ứng bên hệ thống của bạn (bắt buộc)
        ->setCode('ADV001')           // Mã tạm ứng (bắt buộc)
        ->setTitle('Tạm ứng ăn trưa') // Tiêu đề tạm ứng (bắt buộc)
        ->setServiceId(222)           // ID dịch vụ trong tour (bắt buộc)
        ->setAmount(5000000)          // số tiền tạm ứng (bắt buộc)
        ->setCurrency('VND')          // Tùy chọn, mặc định là VND
        ->setNote('Tạm ứng đợt 1')   // Tùy chọn
);

$tour->addGuide($guide);

$tour->syncAdvances(); // Ném ValidationException nếu thiếu trường bắt buộc
```

Xem đầy đủ tại [examples/sync-guide-advances.php](examples/sync-guide-advances.php).

### 4. Đồng bộ chi bổ sung cho hướng dẫn viên

Đồng bộ các khoản **chi bổ sung** — chi phí phát sinh thêm trong tour, ngoài những gì đã tạm ứng. Cách dùng giống mục 3, chỉ khác lớp `AdditionalExpense` và hàm `syncAdditionalExpenses()`.

Các trường **bắt buộc**: `card_number` của hướng dẫn viên, và với mỗi khoản chi: `external_expense_id` (`setId`), `expense_code` (`setCode`), `title` (`setTitle`), `amount` (`setAmount`). Khác với tạm ứng, chi bổ sung không gắn với dịch vụ nào nên không có `tour_service_id`.

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Guide\AdditionalExpense;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần đồng bộ

$guide = new Guide('101153183'); // card_number: số thẻ hướng dẫn viên (bắt buộc)

$guide->addAdditionalExpense(
    (new AdditionalExpense())
        ->setId(2001)                        // ID khoản chi bên hệ thống của bạn (bắt buộc)
        ->setCode('EXP001')                  // Mã khoản chi (bắt buộc)
        ->setTitle('Vé tham quan phát sinh') // Tiêu đề (bắt buộc)
        ->setAmount(1200000)                 // Số tiền (bắt buộc)
        ->setCurrency('VND')                 // Tùy chọn, mặc định là VND
        ->setNote('Khách yêu cầu thêm điểm tham quan') // Tùy chọn
);

$tour->addGuide($guide);

$tour->syncAdditionalExpenses(); // Ném ValidationException nếu thiếu trường bắt buộc
```

Xem đầy đủ tại [examples/sync-guide-additional-expenses.php](examples/sync-guide-additional-expenses.php).

### 5. Đồng bộ hoàn tạm ứng của hướng dẫn viên

Đồng bộ các khoản **hoàn tạm ứng** — số tiền hướng dẫn viên trả lại sau khi quyết toán tour (phần tạm ứng chưa dùng hết).

Các trường **bắt buộc**: `card_number` của hướng dẫn viên, và với mỗi khoản hoàn: `external_expense_id` (`setId`), `expense_code` (`setCode`), `title` (`setTitle`), `amount` (`setAmount`). Khoản hoàn không gắn với dịch vụ nào nên không có `tour_service_id`.

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Guide\Guide;
use Vietiso\OneGuide\Guide\Refund;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần đồng bộ

$guide = new Guide('101153183'); // card_number: số thẻ hướng dẫn viên (bắt buộc)

$guide->addRefund(
    (new Refund())
        ->setId(3001)                      // ID khoản hoàn bên hệ thống của bạn (bắt buộc)
        ->setCode('REF001')                // Mã khoản hoàn (bắt buộc)
        ->setTitle('Hoàn tạm ứng ăn trưa') // Tiêu đề (bắt buộc)
        ->setAmount(500000)                // Số tiền hoàn lại (bắt buộc)
        ->setCurrency('VND')               // Tùy chọn, mặc định là VND
        ->setNote('Hoàn phần chưa dùng hết') // Tùy chọn
);

$tour->addGuide($guide);

$tour->syncRefunds(); // Ném ValidationException nếu thiếu trường bắt buộc
```

Xem đầy đủ tại [examples/sync-guide-refunds.php](examples/sync-guide-refunds.php).

### 6. Xoá phiếu đã đồng bộ

Xoá các phiếu đã đẩy sang OneGuide ở mục 3–5. Mỗi loại phiếu một hàm riêng, tham số là danh sách `external_expense_id` — chính giá trị đã gửi ở `setId()`.

| Loại phiếu | Hàm | Endpoint |
| --- | --- | --- |
| Tạm ứng | `deleteAdvances()` | `POST integration-tours/{id}/delete-expenses` |
| Chi bổ sung | `deleteAdditionalExpenses()` | `POST integration-tours/{id}/delete-additional-expenses` |
| Hoàn tạm ứng | `deleteRefunds()` | `POST integration-tours/{id}/delete-advance-refunds` |

```php
use Vietiso\OneGuide\Tour\Tour;

$tour = new Tour($client);
$tour->setId(111);

$tour->deleteAdvances([1001, 1002]);      // Ném ValidationException nếu thiếu id tour hoặc danh sách rỗng
$tour->deleteAdditionalExpenses([2001]);
$tour->deleteRefunds([3001]);
```

Mỗi hàm chỉ xoá đúng loại phiếu của nó, nên gửi nhầm id sang hàm khác thì không có gì bị xoá.

Phiếu bị xoá sẽ biến mất khỏi màn hình của hướng dẫn viên và **hướng dẫn viên nhận email báo phiếu đã bị huỷ**. Khoản chi do chính hướng dẫn viên nhập cho nhà cung cấp không bị ảnh hưởng.

Xem đầy đủ tại [examples/delete-guide-expenses.php](examples/delete-guide-expenses.php).

### 7. Lấy danh mục có phân trang (tỉnh/thành)

API trả về dữ liệu theo con trỏ (cursor). `list()` trả về một `Collection`; có thể duyệt trực tiếp bằng `foreach` (tự động lấy trang tiếp theo) hoặc lặp thủ công.

```php
use Vietiso\OneGuide\Province\Province;

$province = new Province($client);

// Cách 1 (khuyến nghị): foreach tự lấy hết các trang
$provinces = [];
foreach ($province->list() as $item) {
    $provinces[] = $item;
}

// Cách 2: lặp thủ công bằng con trỏ
$provinces = [];
$page = $province->list();
$provinces = array_merge($provinces, $page->getItems());
while ($page->hasMore()) {
    $page = $province->list(null, 10, $page->getNextCursor());
    $provinces = array_merge($provinces, $page->getItems());
}
```

Xem đầy đủ tại [examples/get-province.php](examples/get-province.php).

### 8. Lấy danh sách feedback của tour

Lấy các feedback (ảnh/video) mà hướng dẫn viên gửi về cho một tour. Kết quả phân trang theo con trỏ giống mục 7, trả về `Collection`.

```php
use Vietiso\OneGuide\Tour\Tour;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần lấy feedback

// Cách 1 (khuyến nghị): foreach tự lấy hết các trang
foreach ($tour->listFeedbacks() as $feedback) {
    // mỗi $feedback là mảng dữ liệu thô do API trả về (ảnh hoặc video)
}

// Cách 2: lặp thủ công bằng con trỏ
$page = $tour->listFeedbacks(20);
$feedbacks = $page->getItems();
while ($page->hasMore()) {
    $page = $tour->listFeedbacks(20, $page->getNextCursor());
    $feedbacks = array_merge($feedbacks, $page->getItems());
}
```

Cần `setId()` trước khi gọi, nếu không sẽ ném `ValidationException`.

Xem đầy đủ tại [examples/get-tour-feedbacks.php](examples/get-tour-feedbacks.php).

### 9. Xem tình trạng xác nhận của hướng dẫn viên

Sau khi mời (mục 2), hướng dẫn viên tự nhận hoặc từ chối tour. Hàm này cho biết ai đã phản hồi, ai còn đang chờ. Kết quả **không phân trang** vì một tour chỉ có vài hướng dẫn viên.

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Guide\InvitationStatus;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần xem

foreach ($tour->listGuideInvitations() as $invitation) {
    // mỗi $invitation là mảng dữ liệu thô do API trả về
    if ($invitation['status'] === InvitationStatus::DECLINED) {
        echo $invitation['card_number'] . ' từ chối: ' . $invitation['decline_reason'];
    }
}
```

Cần `setId()` trước khi gọi, nếu không sẽ ném `ValidationException`.

Xem đầy đủ tại [examples/get-tour-guide-invitations.php](examples/get-tour-guide-invitations.php).

## Xử lý lỗi

SDK ném hai loại ngoại lệ, đều kế thừa từ `OneGuideException`:

- **`ValidationException`** — dữ liệu không hợp lệ, phát hiện ở phía SDK **trước khi** gửi request (thiếu trường bắt buộc, sai định dạng URL/email, số điện thoại quá dài...).
- **`ApiException`** — request thất bại hoặc server trả về mã lỗi (không phải 2xx). Cung cấp thêm `getStatusCode()`, `getErrors()`, `hasErrors()`, `getResponse()`.

```php
use Vietiso\OneGuide\Exception\ValidationException;
use Vietiso\OneGuide\Exception\ApiException;

try {
    $tour->sync();
} catch (ValidationException $e) {
    // Dữ liệu sai trước khi gửi
    echo $e->getMessage();
} catch (ApiException $e) {
    // Lỗi từ phía server
    echo $e->getStatusCode() . ': ' . $e->getMessage();
    if ($e->hasErrors()) {
        var_dump($e->getErrors());
    }
}
```

## Hằng số tham chiếu

| Loại tour (`TourType`) | Giá trị |
| --- | --- |
| `PRIVATE` | 1 |
| `SIC` | 2 |
| `OUTBOUND` | 3 |

| Loại dịch vụ (`ServiceType`) | Giá trị | Loại dịch vụ (`ServiceType`) | Giá trị |
| --- | --- | --- | --- |
| `HOTEL` (khách sạn) | 1 | `LANDTOUR` | 8 |
| `RESTAURANT` (nhà hàng) | 2 | `BOAT` (thuyền) | 9 |
| `CRUISE` (du thuyền) | 3 | `SIGHTSEEING_TICKET` (vé thắng cảnh) | 10 |
| `CAR` (xe ô tô) | 4 | `BUS` | 11 |
| `VISA` | 5 | `TRAIN` | 12 |
| `VOUCHER` | 6 | `INSURANCE` (bảo hiểm) | 13 |
| `FLIGHT_TICKET` (vé máy bay) | 7 | `OTHER` (dịch vụ khác) | 99 |

| Giới tính (`Gender`) | Giá trị |
| --- | --- |
| `MALE` | 1 |
| `FEMALE` | 2 |
| `OTHER` | 3 |

| Tình trạng đặt dịch vụ với nhà cung cấp (`BookingStatus`) | Giá trị |
| --- | --- |
| `NOT_BOOKED` (chưa đặt) | 0 |
| `BOOKED` (đã đặt) | 1 |

| Tình trạng lời mời hướng dẫn viên (`InvitationStatus`) | Giá trị |
| --- | --- |
| `PENDING` (chưa phản hồi) | 1 |
| `ACCEPTED` (đã nhận tour) | 2 |
| `DECLINED` (đã từ chối) | 3 |

## Ví dụ

Thư mục [examples/](examples/) chứa các ví dụ chạy được kèm chú thích chi tiết:

- [sync-tour.php](examples/sync-tour.php) — đẩy tour điều hành đầy đủ (kèm thành viên trong đoàn).
- [invite-tour-guides.php](examples/invite-tour-guides.php) — mời hướng dẫn viên vào tour.
- [sync-guide-advances.php](examples/sync-guide-advances.php) — đồng bộ tạm ứng cho hướng dẫn viên.
- [sync-guide-additional-expenses.php](examples/sync-guide-additional-expenses.php) — đồng bộ chi bổ sung của hướng dẫn viên.
- [sync-guide-refunds.php](examples/sync-guide-refunds.php) — đồng bộ hoàn tạm ứng của hướng dẫn viên.
- [get-tour-feedbacks.php](examples/get-tour-feedbacks.php) — lấy danh sách feedback (ảnh/video) của tour.
- [get-tour-guide-invitations.php](examples/get-tour-guide-invitations.php) — xem tình trạng xác nhận của hướng dẫn viên.
- [get-province.php](examples/get-province.php) — lấy danh sách tỉnh/thành có phân trang.
