<template>
    <div class="container" @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Jenis Kendaraan</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(kd,index) in filterkendaraan" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{kd.nama}}</td>
                <td>{{kd.keterangan}}</td>
                <td style="text-align:center">
                    <button @click="updateKendaraan(kd)" class="btn btn-primary">Edit</button>
                    <button @click="deleteKendaraan(kd)" class="btn btn-danger">Hapus</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Form Kendaraan</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jenis</label>
                        <input v-model="form.nama" type="text"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="form.keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="createKendaraan()" class="btn btn-primary">Save changes</button>
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
            jeniskendaraan:[]
        }
    },
    created(){
        this.getKendaraan();
    },
    computed:{
        filterkendaraan(){
            return this.jeniskendaraan.filter(elem=>{
            return elem.nama.toLowerCase().includes(this.search.toLowerCase()); 
            });
        }
    },
    methods:{
        getKendaraan(){
            axios.get("/api/jeniskendaraan")
            .then(res=>{this.jeniskendaraan=res.data.data
            this.load=false;
            }); 
        },
        showmodal(){
            $("#modal-form").modal("show");
        },
        createKendaraan(){
            if(this.edit===false){
                axios.post("/api/jeniskendaraan",this.form)
                .then((response)=>{
                    this.getKendaraan();
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })

            }else{
                axios.put("/api/jeniskendaraan/"+  this.target,this.form)
                .then((response)=>{
                    this.getKendaraan();
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
        updateKendaraan(kd){
            this.getKendaraan();
            this.form.nama=kd.nama;
            this.form.keterangan=kd.keterangan;
            this.target=kd.id;
            this.edit=true;
            this.showmodal();
        },
        deleteKendaraan(kd){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/jeniskendaraan/" + kd.id)
                .then(response=>{
                    this.getKendaraan();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.form={};
            this.edit=false;
        },
    }
}
</script>


<style>

</style>