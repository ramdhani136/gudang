<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor BBK :</label>
                    <input v-model="up.bbk" type="text" class="form-control col-12" >
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" >
                </div>
                <div class="form-group">
                    <label>Nomor BCK</label>
                    <input v-model="up.nomor_bck" @click="showSo()" type="text" class="form-control" placeholder="Pilih BCK">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor SO</label>
                    <input v-model="ket.nomor_so" type="text" class="form-control" disabled>
                </div>
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
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Lokasi</label>
                    <input v-model="ket.lokasi" type="text" class="form-control" disabled>
                </div>
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
            <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{total | currency}}</div>
            <div class="row mt-1 mx-auto col-12" >
                <Circle5 id="load3" v-if="load"></Circle5>
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Qty BCK</th>
                            <th>Qty termuat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(listbcm,index) in checker" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{listbcm.kode_barang}}</td>
                            <td>{{listbcm.nama_barang}}</td>
                            <td style="text-align:center">{{listbcm.satuan}}</td>
                            <td style="text-align:center">{{listbcm.harga | currency}}</td>
                            <td style="text-align:center">{{listbcm.qty}}</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Form List Bukti Checker</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih BCK</label>
                        <select @change="pilihBck(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in bckaktif" :key="index">{{aktif.bck}}</option>
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
                                <th style="text-align:center">jumlah BCK</th>
                                <th style="text-align:center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(ls,index) in listbck" :key="index">
                                <td style="text-align:center">{{index+1}}</td>
                                <td>{{ls.kode_barang}}</td>
                                <td>{{ls.nama_barang}}</td>
                                <td style="text-align:center">{{ls.satuan}}</td>
                                <td style="text-align:center">{{ls.qty}}</td>
                                <td style="text-align:center">
                                    <input @change="pilihlistchecker()" v-model="checker" type="checkbox" :value="ls" disabled>
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
                bbk:this.bbk_nomor(),
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
                jumlah:[]
            },
            uplistsisa:{
                qty_masuk:0,
            },
            bckaktif:{},
            listso:{},
            gagal:false,
            uplist:{},
            qty:0,
            listrso:{},
            nilai:null,
            harga:0,
            subtotal:0,
            total:0,
            listbck:{},
            uplistrso:{},
            qtyupdate:0,
            upso:{}
        }
    },
    created(){
        this.getBckAktif();
        this.resetForm();
        this.timer();
    },  
    computed:{  

    },
    methods:{
        getBckAktif(){
            axios.get("/api/bck/data/aktif")
            .then(res=>{
                this.bckaktif=res.data.data;
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
        bbk_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "BBK-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showSo(){
            $("#modal-po").modal("show");
        },
        resetForm(){
            this.checker=[];
        },
        pilihBck(aktif){
            this.hitung.qty=[];
            this.checker=[];
            this.ket.customer=aktif.customer;
            this.ket.nomor_so=aktif.nomor_so;
            this.ket.lokasi=aktif.lokasi;
            this.ket.alamat=aktif.alamat;
            this.ket.distribusi=aktif.distribusi;
            this.up.nomor_bck=aktif.bck;

            axios.get("/api/listbck/data/aktif/"+this.up.nomor_bck)
            .then(res=>{
                this.listbck=res.data.data;  
                for (let i in this.listbck) {
					this.checker.push(this.listbck[i]);
				}   
            });
        },
        checklist(){ 
            $("#modal-po").modal("hide");
        },
        submitBck(){
            let yakin=confirm("Yakin ingin membuat Bukti Checker ini?");
            if(yakin===true){
                if(this.checker.length>0){
                axios.post("/api/bbk",this.up)
                .then(res=>{
                    for(let i=0;i<this.checker.length;i++){
                        this.nomor_bbk=this.up.bbk;
                        this.kode_barang=this.checker[i].kode_barang;
                        this.qty=0;
                        this.qty=parseInt(this.hitung.qty[i]);
                        this.harga=this.checker[i].harga;
                        this.qtybck=this.checker[i].qty;    
                        this.keterangan=this.hitung.keterangan[i];
                        this.uplist={nomor_bbk:this.nomor_bbk,kode_barang:this.kode_barang,qty:this.qty,qty_bck:this.qtybck,keterangan:this.keterangan,harga:this.harga};
                        axios.post("/api/listbbk",this.uplist)
                        .then(res=>{
                            axios.put("/api/bck/"+this.up.nomor_bck,{status:'close'})
                            .then(res=>{
                                axios.get("/api/listrso/data/"+this.checker[i].nomor_rso+"/"+this.checker[i].kode_barang)
                                .then(res=>{
                                    this.listrso=res.data.data;
                                    for(let o=0;o<this.listrso.length;o++){
                                        if(parseInt(this.hitung.qty[i])<this.checker[i].qty){     
                                            if(this.checker[i].statusso==='tersedia'){
                                                this.qtyupdate=(parseInt(this.listrso[o].keluar_tersedia)-parseInt(this.checker[i].qty))+parseInt(this.hitung.qty[i]);
                                                this.uplistrso={out_yes:this.qtyupdate,sotersedia_close:'N',bbk_tersedia:this.hitung.qty[i]};
                                                axios.put("/api/listrso/"+this.listrso[o].id,this.uplistrso)
                                                .then(res=>{
                                                    this.upso={status:'Acc'};
                                                    axios.put("/api/so/"+this.listbck[i].nomor_so,this.upso)
                                                    .then(res=>{
                                                        this.$router.push({name:'distribusibbk'});
                                                    })
                                                })
                                            }else{
                                                this.qtyupdate=(parseInt(this.listrso[o].keluar_tdktersedia)-parseInt(this.checker[i].qty))+parseInt(this.hitung.qty[i]);
                                                this.uplistrso={out_no:this.qtyupdate,sotdk_close:'N',bbk_tdktersedia:this.hitung.qty[i]};
                                                axios.put("/api/listrso/"+this.listrso[o].id,this.uplistrso)
                                                .then(res=>{
                                                    this.upso={status:'Acc'};
                                                    axios.put("/api/so/"+this.listbck[i].nomor_so,this.upso)
                                                    .then(res=>{
                                                        this.$router.push({name:'distribusibbk'});
                                                    })
                                                })
                                            }
                                        }else{
                                            if(this.checker[i].statusso==='tersedia'){
                                                this.qtyupdate=parseInt(this.listrso[o].bbk_tersedia)+parseInt(this.hitung.qty[i]);
                                                this.uplistrso={bbk_tersedia:this.qtyupdate};
                                                axios.put("/api/listrso/"+this.listrso[o].id,this.uplistrso)
                                                .then(res=>{ 
                                                        this.$router.push({name:'distribusibbk'});  
                                                })
                                            }else{
                                                this.qtyupdate=parseInt(this.listrso[o].bbk_tdktersedia)+parseInt(this.hitung.qty[i]);
                                                this.uplistrso={bbk_tdktersedia:this.qtyupdate};
                                                axios.put("/api/listrso/"+this.listrso[o].id,this.uplistrso)
                                                .then(res=>{
                                                        this.$router.push({name:'distribusibbk'});
                                                })
                                            }
                                        }
                                    }
                                    
                                });
                            });
                        })
                    }
                });
                }else{
                    this.gagal=true;
                }
            }
        },
        validqty(index){
                if(parseInt(this.hitung.qty[index])>(parseInt(this.checker[index].qty)+(parseInt(this.checker[index].qty)/100*10))){
                    this.hitung.qty[index]=this.checker[index].qty;
                }
                this.total=0;
                for(let i=0;i<this.checker.length;i++){
                    if(this.hitung.qty[i]===undefined || this.hitung.qty[i]===""){
                        this.hitung.jumlah[i]=0;
                    }else{
                        this.hitung.jumlah[i]=this.hitung.qty[i];
                    }
                    this.subtotal=parseInt(this.checker[i].harga)*parseInt(this.hitung.jumlah[i]);
                    this.total+=parseInt(this.subtotal);
                }
        },
        batal(){
            this.$router.push({name:'distribusibbk'});
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