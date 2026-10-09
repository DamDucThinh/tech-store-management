# Debug Report

Các lỗi gặp thật trong quá trình làm project, ghi theo mẫu: Issue, Nguyên nhân, Cách điều tra, Cách xử lý, Kết quả.

---

## Issue 1: Username có dấu chấm (`hang.le`) bị validation từ chối

**Issue**
Feature test tạo nhân viên với username `hang.le` fail, kèm lỗi validation "Tên đăng nhập chỉ được chứa chữ cái, chữ số, dấu gạch ngang và gạch dưới." Hệ quả: tài khoản `hang.le` có sẵn trong dữ liệu mẫu sẽ không lưu được khi Quản lý sửa thông tin, vì form gửi lại username và username bị validate lại.

**Nguyên nhân**
Rule validation của username là `alpha_dash`. Rule này chỉ chấp nhận chữ, số, `-` và `_`, không chấp nhận dấu chấm. Trong khi đó, seeder tạo sẵn tài khoản `hang.le`. Seeder ghi thẳng vào database, không đi qua validation, nên lúc seed không có lỗi gì. Lỗi chỉ lộ ra khi người dùng gửi form sửa.

**Cách điều tra**
1. Chạy `php artisan test`. Test `test_admin_can_create_employee_with_profile` fail: mong đợi redirect về `/employees` nhưng lại redirect về trang trước.
2. Output của test in kèm lỗi validation trong session. Câu lỗi đúng là câu của rule `alpha_dash`.
3. Đọc tài liệu Laravel về `alpha_dash` để xác nhận rule không cho phép dấu `.`.
4. Kiểm tra `EmployeeSeeder` thấy username `hang.le`, tức là dữ liệu có sẵn đang vi phạm rule.

**Cách xử lý**
- Đổi rule trong `app/Http/Requests/EmployeeRequest.php` thành `regex:/^[A-Za-z0-9._-]+$/`, kèm câu báo lỗi riêng.
- Sửa thuộc tính `pattern` của ô username trong form cho khớp với backend: `[A-Za-z0-9._\-]+`.

**Kết quả**
Test pass, sửa được tài khoản `hang.le`. Rule ở frontend và backend đã thống nhất.
Bài học: dữ liệu mẫu phải tuân theo đúng rule validation của hệ thống, và nên có test với dữ liệu giống thực tế.

---

## Issue 2: Test pass trên SQLite nhưng fail trên MySQL

**Issue**
Bộ test chạy mặc định (SQLite trong bộ nhớ) pass hết. Chạy lại trên MySQL (`DB_CONNECTION=mysql DB_DATABASE=mini_sales_test php artisan test`) thì 1 test fail:

```
Expected response status code [200] but received 404.
```

tại dòng `$this->get(route('orders.show', 1))`.

**Nguyên nhân**
Test gán cứng id = 1. Trait `RefreshDatabase` bọc mỗi test trong một transaction rồi rollback sau khi test xong.
- **MySQL (InnoDB)** không trả lại giá trị AUTO_INCREMENT khi rollback. Các test chạy trước đã "tiêu" mất id 1, 2, 3..., nên dữ liệu seed trong test sau có id lớn hơn 1. Vì vậy không có đơn nào mang id 1, dẫn tới 404.
- **SQLite** rollback cả bộ đếm id, nên id luôn bắt đầu lại từ 1. Lỗi bị che đi.

**Cách điều tra**
1. Đọc thông báo lỗi: 404 xảy ra ở dòng dùng id cố định `1`. Các test không dùng id cố định đều pass.
2. Chỉ MySQL fail còn SQLite pass, nên nghi ngờ khác biệt về cách 2 database cấp id.
3. Làm thí nghiệm nhỏ để xác nhận: insert trong transaction, rollback 2 lần, rồi insert thật.

   ```sql
   START TRANSACTION; INSERT INTO demo_ai (name) VALUES ('test 1'); ROLLBACK;
   START TRANSACTION; INSERT INTO demo_ai (name) VALUES ('test 2'); ROLLBACK;
   INSERT INTO demo_ai (name) VALUES ('ban ghi that');
   SELECT id, name FROM demo_ai;
   ```

   | Database | id của bản ghi thật |
   |---|---|
   | MySQL | **3** |
   | SQLite | **1** |

**Cách xử lý**
Không gán cứng id trong test. Lấy bản ghi thật rồi truyền vào route:

```php
$order = Order::first();
$this->get(route('orders.show', $order))->assertOk();
```

