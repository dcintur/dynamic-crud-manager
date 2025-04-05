<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>{{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                    <!-- Dashboard Link -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <!-- Dynamic Pages Admin Link -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dynamic-pages.index') }}">
                            <i class="bi bi-gear-fill"></i> Manage Pages
                        </a>
                    </li>

                    <!-- User Management Link (for admins) -->
                    @if(auth()->user()->hasRole('Admin'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">
                            <i class="bi bi-people-fill"></i> Users
                        </a>
                    </li>
                    @endif

                    <!-- Dynamic Content Divider -->
                    <li class="nav-item">
                        <div class="dropdown-divider my-2"></div>
                    </li>

                    <!-- Dynamic Content Menu -->
                    <li class="nav-item">
                        <span class="nav-link text-muted small text-uppercase">Dynamic Content</span>
                    </li>

                    @if(isset($menuGroups) && count($menuGroups) > 0)
                        @foreach($menuGroups as $groupName => $pages)
                            @if(count($pages) > 1)
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown{{ Str::slug($groupName) }}" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bi bi-folder2"></i> {{ $groupName }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-start shadow-sm" aria-labelledby="navbarDropdown{{ Str::slug($groupName) }}">
                                        @foreach($pages as $page)
                                            <a class="dropdown-item" href="{{ route('dynamic-data.page', $page) }}">
                                                @if($page->icon)
                                                    <i class="{{ $page->icon }}"></i>
                                                @else
                                                    <i class="bi bi-file-earmark-text"></i>
                                                @endif
                                                {{ $page->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </li>
                            @else
                                @foreach($pages as $page)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dynamic-data.page', $page) }}">
                                            @if($page->icon)
                                                <i class="{{ $page->icon }}"></i>
                                            @else
                                                <i class="bi bi-file-earmark-text"></i>
                                            @endif
                                            {{ $page->name }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> {{ __('Login') }}
                            </a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> {{ __('Register') }}
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <!-- Scripts -->
    @stack('scripts')
</body>
</body>
</html>
