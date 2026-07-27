# OneGuide SDK

SDK PHP giúp tích hợp với hệ thống **OneGuide**: đồng bộ tour điều hành, gán hướng dẫn viên cho tour và truy vấn danh mục (tỉnh/thành...).

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

// Dịch vụ đi kèm
$tour->addService(
    (new Service())
        ->setType(ServiceType::SIC)
        ->setTitle('Dịch vụ test')
        ->setTourDays([1, 2, 3])
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

Thành viên được gửi kèm trong chính payload của `sync()` (khóa `guests`), không có endpoint riêng. Danh sách này **không bắt buộc** — tour không có thành viên nào vẫn đồng bộ được (`guests` sẽ là mảng rỗng). Trường tùy chọn nào không set cũng được lược khỏi payload.

Xem đầy đủ tại [examples/sync-tour.php](examples/sync-tour.php).

### 2. Gán hướng dẫn viên cho tour

Gán (đồng bộ) danh sách hướng dẫn viên cho một tour đã tồn tại, kèm những ngày mỗi người phụ trách.

```php
use Vietiso\OneGuide\Tour\Tour;
use Vietiso\OneGuide\Guide\Guide;

$tour = new Tour($client);
$tour->setId(111); // ID tour cần gán

$guide = new Guide('148235149'); // card_number: số thẻ hướng dẫn viên
$guide->setEmail('guide@example.com'); // Email bắt buộc & phải hợp lệ
$guide->setPhone('0327145495');

// Tham số thứ hai là các ngày trong tour mà hướng dẫn viên phụ trách
$tour->addGuide($guide, [1, 2, 3, 4]);

$tour->syncGuides();
```

Xem đầy đủ tại [examples/add-tour-guide.php](examples/add-tour-guide.php).

### 3. Đồng bộ tạm ứng cho hướng dẫn viên

Đồng bộ danh sách tạm ứng (chi phí ứng trước) của hướng dẫn viên cho một tour đã tồn tại. Mỗi hướng dẫn viên có thể có nhiều khoản tạm ứng.

Các trường **bắt buộc** khi gọi `syncAdvances()`:

- `card_number` của hướng dẫn viên (`new Guide(...)`).
- Mỗi khoản tạm ứng (`Advance`): `external_expense_id` (`setId`), `expense_code` (`setCode`), `tour_service_id` (`setServiceId`) và `amount` (`setAmount`).

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
        ->setServiceId(222)           // ID dịch vụ trong tour (bắt buộc)
        ->setAmount(5000000)          // số tiền tạm ứng (bắt buộc)
        ->setCurrency('VND')          // Tùy chọn, mặc định là VND
        ->setNote('Tạm ứng đợt 1')   // Tùy chọn
);

$tour->addGuide($guide);

$tour->syncAdvances(); // Ném ValidationException nếu thiếu trường bắt buộc
```

Xem đầy đủ tại [examples/sync-guide-advances.php](examples/sync-guide-advances.php).

### 4. Lấy danh mục có phân trang (tỉnh/thành)

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

| Loại tour (`TourType`) | Giá trị | Loại dịch vụ (`ServiceType`) | Giá trị |
| --- | --- | --- | --- |
| `PRIVATE` | 1 | `PRIVATE` | 1 |
| `SIC` | 2 | `SIC` | 2 |
| `OUTBOUND` | 3 | `OUTBOUND` | 3 |

| Giới tính (`Gender`) | Giá trị |
| --- | --- |
| `MALE` | 1 |
| `FEMALE` | 2 |
| `OTHER` | 3 |

## Ví dụ

Thư mục [examples/](examples/) chứa các ví dụ chạy được kèm chú thích chi tiết:

- [sync-tour.php](examples/sync-tour.php) — đẩy tour điều hành đầy đủ (kèm thành viên trong đoàn).
- [add-tour-guide.php](examples/add-tour-guide.php) — gán hướng dẫn viên cho tour.
- [sync-guide-advances.php](examples/sync-guide-advances.php) — đồng bộ tạm ứng cho hướng dẫn viên.
- [get-province.php](examples/get-province.php) — lấy danh sách tỉnh/thành có phân trang.
