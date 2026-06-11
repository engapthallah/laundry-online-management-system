<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Iimaan Dry Cleaner') }} - Authentication</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0ea5e9;
            --accent: #f59e0b;
            --dark: #1e293b;
            --muted: #64748b;
            --light: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --radius: 8px;
            --radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #fefce8 100%);
        }

        .auth-card {
            border: none;
            box-shadow: 0 10px 15px rgba(0,0,0,0.05), 0 4px 6px rgba(0,0,0,0.03);
            border-radius: var(--radius-lg);
            background: var(--white);
        }

        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            border-radius: var(--radius) !important;
            font-weight: 600 !important;
            padding: 0.65rem 1.5rem !important;
            transition: var(--transition) !important;
        }

        .btn-primary:hover {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            transform: translateY(-1px) !important;
        }
        
        .form-control {
            border-radius: var(--radius) !important;
            padding: 0.6rem 0.8rem !important;
            border: 1.5px solid var(--border) !important;
            transition: var(--transition) !important;
        }

        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none !important;
        }
    </style>
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="text-center mb-4">
                    <a href="/" class="text-decoration-none fs-2 fw-extrabold text-primary" style="font-weight: 800; letter-spacing: -0.5px;">
                        Iimaan<span style="color: var(--accent);">.</span>
                    </a>
                </div>
                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        {{ $slot }}
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="/" class="text-decoration-none text-muted small fw-medium">
                        <i class="fas fa-arrow-left me-1"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
