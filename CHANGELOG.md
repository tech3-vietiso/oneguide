# Changelog

Các thay đổi đáng chú ý của SDK sẽ được ghi lại trong file này.

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
