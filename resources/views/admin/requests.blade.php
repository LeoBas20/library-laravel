<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | Pending Requests</title>
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
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.requests') }}">Request</a></li>
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
    @if(session('msg'))
    <div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:1055;width:350px;">
        <div class="alert alert-{{ session('msg') == 'approved' ? 'success' : 'danger' }} text-center py-2 m-0 shadow-sm border-0">
            {{ session('msg') == 'approved' ? 'Request Approved' : 'Request Rejected' }}
        </div>
    </div>
    <script>setTimeout(() => document.querySelector('.alert-container')?.remove(), 2000);</script>
    @endif

    <h2 class="fw-bold mb-3">Pending Book Requests</h2>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="myTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                <tr>
                <th style="width: 50%;">Book Title</th>
                <th style="width: 15%;">Student</th>
                <th style="width: 12%;">Request Date</th>
                <th class="text-center" style="width: 8%;">Status</th>
                <th class="text-center" style="width: 15%;">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($requests as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row->book->title ?? 'Unknown' }}</td>
                        <td>{{ $row->user->name ?? 'Unknown' }}</td>
                        <td>{{ $row->request_date ? $row->request_date->format('Y-m-d') : '—' }}</td>
                        <td class="text-center"><span class="badge bg-secondary">Pending</span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal"
                                    data-id="{{ $row->id }}" 
                                    data-title="{{ $row->book->title ?? 'Book' }}" 
                                    data-student="{{ $row->user->name ?? 'Student' }}">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <a href="{{ route('admin.requests.reject', $row->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Reject this request?');">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
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

<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.requests.approve') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Approve Request</h5></div>
                <div class="modal-body">
                    <input type="hidden" name="transaction_id" id="modal-transaction-id">
                    <div class="mb-2 small text-muted" id="modal-context"></div>
                    <div class="mb-3"><label class="form-label">Issue Date</label><input type="date" name="issue_date" id="modal-issue-date" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Due Date</label><input type="date" name="due_date" id="modal-due-date" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">Approve</button></div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() { $('#myTable').DataTable({ language: { emptyTable: "No pending requests." } }); });

    document.getElementById('approveModal')?.addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        const modal = event.target;
        modal.querySelector('#modal-transaction-id').value = btn.getAttribute('data-id');
        modal.querySelector('#modal-context').textContent = `Approve: "${btn.getAttribute('data-title')}" for ${btn.getAttribute('data-student')}`;
        
        const today = new Date().toISOString().split('T')[0];
        const due = new Date();
        due.setDate(new Date().getDate() + 7);
        modal.querySelector('#modal-issue-date').value = today;
        modal.querySelector('#modal-due-date').value = due.toISOString().split('T')[0];
    });
</script>
</body>
</html>