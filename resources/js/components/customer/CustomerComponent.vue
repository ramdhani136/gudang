<template>
    <div class="container" >
        <button class="btn btn-success my-3">+ Tambah Customer</button>
        <div class="form-group col-3 my-3 float-right">
            <input  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(cs , index) in customer" :key="cs">
                <td>{{index+1}}</td>
                <td>{{cs.kode}}</td>
                <td>{{cs.nama}}</td>
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
            customer:{}
        }
    },
    created(){
        this.getCustomer()
    },
    methods:{
        getCustomer(){
            axios.get("/api/customer")
            .then(res=>this.customer=res.data.data)
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