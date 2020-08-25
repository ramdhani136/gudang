<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Checker :</label>
                    <input v-model="$route.params.bck" type="text" class="form-control col-12" disabled >
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="view.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled >
                </div>
                <div class="form-group">
                    <label>Nomor SO</label>
                    <input v-model="view.nomor_so" @click="showSo()" type="text" class="form-control" placeholder="Pilih Sales Order" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select v-model="up.customer" name="customer" class="col-12 form-control" disabled>
                        <option :value="up.customer">{{up.customer}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Distribusi</label>
                    <input v-model="up.distribusi" type="text" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input v-model="up.lokasi" type="text" class="form-control" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea v-model="up.alamat" name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="view.keterangan" name="keterangan" class="form-control col-12" :disabled="disabled===1"></textarea>
                </div>
            </div>
        </div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{total | currency}}</div>
            <div class="row mt-1 mx-auto col-12" >
                <button v-if="aktiftombol" @click="showSo()" class="btn btn-success float-left">+ Tambah Item</button>
                <Circle5 id="load3" v-if="load"></Circle5>
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>harga</th>
                            <th>Sisa SO</th>
                            <th>Qty Tersedia</th>
                            <th>Rencana Kirim</th>
                            <th>Keterangan</th>
                            <th v-if="disabled===0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(lbck,index) in listbck" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{lbck.kode_barang}}</td>
                            <td>{{lbck.nama_barang}}</td>
                            <td style="text-align:center">{{lbck.satuan}}</td>
                            <td style="text-align:center">{{lbck.harga | currency}}</td>
                            <td style="text-align:center">{{lbck.sisaso}}</td>
                            <td style="text-align:center">{{inputin.tersedia[index]}}</td>
                            <td  style="text-align:center">
                                <input @input="validqty(index)"  v-model="inputin.qty[index]" type="number" class="form-control" :disabled="disabled===1">
                            </td>
                            <td style="text-align:center">
                                <textarea v-model="lbck.keterangan" class="form-control" :disabled="disabled===1"></textarea>
                            </td>
                            <td v-if="disabled===0" style="text-align:center">
                                <button class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2">
                <button v-if="!aktiftombol"  class="btn-success btn ml-2" >
                    Simpan  
                </button>
                <button @click="aktiftombol=!aktiftombol" class="btn-orange btn ml-2" >
                    {{namatombol}} {{aktiftombol? edit():close()}}  
                </button>
                <button v-if="aktiftombol" @click="batal()" class="btn-primary btn ml-1" >
                    Kembali
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
                        <label>Nomor SO</label>
                        <input type="text" class="form-control" v-model="view.nomor_so" disabled>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <input v-model="up.customer" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody overflow-auto ">
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
                                    <input v-model="checker" type="checkbox" :value="ls" :disabled="ket.ltersedia[index]===0">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div v-if="itemlain" id="kosong" class="col-12 text-justify text-center align-self-center">
                            Tidak ada item lain
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="tambahitem()" type="button" class="btn btn-primary">Tambah Item</button>
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
            load:true,
            up:{},
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
            uplistsisa:{
                qty_masuk:0,
            },
            soaktif:{},
            listso:{},
            gagal:false,
            uplist:{},
            qty:0,
            listrso:{},
            nilai:null,
            disabled:1,
            bck:{},
            view:{},
            so:{},
            listbck:{},
            total:0,
            tomboledit:false,
            namatombol:'Edit',
            aktiftombol:true,
            lbck:{
                qty:[]
            },
            inputin:{
                qty:[],
                tersedia:[],
                qtyhasil:[]
            },
            tersedia:0,
            subtotal:0,
            itemlain:false,
        }
    },
    created(){
        this.getBck();      /* 
        this.resetForm();
        this.timer(); */
    },  
    computed:{  

    },
    methods:{
        getBck(){
            axios.get("/api/bck/"+this.$route.params.bck)
            .then(res=>{
                this.bck=res.data.data;
                this.view=this.bck[0];
                axios.get("/api/so/"+this.view.nomor_so)
                .then(res=>{
                    this.so=res.data.data;
                    this.up=this.so[0];
                    axios.get("/api/listbck/"+this.$route.params.bck)
                    .then(res=>{
                        this.listbck=res.data.data;
                        for(let k=0;k<this.listbck.length;k++){
                            this.total+=parseInt(this.listbck[k].qty)*parseInt(this.listbck[k].harga);
                            this.inputin.qty[k]=this.listbck[k].qty;
                            this.inputin.tersedia[k]=this.listbck[k].tersedia;
                        }
                        this.load=false;
                    });



                    if(this.so[0].statusso==="tersedia"){
                    axios.get("/api/listrso/data/pilihsotersedia/view/"+this.so[0].nomor_rso)
                    .then(res=>{
                        this.listso=res.data.data;
                        for(let i=0;i<this.listso.length;i++){
                            this.ket.sisaso[i]=parseInt(this.listso[i].qty_tersedia)-parseInt(this.listso[i].keluar_tersedia);
                            this.ket.ltersedia[i]=parseInt(this.listso[i].qty_tersedia)-parseInt(this.listso[i].keluar_tersedia);
                        }
                        if(this.listso.length<1){
                            this.itemlain=true;
                        }else{
                            this.itemlain=false;
                        }
                    
                    })
                    }else if(this.so[0].statusso==="tidaktersedia"){
                    axios.get("/api/listrso/data/pilihsotidak/view/"+this.so[0].nomor_rso)
                    .then(res=>{
                        this.listso=res.data.data;
                        for(let i=0;i<this.listso.length;i++){
                            this.ket.sisaso[i]=parseInt(this.listso[i].qty_tdktersedia)-parseInt(this.listso[i].keluar_tdktersedia);
                            this.ket.ltersedia[i]=parseInt(this.listso[i].qty_masuk)-parseInt(this.listso[i].keluar_tdktersedia);
                        }   
                        if(this.listso.length<1){
                            this.itemlain=true;
                        }else{
                            this.itemlain=false;
                        }
                    })
                    }




                });   
            })
        },
        edit(){
            this.namatombol='Edit';   
            this.disabled=1;
            this.total=0;
            for(let k=0;k<this.listbck.length;k++){
                this.inputin.tersedia[k]=this.listbck[k].tersedia;
                this.inputin.qty[k]=this.listbck[k].qty;
                this.total+=parseInt(this.listbck[k].qty)*parseInt(this.listbck[k].harga);
            };    
        },
        close(){
            this.namatombol='Close'; 
            this.disabled=0;
            for(let k=0;k<this.listbck.length;k++){
                if(this.listbck[k].statusso==="tersedia"){
                    axios.get("/api/listrso/data/"+this.listbck[k].nomor_rso+"/"+this.listbck[k].kode_barang)
                    .then(res=>{
                        this.list={};
                        this.list=res.data.data;
                        for(let j=0;j<this.listbck.length;j++){
                            this.tersedia=parseInt(this.list[k].qty_tersedia)-parseInt(this.list[k].keluar_tersedia);
                            if(this.tersedia===0){
                                this.inputin.tersedia[k]=this.listbck[k].qty;
                                this.bck={};
                            }
                        }
                    });
                }else{
                    axios.get("/api/listrso/data/"+this.listbck[k].nomor_rso+"/"+this.listbck[k].kode_barang)
                    .then(res=>{
                        this.list={};
                        this.list=res.data.data;
                        for(let j=0;j<this.listbck.length;j++){
                            this.tersedia=parseInt(this.list[k].qty_tdktersedia)-parseInt(this.list[k].keluar_tdktersedia);
                            if(this.tersedia===0){
                                this.inputin.tersedia[k]=this.listbck[k].qty;
                                this.bck={};
                            }
                        }
                    });
                }
                
            }; 
        },
       /*  getSoAktif(){
            axios.get("/api/so/data/aktif")
            .then(res=>{
                this.soaktif=res.data.data;
            });
        }, */
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
        bck_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "BCK-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showSo(){
            $("#modal-po").modal("show");
        },
        tambahitem(){
            console.log(this.checker);
            /* for(let i=0;i<this.checker.length;i++){
                if(this.aktif.statusso==="tersedia"){
                    this.ket.sisasopilih[i]=parseInt(this.checker[i].qty_tersedia)-parseInt(this.checker[i].keluar_tersedia);
                    this.ket.tersedia[i]=parseInt(this.checker[i].qty_tersedia)-parseInt(this.checker[i].keluar_tersedia);
                }else{
                    this.ket.sisasopilih[i]=parseInt(this.checker[i].qty_tdktersedia)-parseInt(this.checker[i].keluar_tdktersedia);
                    this.ket.tersedia[i]=parseInt(this.checker[i].qty_masuk)-parseInt(this.checker[i].keluar_tdktersedia);
                }
                
            } */
        },
        resetForm(){
            this.checker=[];
        },
        /*
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
        submitBck(){
            let yakin=confirm("Yakin ingin membuat Bukti Checker ini?");
            if(yakin===true){
                if(this.checker.length>0){
                    axios.post("/api/bck",this.up)
                    .then(res=>{
                        for(let i=0;i<this.checker.length;i++){
                        this.nomor_bck=this.up.bck;
                        this.kode_barang=this.checker[i].lkode_barang;
                        this.qty=0;
                        this.qty=parseInt(this.hitung.qty[i]);
                        this.keterangan=this.hitung.keterangan[i];
                        if(this.aktif.statusso==="tersedia"){
                            this.sisaso=parseInt(this.checker[i].qty_tersedia)-parseInt(this.checker[i].keluar_tersedia);  
                        }else{
                            this.sisaso=parseInt(this.checker[i].qty_tdktersedia)-parseInt(this.checker[i].keluar_tdktersedia);
                        }
                        this.uplist={nomor_bck:this.nomor_bck,kode_barang:this.kode_barang,qty:this.qty,sisaso:this.sisaso,keterangan:this.keterangan};
                        axios.post("/api/listbck",this.uplist)
                        .then(res=>{
                            if(this.aktif.statusso==="tersedia"){
                                this.uprso={out_yes:parseInt(this.hitung.qty[i])+parseInt(this.checker[i].keluar_tersedia)};
                                    axios.put("/api/listrso/"+this.checker[i].id,this.uprso)
                                    .then(res=>{
                                        axios.get("/api/listrso/"+this.checker[i].id+"/edit")
                                        .then(res=>{
                                        this.listrso=res.data.data;
                                        for(let k=0;k<this.listrso.length;k++){
                                            if(this.listrso[k].keluar_tersedia>=this.listrso[k].qty_tersedia){
                                            this.uprso={},
                                            this.uprso={sotersedia_close:'Y'};
                                            axios.put("/api/listrso/"+this.checker[i].id,this.uprso)
                                                .then(res=>{
                                                    axios.get("/api/listrso/data/pilihsotersedia/"+this.aktif.nomor_rso)
                                                    .then(res=>{
                                                        this.listso=res.data.data;
                                                        if(this.listso.length<1){
                                                            this.updateso={status:'Selesai'};
                                                            axios.put("/api/so/"+this.aktif.nomor_so,this.updateso)
                                                            .then(res=>{
                                                                this.$router.push({name:'bck'});
                                                            });
                                                        }
                                                    });
                                                })
                                            }   
                                        }
                                        })
                                    });
                            }else{
                            this.uprso={out_no:parseInt(this.hitung.qty[i])+parseInt(this.checker[i].keluar_tdktersedia)};
                            axios.put("/api/listrso/"+this.checker[i].id,this.uprso)
                                .then(res=>{
                                    axios.get("/api/listrso/"+this.checker[i].id+"/edit")
                                    .then(res=>{
                                        this.listrso=res.data.data;
                                        for(let k=0;k<this.listrso.length;k++){
                                            if(this.listrso[k].keluar_tdktersedia>=this.listrso[k].qty_tdktersedia){
                                            this.uprso={},
                                            this.uprso={sotdk_close:'Y'};
                                            axios.put("/api/listrso/"+this.checker[i].id,this.uprso)
                                                .then(res=>{
                                                    axios.get("/api/listrso/data/pilihsotidak/"+this.aktif.nomor_rso)
                                                    .then(res=>{
                                                        this.listso=res.data.data;
                                                        if(this.listso.length<1){
                                                            this.updateso={status:'Selesai'};
                                                            axios.put("/api/so/"+this.aktif.nomor_so,this.updateso)
                                                            .then(res=>{
                                                                this.$router.push({name:'bck'});
                                                            });
                                                        }
                                                    });
                                                })
                                            }   
                                        }
                                    })
                                });
                            }
                            this.$router.push({name:'bck'});
                        })
                    }
                });
                }else{
                    this.gagal=true;
                }
            }   
        }, */
        validqty(index){
                this.subtotal=0;
                if(parseInt(this.inputin.qty[index])>parseInt(this.inputin.tersedia[index])){
                    this.inputin.qty[index]=this.inputin.tersedia[index];  
                }

                for(let k=0;k<this.listbck.length;k++){
                    this.total=0;
                    this.inputin.qtyhasil[k]=this.inputin.qty[k];
                    if(this.inputin.qty[k]===""){
                        this.inputin.qtyhasil[k]=0;
                    }
                    this.subtotal+=parseInt(this.inputin.qtyhasil[k])*parseInt(this.listbck[k].harga); 
                    this.total+=this.subtotal;
                }
        },
        
        batal(){
            this.$router.push({name:'bck'});
        },
        /* timer() {
                setInterval(() => {
                    this.gagal=false;
                }, 5000);
            }, */
    },
} 
</script>

<style>
    #kosong{
        margin-top: 20px;
        margin-bottom: 20px;
        color:darkslategrey;
    }
</style>