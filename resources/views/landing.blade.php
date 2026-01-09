@extends('layouts.app')

@section('content')
<div class="right-side">
    <div class="text-center p-4">
        <img src="{{ asset('img/pup_logo.png') }}" alt="PUP Logo" class="img-fluid mb-3" style="width:90px;">

        <h3 class="fw-bold">Hi, PUPian!</h3>
        <p class="text-muted mb-4">Please click or tap your destination.</p>

        <div class="d-grid gap-2">
            <a href="{{ route('login.student') }}" class="btn btn-primary btn-lg">Student</a>
            <a href="{{ route('login.admin') }}" class="btn btn-danger btn-lg">Librarian</a>
        </div>

        <p class="small text-muted mt-4 mb-0">
            By using this service, you understood and agree to the PUP Online Services
            <a href="https://www.pup.edu.ph/terms/">Terms of Use</a> and <a href="https://www.pup.edu.ph/privacy/">Privacy Statement</a>.
        </p>
    </div>
</div>
@endsection