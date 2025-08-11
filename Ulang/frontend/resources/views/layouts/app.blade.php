<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AKADEMIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        header, footer {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
        }
        .content-wrapper {
            min-height: 80vh;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="container text-center">
            <h1 class="h4 mb-0">AKADEMIK</h1>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container my-4">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} My App. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
