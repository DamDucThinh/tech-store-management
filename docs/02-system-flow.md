# System Flow

## 1. Tổng quan: Frontend → Backend → Database → Server

![Tổng quan hệ thống](images/system-overview.png)

<!-- diagram: system-overview -->
```mermaid
flowchart LR
    subgraph FE["Frontend (trình duyệt)"]
        U(["Người dùng"]) --> V["HTML + CSS + JavaScript<br/>Blade view đã render"]
    end
    subgraph SV["Server"]
        WS["Web server<br/>php artisan serve (local)<br/>Nginx + PHP-FPM (production)"]
    end
    subgraph BE["Backend (Laravel MVC)"]
        R["Route<br/>routes/web.php"] --> M["Middleware<br/>auth · active · can:manager"]
        M --> C["Controller<br/>app/Http/Controllers"]
        C --> FR["Form Request<br/>validation"]
        C --> MD["Model (Eloquent)<br/>app/Models"]
        C --> VW["View (Blade)<br/>resources/views"]
    end
    subgraph DB["Database"]
        MY[("MySQL<br/>mini_sales")]
    end
    V -- "HTTP request" --> WS --> R
    MD -- "SQL" --> MY
    MY -- "kết quả" --> MD
    VW -- "HTML response" --> WS -- "hiển thị" --> V
```

| Lớp | Thành phần | Trong project |
|---|---|---|
| Frontend | HTML, CSS (Flexbox, responsive), JavaScript thuần | `resources/views/*.blade.php`, `public/css/app.css`, `public/js/app.js` |
| Server | Nhận request HTTP, chuyển cho PHP chạy Laravel | Local: `php artisan serve`. Production: Nginx + PHP-FPM (xem [04-deploy.md](04-deploy.md)) |
| Backend | Laravel theo mô hình MVC | `routes/web.php`, `app/Http`, `app/Models`, `app/Policies` |
| Database | MySQL, cấu trúc tạo bằng migration | `database/migrations`, kết nối cấu hình trong `.env` |

## 2. Flow thêm sản phẩm

User → Form → Route → Controller → Model → Database → Response → View

![Flow thêm sản phẩm](images/flow-add-product.png)

<!-- diagram: flow-add-product -->
```mermaid
sequenceDiagram
    autonumber
    actor U as Quản lý
    participant F as Form<br/>products/create
    participant R as Route<br/>POST /products
    participant MW as Middleware
    participant C as ProductController<br/>@store
    participant RQ as ProductRequest
    participant M as Product Model
    participant DB as MySQL
    participant V as View<br/>products/index

    U->>F: Nhập tên, danh mục, giá, mô tả, chọn ảnh, bấm Lưu
    F->>R: POST /products (multipart/form-data, kèm @csrf)
    R->>MW: auth → active → can:manager
    alt Chưa đăng nhập / không phải Quản lý
        MW-->>U: Chuyển về /login hoặc trang 403
    end
    MW->>C: Gọi ProductController@store
    C->>RQ: Validate dữ liệu
    alt Dữ liệu sai (ví dụ price = "abc")
        RQ-->>F: Redirect về form kèm lỗi "Giá phải là số."
    end
    C->>C: Lưu file ảnh vào storage/app/public/products
    C->>M: Product::create($data)
    M->>DB: INSERT INTO products (...)
    DB-->>M: id sản phẩm mới
    M-->>C: Đối tượng Product
    C-->>U: Response: redirect /products + thông báo "Đã thêm sản phẩm"
    U->>R: GET /products
    R->>C: ProductController@index
    C->>M: Product::with('category')->paginate(10)
    M->>DB: SELECT ... FROM products
    C->>V: Truyền danh sách sản phẩm
    V-->>U: HTML danh sách, sản phẩm mới ở đầu
```

### Vai trò từng thành phần

| Thành phần | Làm gì | File |
|---|---|---|
| **User** | Quản lý mở trang thêm sản phẩm và điền form | |
| **Form** | Thu thập dữ liệu. Có validation phía trình duyệt (`required`, `type="number"`, `min="0"`, `accept="image/*"`) để báo lỗi sớm. JavaScript hiện ảnh xem trước. | `resources/views/products/_form.blade.php` |
| **Route** | Ánh xạ `POST /products` tới `ProductController@store`. Gắn middleware kiểm tra đăng nhập và quyền. | `routes/web.php` |
| **Middleware** | `auth`: phải đăng nhập. `active`: tài khoản chưa bị khóa. `can:manager`: chỉ Quản lý được thêm sản phẩm. | `bootstrap/app.php`, `app/Http/Middleware/EnsureEmployeeIsActive.php` |
| **Controller** | Điều phối: nhận request đã validate, lưu ảnh, gọi Model, trả response | `app/Http/Controllers/ProductController.php` |
| **Form Request** | Validation phía server, không tin dữ liệu từ trình duyệt: tên bắt buộc, danh mục phải tồn tại, giá là số ≥ 0, ảnh đúng định dạng và ≤ 2 MB | `app/Http/Requests/ProductRequest.php` |
| **Model** | Đại diện bảng `products`. Eloquent sinh câu SQL. Khai báo quan hệ `belongsTo(Category)`. | `app/Models/Product.php` |
| **Database** | Lưu dữ liệu. Khóa ngoại `category_id` bảo đảm danh mục tồn tại. | MySQL, bảng `products` |
| **Response** | Redirect về danh sách kèm flash message (Post/Redirect/Get, tránh gửi trùng form khi F5) | |
| **View** | Blade render HTML từ dữ liệu Controller truyền sang | `resources/views/products/index.blade.php` |

