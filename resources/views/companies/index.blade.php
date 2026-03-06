@extends('layout')
 
@section('content')
    @php
        $sortableHeaders = [
            'id' => 'ID',
            'title' => 'Title',
            'company_id' => 'Company ID',
            'color' => 'Color',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Companies</h2>
            <p class="text-muted mb-0">
                {{ $adminUnlocked ? 'Manage your company locations' : 'Unlock access to view company locations' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($adminUnlocked)
                <span class="badge text-bg-success align-self-center">Access unlocked</span>
                <a class="btn btn-outline-success" href="{{ route('companies.import') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-up me-1" viewBox="0 0 16 16">
                      <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 8.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z"/>
                      <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                    </svg>
                    Import Excel
                </a>
                <a class="btn btn-outline-secondary" href="{{ route('companies.export') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-down me-1" viewBox="0 0 16 16">
                        <path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707V11.5z"/>
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                    </svg>
                    Export Excel
                </a>
                <a class="btn btn-primary" href="{{ route('companies.create') }}">
                    + New Company
                </a>
                <form action="{{ route('admin.lock') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Lock</button>
                </form>
            @else
                <span class="badge text-bg-secondary align-self-center">Password required</span>
                <a class="btn btn-primary" href="{{ route('admin.unlock') }}">Unlock Access</a>
            @endif
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

    @if(!$adminUnlocked)
        <div class="alert alert-light border d-flex align-items-center mb-4" role="alert">
            <div class="me-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                    <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.531 0-1.552.223-2.662.524z"/>
                    <path d="M8 5a1 1 0 0 1 1 1v1h.5A1.5 1.5 0 0 1 11 8.5v2A1.5 1.5 0 0 1 9.5 12h-3A1.5 1.5 0 0 1 5 10.5v-2A1.5 1.5 0 0 1 6.5 7H7V6a1 1 0 0 1 1-1zm-1 2h2V6a1 1 0 0 0-2 0v1z"/>
                </svg>
            </div>
            <div>
                Enter the password to access the company table, map details, and individual company pages.
            </div>
        </div>
    @endif
   
    <!-- Map Container -->
    <div id="map"></div>

    @if($adminUnlocked)
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                @foreach ($sortableHeaders as $column => $label)
                                    @php
                                        $isActiveSort = $sortColumn === $column;
                                        $nextDirection = $isActiveSort && $sortDirection === 'asc' ? 'desc' : 'asc';
                                        $sortIndicator = $isActiveSort ? ($sortDirection === 'asc' ? '↑' : '↓') : '';
                                    @endphp
                                    <th @class(['ps-4' => $column === 'id'])>
                                        <a
                                            class="text-decoration-none text-reset d-inline-flex align-items-center gap-1"
                                            href="{{ route('companies.index', ['sort' => $column, 'direction' => $nextDirection]) }}"
                                        >
                                            <span>{{ $label }}</span>
                                            <span class="small">{{ $sortIndicator }}</span>
                                        </a>
                                    </th>
                                @endforeach
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
                                    <form action="{{ route('companies.destroy',$company->id) }}" method="POST" class="d-inline-flex gap-1 flex-nowrap align-items-center">
                                        <a class="btn btn-sm btn-outline-secondary p-1 lh-1" href="{{ route('companies.show',$company->id) }}" aria-label="View" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                                                <path d="M8 5.5A2.5 2.5 0 1 0 8 10a2.5 2.5 0 0 0 0-5z"/>
                                            </svg>
                                        </a>
                                        <a class="btn btn-sm btn-outline-primary p-1 lh-1" href="{{ route('companies.edit',$company->id) }}" aria-label="Edit" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z"/>
                                                <path d="M11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207z"/>
                                            </svg>
                                        </a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-1 lh-1" onclick="return confirm('Are you sure?')" aria-label="Delete" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0A.5.5 0 0 1 8.5 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3h11V2h-11z"/>
                                            </svg>
                                        </button>
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
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <h3 class="h5 mb-3">Company data is locked</h3>
                <p class="text-muted mb-4">
                    Enter the password to reveal the table, map details, and company detail pages.
                </p>
                <a class="btn btn-primary" href="{{ route('admin.unlock') }}">Enter Password</a>
            </div>
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

        const mapMarkers = @json($mapMarkers);

        // Add Markers
        const bounds = new maplibregl.LngLatBounds();
        let hasCoordinates = false;

        mapMarkers.forEach(marker => {
            if (marker.latitude && marker.longitude) {
                const mapMarker = new maplibregl.Marker({ color: marker.color || '#3FB1CE' })
                    .setLngLat([parseFloat(marker.longitude), parseFloat(marker.latitude)]);

                @if($adminUnlocked)
                const popup = new maplibregl.Popup({ offset: 25 })
                    .setHTML(`
                        <div style="text-align:center;">
                            <b>${marker.title}</b><br>
                            ${marker.street}, ${marker.city}<br>
                            <a href="/companies/${marker.id}">View Details</a>
                        </div>
                    `);

                mapMarker.setPopup(popup);
                @endif

                mapMarker.addTo(map);

                bounds.extend([parseFloat(marker.longitude), parseFloat(marker.latitude)]);
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
