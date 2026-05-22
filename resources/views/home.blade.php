@extends('layouts.app')
@section('title', 'Trang chủ')
@section('content')

<style>
.hero-section {
    min-height: 85vh;
    background: url('https://images.unsplash.com/photo-1528127269322-539801943592?w=1920&q=85') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
}
.hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(15,23,42,0.72) 0%, rgba(15,23,42,0.45) 60%, rgba(26,58,42,0.35) 100%);
}
.hero-search-box {
    background: rgba(255,255,255,0.97);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    box-shadow: 0 20px 60px rgba(15,23,42,0.25);
    backdrop-filter: blur(10px);
}
.hero-tag {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    padding: 0.3rem 0.9rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(6px);
}
.hero-tag:hover {
    background: var(--gold);
    border-color: var(--gold);
    color: white;
}
.cat-card {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    height: 200px;
    cursor: pointer;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.cat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(15,23,42,0.2); }
.cat-card img { width:100%; height:100%; object-fit:cover; transition: transform 0.5s ease; }
.cat-card:hover img { transform: scale(1.08); }
.cat-card-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(transparent 30%, rgba(15,23,42,0.82));
    display: flex; flex-direction: column;
    justify-content: flex-end;
    padding: 1.25rem;
}
.featured-large-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    min-height: 420px;
    height: 100%;
}
.featured-large-card img { width:100%; height:100%; object-fit:cover; transition: transform 0.5s ease; }
.featured-large-card:hover img { transform: scale(1.04); }
.featured-large-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(transparent 30%, rgba(15,23,42,0.88));
    display: flex; flex-direction: column;
    justify-content: flex-end;
    padding: 2rem;
}
.featured-small-card {
    display: flex;
    gap: 1rem;
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid rgba(212,163,115,0.18);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(15,23,42,0.06);
}
.featured-small-card:hover { transform: translateX(4px); box-shadow: 0 8px 24px rgba(15,23,42,0.1); border-color: rgba(212,163,115,0.4); }
.featured-small-card img { width:110px; height:90px; object-fit:cover; flex-shrink:0; }
.sidebar-numbered-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 0.85rem 0;
    border-bottom: 1px solid rgba(212,163,115,0.15);
    text-decoration: none;
    transition: all 0.3s ease;
}
.sidebar-numbered-item:hover { padding-left: 4px; }
.sidebar-num {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: rgba(212,163,115,0.35);
    line-height: 1;
    min-width: 32px;
}
.cta-section {
    background: linear-gradient(135deg, var(--navy) 0%, var(--forest) 100%);
    border-radius: 24px;
    padding: 4rem 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cta-section::before {
    content: '';
    position: absolute; inset: 0;
    background: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=60') center/cover;
    opacity: 0.08;
}
</style>

<!-- ═══ HERO ═══ -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-9">
                <span style="display:inline-block;background:rgba(212,163,115,0.25);border:1px solid rgba(212,163,115,0.5);color:var(--gold);padding:0.35rem 1.1rem;border-radius:9999px;font-size:0.8rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:1.5rem;">
                    ✈ Cẩm nang du lịch Việt Nam
                </span>
                <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2.8rem,6vw,4.5rem);font-weight:800;color:#ffffff;line-height:1.12;letter-spacing:-0.02em;margin-bottom:1.25rem;">
                    Khám phá thế giới<br><span style="color:var(--gold);">Theo cách của bạn</span>
                </h1>
                <p style="color:rgba(255,255,255,0.8);font-size:1.15rem;max-width:560px;margin:0 auto 2.5rem;line-height:1.75;">
                    Chia sẻ kinh nghiệm, khám phá điểm đến mới và tìm cảm hứng cho chuyến đi tiếp theo.
                </p>
                <!-- Search Box -->
                <div class="hero-search-box">
                    <form action="{{ route('posts.index') }}" method="GET">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label-custom text-start d-block"><i class="fas fa-search me-1" style="color:var(--gold);"></i> Tìm kiếm</label>
                                <input type="text" name="search" class="form-control form-control-dark" placeholder="Tìm bài viết, địa điểm...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom text-start d-block"><i class="fas fa-folder me-1" style="color:var(--gold);"></i> Danh mục</label>
                                <select name="category" class="form-control form-control-dark">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom text-start d-block"><i class="fas fa-map-marker-alt me-1" style="color:var(--gold);"></i> Địa điểm</label>
                                <input type="text" name="location" class="form-control form-control-dark" placeholder="Đà Nẵng...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary-custom w-100" style="padding:0.7rem;">
                                    <i class="fas fa-search me-1"></i> Tìm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Quick Tags -->
                <div class="mt-3 d-flex align-items-center gap-2 flex-wrap justify-content-center">
                    <span style="color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:500;">Nổi bật:</span>
                    <a href="{{ route('posts.index', ['location' => 'Đà Nẵng']) }}" class="hero-tag">Đà Nẵng</a>
                    <a href="{{ route('posts.index', ['location' => 'Phú Quốc']) }}" class="hero-tag">Phú Quốc</a>
                    <a href="{{ route('posts.index', ['location' => 'Đà Lạt']) }}" class="hero-tag">Đà Lạt</a>
                    <a href="{{ route('posts.index', ['location' => 'Nha Trang']) }}" class="hero-tag">Nha Trang</a>
                    <a href="{{ route('posts.index', ['location' => 'Hà Giang']) }}" class="hero-tag">Hà Giang</a>
                    <a href="{{ route('posts.index', ['location' => 'Hội An']) }}" class="hero-tag">Hội An</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ CATEGORIES ═══ -->
