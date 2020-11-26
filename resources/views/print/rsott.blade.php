<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <title>{{$rso[0]->nomor_rso}}</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,2, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-10px">
        <b >RSO TIDAK TERSEDIA</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($rso as $s)
    <table style="font-size: 10pt;width:100%">
        <tbody>
            <tr>
                <td style="width:25%;"><b>Nomor RSO</b> : {{$s->nomor_rso}}</td>
                <td style="width:40%;margin-left: 10%;"><b>Customer</b> : {{$s->customer->nama}}</td>
                <td style="width:20%;margin-left: 5%;"><b>Sales</b> : {{$s->user->name}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Tanggal</b> : {{$s->tanggal_rso}}</td>
                <td style="width:35%;margin-left: 10%;"><b>Group</b> : {{$s->groupso->area}}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    <div class="row" style="height: 240px;">  
        <table cellspacing="0" class="table" style="font-size: 10pt; width:100% ;margin-top:20px">
            <thead style="border:solid 1px #000;">
                <tr>
                    <th style="height: 25px;text-align:center;width:5%">No</th>
                    <th style="text-align:left;width:1%;"></th>
                    <th style="text-align:left;width:15%;">Kode Barang</th>
                    <th style="text-align:left;width:1%;"></th>
                    <th style="text-align:left;width:37%">Barang</th>
                    <th style="text-align:left;width:1%;"></th>
                    <th style="text-align:left;width:13%">Jumlah</th>
                    <th style="text-align:left;width:1%;"></th>
                    <th style="text-align:left;width:10%">Satuan</th>
                    <th style="text-align:left;width:1%;"></th>
                    <th style="text-align:left;width:15%;">Estimasi Kedatangan</th>
                </tr>
            </thead>
            <tbody  style="border-bottom:solid 1px #000">
                @php $i=1;
                $total=0; @endphp
                @foreach($list as $p)
                <tr>
                    <td style="text-align:center;padding-top:3px;padding-bottom:3px">{{ $i++ }}</td>
                    <td style="text-align:left;"></td>
                    <td style="text-align:left;">{{$p->kode_barang}}</td>
                    <td style="text-align:left;"></td>
                    <td style="text-align:left;">{{$p->barang->nama}}</td>
                    <td style="text-align:left;"></td>
                    <td style="text-align:left;">{{format_uang($p->qty)}}</td>
                    <td style="text-align:left;"></td>
                    <td style="text-align:left;">{{$p->barang->satuan}}</td>
                    <td style="text-align:left;"></td>
                    <td style="text-align:left;">{{$p->tanggal_datang}}</td>  
                </tr>  
                {{$total+=$p->qty*($p->harga-$p->diskon)}};
                @endforeach
            </tbody>
        </table>
    </div> 
    <div class="row" style="height:30px;font-size:10pt">
        <b>Catatan :</b>
        @foreach($rso as $s)
        {{$s->keterangan}}
        @endforeach
    </div>
    <div class="row">
    <table  cellspacing="0" class="ttd" style="width: 80%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="width:20%">Sales</th>
                    <th style="width:5%"></th>
                    <th style="width:20%">DIC</th>
                    <th style="width:5%"></th>
                    <th style="width:20%">Purchasing</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: auto;;width:20%;border-bottom:solid 1px #000;">
                    @php $i=1 @endphp
                    @foreach($rso as $s)
                        {{$s->tgl_sales}} <br>
                        {{$s->user->name}}
                    @endforeach
                    </td>
                    <td style="height: auto;width:5%"></td>
                    <td style="height: auto;width:20%;border-bottom:solid 1px #000;">
                    @php $i=1 @endphp
                    @foreach($rso as $s)
                        {{$s->tgl_dic}} <br>
                        Approved by : {{$s->dic}}
                    @endforeach
                    </td>
                    <td style="height: auto;width:5%"></td>
                    <td style="height: auto;width:20%;border-bottom:solid 1px #000;">
                    @php $i=1 @endphp
                    @foreach($rso as $s)
                        {{$s->tgl_purchasing}} <br>
                        Approved by : {{$s->purchasing}}
                    @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>