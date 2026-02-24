@extends('layout')
 
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Companies</h2>
            <p class="text-muted mb-0">Manage your company locations</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-success" href="{{ route('companies.import') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-up me-1" viewBox="0 0 16 16">
                  <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 8.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z"/>
                  <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                Import Excel
            </a>
            <a class="btn btn-primary" href="{{ route('companies.create') }}">
                + New Company
            </a>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
   
    <!-- Map Container -->
    <div id="map"></div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Title</th>
                            <th>Company ID</th>
                            <th>Color</th>
                            <th>Address</th>
                            <th>Coordinates</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        <tr>
                            <td class="ps-4">{{ $company->id }}</td>
                            <td class="fw-medium">{{ $company->title }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $company->company_id }}</span></td>
                            <td>
                                @if($company->color)
                                    <div style="width: 24px; height: 24px; background-color: {{ $company->color }}; border-radius: 4px; border: 1px solid #ddd;" title="{{ $company->color }}"></div>
                                @else
                                    <span class="text-muted small">Default</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $company->street }}</div>
                                <div class="small text-muted">{{ $company->city }}</div>
                            </td>
                            <td>
                                @if($company->latitude && $company->longitude)
                                    <small class="text-muted font-monospace">{{ number_format($company->latitude, 4) }}, {{ number_format($company->longitude, 4) }}</small>
                                @else
                                    <span class="text-muted small">Not set</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('companies.destroy',$company->id) }}" method="POST" class="d-inline">
                                    <a class="btn btn-sm btn-outline-secondary me-1" href="{{ route('companies.show',$company->id) }}">View</a>
                                    <a class="btn btn-sm btn-outline-primary me-1" href="{{ route('companies.edit',$company->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if($companies->isEmpty())
        <div class="text-center py-5 text-muted">
            <p>No companies found. Create one to get started.</p>
        </div>
    @endif
  
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Map
        const map = new maplibregl.Map({
            container: 'map',
            style: {
                "version": 8,
                "sources": {
                    "mapy-cz": {
                        "type": "raster",
                        "tiles": [
                            "https://api.mapy.cz/v1/maptiles/basic/256/{z}/{x}/{y}?apikey={{ config('services.mapy_cz.api_key', env('MAPY_CZ_API_KEY')) }}"
                        ],
                        "tileSize": 256,
                        "attribution": '<a href="https://mapy.cz/" target="_blank">&copy; Seznam.cz, a.s.</a>'
                    }
                },
                "layers": [
                    {
                        "id": "mapy-cz-tiles",
                        "type": "raster",
                        "source": "mapy-cz",
                        "minzoom": 0,
                        "maxzoom": 19
                    }
                ]
            },
            center: [14.42, 50.08], // Default center (Prague)
            zoom: 6
        });

        // Add Navigation Control
        map.addControl(new maplibregl.NavigationControl(), 'top-right');

        // Company Data
        const companies = @json($companies);

        // Add Markers
        const bounds = new maplibregl.LngLatBounds();
        let hasCoordinates = false;

        companies.forEach(company => {
            if (company.latitude && company.longitude) {
                // Use default MapLibre marker (blue pin)
                // Create Popup
                const popup = new maplibregl.Popup({ offset: 25 })
                    .setHTML(`
                        <div style="text-align:center;">
                            <b>${company.title}</b><br>
                            ${company.street}, ${company.city}<br>
                            <a href="/companies/${company.id}">View Details</a>
                        </div>
                    `);

                // Add Marker to Map
                new maplibregl.Marker({ color: company.color || '#3FB1CE' })
                    .setLngLat([parseFloat(company.longitude), parseFloat(company.latitude)])
                    .setPopup(popup)
                    .addTo(map);
                
                bounds.extend([parseFloat(company.longitude), parseFloat(company.latitude)]);
                hasCoordinates = true;
            }
        });

        // Fit bounds to markers if there are companies with coordinates
        if (hasCoordinates) {
            map.fitBounds(bounds, { padding: 50, maxZoom: 15 });
        }
    });
</script>
@endsection