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
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Location</h5>

                <div id="company-map" style="height: 420px; width: 100%; border-radius: 0.75rem; overflow: hidden;"></div>

                @if(!($company->latitude && $company->longitude))
                    <div class="alert alert-light border mt-3 mb-0" role="alert">
                        Coordinates are not set for this company.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const company = @json($company);

        const hasCoordinates = company.latitude && company.longitude;
        const center = hasCoordinates ? [parseFloat(company.longitude), parseFloat(company.latitude)] : [14.42, 50.08];

        const map = new maplibregl.Map({
            container: 'company-map',
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
            center,
            zoom: hasCoordinates ? 14 : 6
        });

        map.addControl(new maplibregl.NavigationControl(), 'top-right');

        if (hasCoordinates) {
            const popup = new maplibregl.Popup({ offset: 25 })
                .setHTML(`
                    <div style="text-align:center;">
                        <b>${company.title}</b><br>
                        ${company.street}, ${company.city}
                    </div>
                `);

            new maplibregl.Marker({ color: company.color || '#3FB1CE' })
                .setLngLat(center)
                .setPopup(popup)
                .addTo(map);
        }
    });
</script>
@endsection
