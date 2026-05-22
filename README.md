# 🌍 Cẩm Nang Du Lịch Việt

Website chia sẻ kinh nghiệm và cẩm nang du lịch Việt Nam, xây dựng bằng **Laravel 12**.

🔗 **Demo trực tuyến:** [https://du-lich-viet.onrender.com](https://du-lich-viet.onrender.com)

---

## ✨ Tính năng

### 👤 Người dùng
- Đăng ký / Đăng nhập / Quên mật khẩu
- Xem danh sách và chi tiết bài viết
- Tìm kiếm theo tiêu đề, nội dung, địa điểm
- Lọc theo danh mục, sắp xếp (mới nhất / phổ biến)
- Bình luận bài viết (chờ admin duyệt)
- Lưu bài viết yêu thích
- Đánh giá bài viết 1–5 sao
- Cập nhật hồ sơ & avatar

### 👑 Quản trị viên
- Dashboard thống kê với biểu đồ Chart.js
- Quản lý bài viết: thêm, sửa, xóa, lọc
- Quản lý danh mục: CRUD đầy đủ
- Quản lý người dùng: đổi role, xóa
- Kiểm duyệt bình luận: duyệt, ẩn, xóa

---

## 🛠 Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Database | MySQL |
| Frontend | Bootstrap 5, Font Awesome 6, Chart.js |
| Build tool | Vite |
| Deploy | Render.com (Docker) |

---

## ⚙️ Cài đặt Local (XAMPP)

```bash
# 1. Clone dự án
git clone https://github.com/your-username/du-lich.git
cd du-lich

# 2. Cài PHP dependencies
composer install

# 3. Cấu hình môi trường
cp .env.example .env
# Mở .env, sửa thông tin database MySQL

# 4. Tạo APP_KEY
php artisan key:generate

# 5. Tạo database và chạy migration + seed
php artisan migrate:fresh --seed

# 6. Tạo symlink storage
php artisan storage:link

# 7. Build frontend
npm install && npm run build

# 8. Chạy server
php artisan serve
```

Truy cập: `http://localhost:8000`

---

## 🔐 Tài khoản mặc định (sau khi seed)

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | admin@dulich.com | password |
| User | user1@dulich.com | password |

---

## 🚀 Deploy lên Render.com (Miễn phí)

### Bước 1 — Tạo MySQL miễn phí trên Aiven.io

1. Truy cập [aiven.io](https://aiven.io) → **Sign up** (miễn phí)
2. Chọn **Create Service → MySQL**
3. Chọn plan **Free** → chọn region gần nhất → **Create Service**
4. Sau khi tạo xong, vào tab **Overview** lưu lại:
   - `Host` (ví dụ: `mysql-xxx.aivencloud.com`)
   - `Port` (thường là `3306` hoặc `28774`)
   - `Database` (mặc định: `defaultdb`)
   - `Username` (mặc định: `avnadmin`)
   - `Password`

### Bước 2 — Push code lên GitHub

```bash
# Trong thư mục dự án
git add .
git commit -m "feat: ready for production deploy"
git push origin main
```

> Nếu chưa có remote: `git remote add origin https://github.com/your-username/du-lich.git`

### Bước 3 — Tạo Web Service trên Render

1. Truy cập [render.com](https://render.com) → **Sign up** (dùng GitHub account)
2. Click **New +** → **Web Service**
3. Chọn **Connect a repository** → chọn repo `du-lich`
4. Cấu hình:

| Trường | Giá trị |
|---|---|
| **Name** | `du-lich-viet` |
| **Region** | Singapore (gần VN nhất) |
| **Branch** | `main` |
| **Runtime** | **Docker** |
| **Plan** | Free |

5. Click **Create Web Service**

### Bước 4 — Cấu hình Environment Variables

Sau khi tạo service, vào tab **Environment** → **Add Environment Variable**, thêm lần lượt:

| Key | Value |
|---|---|
| `APP_NAME` | `Du Lich Viet` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | *(chạy `php artisan key:generate --show` ở local, copy kết quả)* |
| `APP_URL` | `https://du-lich-viet.onrender.com` *(URL Render cấp)* |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | *(host từ Aiven)* |
| `DB_PORT` | *(port từ Aiven, thường 28774)* |
| `DB_DATABASE` | `defaultdb` |
| `DB_USERNAME` | `avnadmin` |
| `DB_PASSWORD` | *(password từ Aiven)* |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `public` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `error` |
| `MAIL_MAILER` | `log` |

6. Click **Save Changes** → Render sẽ tự động redeploy

### Bước 5 — Chạy Seeder (tạo dữ liệu mẫu)

Sau khi deploy thành công, vào tab **Shell** của Render service:

```bash
php artisan db:seed --force
```

### Bước 6 — Truy cập website

URL có dạng: `https://du-lich-viet.onrender.com`

> **Lưu ý:** Render free tier sẽ ngủ sau 15 phút không có request. Lần đầu truy cập sau khi ngủ mất ~30 giây để khởi động lại.

---

## 📁 Cấu trúc thư mục

```
du-lich/
├── app/Http/Controllers/     # Controllers (Admin + Frontend)
├── app/Models/               # Eloquent Models
├── database/migrations/      # Database schema
├── database/seeders/         # Dữ liệu mẫu
├── resources/views/          # Blade templates
├── routes/web.php            # Routes
├── Dockerfile                # Docker image cho Render
├── docker-entrypoint.sh      # Script khởi động container
├── render.yaml               # Cấu hình Render.com
└── .env.production.example   # Template biến môi trường production
```
