<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Checker :</label>
                    <input v-model="up.bcm" type="text" class="form-control col-12" >
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" >
                </div>
                <div class="form-group">
                    <label>Nomor SO</label>
                    <input v-model="up.nomor_so" @click="showSo()" type="text" class="form-control" placeholder="Pilih Sales Order">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select v-model="ket.customer" name="customer" class="col-12 form-control" disabled>
                        <option :value="ket.customer">{{ket.customer}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Distribusi</label>
                    <input v-model="ket.distribusi" type="text" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input v-model="ket.lokasi" type="text" class="form-control" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea v-model="ket.alamat" name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12"></textarea>
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
                            <th>Sisa SO</th>
                            <th>Qty Tersedia</th>
                            <th>Rencana Kirim</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(listbcm,index) in checker" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{listbcm.lkode_barang}}</td>
                            <td>{{listbcm.nama_barang}}</td>
                            <td style="text-align:center">{{listbcm.satuan}}</td>
                            <td style="text-align:center">{{ket.sisasopilih[index]}}</td>
                            <td style="text-align:center">{{ket.tersedia[index]}}</td>
                            <td  style="text-align:center">
                                <input @input="validqty(index)"  v-model="hitung.qty[index]" type="number" class="form-control">
                            </td>
                            <td style="text-align:center">
                                <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2">
                <!-- <button @click="draftBcm()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button> -->
                <button @click="submitBck()" class="btn-success btn ml-2" >
                    Simpan  
                </button>
                <button @click="batal()" class="btn-orange btn ml-2" >
                    Batal
                </button>
        </div>
        <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form SO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih SO</label>
                        <select @change="pilihSo(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in soaktif" :key="index">{{aktif.nomor_so}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <input v-model="ket.customer" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody">
                    <table class="table mt-2 table-striped table-bordered" style="width:100%">
                        <thead  id="rsthead">
                            <tr>
                                <th style="text-align:center">No</th>
                                <th style="text-align:center">Kode</th>
                                <th style="text-align:center">Item</th>
                                <th style="text-align:center">Satuan</th>
                                <th style="text-align:center">Sisa SO</th>
                                <th style="text-align:center">Qty Tersedia</th>
                                <th style="text-align:center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(ls,index) in listso" :key="index">
                                <td style="text-align:center">{{index+1}}</td>
                                <td>{{ls.lkode_barang}}</td>
                                <td>{{ls.nama_barang}}</td>
                                <td style="text-align:center">{{ls.satuan}}</td>
                                <td style="text-align:center">{{ket.sisaso[index]}}</td>
                                <td style="text-align:center">{{ket.ltersedia[index]}}</td>
                                <td style="text-align:center">
                                    <input @change="pilihlistchecker()" v-model="checker" type="checkbox" :value="ls" :disabled="ket.ltersedia[index]===0">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="resetForm()" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="checklist()" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div>
            <div class="modal fade" id="modal-pr" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="scrollList">
                        <table  id="thead" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Nomor SO</th>
                                    <th>Customer</th>
                                    <th style="text-align:center">Jumlah</th>
                                    <th style="text-align:center">Satuan</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align:center"></td>
                                    <td style="text-align:center"></td>
                                    <td></td>
                                    <td style="text-align:center"></td>
                                    <td style="text-align:center"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                <div class="error-actions">
                        <router-link to="/so"  class="btn btn-primary btn-lg">
                                Lihat Data SO
                        </router-link>
                </div>
                </div>
            </div>
        </div>
        <div v-if="gagal" class="row mt-2">
            <div id="alastolak">
                <div>
                    <b>Belum ada list Request Sales Order Yang di pilih! </b> 
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
                bcm:this.bcm_nomor(),
                tanggal:this.now(),
            },
            ket:{
                sisaso:[],
                sisasopilih:[],
                tersedia:[],
                ltersedia:[]
            },
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
                qty_masuk:0,
            },
            uplist:{},
            sipo:0,
            uppo:{},
            listpo:{},
            persen:0,
            listrso:{},
            coba:[],
            soaktif:{},
            listso:{},
            gagal:false,
        }
    },
    created(){
        this.getSoAktif();
        this.resetForm();
        this.timer();
    },  
    computed:{  

    },
    methods:{
        getSoAktif(){
            axios.get("/api/so/data/aktif")
            .then(res=>{
                this.soaktif=res.data.data;
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

            var output = "BCK-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showSo(){
            $("#modal-po").modal("show");
        },
        resetForm(){
            this.checker=[];
        },
        pilihSo(aktif){
            this.hitung.qty=[];
            this.checker=[];
            this.ket.customer=aktif.customer;
            this.ket.lokasi=aktif.lokasi;
            this.ket.alamat=aktif.alamat;
            this.ket.distribusi=aktif.distribusi;
            this.up.nomor_so=aktif.nomor_so;
            this.ket.status=aktif.statusso;
            this.ket.nomor_rso=aktif.nomor_rso;
            if(aktif.statusso==="tersedia"){
                axios.get("/api/listrso/data/pilihsotersedia/"+aktif.nomor_rso)
                .then(res=>{
                    this.listso=res.data.data;
                    for(let i=0;i<this.listso.length;i++){
                        this.ket.sisaso[i]=parseInt(this.listso[i].qty_tersedia)-parseInt(this.listso[i].keluar_tersedia);
                        this.ket.ltersedia[i]=parseInt(this.listso[i].qty_tersedia)-parseInt(this.listso[i].keluar_tersedia);
                    }
                    
                })
            }else if(aktif.statusso==="tidaktersedia"){
                axios.get("/api/listrso/data/pilihsotidak/"+aktif.nomor_rso)
                .then(res=>{
                    this.listso=res.data.data;
                    for(let i=0;i<this.listso.length;i++){
                        this.ket.sisaso[i]=parseInt(this.listso[i].qty_tdktersedia)-parseInt(this.listso[i].keluar_tdktersedia);
                        this.ket.ltersedia[i]=parseInt(this.listso[i].qty_masuk)-parseInt(this.listso[i].keluar_tdktersedia);
                    }
                    
                })
            }

        },
        checklist(){ 
            $("#modal-po").modal("hide");
        },
        pilihlistchecker(){
            for(let i=0;i<this.checker.length;i++){
                if(this.aktif.statusso==="tersedia"){
                    this.ket.sisasopilih[i]=parseInt(this.checker[i].qty_tersedia)-parseInt(this.checker[i].keluar_tersedia);
                    this.ket.tersedia[i]=parseInt(this.checker[i].qty_tersedia)-parseInt(this.checker[i].keluar_tersedia);
                }else{
                    this.ket.sisasopilih[i]=parseInt(this.checker[i].qty_tdktersedia)-parseInt(this.checker[i].keluar_tdktersedia);
                    this.ket.tersedia[i]=parseInt(this.checker[i].qty_masuk)-parseInt(this.checker[i].keluar_tdktersedia);
                }
                
            }
        },
        draftBcm(){
            let jawab=confirm("Simpan Draft?");
        if(jawab===true){
            this.up.status="Draft";
            axios.post("/api/bcm",this.up)
            .then(res=>{
                for(let i=0;i<this.checker.length;i++){
                    this.uploadlist.kode_barang=this.checker[i].kode_barang;
                    this.uploadlist.qty=this.hitung.qty[i];
                    this.uploadlist.keterangan=this.hitung.keterangan[i];
                    this.uploadlist.nomor_bcm=this.up.bcm;
                    this.uploadlist.status="open";
                    this.uploood={kode_barang:this.uploadlist.kode_barang,nomor_bcm:this.uploadlist.nomor_bcm,
                    qty:this.uploadlist.qty,keterangan:this.uploadlist.keterangan
                    };
                    axios.post("/api/listbcm",this.uploood)
                    .then(res=>{ 
                    });
                }
                for(let h=0;h<this.checker.length;h++){
                        axios.get("/api/view/detailpo/"+this.checker[h].nomor_po+"/"+this.checker[h].kode_barang)
                        .then(res=>{
                            this.listpo=res.data.data;
                            this.persen=0
                            for(let j=0;j<this.listpo.length;j++){  
                                this.persen+=parseInt(this.listpo[j].qty_tdktersedia);
                                this.coba=[];
                                this.coba=[{qty: this.persen/100,}];
                            }
                            for(let z=0;z<this.listpo.length;z++){
                                this.uplistsisa={qty_masuk:((((this.listpo[z].qty_tdktersedia/this.coba[0].qty)/100)*(this.hitung.qty[h]))+this.listpo[z].qty_masuk)};
                                axios.put("/api/listrso/"+this.listpo[z].id,this.uplistsisa)
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
                                        if(this.sipo<1){
                                            this.uppo.status="Selesai";
                                            axios.put("/api/po/"+this.aktif.nomor_po,this.uppo)
                                            .then(res=>{
                                                this.$router.push({name:'bcmcomponent'});
                                            });
                                        }else{
                                            this.$router.push({name:'bcmcomponent'});
                                        }
                                    });
                                });
                                
                            }
                        });
                }
        },
        )}
        },
        submitBck(){
            let yakin=confirm("Yakin ingin membuat Bukti Checker ini?");
            if(yakin===true){
                if(this.checker.length>0){

                }else{
                    this.gagal=true;
                }
            }   
        },
        validqty(index){
                this.pembanding="";
                this.aksicek="";
                if(parseInt(this.hitung.qty[index])>parseInt(this.ket.tersedia[index])){
                    this.hitung.qty[index]=this.ket.tersedia[index];
                }

        },
        batal(){
            this.$router.push({name:'bck'});
        },
        timer() {
                setInterval(() => {
                    this.gagal=false;
                }, 5000);
            },
    },
} 
</script>

<style>

</style>