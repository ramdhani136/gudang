@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
            <div class="card" style="width:130%;margin-left:-1%">
                <div class="card-body">
                   <!--  {{Request::path() === '/' ? 'tes':'no'}} -->
                  <!--  {{Request::is('/')? 'aktif':'tdk aktif'}} -->
                    <router-view :ambiluser="{{ auth()->user() }}" ></router-view>
                </div>
            </div>
    </div>
</div>
@endsection
