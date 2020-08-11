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
                    <textarea v-if="up.status==='open' || up.status==='tolak' || up.status==='close' || up.status==='sent'" v-model="up.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
                    <textarea v-if="up.status==='draft' || up.status==='tolak'" v-model="up.keterangan" name="keterangan" class="form-control col-12"></textarea>
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
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{list.kode_barang}}</td>
                            <td>{{list.nama_barang}}</td>
                            <td style="text-align:center">{{list.satuan}}</td>
                            <td style="text-align:center">{{list.masuk+list.sisapo}}</td>
                            <td  style="text-align:center" v-for="(bc,index) in bcm" :key="index">
                                <input v-if="bc.status==='sent' || bc.status==='open' || bc.status==='close'"    v-model="list.qty" type="number" class="form-control" disabled>
                                <input v-if="bc.status==='draft' || bc.status==='tolak'"  @input="validqty(indexlist)"  v-model="hitung.qty[indexlist]" type="number" class="form-control">
                            </td>
                            <td style="text-align:center" v-for="(bc,index) in bcm" :key="index">
                                <textarea v-if="bc.status==='sent' || bc.status==='open' || bc.status==='close'" v-model="list.keterangan" class="form-control" disabled></textarea>
                                <textarea  v-if="bc.status==='draft' || bc.status==='tolak'" v-model="list.keterangan" class="form-control"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(bc,index) in bcm" :key="index">
                <button v-if="bc.status==='draft'" @click="draftBcm()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button>
                <button v-if="jenbutton && bc.status==='draft' || bc.status==='tolak'" @click="submitBCM()" class="btn-success btn ml-2" >
                    Kirim Warehouse
                </button>
                <button v-if="!jenbutton && bc.status==='draft' || bc.status==='tolak'" @click="requestBcm()" class="btn-primary btn ml-2" >
                    Request Acc
                </button>
        </div>
        <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form PO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
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
            up:{
                bcm:this.$route.params.nomor,
                tanggal:this.now(),
            },
            poaktif:{},
            ket:{},
            aktif:{},
            listsisa:{},
            checker:[],
            listbcm:{},
            uploadlist:[],
            hitung:{
                qty:[],
                keterangan:[],
            },
            uploood:{},
            aksicek:'benar',
            jenbutton:true,
            cek:{},
            uplistsisa:{
                qty_masuk:0
            },
            uplist:{},
            sipo:0,
            uppo:{},
            bcm:{},
            listbcm:{},
            list:{
                qty:[],
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
            });     
        },
        getListBcm(){
                axios.get("/api/listbcm/"+this.$route.params.nomor)
                .then(res=>{
                    this.listbcm=res.data.data;
                    for(let i=0;i<this.listbcm.length;i++){
                        this.hitung.qty[i]=this.listbcm[i].masuk 
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
        validate(){
            if(this.so.tanggal_so < this.now()){
                this.so.tanggal_so=this.now();
            }
        },
        tglKirim(){
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate()+2;

            var output = d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" + (day<10 ? '0' : '') + day;
            return output
            },
        bcm_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "CM-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        validqty(indexlist){
                this.pembanding="";
                this.aksicek="";
                axios.get("/api/listbcm/"+this.$route.params.nomor)
                .then(res=>{
                    this.listbcm=res.data.data;
                    for(let i=0;i<this.listbcm.length;i++){
                        if(this.hitung.qty[i]>this.listbcm[i].masuk-this.listbcm[i].sisapo){
                            this.cek[i]="error"
                        }else{
                            this.cek[i]="tidak"
                        }
                        for(let i=0;i<this.listbcm.length;i++){
                            this.aksicek+=this.cek[i];
                            this.pembanding+="tidak";
                        }
                        if(this.aksicek===this.pembanding){
                            this.jenbutton=true;
                        }else{
                            this.jenbutton=false;
                        } 
                    }
                }); 
        }
    },
} 
</script>

<style>

</style>