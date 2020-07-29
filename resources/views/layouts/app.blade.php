<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}"">

    <title>ETM System - Ekatunggal Group</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar fixed-top navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    ETM System
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else

                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Data <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/barang" class="dropdown-item" >Barang</router-link>
                                    <router-link to="/customer" class="dropdown-item" >Customer</router-link>
                                    <router-link to="/divisi" class="dropdown-item" >Divisi</router-link>
                                    <router-link to="/supplier" class="dropdown-item" >Supplier</router-link>
                                    <router-link to="/sales" class="dropdown-item" >Sales</router-link>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Marketing <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/rso" class="dropdown-item" >Data RSO</router-link>
                                    <router-link to="/confirmso" class="dropdown-item" >Request SO</router-link>
                                    <router-link to="/so" class="dropdown-item" >Data SO</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Inventory Control <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/dic" class="dropdown-item" >Request RSO</router-link>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Purchasing <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/purchase" class="dropdown-item" >Estimation Request RSO</router-link>
                                </div>
                            </li>
                                
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 mt-5">
            @yield('content')
        </main>
    </div>
</body>
<script>

</script>
</html>
