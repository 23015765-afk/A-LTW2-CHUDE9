<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 🔴 ĐS THÊM: Thư viện bắt buộc để sử dụng tính năng forceScheme cho URL
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Giữ nguyên cấu hình phân trang Bootstrap 5 sẵn có của bạn
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // 🔴 ĐÃ THÊM: Ép các form và request trong hệ thống luôn sinh link dạng https khi chạy thực tế
        if (config('app.env') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            URL::forceScheme('https');
        }
    }
}
