<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Books</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .container { margin-top: 50px; }
        
        /* Table Interaction Styles */
        tr.selectable { cursor: pointer; transition: background-color 0.2s; }
        tr.selectable:hover { background-color: #f1f3f5; }
        tr.selectable.table-active { background-color: #e8f5e9 !important; outline: 2px solid #198754; outline-offset: -2px; }
        tr.disabled { opacity: 0.6; cursor: not-allowed; background-color: #f8f9fa; }
        
        /* Alert Animation */
        .alert-container { transition: opacity 0.6s ease; }
        .fade-out { opacity: 0; }
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
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('student.dashboard') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('student.books') }}">Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('student.borrowed') }}">Borrowed</a></li>
                </ul>
            </div>

            <div class="dropdown ms-3">
                <button class="btn btn-outline-dark btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
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

    <main class="container">
        
        @if(session('msg') == 'pending')
            <div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:1055; width:350px;">
                <div class="alert alert-success text-center py-2 m-0 shadow-sm">
                    Book request sent successfully!
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="m-0">Books</h2>
            <button id="openBorrowBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#borrowModal" disabled>
                Borrow Book
            </button>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table id="myTable" class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%;">Title</th>
                            <th style="width: 20%;">Author</th>
                            <th style="width: 15%;">ISBN</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 5%;">Copies</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            @php
                                $qty = (int)$book->quantity;
                                $status = ($qty > 0) ? 'Available' : 'Not Available';
                                
                                if ($book->borrow_status === 'pending') {
                                    $displayStatus = 'Pending Approval';
                                    $badgeClass = 'bg-warning text-dark';
                                } elseif ($book->borrow_status === 'borrowed') {
                                    $displayStatus = 'Already Borrowed';
                                    $badgeClass = 'bg-primary';
                                } elseif ($qty <= 0) {
                                    $displayStatus = 'Not Available';
                                    $badgeClass = 'bg-secondary';
                                } else {
                                    $displayStatus = 'Available';
                                    $badgeClass = 'bg-success';
                                }

                                // Disable row if user has status OR no copies
                                $isDisabled = !empty($book->borrow_status) || $qty <= 0;
                                $rowClass = $isDisabled ? 'disabled' : 'selectable';
                            @endphp

                            <tr class="{{ $rowClass }}" 
                                data-book-id="{{ $book->book_id }}" 
                                data-book-title="{{ $book->title }}" 
                                data-available="{{ $qty }}">
                                
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->isbn }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <form action="{{ route('student.books.borrow') }}" method="POST">
            @csrf
            <div class="modal fade" id="borrowModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Borrow Book</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Book Name</label>
                                <input id="modalBookName" type="text" name="book_name" class="form-control" readonly>
                            </div>
                            <div class="mb-1">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="qty" class="form-control" value="1" readonly>
                            </div>
                            <p class="text-muted small">Max 1 copy per student.</p>
                            <input type="hidden" id="modalBookId" name="book_id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Confirm Borrow</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <footer class="text-center py-3 mt-5">
        <small>&copy; {{ date('Y') }} Library Management System | Student Dashboard</small>
    </footer>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Init DataTable
            $('#myTable').DataTable();

            // Alert Fade Out
            const alertBox = document.querySelector('.alert-container');
            if (alertBox) {
                setTimeout(() => {
                    alertBox.classList.add('fade-out');
                    setTimeout(() => alertBox.remove(), 600);
                }, 2000);
            }

            // Selection Logic
            const borrowBtn = document.getElementById('openBorrowBtn');
            const bookNameInput = document.getElementById('modalBookName');
            const hiddenBookId = document.getElementById('modalBookId');
            let selected = null;

            $('tbody').on('click', 'tr.selectable', function() {
                const row = $(this);
                
                // Toggle Logic
                if(row.hasClass('table-active')){
                    row.removeClass('table-active');
                    borrowBtn.disabled = true;
                    selected = null;
                } else {
                    $('tr.selectable').removeClass('table-active'); // clear others
                    row.addClass('table-active'); // select this
                    
                    selected = {
                        id: row.data('book-id'),
                        title: row.data('book-title')
                    };
                    borrowBtn.disabled = false;
                }
            });

            // Pass data to modal
            borrowBtn.addEventListener('click', () => {
                if (selected) {
                    bookNameInput.value = selected.title;
                    hiddenBookId.value = selected.id;
                }
            });
        });
    </script>
</body>
</html>