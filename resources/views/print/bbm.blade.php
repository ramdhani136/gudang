<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <title>{{$bbm[0]->bbm}}</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,0, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-20px">
        <b >BUKTI BARANG KELUAR</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($bbm as $s)
    <table style="font-size: 9pt;width:100%">
        <tbody>
            <tr>
                <td style="width:25%;"><b>Nomor BBM</b> : {{$s->bbm}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Nomor PO</b> : {{$s->bcm->nomor_po}}</td>
                <td style="width:35%;margin-left: 5%;"><b>Nopol</b> : {{$s->bcm->nopol}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Tanggal</b> : {{$s->tanggal}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Supplier</b> : {{$s->bcm->po->supplier->nama}}</td>
                <td style="width:35%;margin-left: 5%;"> <b>Keterangan</b> : {{$s->keterangan}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Nomor BCM</b> : {{$s->nomor_bcm}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Purchasing</b> : {{$s->bcm->po->user->name}}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    <div class="row">  
        <table cellspacing="0" class="table" style="font-size: 8.5pt; width:100% ;margin-top:20px">
            <thead style="border:solid 1px #000">
                <tr>
                    <th style="height: 20px;border:solid 1px #000">NO</th>
                    <th style="border:solid 1px #000">KODE ITEM</th>
                    <th style="border:solid 1px #000">ITEM</th>
                    <th style="border:solid 1px #000">JUMLAH</th>
                    <th style="border:solid 1px #000">SATUAN</th> 
                </tr>
            </thead>
            <tbody  style="border:solid 1px #000">
                @php $i=1;
                $total=0; @endphp
                @foreach($list as $p)
                <tr>
                    <td style="text-align:center;border:solid 1px #000">{{ $i++ }}</td>
                    <td style="border:solid 1px #000">{{$p->kode_barang}}</td>
                    <td style="border:solid 1px #000">{{$p->barang->nama}}</td>
                    <td style="text-align:center;border:solid 1px #000">{{format_uang($p->qty)}}</td> 
                    <td style="text-align:center;border:solid 1px #000">{{$p->barang->satuan}}</td>      
 
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div> 
    <div class="row">
        <table  cellspacing="0"  style="width: 60%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="border:solid 1px #000;width:25%">Admin</th>
                    <th style="border:solid 1px #000;width:25%">Checker</th>
                    <th style="border:solid 1px #000;width:25%">Kabag. Gudang</th>
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