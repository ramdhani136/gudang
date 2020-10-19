<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <title>{{$po[0]->nomor_po}}</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,0, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-20px">
        <b >PURCHASE ORDER</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($po as $s)
    <table style="font-size: 9pt;width:100%">
        <tbody>
            <tr>
                <td style="width:25%;"><b>Nomor PO</b> : {{$s->nomor_po}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Supplier</b> : {{$s->supplier->nama}}</td>
                <td style="width:35%;margin-left: 5%;"><b>Tanggal Datang</b> : {{$s->tanggal_datang}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Tanggal</b> : {{$s->tanggal_po}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Purchasing</b> : {{$s->user->name}}</td>
                <td style="width:35%;margin-left: 5%;"> <b>Keterangan</b> : {{$s->keterangan}}</td>
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
                    <th>JUMLAH</th>
                    <th>SATUAN</th>
                    <th>HARGA</th>
                    <th>SUB TOTAL</th>
                </tr>
            </thead>
            <tbody  style="border:solid 1px #000">
                @php $i=1;
                $total=0; @endphp
                @foreach($list as $p)
                <tr>
                    <td style="text-align:center">{{ $i++ }}</td>
                    <td>{{$p->kode_barang}}</td>
                    <td>{{$p->barang->nama}}</td>
                    <td style="text-align:center">{{format_uang($p->qty)}}</td>
                    <td style="text-align:center">{{$p->barang->satuan}}</td>
                    <td style="text-align:center">Rp. {{format_uang($p->harga)}}</td>      
                    <td style="text-align:center">Rp. {{format_uang($p->qty*$p->harga)}}</td>
                </tr>  
                {{$total+=$p->qty*$p->harga}};
                @endforeach
            </tbody>
        </table>
        <table style="width:100.3%;font-size:10pt">
            <thead>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:15%;border:solid 1px #000;">Grand Total :</th> 
                    <th style="width:15%;border:solid 1px #000;background-color:lightgrey">@php echo "Rp. ",format_uang($total) @endphp</th>
            </thead>
        </table>
    </div> 
    <div class="row">
        <table class="ttd" style="width: 50%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="border:solid 1px #000;width:25%">Dibuat oleh</th>
                    <th style="border:solid 1px #000;width:25%">Mengetahui</th>
                    <th style="border:solid 1px #000;width:25%">Supplier</th>
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