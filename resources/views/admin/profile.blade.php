<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | Admin Profile</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" style="height:40px;width:auto;margin-right:10px;">
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Library Admin Panel</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.books') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.borrowed.list') }}">Borrowed</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.requests') }}">Request</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.rejected') }}">Rejected</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.students') }}">Students</a></li>
            </ul>
        </div>
        <div class="dropdown ms-3">
            <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> {{ $admin_name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.changepass') }}"><i class="bi bi-gear"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container d-flex justify-content-center mt-5">
    <div class="col-md-9">
        <h2 class="fw-bold mb-3">Admin Profile</h2>

        @if(session('message'))
            <div id="alertBox" class="alert alert-success text-center py-2 position-fixed top-0 start-50 translate-middle-x mt-3 shadow" style="z-index:1055; width:350px;">
                {{ session('message') }}
            </div>
            <script>
                setTimeout(() => {
                    const box = document.getElementById('alertBox');
                    if(box) box.remove();
                }, 2000);
            </script>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">{{ $admin->name }} ({{ $admin->user_id }})</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr>
                                <td class="fw-semibold" style="width: 30%;">User ID</td>
                                <td>{{ $admin->user_id }}</td>
                                <td class="text-end"><i class="bi bi-person-badge text-danger"></i></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Name</td>
                                <td>{{ $admin->name }}</td>
                                <td class="text-end"><i class="bi bi-person text-danger"></i></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email</td>
                                <td>
                                    <input type="email" name="email" id="emailInput" class="form-control" value="{{ old('email', $admin->email) }}" required>
                                </td>
                                <td class="text-end"><i class="bi bi-envelope text-danger"></i></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Role</td>
                                <td>{{ ucfirst($admin->role) }}</td>
                                <td class="text-end"><i class="bi bi-shield-lock text-danger"></i></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="mt-3 small text-muted">I hereby certify that all information provided is true and correct.</p>
                    <button type="submit" id="saveBtn" class="btn btn-success btn-sm px-4" disabled>Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-3 mt-5">
  <small>&copy; 2026 Library Management System | Admin Dashboard</small>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const emailInput = document.getElementById('emailInput');
        const saveBtn = document.getElementById('saveBtn');
        const originalEmail = emailInput.value.trim();

        emailInput.addEventListener("input", function () {
            saveBtn.disabled = (emailInput.value.trim() === originalEmail || emailInput.value.trim() === "");
        });
    });
</script>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>