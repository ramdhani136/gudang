<template>
    <div class="container" @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Kendaraan</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Merk</th>
                <th>Plat.No</th>
                <th>Jenis</th>
                <th>Kubikasi (M3)</th>
                <th>Tonase (KG)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(kd,index) in filterkendaraan" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{kd.nama}}</td>
                <td style="text-align:center">{{kd.nopol}}</td>
                <td style="text-align:center">{{kd.jenis}}</td>
                <td style="text-align:center">{{kd.kubikasi}}</td>
                <td style="text-align:center">{{kd.tonase}}</td>
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
                        <label>Merk</label>
                        <input v-model="form.nama" type="text"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Plat.No</label>
                        <textarea v-model="form.nopol" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select v-model="form.id_jenis" class="form-control">
                            <option v-for="(jn,index) in jeniskendaraan" :key="index" :value="jn.id">{{jn.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kubikasi Muatan (M3)</label>
                        <input type="number" class="form-control" v-model="form.kubikasi">
                    </div>
                    <div class="form-group">
                        <label>Tonase Muatan (Kg)</label>
                        <input type="number" class="form-control" v-model="form.tonase">
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
            kendaraan:[],
            jeniskendaraan:{}
        }
    },
    created(){
        this.getKendaraan();
        this.getJenis();
    },
    computed:{
        filterkendaraan(){
            return this.kendaraan.filter(elem=>{
            return elem.nopol.toLowerCase().includes(this.search.toLowerCase()); 
            });
        }
    },
    methods:{
        getKendaraan(){
            axios.get("/api/kendaraan")
            .then(res=>{this.kendaraan=res.data.data
                this.load=false;
            });
        },
        getJenis(){
            axios.get("/api/jeniskendaraan")
            .then(res=>{this.jeniskendaraan=res.data.data
            }); 
        },
        showmodal(){
            $("#modal-form").modal("show");
        },
        createKendaraan(){
            if(this.edit===false){
                axios.post("/api/kendaraan",this.form)
                .then((response)=>{
                    this.getKendaraan();
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })

            }else{
                axios.put("/api/kendaraan/"+  this.target,this.form)
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
            this.form.nopol=kd.nopol;
            this.form.id_jenis=kd.id_jenis;
            this.form.kubikasi=kd.kubikasi; 
            this.form.tonase=kd.tonase; 
            this.target=kd.id;
            this.edit=true;
            this.showmodal();
        },
        deleteKendaraan(kd){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/kendaraan/" + kd.id)
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