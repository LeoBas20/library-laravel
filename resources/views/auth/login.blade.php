@extends('layouts.app')

@section('content')
<div class="right-side">
    <div class="p-4 text-center" style="width: 100%; max-width: 400px;">
        
        @if (session('msg') === 'reset_success')
            <div class="alert alert-success shadow-sm">
                Password reset successfully. You may now log in.
            </div>
        @endif

        <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" class="img-fluid mb-3" style="width:90px;">
        <h4 class="fw-bold mb-3">{{ ucfirst($role) }} Login</h4>

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            <div class="mb-3">
                <input type="text" name="user_id" class="form-control" 
                       placeholder="{{ $role == 'student' ? 'Student Number' : 'User ID' }}" 
                       value="{{ old('user_id') }}" required autofocus>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <button type="button" id="togglePassword"
                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2"
                        style="border:none; background:none;">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <button type="submit" class="btn {{ $role == 'admin' ? 'btn-danger' : 'btn-primary' }} w-100 py-2">
                Sign in
            </button>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger mt-3 py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <p class="small text-muted mt-4 mb-0">
            By using this service, you understood and agree to the PUP Online Services
            <a href="https://www.pup.edu.ph/terms/">Terms of Use</a> and <a href="https://www.pup.edu.ph/privacy/">Privacy Statement</a>.
        </p>        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const input = document.getElementById('password');

    toggle.addEventListener('click', () => {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        toggle.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
});
</script>

@endsection