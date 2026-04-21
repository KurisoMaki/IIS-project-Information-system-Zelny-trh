<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zelný trh')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        main{
            margin-top: 10px;
        }

        body {
            background-color: #e6ffe6; /* Light green background */
            font-family: Arial, sans-serif;
        }
        header {
            text-align: center;
            padding: 20px 0;
            background-color: #4CAF50; /* Green background */
            color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        header h1 {
            font-size: 3rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin: 0;
        }
        nav {
            margin-top: 20px;
        }
        nav ul {
            display: flex;
            justify-content: center;
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            margin-right: 15px;
        }
        nav ul li a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #ffffff;
            color: #4CAF50; /* Green background */
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        nav ul li a:hover {
            background-color: #45a049; /* Darker green on hover */
            color: white;
        }
        .btn-primary {
            background-color: #4CAF50; /* Green background */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition: background-color 0.3s ease;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <header>
        <h1>Zelný trh</h1>
        <nav>
            <ul>
                <li><a href="{{ route('home') }}">Domů</a></li>
                <li><a href="{{ route('uzivatele.index') }}">Farmáři</a></li>
                <li><a href="{{ route('nabidky.index') }}">Nabídky</a></li>
                <li><a href="{{ route('kategorie.index') }}">Kategorie</a></li>
                <!-- <li><a href="{{ route('objednavky.index') }}">Objednavky</a></li> -->
                <!-- <li><a href="{{ route('atributy.index') }}">Atributy</a></li> -->
                <li><a href="{{ route('hodnoceni.index') }}">Hodnocení</a></li>
                @guest
                    <li><a href="{{ route('login') }}">Přihlášení</a></li>
                    <li><a href="{{ route('register') }}">Registrace</a></li>
                @else
                    @if(Auth::check() && Auth::user()->urole === 'Admin')
                        <!-- Admin Dashboard Button -->
                        <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    @endif
                    @if(Auth::check() && Auth::user()->urole === 'Moderator')
                        <!-- Mod Dashboard Button -->
                        <li><a href="{{ route('moderator.dashboard') }}">Moderator Dashboard</a></li>
                    @endif
                    <li><a href="{{ route('dashboard') }}">Profil</a></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Odhlásit se
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')

        <!-- @guest
            <p>You are currently a guest.</p>
        @endguest

        @auth
            <p>You are logged in as {{ Auth::user()->email }}. Your role is {{ Auth::user()->urole }}.</p>
        @endauth -->

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- <p>Session ID: {{ session()->getId() }}</p> -->
    </main>

    <!-- Modální okno -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-light-red">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger text-center" id="errorModalLabel">Chyba</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavřít"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="errorMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-light-red {
            background-color: #ffbcc2; /* Light red background */
        }

        .modal-header {
            position: relative; /* Enable absolute positioning for children */
            justify-content: center; /* Center the title horizontally */
            padding: 1rem;
        }

        .modal-title {
            color: red;
            font-weight: bold;
            margin: 0 auto; /* Center title within its container */
        }

        .btn-close {
            position: absolute;
            top: 0.5rem; /* Adjust for spacing from top */
            right: 0.5rem; /* Adjust for spacing from right */
        }
    </style>

    @if(session('modalError'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const errorMessage = @json(session('modalError'));
                document.getElementById('errorMessage').textContent = errorMessage;
                new bootstrap.Modal(document.getElementById('errorModal')).show();
            });
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>