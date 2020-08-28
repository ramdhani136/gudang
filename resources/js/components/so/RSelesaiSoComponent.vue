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
                <th>Nomor SO</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Alasan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(ln,index) in Filteredlist" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td>
                    <router-link :to="{name:'formso',params:{id:ln.nomor_so}}" class="btn btn-primary" >
                        {{ln.nomor_so}}
                    </router-link>
                </td>
                <td>{{ln.tanggal_so}}</td>
                <td style="text-align:center">{{ln.customer}}</td>
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
            return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
            });
        }
    },
    methods:{
        getlist(){
            axios.get("/api/so/selesai/view")
            .then(res=>{
                this.list=res.data.data
                this.load=false;
            });
        },
        terima(ln){
            let tanya=confirm("Yakin menerima permintaan ini?");
            if(tanya===true){
                axios.put("/api/so/"+ln.nomor_so,{status:'Di Selesaikan'})
                .then(res=>{
                    this.getlist();
                })
            }
        },
        tolak(ln){
            this.ket.id=ln.nomor_so;
            $("#modal-tolak").modal("show");
        },
        kirimTolak(){
            let tanya=confirm("Yakin ingin menolak permintaan ini?");
            if(tanya===true){
                this.up.alastolakselesai=this.ket.tolak;
                this.up.rs="T";
                axios.put("/api/so/"+this.ket.id,this.up)
                .then(res=>{
                    this.getlist();
                    $("#modal-tolak").modal("hide");
                })
            }
        },
        
    }
}
</script>


<style>
    
</style>