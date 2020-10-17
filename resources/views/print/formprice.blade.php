<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <title>{{$form[0]->nomor_price}}</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,0, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-20px">
        <b >Form Permintaan Harga</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($form as $s)
    <table style="font-size: 9pt;width:100%">
        <tbody>
            <tr>
                <td style="width:25%;"><b>Nomor</b> : {{$s->nomor_price}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Customer</b> : {{$s->customer->nama}}</td>
                <td style="width:35%;margin-left: 5%;"><b>Keterangan</b> : {{$s->keterangan}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Tanggal</b> : {{$s->tanggal}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Sales</b> : {{$s->user->name}}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    <div class="row">  
        <table class="table" style="font-size: 8.5pt; width:100% ;margin-top:20px">
            <thead style="border:solid 1px #000">
                <tr>
                    <th>NO</th>
                    <th>KODE ITEM</th>
                    <th>ITEM</th>
                    <th>SATUAN</th>
                    <th>HARGA TERAKHIR</th>
                    <th>REQUEST HARGA</th> 
                </tr>
            </thead>
            <tbody  style="border:solid 1px #000">
                @php $i=1;
                @endphp
                @foreach($list as $p)
                <tr>
                    <td style="text-align:center">{{ $i++ }}</td>
                    <td>{{$p->kode_barang}}</td>
                    <td>{{$p->barang->nama}}</td>
                    <td style="text-align:center">{{$p->barang->satuan}}</td>
                    <td style="text-align:center">Rp. {{format_uang($p->harga)}}</td>  
                    <td style="text-align:center">Rp. {{format_uang($p->harga)}}</td>      
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div> 
    <div class="row">
        <table class="ttd" style="width: 60%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="border:solid 1px #000;width:25%">Sales</th>
                    <th style="border:solid 1px #000;width:25%">Kordinator</th>
                    <th style="border:solid 1px #000;width:25%">Supervisor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 75px;border:solid 1px #000;width:25%"></td>
                    <td style="height: 75px;border:solid 1px #000;width:25%"></td>
                    <td style="height: 75px;border:solid 1px #000;width:25%"></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>