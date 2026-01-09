<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | All Transactions</title>
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

<main class="container my-5">
    <h2 class="fw-bold mb-4">Transaction History</h2>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="myTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                <tr>
                    <th style="width:40%;">Book Title</th>
                    <th style="width:15%;">Student</th>
                    <th style="width:10%;">Request Date</th>
                    <th style="width:10%;">Date Borrowed</th>
                    <th style="width:10%;">Due Date</th>
                    <th style="width:10%;">Return Date</th>
                    <th style="width:5%;">Status</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $row)
                        @php
                            $status = strtolower($row->status);
                            $badge = match($status) {
                                'pending'  => 'bg-secondary',
                                'borrowed' => 'bg-warning text-dark',
                                'returned' => 'bg-success',
                                'overdue'  => 'bg-danger',
                                'rejected' => 'bg-dark',
                                default    => 'bg-light text-dark'
                            };
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $row->book->title ?? 'N/A' }}</td>
                            <td>{{ $row->user->name ?? 'N/A' }}</td>
                            <td class="small">{{ $row->request_date ? $row->request_date->format('Y-m-d') : '—' }}</td>
                            <td class="small">{{ $row->issue_date ? $row->issue_date->format('Y-m-d') : '—' }}</td>
                            <td class="small">{{ $row->due_date ? $row->due_date->format('Y-m-d') : '—' }}</td>
                            <td class="small">{{ $row->return_date ? $row->return_date->format('Y-m-d') : '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badge }} px-3">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="text-center py-3 mt-5 text-muted">
    <small>&copy; 2026 Library Management System | Admin Dashboard</small>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            language: { emptyTable: "No transactions found." },
            order: [[2, 'asc']] 
        });
    });
</script>
</body>
</html>