<template>
    <div class="container">
        <div class="row row-cols-2" v-for="(up,index) in bcm" :key="index">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Checker :</label>
                    <input v-model="up.bcm" type="text" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled >
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Supplier</label>
                    <select v-model="up.supplier" name="customer" class="col-12 form-control" disabled>
                        <option :value="up.supplier">{{up.supplier}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor PO</label>
                    <input v-model="up.nomor_po"  type="text" class="form-control" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea  v-model="up.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
            </div>
        </div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <div class="row mt-1 mx-auto col-12" >
                <Circle5 id="load3" v-if="load"></Circle5>
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Sisa PO</th>
                            <th>Qty SJ</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(list,indexlist) in listbcm" :key="indexlist">
                            <td style="text-align:center">{{indexlist+1}}</td>
                            <td style="text-align:center">{{list.kode_barang}}</td>
                            <td>{{list.nama_barang}}</td>
                            <td style="text-align:center">{{list.satuan}}</td>
                            <td style="text-align:center">{{list.masuk+list.sisapo}}</td>
                            <td  style="text-align:center">
                                <input v-model="list.qty" type="number" class="form-control" disabled>
                            </td>
                            <td style="text-align:center">
                                <textarea v-model="hitung.keterangan[indexlist]" class="form-control" disabled></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(bc,index) in bcm" :key="index">
                <button v-if="bc.status==='sent'" @click="confirm(bc)" class="btn-success btn ml-4" >
                    Terima Permintaan
                </button>
                <button v-if="bc.status==='sent'" @click="tolak(bc)" class="btn-orange btn ml-1" >
                    Tolak Permintaan
                </button>
        </div>
        <div class="modal fade" id="modal-form" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Penolakan SO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="update.alastolak" class="form-control"></textarea>
                    </div>               
                </div>
                <div  class="modal-footer" v-for="(bc,index) in bcm" :key="index">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="tolakSo(bc)" class="btn btn-primary">Konfirmasi Tolak</button>
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
        return {
            load:false,
            listbcm:{},
            hitung:{
                qty:[],
                keterangan:[],
            },
            bcm:{},
            update:{
                alastolak:'',
            }
        }
    },
    created(){
        this.getBcm();
        this.getListBcm();
    },  
    computed:{  

    },
    methods:{
        getBcm(){
            axios.get("/api/bcm/"+this.$route.params.nomor)
            .then(res=>{this.bcm=res.data.data
                this.uploood.keterangan=this.bcm[0].keterangan;
            });     
        },
        getListBcm(){
                axios.get("/api/listbcm/"+this.$route.params.nomor)
                .then(res=>{
                    this.listbcm=res.data.data;
                    for(let i=0;i<this.listbcm.length;i++){
                        this.hitung.qty[i]=this.listbcm[i].masuk; 
                        this.hitung.keterangan[i]=this.listbcm[i].keterangan; 
                    }
                });  
        },
        now(){
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" + (day<10 ? '0' : '') + day;
            return output
            },
        tolak(bs){
            $("#modal-form").modal("show");
        },
        tolakSo(bc){
            let tanya=confirm("Apakah anda yakin?");
            if(tanya===true){
                if(this.update.alastolak===""){
                console.log("jangan kosong");
                }else{
                    this.update.status="open";
                    axios.put("/api/bcm/"+this.$route.params.nomor,this.update)
                    .then(res=>{
                        axios.get("/api/listbcm/"+this.$route.params.nomor)
                        .then(res=>{
                            this.listbcm=res.data.data;
                            for(let i=0;i<this.listbcm.length;i++){
                                this.defaultqty={qty:this.listbcm[i].qty+this.listbcm[i].sisapo}
                                axios.put("/api/listbcm/"+this.listbcm[i].id,this.defaultqty)
                                .then(res=>{
                                    $("#modal-form").modal("hide");
                                    this.$router.push({name:'rbcm'})
                                });     
                            }
                        });
                    });
                }  
            }
        },
        confirm(bc){
        let tanya=confirm("Yakin ingin menerima barang masuk ini?");
        if(tanya===true){
            this.update={status:"open"};
            axios.put("/api/bcm/"+bc.bcm,this.update)
            .then(res=>{
                this.$router.push({name:'rbcm'})
            })
        }
        }
    },
} 
</script>

<style>

</style>