**Kết quả**
Toàn bộ test pass trên cả SQLite và MySQL.
Bài học: nên chạy test trên cùng loại database với môi trường thật, và không giả định giá trị id.

---

## Issue 3: Thẻ thống kê ở trang Tổng quan bị lệch hàng

**Issue**
Trên trang Tổng quan, 4 thẻ thống kê nằm cùng một hàng nhưng thẻ đầu tiên cao hơn 3 thẻ còn lại. Hàng thẻ trạng thái đơn hàng bên dưới cũng bị lệch giống vậy.

**Nguyên nhân**
Trong `public/css/app.css` có một rule chung:

```css
.card + .card { margin-top: 20px; }
```

Rule này được viết để tạo khoảng cách giữa các card **xếp dọc**. Nhưng selector `+` chọn mọi card đứng ngay sau một card khác, kể cả các card **nằm ngang** trong một hàng Flexbox. Kết quả là thẻ thứ 2, 3, 4 bị đẩy xuống 20px.

**Cách điều tra**
1. Chụp màn hình trang Tổng quan, thấy thẻ 2–4 thấp hơn thẻ đầu một khoảng bằng nhau.
2. Thẻ đầu không bị lệch, chỉ các thẻ phía sau bị lệch, nên nghi ngờ selector anh em `+` hoặc `~`.
3. Tìm trong `app.css` các rule có `margin-top: 20px` liên quan tới `.card`, thấy `.card + .card`.
4. Có thể xác nhận bằng DevTools của trình duyệt: Inspect thẻ thứ 2, tab Computed hiện `margin-top: 20px`, tab Styles trỏ về rule `.card + .card`.

**Cách xử lý**
Bỏ rule `.card + .card`. Khoảng cách giữa các card giao cho container quyết định bằng thuộc tính `gap` của Flexbox (`.stats`, `.status-strip`, `.columns`, `.stack` đều đã có `gap`).

**Kết quả**
Các thẻ thẳng hàng trên mọi kích thước màn hình.
Bài học: trong bố cục Flexbox nên dùng `gap` ở container thay vì đặt margin cho từng phần tử con.

---

## Issue 4: Đơn hàng mẫu có giờ tạo ở tương lai, doanh thu tháng bằng 0

**Issue**
Sau khi chạy `php artisan migrate:fresh --seed` lúc 16:56, danh sách hiện đơn #11 được tạo lúc **18:56 cùng ngày**, tức là ở tương lai. Ô "Doanh thu tháng 10/2026" trên trang Tổng quan hiện **0 ₫**.

**Nguyên nhân**
- `OrderSeeder` đặt ngày tạo bằng `now()->subDays($n)->setTime(rand(8, 20), rand(0, 59))`. Với đơn "hôm nay" (`$n = 0`), giờ ngẫu nhiên có thể sau giờ hiện tại.
- Các đơn mẫu ở trạng thái Hoàn thành đều cách hiện tại từ 8 ngày trở lên. Chạy seed vào đầu tháng thì tất cả rơi vào tháng trước. Doanh thu chỉ tính đơn Hoàn thành, nên doanh thu tháng này bằng 0. Đây là dữ liệu mẫu chưa hợp lý, code tính toán không sai.

**Cách điều tra**
1. So giờ tạo đơn #11 (18:56) với giờ máy (16:56).
2. Đọc `OrderSeeder`, thấy `setTime(rand(8, 20), ...)` không chặn giờ vượt quá hiện tại.
3. Đọc `DashboardController`: doanh thu tháng = tổng `total_amount` của đơn `completed` có `created_at` từ đầu tháng. Đối chiếu dữ liệu seed thì không có đơn hoàn thành nào trong tháng 10.

**Cách xử lý**
- Đổi thành `now()->subDays($n)->subMinutes(rand(30, 360))`, luôn lùi về quá khứ.
- Dời 2 đơn Hoàn thành mẫu về 0 và 1 ngày trước.

**Kết quả**
Kiểm tra lại bằng SQL:

```sql
SELECT id, created_at, created_at > NOW() AS tuong_lai FROM orders ORDER BY created_at DESC LIMIT 5;
-- cột tuong_lai đều bằng 0
```

Doanh thu tháng có số liệu để demo.
Bài học: khi sinh dữ liệu ngẫu nhiên phải đặt giới hạn hợp lệ, và kiểm tra dữ liệu mẫu có phản ánh đúng các chức năng cần demo hay không.
