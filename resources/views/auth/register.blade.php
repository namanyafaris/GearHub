@extends('buyer.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-3">Buat Akun</h4>
                    <p class="text-muted">Daftar untuk mulai berbelanja.</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="position-relative">
                                <input id="password" class="form-control pe-5" type="password" name="password" required autocomplete="new-password">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="toggle-password" role="button" aria-label="Toggle password visibility" style="cursor:pointer;">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </span>
                            </div>
                            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="position-relative">
                                <input id="password_confirmation" class="form-control pe-5" type="password" name="password_confirmation" required autocomplete="new-password">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="toggle-password-confirm" role="button" aria-label="Toggle password visibility" style="cursor:pointer;">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </span>
                            </div>
                            @error('password_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('login') }}" class="small">Sudah punya akun?</a>
                            <button class="btn btn-primary">Daftar</button>
                        </div>
                    </form>
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
    const toggleConfirmBtn = document.getElementById('toggle-password-confirm');
    const confirmInput = document.getElementById('password_confirmation');

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

    if (toggleConfirmBtn && confirmInput) {
        const icon = toggleConfirmBtn.querySelector('i');
        toggleConfirmBtn.addEventListener('click', () => {
            const isHidden = confirmInput.type === 'password';
            confirmInput.type = isHidden ? 'text' : 'password';
            if (icon) {
                icon.classList.toggle('bi-eye', !isHidden);
                icon.classList.toggle('bi-eye-slash', isHidden);
            }
        });
    }
</script>
@endpush