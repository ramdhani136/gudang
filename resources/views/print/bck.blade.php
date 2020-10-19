<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$bck[0]->bck}}</title>
</head>
<body>
    <h1 style="text-align:center;font-size:14pt;margin-top:-20px">BUKTI CHECKER KELUAR</h1>
    <div style="width:100%;height:auto;">
        @foreach($bck as $b)
        <table style="width:100%;height:auto; font-size:9pt;margin-top:5px">
            <tbody>
                <tr style="width: 25%;">
                    <td><b>Nomor Checker : </b> {{$b->bck}}</td>
                    <td><b>Nomor So : </b>{{$b->nomor_so}}</td>
                    <td><b>Distribusi : </b>{{$b->so->distribusi}}</td>
                </tr>
                <tr style="width: 25%;">
                    <td><b>Tanggal : </b>{{ $b->tanggal}}</td>
                    <td><b>Customer : </b>{{$b->so->rso->customer->nama}}</td>
                    <td><b>Lokasi : </b>{{$b->so->lokasi}}</td>
                </tr>
                <tr style="width: 50%;">
                    <td><b>Nopol : </b>{{$b->kendaraan->nopol}}</td>
                    <td><b>Sales : </b>{{$b->so->user->name}}</td>
                    <td><b>Alamat : </b>{{$b->so->alamat}}</td>
                </tr> 
            </tbody>
        </table>
        @endforeach
        <div style="width:100%;height:auto;margin-top:10px;">
            <table cellspacing="0" style="width:100%;height:auto;font-size:9pt;">
                <thead>
                    <tr>
                        <th style="border:solid 1px #000;height:20px">NO</th>
                        <th style="border:solid 1px #000;">KODE</th>
                        <th style="border:solid 1px #000;">ITEM</th>   
                        <th style="border:solid 1px #000;">SO</th>
                        <th style="border:solid 1px #000;">REAL</th>
                        <th style="border:solid 1px #000;">SATUAN</th>
                        <th style="border:solid 1px #000;">KETERANGAN HASIL CHECKER</th>
                        <!-- <th style="border:solid 1px #000;">STOCKER</th> -->
                    </tr>
                </thead>
                <tbody>
                @php $i=1;@endphp
                @foreach($list as $p)
                    <tr style="border:solid 1px #000;">
                        <td style="width: 3%;text-align:center;border:solid 1px #000;height:60px">{{$i++}}</td>
                        <td style="width: 7%;border:solid 1px #000;">{{$p->kode_barang}}</td>
                        <td style="width: 10%;border:solid 1px #000;">{{$p->barang->nama}}</td>
                        <td style="width: 5%;text-align:center;border:solid 1px #000;">{{$p->qty}}</td>
                        <td style="width: 5%;text-align:center;border:solid 1px #000;"></td>
                        <td style="width: 5%;text-align:center;border:solid 1px #000;">{{$p->barang->satuan}}</td>
                        <td style="width: 40%;border:solid 1px #000;"></td>
                        <!-- <td style="width: 5%;border:solid 1px #000;"></td> -->
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <table cellspacing="0" style="width:100%;">
        <thead>
            <tr>
                <th style="width:100%;border:solid 1px #000;background-color:lightslategray;font-size:9pt;height:20px"> Catatan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 99%;height:40px;border:solid 1px #000;font-size:9pt;padding-left:1%">
                    {{$bck[0]->keterangan}}
                </td>
            </tr>
        </tbody>
        </table>   
        <table cellspacing="0" style="font-size: 9pt;width:50%;margin-top:10px">
            <thead>
                <tr>
                    <th style="border:solid 1px #000;height:20px">Admin</th>
                    <th style="border:solid 1px #000;">Kabag. Gudang</th>
                    <th style="border:solid 1px #000;">Checker</th>
                    <th style="border:solid 1px #000;">Supir</th>
                    <th style="border:solid 1px #000;">Facturing</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:solid 1px #000;width:20%;height:80px"></td>
                    <td style="border:solid 1px #000;width:20%"></td>
                    <td style="border:solid 1px #000;width:20%"></td>
                    <td style="border:solid 1px #000;width:20%"></td>
                    <td style="border:solid 1px #000;width:20%"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>