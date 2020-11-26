<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <title>{{$so[0]->nomor_so}}</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,2, ',' , '.'); 
    return $hasil; 
    }
 
    function format_qty($angka1){ 
    $hasil2 =  number_format($angka1,0, ',' , '.'); 
    return $hasil2; 
    } 
    
    @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-10px">
        <b >SALES ORDER</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($so as $s)
    <table style="font-size: 10pt;width:100%">
        <tbody>
            <tr>
                <td style="width:25%;"><b>Nomor SO</b> : {{$s->nomor_so}}</td>
                <td style="width:32%;margin-left: 5%;"><b>Customer</b> : {{$s->rso->customer->nama}}</td>
                <td style="width:32%;margin-left: 5%;"><b>Lokasi</b> : {{$s->lokasi}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Tanggal</b> : {{$s->tanggal_so}}</td>
                <td style="width:32%;margin-left: 5%;"><b>Sales</b> : {{$s->user->name}}</td>
                <td style="width:32%;margin-left: 5%;"> <b>Alamat</b> : {{$s->alamat}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Rencana Kirim</b> : {{$s->tanggal_kirim}}</td>
                <td style="width:32%;margin-left: 5%;"><b>Distibusi</b> : {{$s->distribusi}}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    <div class="row" style="height:240px">  
        <table cellspacing="0" class="table" style="font-size: 10pt; width:100% ;margin-top:20px;border-bottom:solid 1px #000">
            <thead style="border:solid 1px #000;">
                <tr>
                    <th style="height:25px;text-align:left;width:1%"></th>
                    <th style="height:25px;text-align:left;width:14%">Kode Barang</th>
                    <th style="height:25px;text-align:left;width:1%"></th>
                    <th style="text-align:left;width:35%;">Deskripsi</th>
                    <th style="height:25px;text-align:left;width:1%"></th>
                    <th style="text-align:left;width:10%">Jumlah</th>
                    <th style="height:25px;text-align:left;width:1%"></th>
                    <th style="text-align:left;width:8%">Satuan</th>
                    <th style="height:25px;text-align:left;width:1%"></th>
                    <th style="text-align:left;width:15%">Harga</th>
                    <th style="height:25px;text-align:left;width:1%"></th>
                    <th style="text-align:left;width:15%">Sub Total</th>
                </tr>
            </thead>
            <tbody >
                @php $i=1;
                $total=0; @endphp
                @foreach($listso as $p)
                <tr>
                     <td style="padding-bottom: 3px;padding-top:3px;text-align:left"></td>
                    <td style="text-align:left">{{$p->kode_barang}}</td>
                    <td style="text-align:left"></td>
                    <td>{{$p->barang->nama}}</td>
                    <td style="text-align:left"></td>
                    <td style="text-align:left;">{{format_qty($p->qty)}}</td>
                    <td style="text-align:left"></td>
                    <td style="text-align:left">{{$p->barang->satuan}}</td>
                    <td style="text-align:left"></td>
                    <td style="text-align:left;">Rp. {{format_uang($p->harga-$p->diskon)}}</td>   
                    <td style="text-align:left"></td>    
                    <td style="text-align:left;">Rp. {{format_uang($p->qty*($p->harga-$p->diskon))}}</td>
                </tr>  
                {{$total+=$p->qty*($p->harga-$p->diskon)}};
                @endforeach
            </tbody>
        </table>
        <table style="width:100.3%;font-size:10pt">
            <thead>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:10%;"></th>
                    <th style="width:15%;text-align:left;">Grand Total :</th> 
                    <th style="width:15%;text-align:left;">@php echo "Rp. ",format_uang($total) @endphp</th>
            </thead>
        </table>
    </div> 
    <div class="row" style="font-size: 10pt;height:20px;">
    <b>Catatan :</b>
    @foreach($so as $s)
    {{$s->keterangan}}
    @endforeach
    </div>
    <div class="row">
        <table  cellspacing="0" class="ttd" style="width: 80%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="width:20%">Sales</th>
                    <th style="width:5%"></th>
                    <th style="width:20%">Kordinator</th>
                    <th style="width:5%"></th>
                    <th style="width:20%">Supervisor</th>
                    <th style="width:5%"></th>
                    <th style="width:20%">DIC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: auto;;width:20%;border-bottom:solid 1px #000;"></td>
                    <td style="height: auto;width:5%"></td>
                    <td style="height: auto;width:20%;border-bottom:solid 1px #000;"></td>
                    <td style="height: auto;width:5%"></td>
                    <td style="height: auto;width:20%;border-bottom:solid 1px #000;"></td>
                    <td style="height: auto;width:5%"></td>
                    <td style="height: auto;width:20%;border-bottom:solid 1px #000;"></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>