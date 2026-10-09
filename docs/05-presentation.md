# Kịch bản Final Presentation (10–15 phút)

## Chuẩn bị trước giờ trình bày

- [ ] MySQL đang chạy: `brew services list` thấy `mysql` ở trạng thái `started`.
- [ ] Làm mới dữ liệu demo: `php artisan migrate:fresh --seed`.
- [ ] Chạy server: `php artisan serve`, mở `http://127.0.0.1:8000`.
- [ ] Mở sẵn 2 cửa sổ trình duyệt: cửa sổ thường đăng nhập `manager`, cửa sổ ẩn danh đăng nhập `staff` (mật khẩu đều là `password`).
- [ ] Chuẩn bị 1 file ảnh sản phẩm (JPG/PNG, dưới 2 MB) để demo upload.
- [ ] Mở sẵn trong editor: `routes/web.php`, `ProductController.php`, `ProductRequest.php`, `Product.php`, `OrderPolicy.php`.
- [ ] Mở sẵn các hình trong `docs/images/`: `erd.png`, `flow-add-product.png`, `authorization.png`, `deploy-flow.png`.

## 1. Giới thiệu sản phẩm (1–2 phút)

> Mini Sales Management là website quản lý bán hàng cho một cửa hàng nhỏ bán thiết bị điện tử. Vấn đề cửa hàng gặp phải: sản phẩm, đơn hàng ghi sổ tay hoặc Excel, khó tra cứu, không biết nhân viên nào bán đơn nào, và ai cũng sửa được dữ liệu.
>
> Hệ thống có 4 nhóm chức năng: quản lý sản phẩm (có ảnh, tìm kiếm, lọc), quản lý danh mục, quản lý nhân viên (tài khoản + hồ sơ), và quản lý đơn hàng với 4 trạng thái. Có 2 vai trò: Quản lý thấy và quản lý tất cả; Nhân viên bán hàng chỉ quản lý đơn của mình.
>
> Công nghệ: Laravel 13 theo mô hình MVC, MySQL, giao diện HTML/CSS (Flexbox, responsive) và JavaScript thuần, không dùng React/Vue.

## 2. Demo (5–6 phút)

Đăng nhập bằng `manager`, đi theo thứ tự đề bài:

| # | Thao tác | Điểm cần nói |
|---|---|---|
| 1 | **Product**: mở menu Sản phẩm | Danh sách có ảnh, danh mục, giá, trạng thái, phân trang 10 sản phẩm/trang |
| 2 | **Add**: bấm "+ Thêm sản phẩm", nhập tên, chọn danh mục, giá, mô tả, chọn ảnh | Ảnh hiện xem trước ngay nhờ JavaScript. Thử để trống tên để thấy validation |
| 3 | Bấm Lưu | Quay về danh sách, có thông báo, sản phẩm mới ở đầu |
| 4 | **Detail**: bấm vào tên sản phẩm vừa tạo | Trang chi tiết có ảnh lớn, mô tả, số lượng đã bán |
| 5 | **Edit**: bấm Sửa, đổi giá, chuyển trạng thái sang Ngừng bán | Có thể thay ảnh mới hoặc tick "Xóa ảnh hiện tại" |
| 6 | **Delete**: bấm Xóa ở sản phẩm vừa tạo, xác nhận | Xóa thành công vì chưa có trong đơn nào |
| 7 | Thử xóa "iPhone 16 128GB" | Bị chặn: sản phẩm đã có trong đơn hàng, cần giữ lịch sử. Gợi ý chuyển sang Ngừng bán |
| 8 | **Search**: gõ "samsung" vào ô tìm kiếm | Chỉ còn sản phẩm Samsung |
| 9 | **Filter**: chọn danh mục "Máy tính" và trạng thái "Đang bán" | Lọc kết hợp được với tìm kiếm, giữ lại khi chuyển trang |
| 10 | **Order**: Tạo đơn hàng, nhập khách, chọn 2 sản phẩm | Thành tiền và tổng tiền tự tính. Sản phẩm đã chọn ở dòng trên bị khóa ở dòng dưới. Chỉ hiện sản phẩm Đang bán |
| 11 | Lưu đơn, đổi trạng thái Chờ xử lý → Đang xử lý → Hoàn thành | Đơn Hoàn thành không đổi được nữa. Doanh thu chỉ tính đơn Hoàn thành |

## 3. Database (2 phút)

Mở `docs/images/erd.png` (chi tiết trong [01-erd.md](01-erd.md)).

