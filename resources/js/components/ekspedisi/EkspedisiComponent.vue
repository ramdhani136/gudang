<template>
    <div class="container" @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Ekspedisi</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(eks,index) in filterekspedisi" :key="eks.id">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{eks.nama}}</td>
                <td>{{eks.alamat}}</td>
                <td style="text-align:center">
                    <button @click="updateEkspedisi(eks)" class="btn btn-primary">Edit</button>
                    <button @click="deleteEkspedisi(eks)" class="btn btn-danger">Hapus</button>
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
                    <div class="form-group">
                        <label>Nama Ekspedisi</label>
                        <input v-model="form.nama" type="text"  autocomplete="off" class="form-control">
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
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="createEkspedisi()" class="btn btn-primary">Save changes</button>
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
            form:{},
            errors:[],
            edit:false,
            target:'',
            load:true,
            ekspedisi:[]
        }
    },
    created(){
        this.getEkspedisi()
    },
    computed:{
        filterekspedisi(){
            return this.ekspedisi.filter(elem=>{
            return elem.nama.toLowerCase().includes(this.search.toLowerCase()); 
            });
        }
    },
    methods:{
        getEkspedisi(){
            axios.get("/api/ekspedisi")
            .then(res=>{this.ekspedisi=res.data.data
                this.load=false;
            });
        },
        showmodal(){
            $("#modal-form").modal("show");
        },
        createEkspedisi(){
            if(this.edit===false){
                axios.post("/api/ekspedisi",this.form)
                .then((response)=>{
                    this.getEkspedisi();
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
                axios.put("/api/ekspedisi/"+  this.target,this.form)
                .then((response)=>{
                    this.getEkspedisi();
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
        updateEkspedisi(eks){
            this.getEkspedisi();
            this.form.nama=eks.nama;
            this.form.alamat=eks.alamat;
            this.form.kontak=eks.kontak;
            this.target=eks.id;
            this.form.keterangan=eks.keterangan; 
            this.edit=true;
            this.showmodal();
        },
        deleteEkspedisi(eks){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/ekspedisi/" + eks.id)
                .then(response=>{
                    this.getEkspedisi();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.form.nama="";
            this.form.alamat="";
            this.form.kontak="";
            this.target="";
            this.form.keterangan=""; 
            this.edit=false;
        },
    }
}
</script>


<style>

</style>