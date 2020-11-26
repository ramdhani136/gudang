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
    $hasil =  number_format($angka,2, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-10px">
        <b >FORM REQUEST HARGA</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($form as $s)
    <table style="font-size: 10.5pt;width:100%">
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
    <div class="row" style="height:250px">
        <table cellspacing="0" class="table" style="font-size: 9.5Pt; width:100% ;margin-top:20px;border-bottom:solid 1px #000;padding-bottom:10px">
            <thead style="border:solid 1px #000;">
                <tr>
                  <th style="height:25px;width:1%;text-align:left;"></th>
                    <th style="height:25px;width:5%;text-align:left;">No</th>
                    <th style="width:17%;text-align:left">Kode Barang</th>
                    <th style="width:1%;text-align:left"></th>
                    <th style="width: 46%;text-align:left">Deskripsi</th>
                    <th style="width: 10%;text-align:left">Satuan</th>
                    <th style="width: 20%;text-align:left">Harga</th> 
                </tr>
            </thead>
            <tbody>
                @php $i=1;
                @endphp
                @foreach($list as $p)
                <tr>
                     <td style="text-align:left;padding-top:3px;padding-bottom:3px"></td>
                    <td style="text-align:left;">{{ $i++ }}</td>
                    <td>{{$p->kode_barang}}</td>
                    <td></td>
                    <td>{{$p->barang->nama}}</td>
                    <td style="text-align:left;">{{$p->barang->satuan}}</td>
                    <td>Rp. {{format_uang($p->harga)}}</td>     
                </tr>  
                @endforeach
            </tbody>
        </table>
    </div> 
    <div class="row">
        <table cellspacing="0" class="ttd" style="width: 70%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="width:25%;">Sales</th>
                    <th style="width:10%;"></th>
                    <th style="width:25%;">Kordinator</th>
                    <th style="width:10%;"></th>
                    <th style="width:25%;">Supervisor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: auto;width:25%;border-bottom:solid 1px #000;">
                        @php $i=1 @endphp
                         @foreach($form as $s)
                         {{$s->tgl_sent}} <br>
                         {{$s->user->name}}
                         @endforeach
                    </td>
                    <td style="height: auto;width:10%"></td>
                    <td style="height: auto;width:25%;border-bottom:solid 1px #000;">
                      @php $i=1 @endphp
                         @foreach($form as $s)
                         {{$s->tgl_kordinator}} <br>
                         {{$s->kordinator}}
                         @endforeach
                    </td>
                    <td style="height: auto;width:10%"></td>
                    <td style="height: auto;width:25%;border-bottom:solid 1px #000;">
                         @php $i=1 @endphp
                         @foreach($form as $s)
                         {{$s->tgl_spv}} <br>
                         {{$s->spv}}
                         @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>