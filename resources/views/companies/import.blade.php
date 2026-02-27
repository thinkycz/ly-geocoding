@extends('layout')
  
@section('content')
<div class="page-header">
    <h2 class="mb-0">Import Companies from Excel</h2>
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
        <form action="{{ route('companies.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
        
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold">Select Excel File</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv">
                    <div class="form-text">Accepted formats: .xlsx, .xls, .csv (Max size: 10MB)</div>
                </div>
                
                <div class="col-12">
                    <div class="alert alert-light border" role="alert">
                        <h5 class="alert-heading">Excel File Format</h5>
                        <p class="mb-2">Your Excel file should have the following columns in the header row:</p>
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Column</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>title</code></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>Company name</td>
                                </tr>
                                <tr>
                                    <td><code>company_id</code></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>Unique company identifier (existing rows will be updated)</td>
                                </tr>
                                <tr>
                                    <td><code>street</code></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>Street address</td>
                                </tr>
                                <tr>
                                    <td><code>city</code></td>
                                    <td><span class="badge bg-danger">Yes</span></td>
                                    <td>City name</td>
                                </tr>
                                <tr>
                                    <td><code>latitude</code></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Latitude (e.g., 50.0870). If omitted, computed from address</td>
                                </tr>
                                <tr>
                                    <td><code>longitude</code></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Longitude (e.g., 14.4210). If omitted, computed from address</td>
                                </tr>
                                <tr>
                                    <td><code>color</code></td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                    <td>Hex color code (e.g., #dc3545). Default: #3FB1CE</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="alert alert-info border d-flex align-items-center" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-info-circle me-2" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                        </svg>
                        <div>
                            If <code>latitude</code> and <code>longitude</code> are provided, they will be used. Otherwise they will be computed from the address.
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-success px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload me-1" viewBox="0 0 16 16">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                          <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                        </svg>
                        Import Companies
                    </button>
                </div>
            </div>
        
        </form>
    </div>
</div>
@endsection
