<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <title>{{$so[0]->nomor_so}}</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,0, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-20px">
        <b >SALES ORDER</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($so as $s)
    <table class="titlet" style="font-size: 9pt;">
        <tbody>
            <tr>
                <td style="padding-left: 3%;"><b>Nomor SO</b> : {{$s->nomor_so}}</td>
                <td style="padding-left: 3%;"><b>Customer</b> : {{$s->rso->customer->nama}}</td>
                <td style="padding-left: 3%;"><b>Lokasi</b> : {{$s->lokasi}}</td>
            </tr>
            <tr>
                <td style="padding-left: 3%;"><b>Tanggal</b> : {{$s->tanggal_so}}</td>
                <td style="padding-left: 3%;"><b>Nomor RSO</b> : {{$s->nomor_rso}}</td>
                <td style="padding-left: 3%;"> <b>Alamat</b> : {{$s->alamat}}</td>
            </tr>
            <tr>
                <td style="padding-left: 3%;"><b>Rencana Kirim</b> : {{$s->tanggal_kirim}}</td>
                <td style="padding-left: 3%;"><b>Distibusi</b> : {{$s->distribusi}}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    <div class="row">  
        <table class="table" style="font-size: 8pt; width:100% ;margin-top:20px">
            <thead style="border:solid 1px #000">
                <tr>
                    <th>NO</th>
                    <th>KODE ITEM</th>
                    <th>ITEM</th>
                    <th>JUMLAH</th>
                    <th>SATUAN</th>
                    <th>HARGA</th> 
                    <th>SUB TOTAL</th>
                </tr>
            </thead>
            <tbody  style="border:solid 1px #000">
                @php $i=1 @endphp
                @foreach($listso as $p)
                <tr>
                    <td style="text-align:center">{{ $i++ }}</td>
                    <td>{{$p->kode_barang}}</td>
                    <td>{{$p->barang->nama}}</td>
                    <td style="text-align:center">{{format_uang($p->qty)}}</td>
                    <td style="text-align:center">{{$p->barang->satuan}}</td>
                    <td style="text-align:center">Rp. {{format_uang($p->harga)}}</td>       
                    <td style="text-align:center">Rp. {{format_uang($p->qty*$p->harga)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
    <div class="row">
        <table class="ttd" style="width: 60%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="border:solid 1px #000;width:25%">Di buat</th>
                    <th style="border:solid 1px #000;width:25%">Kordinator</th>
                    <th style="border:solid 1px #000;width:25%">Supervisor</th>
                    <th style="border:solid 1px #000;width:25%">DIC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 75px;border:solid 1px #000;width:25%"></td>
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