<section class="py-5" style="background:var(--cream);">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Danh mục <span class="gradient-text">phổ biến</span></h2>
                <p>Chọn chủ đề bạn quan tâm</p>
            </div>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @php
                $catBgs = [
                    'https://images.unsplash.com/photo-1501555088652-021faa106b9b?w=600&q=70',
                    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&q=70',
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=70',
                    'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600&q=70',
                    'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=600&q=70',
                    'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=600&q=70',
                    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=70',
                    'https://images.unsplash.com/photo-1528127269322-539801943592?w=600&q=70',
                ];
                $catIcons = ['fas fa-lightbulb','fas fa-utensils','fas fa-hotel','fas fa-map-marked-alt','fas fa-mountain','fas fa-book-open','fas fa-camera','fas fa-compass'];
            @endphp
            @foreach($categories as $category)
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <a href="{{ route('posts.index', ['category' => $category->slug]) }}" class="text-decoration-none">
                    <div class="cat-card">
                        <img src="{{ $catBgs[$loop->index % count($catBgs)] }}" alt="{{ $category->name }}">
                        <div class="cat-card-overlay">
                            <i class="{{ $catIcons[$loop->index % count($catIcons)] }}" style="color:var(--gold);font-size:1.2rem;margin-bottom:0.4rem;"></i>
                            <div style="font-family:'Playfair Display',serif;font-weight:600;color:#fff;font-size:0.95rem;line-height:1.3;">{{ $category->name }}</div>
                            <div style="color:rgba(255,255,255,0.65);font-size:0.75rem;margin-top:0.2rem;">{{ $category->posts_count }} bài viết</div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ═══ FEATURED POSTS — Magazine Layout ═══ -->
