<template>
    <div class="container"  @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Sales</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(br , index) in filteredMembers" :key="br.kode">
                <td style="text-align:center">{{index+1}}</td>
                <td style="text-align:center">{{br.nip}}</td>
                <td>{{br.nama}}</td>
                <td style="text-align:center">{{br.status}}</td>
                <td style="text-align:center">
                    <button @click="updateSales(br)" class="btn btn-primary">Edit</button>
                    <button @click="deleteSales(br)" class="btn btn-danger">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
    <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="errors.length > 0 " class="alert alert-danger">
                        <ul>
                            <li v-for="(error , index) in errors" :key="index">
                                {{error}}
                            </li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label>NIP</label>
                        <input v-model="form.nip" type="text" name="form.kode" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.nama" type="text" name="form.nama"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="form.status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Non Aktif">Non Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="createSales()" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {Circle5} from 'vue-loading-spinner'
export default {
    components: {
        Circle5
    },
    data(){
        return{
            search  : '',
            sales:[],
            form:{
                nip:'',
                nama:'',
                status:''
            },
            errors:[],
            edit:false,
            target:'',
            load:true,
        }
    },
    created(){
        this.getSales()
    },
    computed:{
        filteredMembers(){
            return this.sales.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search); 
            });
        }
    },
    methods:{
        getSales(){
            axios.get("/api/sales")
            .then(res=>{this.sales=res.data.data
            this.load=false;
            });
        },
        showmodal(){
            $("#modal-form").modal("show");
        },
        createSales(){
            if(this.edit===false){
                axios.post("/api/sales",this.form)
                .then((response)=>{
                    this.getSales();
                    this.$router.push({name:'sales'})
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })  
            }else{
                axios.put("/api/sales/"+  this.target,this.form)
                .then((response)=>{
                    this.getSales();
                    this.$router.push({name:'sales'})
                    $("#modal-form").modal("hide");
                    this.edit=false
                    this.resetForm()
                })
            }
        },
        updateSales(sales){
            this.target=sales.nip
            this.form.nip=sales.nip
            this.form.nama=sales.nama
            this.form.status=sales.status
            this.edit=true
            this.showmodal();
        },
        deleteSales(sales){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/sales/" + sales.nip)
                .then(response=>{
                    this.getSales();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.edit=false
            this.form.nip=""
            this.form.nama=""
            this.form.status="" 

        },
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

    #modal-width{
    width: 120%;
    height: auto;
    right: 13%;
    }
    
</style>