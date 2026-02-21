@extends('layout')
  
@section('content')
<div class="page-header">
    <h2 class="mb-0">Company Details</h2>
    <a class="btn btn-outline-secondary" href="{{ route('companies.index') }}"> Back</a>
</div>
   
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h4 class="card-title mb-4">{{ $company->title }}</h4>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Company ID</div>
                    <div class="col-sm-8 fw-medium">{{ $company->company_id }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Street</div>
                    <div class="col-sm-8">{{ $company->street }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">City</div>
                    <div class="col-sm-8">{{ $company->city }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Color</div>
                    <div class="col-sm-8">
                        @if($company->color)
                            <div style="width: 24px; height: 24px; background-color: {{ $company->color }}; border-radius: 4px; border: 1px solid #ddd;" title="{{ $company->color }}"></div>
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Latitude</div>
                    <div class="col-sm-8 font-monospace">{{ $company->latitude ?? 'N/A' }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Longitude</div>
                    <div class="col-sm-8 font-monospace">{{ $company->longitude ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="card-footer bg-light border-top-0 text-end">
                <form action="{{ route('companies.destroy',$company->id) }}" method="POST" class="d-inline">
                    <a class="btn btn-primary me-2" href="{{ route('companies.edit',$company->id) }}">Edit Company</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">Delete Company</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- We could add a mini map here in the future -->
        <div class="card shadow-sm bg-light h-100 border-0">
            <div class="card-body d-flex align-items-center justify-content-center text-center p-5">
                <div class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-building mb-3 opacity-50" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/>
                      <path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/>
                    </svg>
                    <p class="mb-0">Company Information</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection