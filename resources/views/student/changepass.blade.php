<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | Student Change Password</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .toggle-password.btn-outline-secondary:hover { background-color: transparent; color: inherit; box-shadow: none; }
        .alert-container { transition: opacity 0.6s ease; z-index: 1055; width: 350px; }
        .fade-out { opacity: 0; }
    </style>
</head>
<body class="bg-light">

@if(session('msg'))
    @php
        $status = session('msg');
        $messages = [
            'updated' => ['Password changed successfully.', 'success'],
            'incorrect' => ['Old password is incorrect.', 'danger'],
            'failed' => ['Failed to update password.', 'danger']
        ];
        [$text, $type] = $messages[$status] ?? [null, null];
    @endphp
    @if($text)
        <div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3">
            <div class="alert alert-{{ $type }} text-center py-2 m-0 shadow-sm">{{ $text }}</div>
        </div>
    @endif
@endif

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" style="height:40px;width:auto;margin-right:10px;">
            <a class="navbar-brand fw-bold mb-0" href="{{ route('student.dashboard') }}">Student Dashboard</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('student.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('student.books') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('student.borrowed') }}">Borrowed</a></li>
            </ul>
        </div>
        <div class="dropdown ms-3">
            <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item active" href="{{ route('student.changepass') }}"><i class="bi bi-gear"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-5" style="max-width: 1000px;">
    <h2 class="fw-bold mb-3">Change Password</h2>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0">{{ $student_display }}</h5>
        </div>

        <form method="POST" action="{{ route('student.password.update') }}" autocomplete="off" id="changePassForm">
            @csrf
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="old_password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" minlength="6" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Use at least 6 characters.</div>
                </div>

                <div class="mb-2">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="new_password_confirmation" id="confirm_password" class="form-control" placeholder="Confirm Password" minlength="6" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div id="matchHint" class="form-text"></div>
                </div>
            </div>

            <div class="card-footer bg-light p-3">
                <button type="submit" class="btn btn-danger px-4 shadow-sm" id="submitBtn" disabled>Change Password</button>
            </div>
        </form>
    </div>
</main>

<footer class="text-center py-3 mt-5">
    <small>&copy; {{ date('Y') }} Library Management System | Student Dashboard</small>
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
    // Alert fadeout
    setTimeout(() => {
        const alertBox = document.querySelector('.alert-container');
        if (alertBox) {
            alertBox.classList.add('fade-out');
            setTimeout(() => alertBox.remove(), 600);
        }
    }, 2000);

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector('i');
            const isHidden = target.type === 'password';
            target.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // Match validation logic
    const newPw = document.getElementById('new_password');
    const confirmPw = document.getElementById('confirm_password');
    const btn = document.getElementById('submitBtn');
    const hint = document.getElementById('matchHint');

    function validate() {
        const short = newPw.value.length > 0 && newPw.value.length < 6;
        const match = newPw.value && newPw.value === confirmPw.value;
        if (short) {
            hint.textContent = 'Password must be at least 6 characters.';
            hint.className = 'form-text text-danger';
        } else if (match) {
            hint.textContent = 'Passwords match.';
            hint.className = 'form-text text-success';
        } else if (confirmPw.value) {
            hint.textContent = 'Passwords do not match.';
            hint.className = 'form-text text-danger';
        } else {
            hint.textContent = '';
            hint.className = 'form-text';
        }
        btn.disabled = short || !match;
    }
    newPw.addEventListener('input', validate);
    confirmPw.addEventListener('input', validate);
</script>
</body>
</html>