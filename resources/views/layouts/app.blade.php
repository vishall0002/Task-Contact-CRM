<!DOCTYPE html>
<html>

<head>
    <title>@yield('title', 'My App')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
        }

        /* NAVBAR */
        .navbar {
            background: #1e3c72;
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        .nav-left {
            font-size: 20px;
            font-weight: bold;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-links a.active {
            background: #e74c3c;
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="nav-left">My App</div>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('custom.fields') }}" class="{{ request()->routeIs('custom.fields*') ? 'active' : '' }}">
                Custom Fields
            </a>

            <a href="{{ route('contacts.index') }}" class="{{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                Contacts
            </a>

            <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                Logout
            </a>

            <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
                @csrf
            </form>
        </div>

    </div>

    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
