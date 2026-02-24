@extends('layout')
   
@section('content')
<div class="page-header">
    <h2 class="mb-0">Edit Company</h2>
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
        <form action="{{ route('companies.update',$company->id) }}" method="POST">
            @csrf
            @method('PUT')
        
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" value="{{ $company->title }}" class="form-control" placeholder="Title">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Company ID</label>
                    <input type="text" name="company_id" value="{{ $company->company_id }}" class="form-control" placeholder="Company ID">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Street</label>
                    <input type="text" name="street" value="{{ $company->street }}" class="form-control" placeholder="Street">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">City</label>
                    <input type="text" name="city" value="{{ $company->city }}" class="form-control" placeholder="City">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Color</label>
                    <select name="color" class="form-select">
                        <option value="#dc3545" {{ ($company->color ?? '') == '#dc3545' ? 'selected' : '' }} style="color: #dc3545;">Red</option>
                        <option value="#0d6efd" {{ ($company->color ?? '') == '#0d6efd' ? 'selected' : '' }} style="color: #0d6efd;">Blue</option>
                        <option value="#198754" {{ ($company->color ?? '') == '#198754' ? 'selected' : '' }} style="color: #198754;">Green</option>
                        <option value="#ffc107" {{ ($company->color ?? '') == '#ffc107' ? 'selected' : '' }} style="color: #ffc107;">Yellow</option>
                        <option value="#6f42c1" {{ ($company->color ?? '') == '#6f42c1' ? 'selected' : '' }} style="color: #6f42c1;">Purple</option>
                        <option value="#d63384" {{ ($company->color ?? '') == '#d63384' ? 'selected' : '' }} style="color: #d63384;">Pink</option>
                        <option value="#fd7e14" {{ ($company->color ?? '') == '#fd7e14' ? 'selected' : '' }} style="color: #fd7e14;">Orange</option>
                        <option value="#0dcaf0" {{ ($company->color ?? '') == '#0dcaf0' ? 'selected' : '' }} style="color: #0dcaf0;">Cyan</option>
                        <option value="#212529" {{ ($company->color ?? '') == '#212529' ? 'selected' : '' }} style="color: #212529;">Dark</option>
                        <option value="#6c757d" {{ ($company->color ?? '') == '#6c757d' ? 'selected' : '' }} style="color: #6c757d;">Gray</option>
                    </select>
                </div>
                
                <div class="col-12 mt-3 mb-2">
                    <div class="alert alert-light border d-flex align-items-center mb-0" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt text-primary me-2" viewBox="0 0 16 16">
                          <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                          <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                        <div>
                            Coordinates are auto-computed. Editing address will update them.
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Latitude</label>
                    <input type="text" name="latitude" value="{{ $company->latitude }}" class="form-control bg-light" placeholder="Latitude" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Longitude</label>
                    <input type="text" name="longitude" value="{{ $company->longitude }}" class="form-control bg-light" placeholder="Longitude" readonly>
                </div>
                
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">Update Company</button>
                </div>
            </div>
        
        </form>
    </div>
</div>
@endsection