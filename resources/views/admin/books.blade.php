<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library | Books</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.books') }}">Books</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 fw-bold">Books Inventory</h2>
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Book
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="myTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                <tr>
                    <th style="width:52%;">Title</th>
                    <th style="width:12%;">Author</th>
                    <th style="width:11%;">ISBN</th>
                    <th style="width:10%;">Status</th>
                    <th style="width:5%;">Copies</th>
                    <th style="width:10%;" class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($books as $row)
                    <tr>
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->author }}</td>
                        <td>{{ $row->isbn }}</td>
                        <td>
                            <span class="badge {{ $row->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $row->quantity > 0 ? 'Available' : 'Out of Stock' }}
                            </span>
                        </td>
                        <td>{{ $row->quantity }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->book_id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="{{ route('admin.books.delete', $row->book_id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this book?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal{{ $row->book_id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.books.update') }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header"><h5>Edit Book</h5></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="{{ $row->book_id }}">
                                        <div class="mb-3"><label>Title</label><input type="text" name="title" class="form-control" value="{{ $row->title }}" required></div>
                                        <div class="mb-3"><label>Author</label><input type="text" name="author" class="form-control" value="{{ $row->author }}" required></div>
                                        <div class="mb-3"><label>ISBN</label><input type="text" name="isbn" class="form-control" value="{{ $row->isbn }}" required></div>
                                        <div class="mb-3"><label>Copies</label><input type="number" name="copies" class="form-control" value="{{ $row->quantity }}" min="0" required></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.books.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Add New Book</h5></div>
                <div class="modal-body">
                    <div class="mb-3"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                    <div class="mb-3"><label>Author</label><input type="text" name="author" class="form-control" required></div>
                    <div class="mb-3"><label>ISBN</label><input type="text" name="isbn" class="form-control" required></div>
                    <div class="mb-3"><label>Copies</label><input type="number" name="copies" class="form-control" min="0" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add Book</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('msg'))
    @php
        $messages = [
            'added'     => ['Book added successfully.', 'success'],
            'updated'   => ['Book updated successfully.', 'success'],
            'deleted'   => ['Book deleted successfully.', 'danger'],
            'failed'    => ['Action failed.', 'danger'],
            'duplicate' => ['ISBN already exists.', 'danger']
        ];
        $status = session('msg');
        $text = $messages[$status][0] ?? 'Action completed.';
        $type = $messages[$status][1] ?? 'success';
    @endphp

    <div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:1055; width: 350px;">
        <div class="alert alert-{{ $type }} text-center py-2 m-0 shadow-sm border-0">
            <i class="bi {{ $type == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
            {{ $text }}
        </div>
    </div>

    <script>
        setTimeout(() => {
            const alertBox = document.querySelector('.alert-container');
            if (alertBox) {
                alertBox.style.opacity = '0';
                alertBox.style.transition = 'opacity 0.6s ease';
                setTimeout(() => alertBox.remove(), 600);
            }
        }, 2000);
    </script>
@endif

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() { $('#myTable').DataTable({ order: [[0, 'asc']] }); });
</script>

<footer class="text-center py-3 mt-5">
  <small>&copy; 2026 Library Management System | Admin Dashboard</small>
</footer>

</body>
</html>