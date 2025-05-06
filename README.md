# Web Mua Sắm Cơ Bản (WEB_MSB)

Ứng dụng web mua sắm đơn giản với chức năng đăng nhập, đăng ký, xem sản phẩm, giỏ hàng và thanh toán.

## Yêu cầu hệ thống

- PHP 7.0 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Web server (Apache/Nginx)
- XAMPP/WAMP/MAMP (nếu chạy trên môi trường phát triển)

## Cài đặt

1. Clone hoặc tải repository về máy của bạn
2. Giải nén vào thư mục web server (vd: `htdocs` cho XAMPP)
3. Mở trình duyệt và truy cập `http://localhost/WEB_MSB/setup_database.php` để thiết lập cơ sở dữ liệu
4. Sau khi thiết lập thành công, bạn có thể truy cập ứng dụng tại `http://localhost/WEB_MSB`

## Thiết lập thủ công

Nếu việc thiết lập tự động không hoạt động, bạn có thể thực hiện các bước sau:

1. Tạo cơ sở dữ liệu `db_web` và `db_product` trong MySQL
2. Nhập các file SQL sau vào cơ sở dữ liệu tương ứng:
   - `db_web_setup.sql` cho `db_web`
   - `db_product_setup.sql` cho `db_product`
3. Tạo các thư mục sau (nếu chưa tồn tại):
   - `uploads`
   - `uploads/avatars`
   - `uploads/products`

## Tài khoản mặc định

Sau khi cài đặt, bạn có thể đăng nhập bằng các tài khoản sau:

- **Quản trị viên**:
  - Tên đăng nhập: admin
  - Mật khẩu: admin123

- **Người dùng thường**:
  - Tên đăng nhập: user1
  - Mật khẩu: user123

## Cấu trúc thư mục

```
WEB_MSB/
├── app/                  # Mã nguồn ứng dụng
│   ├── controllers/      # Các controller
│   ├── models/           # Các model
│   └── views/            # Các view
├── db/                   # Các file liên quan đến database
├── public/               # Tài nguyên công khai (hình ảnh, CSS, JS)
├── uploads/              # Thư mục chứa file người dùng tải lên
├── db_web_setup.sql      # Script tạo database db_web
├── db_product_setup.sql  # Script tạo database db_product
├── setup_database.php    # Script thiết lập cơ sở dữ liệu
└── index.php             # Tệp khởi đầu
```

## Chức năng chính

- Đăng nhập, đăng ký, đăng xuất
- Xem danh sách sản phẩm
- Tìm kiếm sản phẩm
- Xem chi tiết sản phẩm
- Thêm sản phẩm vào giỏ hàng
- Quản lý giỏ hàng
- Đặt hàng
- Xem lịch sử mua hàng
- Quản lý thông tin cá nhân 