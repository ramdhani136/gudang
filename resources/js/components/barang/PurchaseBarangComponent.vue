<template>
    <div class="container"  @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Request Barang</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 float-right">
            <select name="status" v-model="status" class="form-control">
                <option value="N">Menunggu Konfirmasi</option>
                <option value="Y">Barang Aktif</option>
            </select>
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(br , index) in filteredMembers" :key="br.kode">
                <td style="text-align:center">{{index+1}}</td>
                <td style="text-align:center">{{br.kode}}</td>
                <td>{{br.nama}}</td>
                <td style="text-align:center">{{br.satuan}}</td>
                <td style="text-align:center">
                    <button @click="updateBarang(br)" class="btn btn-primary">Lihat</button>
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
                        <label>Kode Barang</label>
                        <input v-model="form.kode" type="text" name="form.kode" autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.nama" type="text" name="form.nama"  autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input v-model="form.harga" type="number"  autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Stok</label>
                        <input v-model="form.qty" type="number" name="form.qty" autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <select v-model="form.satuan" name="satuan" id="" class="form-control" :disabled="disabled == 1">
                            <option value="PCS">PCS</option>
                            <option value="ROLL">ROLL</option>
                            <option value="LBR">LBR</option>
                            <option value="KG">KG</option>
                            <option value="MTR">MTR</option>
                        </select>
                    <div class="form-group">
                        <label>Minimal Stok</label>
                        <input v-model="form.min_stok" type="number"  autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Maksimal Stok</label>
                        <input v-model="form.max_stok" type="number"  autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Kubikasi (M3)</label>
                        <input v-model="form.kubikasi" type="number"  autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                    <div class="form-group">
                        <label>Tonase (KG)</label>
                        <input v-model="form.kg" type="number"  autocomplete="off" class="form-control" :disabled="disabled == 1">
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button v-if="!edit" type="button" @click="createBarang()" class="btn btn-primary">Kirim Permintaan</button>
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
            barang:[],
            form:{
                kode:'',
                nama:'',
                satuan:'',
                qty:'',
            },
            errors:[],
            edit:false,
            load:true,
            disabled:0,
            status:'Y',
        }
    },
    created(){
        this.getBarang()
    },
    computed:{
        filteredMembers(){
            if(this.search===""){
                if(this.status==="N"){
                    return this.barang.filter(elem=> elem.aktif==="N")
                }else if(this.status==="Y"){
                    return this.barang.filter(elem=> elem.aktif==="Y")
                }
            }else{
                return this.barang.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
            }
        }
    },
    methods:{
        getBarang(){
            axios.get("/api/barang/data/all/")
            .then(res=>{this.barang=res.data.data
                this.load=false;  
            });
        },
        showmodal(){
            this.disabled=0;
            this.errors=[];
            $("#modal-form").modal("show");
        },
        createBarang(){
            if(this.edit===false){
                axios.post("/api/barang",this.form)
                .then((response)=>{
                    this.getBarang();
                    this.$router.push({name:'purchasebarang'})
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
                    if(error.response.data.errors.qty){
                        this.errors.push(error.response.data.errors.qty[0])
                    }
                    if(error.response.data.errors.satuan){
                        this.errors.push(error.response.data.errors.satuan[0])
                    }
            })
            }else{
                axios.put("/api/barang/"+  this.form.kode,this.form)
                .then((response)=>{
                    this.$router.push({name:'purchasebarang'})
                    this.getBarang();
                    $("#modal-form").modal("hide");
                    this.edit=false
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
                    if(error.response.data.errors.qty){
                        this.errors.push(error.response.data.errors.qty[0])
                    }
                    if(error.response.data.errors.satuan){
                        this.errors.push(error.response.data.errors.satuan[0])
                    }
            })
            }
        },
        updateBarang(barang){
            this.disabled=1;
            this.form.nama=barang.nama
            this.form.qty=barang.qty
            this.form.satuan=barang.satuan
            this.form.kode=barang.kode
            this.form.harga=barang.harga
            this.form.min_stok=barang.min_stok
            this.form.max_stok=barang.max_stok
            this.form.kubikasi=barang.kubikasi
            this.form.kg=barang.kg
            this.edit=true
            $("#modal-form").modal("show");
        },
        deleteBarang(barang){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/barang/" + barang.kode)
                .then(response=>{
                    this.getBarang();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.edit=false;
            this.form.nama="";
            this.form.kode="";
            this.form.qty="";
            this.form.satuan="";
            this.form.harga="";
            this.form.min_stok="";
            this.form.max_stok="";
            this.form.kubikasi="";
            this.form.kg="";
        },
    }
}
</script>


<style>
    
</style>