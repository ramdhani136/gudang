<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    @php 
    function format_uang($angka){ 
    $hasil =  number_format($angka,0, ',' , '.'); 
    return $hasil; 
    } @endphp

    <div class="container-fluid" style="font-size: 7pt; color:black;display:inline-block;">
    <div id="baris" class="flex">
    @php $i=1 @endphp
    @foreach($so as $s)
        <div class="part">
            <div class="form-group">
                <b>Nomor SO</b> : {{$s->nomor_so}}
            </div>
            <div class="form-group">
                <b>Tanggal</b> : {{$s->tanggal_so}}
            </div>
            <div class="form-group">
                <b>Rencana Kirim</b> : {{$s->tanggal_kirim}}
            </div>
        </div>  
        <div class="part">
            <div class="form-group">
                <b>Customer</b> : {{$s->rso->customer->nama}}
            </div>
            <div class="form-group">
                <b>Nomor RSO</b> : {{$s->nomor_rso}}
            </div>
            <div class="form-group">
                <b>Distibusi</b> : {{$s->distribusi}}
            </div>
        </div>
        <div class="part">  
            <div class="form-group">
                <b>Lokasi</b> : {{$s->lokasi}}
            </div>
            <div class="form-group">
                <b>Alamat</b> : {{$s->alamat}}
            </div>
        </div>
    </div>    
    @endforeach
    <div id="baris">  
        <table class='table mt-1 table-bordered' style="border:solid 1px #000;">
            <thead>
                <tr style="text-align:center" style="font-size:7pt;">
                    <th>NO</th>
                    <th>KODE ITEM</th>
                    <th>ITEM</th>
                    <th>JUMLAH</th>
                    <th>SATUAN</th>
                    <th>HARGA</th> 
                    <th>SUB TOTAL</th>
                </tr>
            </thead>
            <tbody>
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
    <div id="baris">
        <div class="col-12">
            <div class="form-group">
                <label>Keterangan</label> 
            </div>
        </div>
    </div>
    </div>
</body>
</html>