## 3. Flow tạo đơn hàng

![Flow tạo đơn hàng](images/flow-create-order.png)

<!-- diagram: flow-create-order -->
```mermaid
flowchart TD
    A["Nhân viên mở /orders/create"] --> B["Chọn sản phẩm và số lượng<br/>JavaScript tự tính thành tiền, tổng tiền"]
    B --> C["POST /orders"]
    C --> D{"StoreOrderRequest<br/>dữ liệu hợp lệ?"}
    D -- "Không" --> E["Quay lại form kèm lỗi"]
    D -- "Có" --> F["DB::transaction bắt đầu"]
    F --> G["Lấy lại giá từ bảng products<br/>(không tin giá do form gửi)"]
    G --> H["INSERT orders<br/>status = pending, employee_id = người đang đăng nhập"]
    H --> I["INSERT order_items<br/>quantity + price lúc bán"]
    I --> J["UPDATE orders.total_amount"]
    J --> K{"Có lỗi?"}
    K -- "Có" --> L["Rollback, không lưu gì"]
    K -- "Không" --> M["Commit"]
    M --> N["Redirect /orders/{id}"]
```

Transaction bảo đảm đơn hàng và các dòng hàng được lưu cùng nhau. Nếu một bước lỗi thì không có đơn "nửa vời" nào trong database.

## 4. Flow trạng thái đơn hàng

![Trạng thái đơn hàng](images/order-status.png)

<!-- diagram: order-status -->
```mermaid
stateDiagram-v2
    [*] --> Pending: Tạo đơn
    Pending --> Processing: Bắt đầu xử lý
    Pending --> Cancelled: Hủy
    Processing --> Completed: Hoàn thành
    Processing --> Cancelled: Hủy
    Completed --> [*]
    Cancelled --> [*]
```

| Trạng thái | Hiển thị | Chuyển được sang |
|---|---|---|
| `pending` | Chờ xử lý | Đang xử lý, Đã hủy |
| `processing` | Đang xử lý | Hoàn thành, Đã hủy |
| `completed` | Hoàn thành | Không đổi được nữa |
| `cancelled` | Đã hủy | Không đổi được nữa |

Quy tắc chuyển trạng thái nằm ở `app/Enums/OrderStatus.php`. Doanh thu chỉ tính các đơn Hoàn thành.

## 5. Phân quyền: Quản lý và Nhân viên bán hàng

![Phân quyền](images/authorization.png)

<!-- diagram: authorization -->
```mermaid
flowchart TD
    REQ["Request"] --> A{"Đã đăng nhập?<br/>middleware auth"}
    A -- "Chưa" --> LOGIN["Chuyển về /login"]
    A -- "Rồi" --> B{"Tài khoản active?<br/>middleware active"}
    B -- "Bị khóa" --> OUT["Đăng xuất, báo tài khoản bị khóa"]
    B -- "Active" --> C{"Trang chỉ dành cho Quản lý?<br/>danh mục, thêm/sửa/xóa sản phẩm, nhân viên"}
    C -- "Có" --> D{"role = manager?<br/>Gate manager"}
    D -- "Không" --> F403["403 Không có quyền"]
    D -- "Có" --> OK["Cho phép"]
    C -- "Không" --> E{"Thao tác trên 1 đơn hàng?"}
    E -- "Không" --> OK
    E -- "Có" --> P{"OrderPolicy<br/>Quản lý, hoặc chủ đơn?"}
    P -- "Không" --> F403
    P -- "Có" --> OK
```

| Chức năng | Quản lý | Nhân viên bán hàng |
|---|---|---|
| Xem danh sách, chi tiết sản phẩm | ✔ | ✔ |
| Thêm, sửa, xóa sản phẩm | ✔ | ✘ |
| Quản lý danh mục | ✔ | ✘ |
| Quản lý nhân viên | ✔ | ✘ |
| Xem đơn hàng | Tất cả đơn | Chỉ đơn mình tạo |
| Tạo đơn hàng | ✔ | ✔ |
| Cập nhật trạng thái đơn | Mọi đơn | Đơn của mình |
| Xóa đơn hàng | Mọi đơn | Đơn của mình, khi còn Chờ xử lý |

Phân quyền được kiểm tra ở 3 chỗ:

1. **Route middleware** `can:manager` chặn cả nhóm trang quản lý (`routes/web.php`).
2. **Query scope** `Order::visibleTo($employee)` lọc danh sách: nhân viên chỉ nhận được đơn của mình (`app/Models/Order.php`).
3. **Policy** `OrderPolicy` kiểm tra từng đơn khi xem, đổi trạng thái, xóa (`app/Policies/OrderPolicy.php`). Nhân viên gõ thẳng URL đơn của người khác vẫn nhận 403.

Ẩn nút trên giao diện chỉ để dễ dùng. Bảo mật thật nằm ở 3 lớp trên phía server.