- 6 bảng: `employees`, `employee_profiles`, `categories`, `products`, `orders`, `order_items`.
- Quan hệ: Employee 1-1 Profile (`employee_id` unique); Category 1-N Product; Employee 1-N Order; Order 1-N OrderItem; Product 1-N OrderItem; Order N-N Product qua `order_items`.
- `order_items.price` lưu giá lúc bán, nên đổi giá sản phẩm không làm sai đơn cũ.
- Khóa ngoại RESTRICT chặn xóa danh mục còn sản phẩm, sản phẩm đã bán.
- Mở 1 file migration và 1 model để chỉ `hasMany` / `belongsTo`.

## 4. System Flow (2 phút)

Mở `docs/images/flow-add-product.png` (chi tiết trong [02-system-flow.md](02-system-flow.md)).

> User điền form, form gửi `POST /products`. Route trong `routes/web.php` chuyển tới `ProductController@store`, đi qua middleware kiểm tra đăng nhập và quyền Quản lý. `ProductRequest` validate dữ liệu; sai thì quay lại form kèm lỗi. Đúng thì Controller lưu ảnh vào storage, gọi `Product::create`, Eloquent sinh câu INSERT vào MySQL. Controller trả response redirect về danh sách, View Blade render HTML mới.

Mở `routes/web.php`, `ProductController@store`, `ProductRequest` để chỉ từng bước trên code.

## 5. Phân quyền Manager và Sales Staff (1–2 phút)

Chuyển sang cửa sổ ẩn danh đăng nhập `staff`:

- Menu chỉ còn Tổng quan, Đơn hàng của tôi, Sản phẩm. Không có Danh mục, Nhân viên.
- Danh sách sản phẩm không có nút Thêm, Sửa, Xóa.
- "Đơn hàng của tôi" chỉ có đơn do `staff` tạo.
- Gõ thẳng URL `/products/create` hoặc URL đơn của người khác thì nhận **403**: chặn ở server, không chỉ ẩn nút.
- Đăng nhập `tuan.pham` thì bị từ chối vì tài khoản đã khóa.

Giải thích 3 lớp: middleware `can:manager`, scope `Order::visibleTo()`, `OrderPolicy` (sơ đồ `docs/images/authorization.png`).

## 6. Debug (1–2 phút)

Chọn 1 issue trong [03-debug-report.md](03-debug-report.md) để kể. Gợi ý Issue 2 (test pass trên SQLite nhưng fail trên MySQL), vì có thí nghiệm SQL chứng minh nguyên nhân. Kể theo đúng 5 ý: Issue, Nguyên nhân, Cách điều tra, Cách xử lý, Kết quả.

## 7. Deploy (nếu được hỏi)

Mở `docs/images/deploy-flow.png` (chi tiết trong [04-deploy.md](04-deploy.md)): Domain → DNS → Nginx → PHP-FPM → Laravel → MySQL.

## 8. Chuẩn bị Q&A

| Câu hỏi có thể gặp | Ý trả lời chính |
|---|---|
| Flow từ lúc user vào web đến khi thấy dữ liệu? | Browser → Route → Middleware → Controller → Model → MySQL → Controller → View → Browser |
| Frontend đã validate sao backend vẫn validate? | Request có thể gửi thẳng bằng Postman, bỏ qua form. Backend là lớp bắt buộc. Có test chứng minh (`test_price_must_be_numeric`) |
| Nhập `price = abc` thì sao? | Rule `numeric` báo "Giá phải là số.", không lưu gì |
| Ảnh lưu ở đâu? Database lưu gì? | File lưu ở `storage/app/public/products`, database chỉ lưu đường dẫn. `php artisan storage:link` để truy cập qua `/storage/...` |
| Vì sao cần `order_items`? Sao lưu lại `price`? | Quan hệ N-N, và giữ giá tại thời điểm bán |
| Xóa sản phẩm đã có trong đơn hàng? | Không cho xóa (Controller kiểm tra + FK RESTRICT), chuyển sang Ngừng bán |
| Tạo đơn mà lỗi giữa chừng? | Dùng `DB::transaction`, lỗi thì rollback toàn bộ |
| Nhân viên sửa URL để xem đơn người khác? | `OrderPolicy::view` trả 403 |
| Sao không cho nhảy thẳng Pending → Completed? | Quy trình nghiệp vụ, khai báo trong `OrderStatus::nextStatuses()` |
| Password lưu thế nào? | Hash bcrypt (cast `hashed`), đăng nhập so khớp bằng `Hash::check` |
| hasMany và belongsTo khác gì? | Bảng không chứa khóa ngoại dùng `hasMany`; bảng chứa khóa ngoại dùng `belongsTo` |
| Migration để làm gì? | Quản lý cấu trúc database bằng code, cả team đồng bộ, rollback được |
| `git fetch` khác `git pull`? Xử lý conflict? | Xem file ôn tập `Final_Project_Mini_Sales_Management_QA.docx` |
