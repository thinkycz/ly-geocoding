<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 CRUD Application</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- MapLibre GL JS -->
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary-color: #4f46e5; /* Indigo 600 */
            --primary-hover: #4338ca; /* Indigo 700 */
            --bg-color: #f9fafb; /* Gray 50 */
            --card-bg: #ffffff;
            --text-color: #1f2937; /* Gray 800 */
            --text-muted: #6b7280; /* Gray 500 */
            --border-color: #e5e7eb; /* Gray 200 */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            color: var(--text-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem; /* 12px */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            background-color: var(--card-bg);
            margin-bottom: 2rem;
            padding: 1.5rem;
        }

        .table thead th {
            border-bottom: 2px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .table td {
            vertical-align: middle;
        }

        #map { 
            height: 450px; 
            width: 100%; 
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
            overflow: hidden; /* Ensure rounded corners clip content */
        }

        .form-control {
            border-radius: 0.5rem;
            border-color: var(--border-color);
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }
        
        /* Map Marker Styles */
        .marker {
            display: block;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            background-color: red; /* Fallback */
            border-radius: 50%;
        }
        
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
  
<div class="container py-5">
    @yield('content')
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')
   
</body>
</html>