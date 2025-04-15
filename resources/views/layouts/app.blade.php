<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IndexCom - Daily Exchange Index')</title>
    <meta name="description" content="@yield('meta_description', 'Track daily exchange indices and visualize progress through graphs')">

    <!-- Tabler CSS from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">

    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Custom CSS -->
    @yield('styles')
</head>
<body class="theme-light">
    <div class="page">
        <!-- Header -->
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="{{ route('index.home') }}" class="text-decoration-none">
                        <span class="fs-3 fw-bold">IndexCom</span>
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1">
                            <i class="ti ti-menu-2 d-lg-none"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navbar menu -->
        <div class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar navbar-light">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item {{ request()->routeIs('index.home') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('index.home') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-home"></i>
                                    </span>
                                    <span class="nav-link-title">Home</span>
                                </a>
                            </li>

                            @php
                                $activeIndices = \App\Models\Index::where('is_active', true)->orderBy('name')->get();
                            @endphp

                            @foreach($activeIndices as $navIndex)
                            <li class="nav-item {{ request()->is($navIndex->slug) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('index.show', $navIndex->slug) }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-chart-line"></i>
                                    </span>
                                    <span class="nav-link-title">{{ $navIndex->name }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page content -->
        <div class="page-wrapper">
            <div class="container-xl">
                <div class="page-header d-print-none">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                @yield('page-title', 'Daily Exchange Index')
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    <a href="#" class="link-secondary">API Documentation</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    &copy; {{ date('Y') }} <a href="/" class="link-secondary">IndexCom</a>.
                                    All rights reserved.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Tabler JS from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>

    <!-- Custom JS -->
    @yield('scripts')
</body>
</html>
