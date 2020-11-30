<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <title>{{$bbk[0]->bbk}}</title>
</head>
<body>
    @php 
    function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);
	
	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }


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
    @foreach($bbk as $s)
    <table style="font-size: 9pt;width:100%">
        <tbody>
            <tr>
                <td style="width:25%;"><b>Nomor BBK</b> : {{$s->bbk}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Nomor SO</b> : {{$s->bck->nomor_so}}</td>
                <td style="width:35%;margin-left: 5%;"><b>Lokasi</b> : {{$s->bck->so->lokasi}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Tanggal</b> : {{tgl_indo($s->tanggal)}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Customer</b> : {{$s->bck->so->rso->customer->nama}}</td>
                <td style="width:35%;margin-left: 5%;"> <b>Alamat</b> : {{$s->bck->so->alamat}}</td>
            </tr>
            <tr>
                <td style="width:25%;"><b>Nomor BCK</b> : {{$s->nomor_bck}}</td>
                <td style="width:30%;margin-left: 5%;"><b>Distibusi</b> : {{$s->bck->so->distribusi}}</td>
                <td style="width:35%;margin-left: 5%;"><b>Keterangan</b> : {{$s->keterangan}}</td>
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
                    <th style="border:solid 1px #000">HARGA</th>
                    <th style="border:solid 1px #000">DISKON</th>
                    <th style="border:solid 1px #000">JUMLAH</th>
                    <th style="border:solid 1px #000">SATUAN</th> 
                    <th style="border:solid 1px #000">SUB TOTAL</th>
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
                    <td style="text-align:center;border:solid 1px #000">Rp.{{format_uang($p->harga)}}</td>
                    <td style="text-align:center;border:solid 1px #000">Rp.{{format_uang($p->diskon)}}</td>
                    <td style="text-align:center;border:solid 1px #000">{{format_uang($p->qty)}}</td> 
                    <td style="text-align:center;border:solid 1px #000">{{$p->barang->satuan}}</td>      
                    <td style="text-align:center;border:solid 1px #000">Rp. {{format_uang($p->qty*($p->harga-$p->diskon))}}</td>
                </tr>  
                <!-- {{$total+=$p->qty*($p->harga-$p->diskon)}}; -->
                @endforeach
            </tbody>
        </table>
        <table  style="width:100.3%;font-size:10pt">
            <thead>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:13.2%;"></th>
                    <th style="width:14%;border:solid 1px #000;">Grand Total :</th> 
                    <th style="width:14%;border:solid 1px #000;background-color:lightgrey">@php echo "Rp. ",format_uang($total) @endphp</th>
            </thead>
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