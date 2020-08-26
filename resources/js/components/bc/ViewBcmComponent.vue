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
                    <label>Nomor Kendaraan</label>
                    <input v-if="up.status==='open' || up.status==='tolak' || up.status==='close' || up.status==='sent'" v-model="up.nopol"  type="text" class="form-control" disabled>
                    <input v-if="up.status==='draft' || up.status==='tolak'" v-model="up.nopol"  type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-if="up.status==='open' || up.status==='tolak' || up.status==='close' || up.status==='sent'" v-model="up.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
                    <textarea v-if="up.status==='draft' || up.status==='tolak'" v-model="uploood.keterangan" name="keterangan" class="form-control col-12"></textarea>
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
                            <td style="text-align:center">{{arr[indexlist].qty}}</td>
                            <td  style="text-align:center" v-for="(bc,index) in bcm" :key="index">
                                <input v-if="bc.status==='sent' || bc.status==='open' || bc.status==='close'"    v-model="list.qty" type="number" class="form-control" disabled>
                                <input v-if="bc.status==='draft' || bc.status==='tolak'"  @input="validqty(indexlist)"  v-model="hitung.qty[indexlist]" type="number" class="form-control">
                            </td>
                            <td style="text-align:center" v-for="(bc,index) in bcm" :key="index">
                                <textarea v-if="bc.status==='sent' || bc.status==='open' || bc.status==='close'" v-model="hitung.keterangan[indexlist]" class="form-control" disabled></textarea>
                                <textarea  v-if="bc.status==='draft' || bc.status==='tolak'" v-model="hitung.keterangan[indexlist]" class="form-control"></textarea>
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
                <button v-if="bc.status==='close'" @click="kembali()" class="btn-primary btn ml-3" >
                    Kembali
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
            uploadlist:{
                qty_masuk:0,
            },
            hitung:{
                qty:[],
                keterangan:[],
            },
            uploood:{},
            aksicek:'benar',
            jenbutton:true,
            cek:{},
            uplistbcm:{
                qty:0,
                keterangan:[]
            },
            uplist:{},
            sipo:0,
            uppo:{},
            bcm:{},
            listbcm:{},
            list:{
                qty:[],
            },
            listrso:{},
            totalmasuk:{},
            masuk:{},
            arr:[]
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
                        axios.get("/api/listbcm/data/"+this.$route.params.nomor+"/"+this.listbcm[i].kode_barang)
                        .then(res=>{
                            this.totalmasuk=res.data.data;
                            for(let o=0;o<this.totalmasuk.length;o++){
                                this.masuk={qty:this.totalmasuk[o].sisaporeal}; 
                            }this.arr.push(this.masuk);
                        });  
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
        },
        kembali(){
            this.$router.push({name:'bcmcomponent'});
        },
        draftBcm(){
            /* let jawab=confirm("Simpan Draft?");
            if(jawab===true){
                this.up.status="Draft";
                axios.put("/api/bcm/"+this.$route.params.nomor,this.uploood)
                .then(res=>{
                    axios.get("/api/listbcm/"+this.$route.params.nomor)
                    .then(res=>{
                        this.listbcm=res.data.data;
                        for(let i=0;i<this.listbcm.length;i++){
                            this.uplistbcm={
                                qty:this.hitung.qty[i],
                                keterangan:this.hitung.keterangan[i]
                            };
                            console.log(this.uplistbcm);
                            axios.put("/api/listbcm/"+this.listbcm[i].id,this.uplistbcm)
                            .then(res=>{
                                this.uploadlist={
                                qty_masuk:parseInt((this.listbcm[i].masuk-this.listbcm[i].qty)+this.hitung.qty[i])
                                };
                                axios.get("/api/view/detailpo/"+this.listbcm[i].nomor_po+"/"+this.listbcm[i].kode_barang)
                                .then(res=>{
                                this.listrso=res.data.data;
                                    axios.put("/api/listrso/"+this.listrso[0].id,this.uploadlist)
                                    .then(res=>{
                                        axios.get("/api/listrso/data/listpo/"+this.aktif.nomor_po)
                                        .then(res=>{
                                            this.listsisa=res.data.data;
                                            for(let i=0;i<this.listsisa.length;i++){
                                                if(this.listsisa[i].sisapo<1){
                                                    this.uplist.po_close="Y";
                                                    axios.put("/api/listrso/data/"+this.listsisa[i].nomor_po+"/"+this.listsisa[i].kode_barang,this.uplist)
                                                    .then(res=>{
                                                    });
                                                }
                                            }
                                        })
                                        this.sipo=0;
                                        axios.get("/api/listrso/data/listall/"+this.aktif.nomor_po)
                                        .then(res=>{
                                            this.sisasemua=0;
                                            this.listsisa=res.data.data;
                                            for(let i=0;i<this.listsisa.length;i++){
                                                this.sipo+=parseInt(this.listsisa[i].sisapo);  
                                            }
                                            if(this.sipo>1){
                                                this.uppo.status="Selesai";
                                                axios.put("/api/po/"+this.aktif.nomor_po,this.uppo)
                                                .then(res=>{
                                                    this.$router.push({name:'bcmcomponent'});
                                                });
                                            }else{
                                                this.uppo.status="Acc";
                                                axios.put("/api/po/"+this.aktif.nomor_po,this.uppo)
                                                .then(res=>{
                                                    this.$router.push({name:'bcmcomponent'});
                                                });
                                            }
                                        });
                                    })
                                });
                            });
                        }
                    });
                })
                
            } */
        }
    },
} 
</script>

<style>

</style>