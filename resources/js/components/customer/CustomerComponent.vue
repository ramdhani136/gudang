<template>
    <div class="container" @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Customer</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
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
            <tr v-for="(cs , index) in FilteredCustomer" :key="cs.kode">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{cs.kode}}</td>
                <td>{{cs.nama}}</td>
                <td style="text-align:center">
                    <button @click="updateCustomer(cs)" class="btn btn-primary">Edit</button>
                    <button @click="deleteCustomer(cs)" class="btn btn-danger">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
    <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Customer</h5>
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
                        <label>Kode Customer</label>
                        <input v-model="form.kode" type="text" name="kode" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.nama" type="text" name="nama"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea v-model="form.alamat" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. Kontak</label>
                        <input v-model="form.kontak" type="text" name="number"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="form.keterangan" class="form-control"></textarea>
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
                    <button type="button" @click="createCustomer()" class="btn btn-primary">Save changes</button>
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
            customer:[],
            form:{
                kode:'',
                nama:'',
                status:'Aktif'
            },
            errors:[],
            edit:false,
            target:'',
            load:true,
        }
    },
    created(){
        this.getCustomer()
    },
    computed:{
        FilteredCustomer(){
            return this.customer.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getCustomer(){
            axios.get("/api/customer")
            .then(res=>{this.customer=res.data.data
                this.load=false;
            });
        },
        showmodal(){
            this.errors=[];
            $("#modal-form").modal("show");
        },
        createCustomer(){
            if(this.edit===false){
                axios.post("/api/customer",this.form)
                .then((response)=>{
                    this.getCustomer();
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })
                .catch(error=>{
                    this.errors=[];
                    if(error.response.data.errors.nama){
                        this.errors.push(error.response.data.errors.nama[0])
                    }
                    if(error.response.data.errors.kode){
                        this.errors.push(error.response.data.errors.kode[0])
                    }
            })
            }else{
                axios.put("/api/customer/"+  this.target,this.form)
                .then((response)=>{
                    this.getCustomer();
                    $("#modal-form").modal("hide");
                    this.resetForm()
                })
                .catch(error=>{
                    this.errors=[];
                    if(error.response.data.errors.nama){
                        this.errors.push(error.response.data.errors.nama[0])
                    }
                    if(error.response.data.errors.kode){
                        this.errors.push(error.response.data.errors.kode[0])
                    }
            })
            }
        },
        updateCustomer(customer){
            this.form.nama=customer.nama
            this.form.kode=customer.kode
            this.form.alamat=customer.alamat
            this.form.kontak=customer.kontak
            this.target=customer.kode
            this.form.status=customer.status
            this.form.keterangan=customer.keterangan 
            this.edit=true;
            this.showmodal();
        },
        deleteCustomer(customer){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/customer/" + customer.kode)
                .then(response=>{
                    this.getCustomer();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.form.nama="";
            this.form.kode="";
            this.form.alamat="";
            this.form.kontak="";
            this.form.status="Aktif";
            this.target="";
            this.form.keterangan=""; 
            this.edit=false;
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