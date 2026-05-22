@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu')
@section('content')

<section style="min-height:calc(100vh - 76px);display:flex;align-items:center;background:linear-gradient(135deg,var(--cream) 0%,var(--beige) 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5" style="max-width:440px;">
                <div data-aos="fade-up" style="background:#fff;border-radius:24px;padding:2.5rem;box-shadow:0 20px 60px rgba(15,23,42,0.1);border:1px solid rgba(212,163,115,0.18);">
                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#D4A373,#b8864e);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 8px 24px rgba(212,163,115,0.35);">
                            <i class="fas fa-lock fa-lg text-white"></i>
                        </div>
                        <h3 style="font-family:'Playfair Display',serif;font-weight:700;font-size:1.7rem;color:var(--navy);margin-bottom:0.25rem;">Đặt lại mật khẩu</h3>
                        <p style="color:var(--text-secondary);font-size:0.9rem;">Nhập mật khẩu mới của bạn</p>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-custom alert-error-custom mb-4">
                        @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label class="form-label-custom">Email</label>
                            <input type="email" name="email" class="form-control form-control-dark"
                                   placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control form-control-dark"
                                   placeholder="Ít nhất 8 ký tự" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-custom">Xác nhận mật khẩu mới</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-dark"
                                   placeholder="Nhập lại mật khẩu" required>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 mb-4" style="padding:0.8rem;font-size:1rem;font-weight:600;border-radius:var(--radius-full);">
                            <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
                        </button>
                    </form>
                    <p class="text-center mb-0" style="color:var(--text-secondary);font-size:0.9rem;">
                        <a href="{{ route('login') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
