<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Borrowed Books</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .container { margin-top: 50px; }
        
        /* Interactive Rows */
        tr.selectable { cursor: pointer; transition: background 0.2s; }
        tr.selectable:hover { background-color: #f1f3f5; }
        tr.selectable.table-active { background-color: #cfe2ff !important; outline: 2px solid #0d6efd; outline-offset: -2px; }
        
        /* Non-interactive rows */
        tr.dimmed { opacity: 0.7; background-color: #f8f9fa; }

        /* Animation */
        .fade-out { opacity: 0; transition: opacity 0.6s ease; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" style="height:40px;width:auto;margin-right:10px;">
            <a class="navbar-brand fw-bold mb-0" href="{{ route('student.dashboard') }}">Student Dashboard</a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('student.dashboard') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('student.books') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('student.borrowed') }}">Borrowed</a></li>
            </ul>
        </div>
        
        <div class="dropdown ms-3">
             <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
             </button>
             <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('student.changepass') }}"><i class="bi bi-gear"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item text-danger">Logout</button>
                    </form>
                </li>
             </ul>
        </div>
    </div>
</nav>

<main class="container">
    
    @if(session('msg') == 'returned')
        <div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:1055;">
            <div class="alert alert-success shadow-sm px-5">Book successfully returned.</div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0">My Borrowed Books</h2>
        <button id="openReturnBtn" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#returnModal" disabled>
            Return Book
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table id="myTable" class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Title</th>
                        <th style="width: 15%;">Author</th>
                        <th class="d-none d-md-table-cell">ISBN</th>
                        <th>Request Date</th>
                        <th>Issued</th>
                        <th>Due</th>
                        <th>Returned</th>
                        <th>Days Left</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrowedBooks as $row)
                        @php
                            // Status Logic
                            $status = strtolower($row->status);
                            
                            // Can the user return this? (Only if Borrowed or Overdue)
                            $isReturnable = in_array($status, ['borrowed', 'overdue']);
                            
                            // Badge Colors
                            $badgeClass = match($status) {
                                'pending' => 'bg-secondary',
                                'borrowed' => 'bg-warning text-dark',
                                'overdue' => 'bg-danger',
                                'returned' => 'bg-success',
                                'rejected' => 'bg-dark',
                                default => 'bg-light text-dark'
                            };

                            // Days Left Logic (Show only if borrowed)
                            $daysLeft = ($status == 'borrowed') ? max(0, $row->days_left) . ' day(s)' : '—';
                        @endphp

                        <tr class="{{ $isReturnable ? 'selectable' : 'dimmed' }}"
                            data-trans-id="{{ $row->transaction_id }}"
                            data-book-title="{{ $row->title }}"
                            data-book-id="{{ $row->book_id }}">
                            
                            <td>{{ $row->title }}</td>
                            <td>{{ $row->author }}</td>
                            <td class="d-none d-md-table-cell">{{ $row->isbn }}</td>
                            <td class="text-center">{{ $row->request_date }}</td>
                            <td class="text-center">{{ $row->issue_date ?? '—' }}</td>
                            <td class="text-center">{{ $row->due_date ?? '—' }}</td>
                            <td class="text-center">{{ $row->return_date ?? '—' }}</td>
                            <td class="text-center fw-bold {{ $row->days_left < 3 ? 'text-danger' : '' }}">{{ $daysLeft }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form action="{{ route('student.books.return') }}" method="POST">
        @csrf
        <div class="modal fade" id="returnModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Return Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Book to return</label>
                            <input id="modalBookName" type="text" class="form-control" readonly>
                        </div>
                        <p class="text-muted small">Are you sure you want to return this book?</p>
                        
                        <input type="hidden" id="modalBookId" name="book_id">
                        <input type="hidden" id="modalTransId" name="transaction_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Confirm Return</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<footer class="text-center py-3 mt-5">
    <small>&copy; {{ date('Y') }} Library Management System | Student Dashboard</small>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
             order: [], 
             language: { emptyTable: "No history found." }
        });

        // Row Selection Logic
        let selected = null;
        const returnBtn = $('#openReturnBtn');
        const bookNameInput = $('#modalBookName');
        const hiddenBookId = $('#modalBookId');
        const hiddenTransId = $('#modalTransId');

        // Handle click on selectable rows
        $('tbody').on('click', 'tr.selectable', function() {
            const row = $(this);
            
            if (row.hasClass('table-active')) {
                // Deselect
                row.removeClass('table-active');
                returnBtn.prop('disabled', true);
                selected = null;
            } else {
                // Select
                $('tr.selectable').removeClass('table-active');
                row.addClass('table-active');
                
                selected = {
                    transId: row.data('trans-id'),
                    bookId: row.data('book-id'),
                    title: row.data('book-title')
                };
                returnBtn.prop('disabled', false);
            }
        });

        // Pass data to modal
        returnBtn.click(function() {
            if (selected) {
                bookNameInput.val(selected.title);
                hiddenBookId.val(selected.bookId);
                hiddenTransId.val(selected.transId);
            }
        });

        // Fade out alert
        setTimeout(() => {
            $('.alert-container').addClass('fade-out');
            setTimeout(() => $('.alert-container').remove(), 600);
        }, 2000);
    });
</script>
</body>
</html>