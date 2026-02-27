@extends('layout')

@section('content')
<div class="page-header">
    <h2 class="mb-0">Admin Unlock</h2>
    <a class="btn btn-outline-secondary" href="{{ route('companies.index') }}"> Back</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted mb-4">Enter the admin password to enable import, export, create, update, and delete actions.</p>

        <form action="{{ route('admin.unlock.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" autofocus>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">Unlock</button>
            </div>
        </form>
    </div>
</div>
@endsection