@if($featuredPosts->count())
<section class="py-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Bài viết <span class="gradient-text">nổi bật</span></h2>
                <p>Những bài viết được đọc nhiều nhất</p>
            </div>
            <a href="{{ route('posts.index', ['sort' => 'popular']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4 align-items-stretch">
            <!-- Large card — cột 1 -->
            <div class="col-lg-4" data-aos="fade-right">
                @if($featuredPosts->first())
                @php $fp = $featuredPosts->first(); @endphp
                <a href="{{ route('posts.show', $fp->slug) }}" class="text-decoration-none d-block h-100">
                    <div class="featured-large-card h-100" style="height:100% !important;">
                        <img src="{{ $fp->image_url }}" alt="{{ $fp->title }}">
                        <div class="featured-large-overlay">
                            <span class="badge-category mb-2" style="background:rgba(212,163,115,0.25);color:var(--gold);border-color:rgba(212,163,115,0.4);">{{ $fp->category->name }}</span>
                            <h3 style="font-family:'Playfair Display',serif;font-weight:700;color:#fff;font-size:1.5rem;line-height:1.3;margin-bottom:0.75rem;">{{ $fp->title }}</h3>
                            <div class="meta-info" style="color:rgba(255,255,255,0.7);">
                                <span><i class="fas fa-user me-1"></i>{{ $fp->user->name }}</span>
                                <span><i class="fas fa-eye me-1"></i>{{ number_format($fp->views_count) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif
            </div>

            <!-- Small cards stack — cột 2, justify-content-between để lấp đầy chiều cao -->
            <div class="col-lg-4 d-flex flex-column justify-content-between" data-aos="fade-up">
                @php
                    $smallCards = $featuredPosts->skip(1)->take(3);
                    $needed = 3 - $smallCards->count();
                    if ($needed > 0) {
                        $extra = $latestPosts->take($needed);
                        $smallCards = $smallCards->concat($extra);
                    }
                @endphp
                @foreach($smallCards as $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none flex-fill" style="margin-bottom: {{ !$loop->last ? '1rem' : '0' }};">
                    <div class="featured-small-card h-100" style="min-height:0;">
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}"
                             style="width:110px;height:100%;min-height:90px;object-fit:cover;flex-shrink:0;">
                        <div class="p-3 d-flex flex-column justify-content-center flex-grow-1">
                            <span class="badge-category mb-1" style="font-size:0.65rem;">{{ $post->category->name }}</span>
                            <h6 style="font-family:'Playfair Display',serif;font-weight:600;color:var(--navy);font-size:0.92rem;line-height:1.35;margin-bottom:0.4rem;">{{ Str::limit($post->title, 55) }}</h6>
                            <div class="meta-info" style="font-size:0.75rem;">
                                <span><i class="fas fa-eye me-1"></i>{{ number_format($post->views_count) }}</span>
                                <span><i class="fas fa-calendar me-1"></i>{{ $post->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Sidebar: Bài viết được yêu thích — cột 3 -->
            <div class="col-lg-4" data-aos="fade-left">
                <div style="background:#fff;border-radius:20px;padding:1.75rem;border:1px solid rgba(212,163,115,0.2);box-shadow:var(--shadow-sm);height:100%;">
                    <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin-bottom:1.25rem;font-size:1.05rem;">
                        <i class="fas fa-fire me-2" style="color:var(--gold);"></i>Bài viết được yêu thích
                    </h5>
                    @foreach($featuredPosts->take(5) as $i => $post)
                    <a href="{{ route('posts.show', $post->slug) }}" class="sidebar-numbered-item">
                        <span class="sidebar-num">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <div style="font-family:'Playfair Display',serif;font-weight:600;color:var(--navy);font-size:0.88rem;line-height:1.4;margin-bottom:0.25rem;">{{ Str::limit($post->title, 50) }}</div>
                            <div class="meta-info" style="font-size:0.75rem;">
                                <span><i class="fas fa-eye me-1"></i>{{ number_format($post->views_count) }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- ═══ LATEST POSTS ═══ -->
@if($latestPosts->count())
<section class="py-5" style="background:var(--cream);">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Bài viết <span class="gradient-text">mới nhất</span></h2>
                <p>Cập nhật những bài viết mới nhất từ cộng đồng</p>
            </div>
            <a href="{{ route('posts.index', ['sort' => 'latest']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($latestPosts as $post)
            @include('components.post-card', ['post' => $post, 'showDate' => true])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ FOOD SECTION ═══ -->
@if($foodPosts->count())
<section class="py-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2><span style="color:#ef4444;">Ẩm thực</span> <span class="gradient-text">Việt Nam</span></h2>
                <p>Khám phá thế giới ẩm thực phong phú</p>
            </div>
            <a href="{{ route('posts.index', ['category' => 'am-thuc']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($foodPosts as $post)
            @include('components.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ DESTINATIONS SECTION ═══ -->
@if($destinationPosts->count())
<section class="py-5" style="background:var(--cream);">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Điểm đến <span class="gradient-text">không thể bỏ lỡ</span></h2>
                <p>Những địa điểm tuyệt vời nhất Việt Nam</p>
            </div>
            <a href="{{ route('posts.index', ['category' => 'diem-den']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($destinationPosts as $post)
            @include('components.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ CHECK-IN SECTION ═══ -->
@if($checkinPosts->count())
<section class="py-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Check-in <span class="gradient-text">& Lưu trú</span></h2>
                <p>Khách sạn, Homestay &amp; Góc sống ảo</p>
            </div>
            <a href="{{ route('posts.index', ['category' => 'checkin']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($checkinPosts as $post)
            @include('components.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ KINH NGHIỆM SECTION ═══ -->
@if($experiencePosts->count())
<section class="py-5" style="background:var(--cream);">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Kinh nghiệm <span class="gradient-text">du lịch</span></h2>
                <p>Bí kíp và kinh nghiệm thực tế từ cộng đồng</p>
            </div>
            <a href="{{ route('posts.index', ['category' => 'kinh-nghiem']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($experiencePosts as $post)
            @include('components.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ KHÁCH SẠN SECTION ═══ -->
@if($hotelPosts->count())
<section class="py-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Khách sạn <span class="gradient-text">& Resort</span></h2>
                <p>Review chỗ ở chất lượng từ người thật việc thật</p>
            </div>
            <a href="{{ route('posts.index', ['category' => 'khach-san']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($hotelPosts as $post)
            @include('components.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ CẨM NANG SECTION ═══ -->
@if($guidePosts->count())
<section class="py-5" style="background:var(--cream);">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3" data-aos="fade-up">
            <div>
                <h2>Cẩm nang <span class="gradient-text">từ A đến Z</span></h2>
                <p>Hướng dẫn du lịch chi tiết cho từng điểm đến</p>
            </div>
            <a href="{{ route('posts.index', ['category' => 'cam-nang']) }}" class="btn btn-outline-custom btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            @foreach($guidePosts as $post)
            @include('components.post-card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══ CTA SECTION ═══ -->
<section class="py-5" style="background:var(--cream);">
    <div class="container" data-aos="fade-up">
        <div class="cta-section">
            <div class="position-relative">
                <span style="display:inline-block;background:rgba(212,163,115,0.25);border:1px solid rgba(212,163,115,0.5);color:var(--gold);padding:0.35rem 1.1rem;border-radius:9999px;font-size:0.8rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:1.25rem;">
                    Tham gia cộng đồng
                </span>
                <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:700;color:#ffffff;margin-bottom:1rem;">
                    Bạn có kinh nghiệm du lịch<br>muốn chia sẻ?
                </h2>
                <p style="color:rgba(255,255,255,0.75);font-size:1.05rem;max-width:520px;margin:0 auto 2rem;line-height:1.75;">
                    Tham gia TravelGuide để chia sẻ trải nghiệm và kết nối với những người yêu du lịch khắp Việt Nam!
                </p>
                @guest
                <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg px-5" style="font-size:1rem;">
                    <i class="fas fa-paper-plane me-2"></i> Đăng ký ngay
                </a>
                @endguest
            </div>
        </div>
    </div>
</section>

@endsection
