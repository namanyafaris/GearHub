@extends('buyer.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-3">Masuk</h4>
                    <p class="text-muted">Selamat datang kembali.</p>

                    @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="position-relative">
                                <input id="password" class="form-control pe-5" type="password" name="password" required autocomplete="current-password">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="toggle-password" role="button" aria-label="Toggle password visibility" style="cursor:pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check mb-3">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label for="remember_me" class="form-check-label">Ingat saya</label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                @if (Route::has('password.request'))
                                <a class="small" href="{{ route('password.request') }}">Lupa password?</a>
                                @endif
                            </div>
                            <button class="btn btn-primary">Masuk</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('register') }}" class="small">Belum punya akun?</a>
                        <a href="{{ route('home') }}" class="small">Kembali ke Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (togglePasswordBtn && passwordInput) {
        const icon = togglePasswordBtn.querySelector('i');
        togglePasswordBtn.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            if (icon) {
                icon.classList.toggle('bi-eye', !isHidden);
                icon.classList.toggle('bi-eye-slash', isHidden);
            }
        });
    }
</script>
@endpush