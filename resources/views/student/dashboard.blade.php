<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Force remove any background images from body */
        body {
            background-image: none !important;  
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" style="height:40px;width:auto;margin-right:10px;">
            <a class="navbar-brand fw-bold mb-0" href="{{ route('student.dashboard') }}">Student Dashboard</a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="{{ route('student.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('student.books') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('student.borrowed') }}">Borrowed</a></li>
            </ul>
        </div>

        <div class="dropdown ms-3">
            <button class="btn btn-outline-dark btn-sm dropdown-toggle d-flex align-items-center"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('student.changepass') }}"><i class="bi bi-gear"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <div class="bg-light p-4 rounded-3 mb-4 shadow-sm">
        <h2 class="fw-bold mb-1">Welcome back, {{ $user->name }}!</h2>
        <p class="text-muted mb-0">Here's an overview of your library activity.</p>
    </div>

    <div class="row g-3">
        <div class="col-6 col-md-4">
            <div class="card text-center shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="text-muted small mb-1">Borrowed Books</div>
                    <div class="display-6 fw-bold text-warning">{{ $stats['total_borrowed'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card text-center shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="text-muted small mb-1">Returned Books</div>
                    <div class="display-6 fw-bold text-success">{{ $stats['total_returned'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card text-center shadow-sm h-100 bg-white">
                <div class="card-body">
                    <div class="text-muted small mb-1">Overdue Books</div>
                    <div class="display-6 fw-bold text-danger">{{ $stats['total_overdue'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Recent Borrowed Books</h4>
            <a href="{{ route('student.borrowed') }}" class="btn btn-outline-primary btn-sm">
                See all <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered align-middle bg-white">
                <thead class="table-light">
                    <tr>
                        <th style="width:40%;">Book Title</th>
                        <th style="width:20%;">Date Borrowed</th>
                        <th style="width:20%;">Due Date</th>
                        <th style="width:20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $row)
                        @php
                            $status = strtolower($row->status);
                            $badge = match($status) {
                                'pending'  => 'bg-secondary text-white', 
                                'borrowed' => 'bg-warning text-dark',     
                                'returned' => 'bg-success text-white',    
                                'overdue'  => 'bg-danger text-white',      
                                'rejected' => 'bg-dark text-white',
                                default    => 'bg-light'
                            };
                        @endphp
                        <tr>
                            <td>{{ $row->book->title }}</td>
                            <td>{{ ($status === 'pending' || $status === 'rejected') ? '—' : ($row->issue_date ?? '—') }}</td>
                            <td>{{ ($status === 'pending' || $status === 'rejected') ? '—' : ($row->due_date ?? '—') }}</td>
                            <td><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No recent activity.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="text-center py-3 mt-5">
    <small>&copy; {{ date('Y') }} Library Management System | Student Dashboard</small>
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>