# Changelog

Các thay đổi đáng chú ý của SDK sẽ được ghi lại trong file này.

## V0.2.4

### Tính năng mới

Thêm `Tour::deleteExpenses(array $expenseIds)` — xoá các phiếu đã đồng bộ (tạm ứng, chi bổ sung, hoàn tạm ứng) theo `external_expense_id`.

```php
$tour = new Tour($client);
$tour->setId(111);

$tour->deleteExpenses([1001, 2001]);
```

- Endpoint: `POST integration-tours/{id}/delete-expenses`, payload `{"expenses": [1001, 2001]}`.
- Ném `ValidationException` nếu thiếu id tour hoặc danh sách rỗng.
- Phía OneGuide xoá mềm phiếu, đồng thời **gửi email báo hướng dẫn viên phiếu đã bị huỷ**. Khoản chi do chính hướng dẫn viên nhập cho nhà cung cấp không bị ảnh hưởng.

Không có breaking change so với V0.2.3.

## V0.2.3

### Tính năng mới

Thêm hai chức năng đồng bộ chi phí của hướng dẫn viên, dùng chung cách khai báo với tạm ứng (`Tour::addGuide()` rồi gọi hàm đồng bộ):

| Chức năng | Lớp dữ liệu | Hàm | Endpoint |
| --- | --- | --- | --- |
| Chi bổ sung (phát sinh ngoài tạm ứng) | `Guide\AdditionalExpense` + `Guide::addAdditionalExpense()` | `Tour::syncAdditionalExpenses()` | `POST integration-tours/{id}/sync-additional-expenses` |
| Hoàn tạm ứng | `Guide\Refund` + `Guide::addRefund()` | `Tour::syncRefunds()` | `POST integration-tours/{id}/sync-advance-refunds` |

- Cả hai lớp bắt buộc: `setId()` (`external_expense_id`), `setCode()` (`expense_code`), `setTitle()`, `setAmount()`; tùy chọn: `setCurrency()`, `setNote()`.
- Khác với tạm ứng, cả hai đều **không có** `tour_service_id` vì chi bổ sung và hoàn tạm ứng không gắn với dịch vụ nào của tour.
- Payload của cả hai endpoint dùng chung khóa `guides[].expenses[]` như `sync-expenses`; phía OneGuide lưu vào bảng `expenses` với `type` riêng (`2 = chi bổ sung`, `3 = hoàn tạm ứng`).

Cả hai đều kiểm tra `Tour::setId()` trước khi gửi và ném `ValidationException` nếu thiếu trường bắt buộc. Không có breaking change so với V0.2.2 — `sync()`, `inviteGuides()` và `syncAdvances()` giữ nguyên payload.

## V0.2.2

### Tính năng mới

Thêm `Tour::listGuideInvitations()` — xem tình trạng xác nhận (chưa phản hồi / đã nhận / đã từ chối) của các hướng dẫn viên đã được mời vào tour. Kèm hằng số `Guide\InvitationStatus`.

Không phân trang vì một tour chỉ có vài hướng dẫn viên. Không có breaking change so với V0.2.1.

## V0.2.1

### Tính năng mới

Thêm `Tour::listFeedbacks($limit, $nextCursor)` — lấy danh sách feedback (ảnh/video) của một tour, phân trang theo con trỏ và trả về `Collection` giống `Province::list()`.

Không có breaking change so với V0.2.0.

## V0.2.0

### Breaking change

Đổi tên chức năng gán hướng dẫn viên thành **mời** hướng dẫn viên:

| Trước | Sau |
| --- | --- |
| `Tour::syncGuides()` | `Tour::inviteGuides()` |
| `POST integration-tours/{id}/sync-guides` | `POST integration-tours/{id}/invite-guides` |
| `examples/add-tour-guide.php` | `examples/invite-tour-guides.php` |

Payload gửi lên (`guides`), cách thêm hướng dẫn viên (`Tour::addGuide()`) và các quy tắc validate (email bắt buộc & hợp lệ, `phone` tối đa 20 ký tự) **không đổi**.

### Cách nâng cấp

Đổi lời gọi `syncGuides()` thành `inviteGuides()`:

```php
$tour = new Tour($client);
$tour->setId(111);
$tour->addGuide($guide, [1, 2, 3, 4]);

// Trước
$tour->syncGuides();

// Sau
$tour->inviteGuides();
```

Không có thay đổi nào khác cần thực hiện. Vì endpoint phía server cũng đổi sang `invite-guides`, phiên bản này yêu cầu backend OneGuide đã hỗ trợ endpoint mới.

### Khác

- Cập nhật README, ví dụ và mô tả trong `composer.json` theo thuật ngữ "mời hướng dẫn viên".
