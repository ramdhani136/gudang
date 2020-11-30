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
    $hasil =  number_format($angka,2, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="wrapper" style="width: 100%;margin:0;padding:0">
    <div style="font-size:12pt;text-align:center;margin-bottom:20px;margin-top:-10px">
        <b >RSO TERSEDIA</b>
    </div>
    <div class="row">
    @php $i=1 @endphp
    @foreach($rso as $s)
    <table style="font-size: 10pt;width:100%">
        <tbody>
            <tr>
                <td style="width:35%;"><b>Nomor RSO</b> : {{$s->nomor_rso}}</td>
                <td style="width:40%;margin-left: 5%;"><b>Customer</b> : {{$s->customer->nama}}</td>
                <td style="width:25%;margin-left: 5%;"><b>Sales</b> : {{$s->user->name}}</td>
            </tr>
            <tr>
                <td style="width:35%;"><b>Tanggal</b> : {{tgl_indo($s->tanggal_rso)}}</td>
                <td style="width:40%;margin-left: 5%;"><b>Group</b> : {{$s->groupso->area}}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
    <div class="row" style="height: 220px;">  
        <table cellspacing="0" class="table" style="font-size: 10pt; width:100% ;margin-top:20px">
            <thead style="border:solid 1px #000">
                <tr>
                    <th style="text-align:center;height: 25px;width:5%">No</th>
                    <th style="text-align:left;width:15%">Kode Barang</th>
                    <th style="text-align:left;width:50%">Nama Barang</th>
                    <th style="text-align:left;width:20%">Jumlah</th>
                    <th style="text-align:left;width:10%">Satuan</th>
                </tr>
            </thead>
            <tbody  style="border-bottom:solid 1px #000">
                @php $i=1;
                $total=0; @endphp
                @foreach($list as $p)
                <tr>
                    <td style="text-align:center;padding-top:3px;padding-bottom:3px">{{ $i++ }}</td>
                    <td style="text-align:left;">{{$p->kode_barang}}</td>
                    <td style="text-align:left;">{{$p->barang->nama}}</td>
                    <td style="text-align:left;">{{format_uang($p->qty)}}</td>
                    <td style="text-align:left;">{{$p->barang->satuan}}</td>
                </tr>  
                {{$total+=$p->qty*($p->harga-$p->diskon)}};
                @endforeach
            </tbody>
        </table>
        <!-- <table style="width:100.3%;font-size:10pt">
            <thead>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:14.2%;"></th>
                    <th style="width:15%;border:solid 1px #000;">Grand Total :</th> 
                    <th style="width:15%;border:solid 1px #000;background-color:lightgrey">@php echo "Rp. ",format_uang($total) @endphp</th>
            </thead>
        </table> -->
    </div> 
    <div class="row" style="height: 50px;font-size:10pt">
    @foreach($rso as $s)
        <b>Catatan :  {{$s->keterangan}}</b>
    @endforeach    
    </div>
    <div class="row">
    <table  cellspacing="0" class="ttd" style="width: 60%;margin-top: 10px;text-align: center;font-size:9pt;">
            <thead>
                <tr >
                    <th style="width:20%">Sales</th>
                    <th style="width:5%"></th>
                    <th style="width:20%">DIC</th>
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
                </tr>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>