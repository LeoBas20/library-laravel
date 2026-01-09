<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" style="height: 40px; margin-right: 10px;">
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Library Admin Panel</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.books') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.borrowed.list') }}">Borrowed</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.requests') }}">Request</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.rejected') }}">Rejected</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.students') }}">Students</a></li>
            </ul>
        </div>
        <div class="dropdown ms-3">
            <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> {{ $admin->name }}
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
    <div class="bg-white p-4 rounded-3 mb-4 shadow-sm border">
        <h2 class="fw-bold mb-1">Welcome back, {{ $admin->name }}!</h2>
        <p class="text-muted mb-0">Here's your library dashboard overview.</p>
    </div>

    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Books</div>
                    <div class="display-6 fw-bold text-primary">{{ $stats['total_books'] }}</div>
                </div>
                <div class="card-footer bg-light border-0">
                    <a href="{{ route('admin.books') }}" class="small text-decoration-none">Manage books <i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Borrowed Books</div>
                    <div class="display-6 fw-bold text-warning">{{ $stats['borrowed_books'] }}</div>
                </div>
                <div class="card-footer bg-light border-0">
                    <a href="{{ route('admin.borrowed.list') }}" class="small text-decoration-none">View borrowed <i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Overdue Books</div>
                    <div class="display-6 fw-bold text-danger">{{ $stats['overdue_books'] }}</div>
                </div>
                <div class="card-footer bg-light border-0">
                    <a href="{{ route('admin.borrowed.list', ['status' => 'overdue']) }}" class="small text-decoration-none">See overdue <i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Registered Students</div>
                    <div class="display-6 fw-bold text-success">{{ $stats['total_students'] }}</div>
                </div>
                <div class="card-footer bg-light border-0">
                    <a href="{{ route('admin.students') }}" class="small text-decoration-none">Manage students <i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">Recent Transactions</h3>
            <a href="{{ route('admin.transactions.all') }}" class="btn btn-outline-primary btn-sm">
                See all <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive bg-white rounded shadow-sm border">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th style="width: 40%;">Book Title</th>
                    <th style="width: 20%;">Student</th>
                    <th style="width: 15%;">Date Borrowed</th>
                    <th style="width: 15%;">Due Date</th>
                    <th style="width: 10%;" class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $row)
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
                            <td class="fw-semibold">{{ $row->book->title ?? 'Unknown Book' }}</td>
                            <td>{{ $row->user->name ?? 'Unknown Student' }}</td>
                            <td>{{ $row->issue_date ? $row->issue_date->format('Y-m-d') : '—' }}</td>
                            <td>{{ $row->due_date ? $row->due_date->format('Y-m-d') : '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badge }} px-3">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No recent transactions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="text-center py-3 mt-5">
    <small>&copy; 2026 Library Management System | Admin Dashboard</small>
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>