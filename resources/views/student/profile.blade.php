<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student Profile</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

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
            <button class="btn btn-outline-dark btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item active" href="{{ route('student.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('student.changepass') }}"><i class="bi bi-gear"></i> Change Password</a></li>
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

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <h2 class="fw-bold mb-3">Personal Data</h2>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        {{ $user->name ?? 'Student' }} ({{ $user->user_id ?? 'N/A' }})
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('message'))
                        <div class="alert alert-success py-2 shadow-sm border-0">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger py-2">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('student.profile.update') }}" method="POST">
                        @csrf
                        <table class="table table-hover align-middle">
                            <tbody>
                                <tr>
                                    <td class="text-secondary fw-semibold" style="width: 30%;">Student Number</td>
                                    <td class="fw-bold">{{ $user->user_id }}</td>
                                    <td class="text-end"><i class="bi bi-person-badge text-danger fs-5"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary fw-semibold">Name</td>
                                    <td class="fw-bold">{{ $user->name }}</td>
                                    <td class="text-end"><i class="bi bi-person text-danger fs-5"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-secondary fw-semibold">Email</td>
                                    <td>
                                        <input type="email" id="emailInput" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </td>
                                    <td class="text-end"><i class="bi bi-envelope text-danger fs-5"></i></td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-muted mt-3 small">I hereby certify that all information provided is true and correct to the best of my knowledge.</p>
                        <button type="submit" id="saveBtn" class="btn btn-success px-4" disabled>Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-3 mt-5">
    <small>&copy; {{ date('Y') }} Library Management System | Student Dashboard</small>
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const emailInput = document.getElementById('emailInput');
    const saveBtn = document.getElementById('saveBtn');
    const originalEmail = emailInput.value.trim();

    emailInput.addEventListener("input", function () {
        const currentEmail = emailInput.value.trim();
        // Enable button only if email changed and is not empty
        saveBtn.disabled = (currentEmail === originalEmail || currentEmail === "");
    });
});
</script>
</body>
</html>