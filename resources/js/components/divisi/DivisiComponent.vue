<template>
    <div class="container" >
        <button class="btn btn-success my-3">+ Tambah Divisi</button>
        <div class="form-group col-3 my-3 float-right">
            <input  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(dv , index) in divisi" :key="dv">
                <td>{{index+1}}</td>
                <td>{{dv.nama}}</td>
                <td>{{dv.keterangan}}</td>
                <td>
                    <button class="btn btn-primary">Edit</button>
                    <button class="btn btn-danger">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            divisi:{}
        }
    },
    created(){
        this.getDivisi()
    },
    methods:{
        getDivisi(){
            axios.get("/api/divisi")
            .then(res=>this.divisi=res.data.data)
        }
    }
}
</script>


<style>
    #overflow{
        width: 100%;
    height: 440px;
    overflow-y: scroll;
    }

    #thead thead tr th{
        text-align: center;
        border-bottom: none;
        position: sticky; top: 0; 
        background-color: #fff;
        top: -1px;
        border-collapse: collapse;
        box-shadow: inset 0 0 0 #dee2e6,
        inset 0 -1px 0 #dee2e6;
    }


    
</style>