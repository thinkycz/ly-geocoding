@extends('layout')
  
@section('content')
<div class="page-header">
    <h2 class="mb-0">Add New Company</h2>
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
        <form action="{{ route('companies.store') }}" method="POST">
            @csrf
        
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Company Name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Company ID</label>
                    <input type="text" name="company_id" class="form-control" placeholder="Unique ID">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Street</label>
                    <input type="text" name="street" class="form-control" placeholder="Street Address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">City</label>
                    <input type="text" name="city" class="form-control" placeholder="City">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Color</label>
                    <select name="color" class="form-select">
                        <option value="#dc3545" style="color: #dc3545;">Red</option>
                        <option value="#0d6efd" style="color: #0d6efd;">Blue</option>
                        <option value="#198754" style="color: #198754;">Green</option>
                        <option value="#ffc107" style="color: #ffc107;">Yellow</option>
                        <option value="#6f42c1" style="color: #6f42c1;">Purple</option>
                        <option value="#d63384" style="color: #d63384;">Pink</option>
                        <option value="#fd7e14" style="color: #fd7e14;">Orange</option>
                        <option value="#0dcaf0" style="color: #0dcaf0;">Cyan</option>
                        <option value="#212529" style="color: #212529;">Dark</option>
                        <option value="#6c757d" style="color: #6c757d;">Gray</option>
                    </select>
                </div>
                
                <div class="col-12 mt-3">
                    <div class="alert alert-light border d-flex align-items-center" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-info-circle text-primary me-2" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                        </svg>
                        <div>
                            Latitude and Longitude will be automatically computed based on the address.
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                </div>
            </div>
        
        </form>
    </div>
</div>
@endsection