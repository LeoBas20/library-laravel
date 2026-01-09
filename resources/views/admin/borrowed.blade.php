<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | Borrowed & Overdue</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
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
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.borrowed.list') }}">Borrowed</a></li>
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

<main class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Borrowed Books</h2>
        <form method="GET" action="{{ route('admin.borrowed.list') }}" class="m-0">
            <select name="status" class="form-select form-select-sm shadow-sm w-auto" onchange="this.form.submit()">
                <option value="borrowed" {{ $status === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                <option value="overdue" {{ $status === 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="myTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width:35%;">Title</th>
                        <th style="width:20%;">Student</th>
                        <th style="width:15%;">Request Date</th>
                        <th style="width:15%;">Borrowed Date</th>
                        <th style="width:15%;">Due Date</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row->book->title ?? 'Unknown' }}</td>
                        <td>{{ $row->user->name ?? 'Unknown' }}</td>
                        <td>{{ $row->request_date ? $row->request_date->format('Y-m-d') : '—' }}</td>
                        <td>{{ $row->issue_date ? $row->issue_date->format('Y-m-d') : '—' }}</td>
                        <td>{{ $row->due_date ? $row->due_date->format('Y-m-d') : '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $row->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ ucfirst($row->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="text-center py-3 mt-5">
  <small>&copy; 2026 Library Management System | Admin Dashboard</small>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() { $('#myTable').DataTable({ language: { emptyTable: "No records found." } }); });
</script>
</body>
</html>