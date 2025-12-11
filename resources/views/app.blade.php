<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'E-Shop') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        #app {
            min-height: 100vh;
        }

        /* Global styles */
        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: 2px solid transparent;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: #6a11cb;
            border-color: #6a11cb;
        }

        .btn-outline:hover {
            background: #6a11cb;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
        }
    </style>

    @routes
</head>
<body>
    @inertia

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <script>
        // Global event listener for auth changes
        window.addEventListener('storage', function(event) {
            if (event.key === 'authToken') {
                window.location.reload();
            }
        });
    </script>
</body>
</html>