<template>
    <div class="container" @keyup.esc="resetForm()">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor PO</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Alasan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(ln,index) in Filteredlist" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td>
                    <router-link :to="{name:'poCreateView',params:{nomor:ln.nomor_po}}" class="btn btn-primary" >
                        {{ln.nomor_po}}
                    </router-link>
                </td>
                <td>{{ln.tanggal_po}}</td>
                <td style="text-align:center">{{ln.supplier}}</td>
                <td>
                    <textarea v-model="ln.alasselesai" class="form-control" disabled></textarea>
                </td>
                <td style="text-align:center">
                    <button @click="terima(ln)" class="btn btn-success">Terima</button>
                    <button @click="tolak(ln)" class="btn btn-danger">Tolak</button>
                </td>
            </tr>
        </tbody>
    </table>
    <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <label>Alasan Penolakan</label>
                        <textarea v-model="ket.tolak" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimTolak()" class="btn btn-primary">Kirim Penolakan</button>
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
            list:[],
            form:{
                kode:'',
                nama:'',
                status:'Aktif'
            },
            errors:[],
            edit:false,
            target:'',
            load:true,
            up:{},
            ket:{}
        }
    },
    created(){
        this.getlist()
    },
    computed:{
        Filteredlist(){
            return this.list.filter(elem => {
            return elem.nomor_po.toLowerCase().includes(this.search.toLowerCase());
            });
        }
    },
    methods:{
        getlist(){
            axios.get("/api/po/selesai/view")
            .then(res=>{
                this.list=res.data.data
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
                    this.getlist();
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
        terima(ln){
            let tanya=confirm("Yakin menerima permintaan ini?");
            if(tanya===true){
                axios.put("/api/po/"+ln.nomor_po,{status:'Di Selesaikan'})
                .then(res=>{
                    this.getlist();
                })
            }
        },
        tolak(ln){
            this.ket.id=ln.nomor_po;
            $("#modal-tolak").modal("show");
        },
        kirimTolak(){
            let tanya=confirm("Yakin ingin menolak permintaan ini?");
            if(tanya===true){
                this.up.alastolakrequest=this.ket.tolak;
                this.up.rs="T";
                axios.put("/api/po/"+this.ket.id,this.up)
                .then(res=>{
                    this.getlist();
                    $("#modal-tolak").modal("hide");
                })
            }
        }
    }
}
</script>


<style>
    
</style>