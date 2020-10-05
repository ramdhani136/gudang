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
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li> -->
                            @if (Route::has('register'))
                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li> -->
                            @endif
                        @else


                            <!-- Halaman Acc -->
                            @if(Auth::user()->acc == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Data <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/user" class="dropdown-item" >User</router-link>
                                    <router-link to="/barang" class="dropdown-item" >Barang</router-link>
                                    <router-link to="/customer" class="dropdown-item" >Customer</router-link>
                                    <router-link to="/divisi" class="dropdown-item" >Divisi</router-link>
                                    <router-link to="/supplier" class="dropdown-item" >Supplier</router-link>
                                    <router-link to="/sales" class="dropdown-item" >Sales</router-link>
                                </div>
                            </li>
                            @endif

                            <!-- halaman sales -->
                            @if(Auth::user()->sales == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Sales <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/rso" class="dropdown-item" >Request Sales Order</router-link>
                                    <router-link to="/so" class="dropdown-item" >Sales Order</router-link>
                                    <router-link to="/formprice" class="dropdown-item" >Customer Price</router-link>
                                    <router-link to="/price/customer" class="dropdown-item" >History Price</router-link>
                                    <router-link to="/retur" class="dropdown-item" >Retur Penjualan</router-link>
                                    <router-link to="/ekspedisi" class="dropdown-item" >Data Ekspedisi</router-link>
                                </div>
                            </li>
                            @endif

                             <!-- Halaman Sales Kordinator -->
                             @if(Auth::user()->kordisales == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Sales <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/confirmso" class="dropdown-item" >Konfirmasi SO</router-link>
                                    <router-link to="/formprice" class="dropdown-item" >Permintaan Harga</router-link>
                                    <router-link to="/price/customer" class="dropdown-item" >Data Harga</router-link>
                                </div>
                            </li>
                            @endif
                            

                            <!-- Halaman Sales SPV -->
                            @if(Auth::user()->susales == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Sales <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/confirmso" class="dropdown-item" >SO Confirmation</router-link>
                                    <router-link to="/so/data/selesai" class="dropdown-item" >Request SO Selesai</router-link>
                                    <!-- <router-link to="/data/price/request" class="dropdown-item" >Request Special Price</router-link> -->
                                    <router-link to="/formprice" class="dropdown-item" >Request Harga</router-link>
                                    <router-link to="/price/customer" class="dropdown-item" >Data Special Price</router-link>
                                    <router-link to="/groupso" class="dropdown-item" >Input Group Customer </router-link>
                                </div>
                            </li>
                            @endif

                            <!-- Halaman Inventory -->
                            @if(Auth::user()->inventory == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Inventory Control <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/dic" class="dropdown-item" >Request Sales Order</router-link>
                                    <router-link to="/pr" class="dropdown-item" >Item Request</router-link>
                                    <router-link to="/purchase/dpo" class="dropdown-item" >Purchase Order List</router-link>
                                    <!-- <router-link to="/dic/incoming/confirm" class="dropdown-item" >Incoming Goods</router-link> -->
                                </div>
                            </li>
                            @endif

                            <!-- halaman Incoming -->
                            @if(Auth::user()->incoming == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Incoming Goods<span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <!--     <router-link to="/purchase/dpo" class="dropdown-item" >Purchase Order List</router-link> -->
                                    <router-link to="/purchase/po/" class="dropdown-item" >Purchasing Order</router-link>
                                    <router-link to="/dic/bcm" class="dropdown-item" >Bukti Checker</router-link>
                                    <!-- <router-link to="/dic/rbcm" class="dropdown-item" >Request Bukti Checker</router-link> -->
                                    <router-link to="/dic/incoming" class="dropdown-item" >Bukti Barang Masuk</router-link>
                                    <!-- <router-link to="/dic/incoming/confirm" class="dropdown-item" >Incoming Goods</router-link> -->
                                </div>
                            </li>
                            @endif


                            <!-- Halaman Distribusi -->
                            @if(Auth::user()->distribusi == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Distribution Control <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/distribusi/so" class="dropdown-item" >Sales Order</router-link>
                                    <router-link to="/distribusi/bck" class="dropdown-item" >Bukti Checker Keluar</router-link>
                                    <router-link to="/distribusi/bbk" class="dropdown-item" >Bukti Barang Keluar</router-link>
                                    <router-link to="/distribusi/kendaraan" class="dropdown-item" >Data Kendaraan</router-link>
                                    <router-link to="/distribusi/jkendaraan" class="dropdown-item" >Jenis Kendaraan</router-link>
                                </div>
                            </li>
                            @endif

                            <!-- Halaman Purchasing -->
                            @if(Auth::user()->purch == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Purchasing <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/purchase" class="dropdown-item" >Estimation Request RSO</router-link>
                                    <router-link to="/purchase/pr" class="dropdown-item" >Purchasing Request</router-link>
                                    <router-link to="/purchase/po/" class="dropdown-item" >Purchasing Order</router-link>
                                    <router-link to="/purchase/barang/" class="dropdown-item" >Input Barang</router-link>
                                </div>
                            </li>
                            @endif  

                            <!-- Halaman Purch SPV -->
                            @if(Auth::user()->suppurch == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Purchasing <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/purchase/po/" class="dropdown-item" >Request Acc PO</router-link>
                                  <!--   <router-link to="/purchase/po/acc" class="dropdown-item" >Request Acc PO</router-link> -->
                                    <router-link to="/purchase/po/selesai" class="dropdown-item" >Request Selesai PO</router-link>
                                </div>
                            </li>
                            @endif   

                            <!-- Halaman Warehouse -->
                            @if(Auth::user()->warehouse == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Warehouse <span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <router-link to="/warehouse/muatan" class="dropdown-item" >List Muat Barang</router-link>
                                    <router-link to="/warehouse/bongkar" class="dropdown-item" >List Bongkar Barang</router-link>
                                </div>
                            </li>
                            @endif
                            

                          <!-- halaman Incoming spv -->
                          @if(Auth::user()->incomingspv == 1 || Auth::user()->superadmin == 1 )
                            <li class="nav-item dropdown">
                                <router-link to="/transaksi" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Incoming Goods<span class="caret"></span>
                                </router-link>

                                <div class="dropdown-menu flex-center dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <!--     <router-link to="/purchase/dpo" class="dropdown-item" >Purchase Order List</router-link> -->
                                    <router-link to="/dic/bcm" class="dropdown-item" >Request BCM</router-link>
                                    <!-- <router-link to="/dic/incoming/confirm" class="dropdown-item" >Incoming Goods</router-link> -->
                                </div>
                            </li>
                            @endif


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
