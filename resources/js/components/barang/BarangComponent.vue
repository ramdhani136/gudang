<template>
    <div>
        <button class="btn btn-success my-3">+ Tambah Barang</button>
        <div class="form-group col-3 my-3 float-right">
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <table  class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Awal</th>
                <th>Booking</th>
                <th>Tersedia</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(br , index) in barang" :key="br">
                <td>{{index+1}}</td>
                <td>{{br.nama}}</td>
                <td>1000</td>
                <td>200</td>
                <td>800</td>
                <td>{{br.satuan}}</td>
                <td>
                    <button class="btn btn-primary">Edit</button>
                    <button class="btn btn-danger">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
</template>

<script>
export default {
    data(){
        return{
            barang:{}
        }
    },
    created(){
        this.getBarang()
    },
    methods:{
        getBarang(){
            axios.get("/api/barang")
            .then(res=>this.barang=res.data.data)
        }
    }
}
</script>


<style>

</style>