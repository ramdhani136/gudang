<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor SO :</label>
                    <input v-model="up.nomor_so" type="text" class="form-control col-12" >
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="up.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12" >
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
                    <label>Nomor RSO</label>
                    <input v-model="up.nomor_rso" @click="showPo()" type="text" class="form-control" placeholder="Pilih RSO">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Tanggal Kirim :</label>
                    <input v-model="up.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" >
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
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th>Status</th>
                            <th v-if="tampil">Estimasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ls,index) in listso" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{ls.nama_barang}}</td>
                            <td style="text-align:center">{{qty}}</td>
                            <td style="text-align:center">{{ls.satuan}}</td>
                            <td style="text-align:center">{{ls.harga |currency}}</td>
                            <td style="text-align:center">{{qty*ls.harga |currency}}</td>
                            <td style="text-align:center">{{lstatus}}</td>
                            <td v-if="tampil" style="text-align:center">{{ls.tgl_datang}}</td>
                            <td  style="text-align:center">
                                <button @click="hapuslistSo(ls)" class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2">
                <button @click="draftSo()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button>
                <button @click="submitSo()" class="btn-success btn ml-2" >
                    Kirim SO
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
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih RSO</label>
                        <select @change="pilihRso(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in rso" :key="index">{{aktif.nomor_rso}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <input v-model="ket.customer" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="status" class="form-control">
                            <option value="Tersedia">Tersedia</option>
                            <option value="TidakTersedia">Tidak Tersedia</option>
                        </select>
                    </div>
                    <div id="overflowBody">
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="resetForm()" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="pilihStatus()" class="btn btn-primary">Save changes</button>
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
                nomor_so:this.so_nomor(),
                tanggal_so:this.now(),
                tanggal_kirim:this.tglKirim(),
            },
            poaktif:{},
            ket:{},
            aktif:{},
            listsisa:{},
            checker:[],
            rso:{},
            status:'Tersedia',
            listso:{},
            upload:{},
            ubah:{},
            banding2:{
                jumlah:'',
                status:'',
                penyama:''
            },
            updaterso:{},
            updatersot:{},
            listrso:{},
            listdel:{
                qty:0,
            },
            gagal:false,
        }
    },
    created(){
        this.getPoAktif();
        this.getRso();
        this.resetForm();
        this.timer();
    },  
    computed:{  
        
    },
    methods:{
        getRso(){
            axios.get("/api/rso/data/confirm/")
            .then(res=>{
                this.rso=res.data.data;
            });
        },
        getPoAktif(){
            axios.get("/api/poaktif/")
            .then(res=>{
                this.poaktif=res.data.data;
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
        so_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "SO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showPo(){
            $("#modal-po").modal("show");
        },
        resetForm(){
            this.checker=[];
        },
        pilihRso(aktif){
            this.checker=[];
            this.ket.customer=aktif.customer;
            this.up.nomor_rso=aktif.nomor_rso;
            axios.get("/api/listrso/data/listpo/"+aktif.nomor_po)
            .then(res=>{
                this.listsisa=res.data.data;
            });
        },
        checklist(){
            $("#modal-po").modal("hide");
        },
        pilihStatus(){
            if(this.status==="Tersedia"){
                axios.get("/api/listrso/data/dic/"+this.up.nomor_rso)
                .then(res=>{this.listso=res.data.data
                    this.total=0;
                    for (let i = 0; i < this.listso.length; i++) {
                        this.qty=this.listso[i].qty_tersedia;   
                        this.subTotal=parseInt(this.qty)*parseInt(this.listso[i].harga); 
                        this.total += this.subTotal;
                        this.lstatus="Tersedia";
                        this.tampil=false;
                        this.statuspo=true;
                }
                    });
            }else if(this.status==="TidakTersedia"){
                axios.get("/api/listrso/data/acc/"+this.up.nomor_rso)
                .then(res=>{this.listso=res.data.data
                    this.total=0;
                    for (let i = 0; i < this.listso.length; i++) {
                    this.qty=this.listso[i].qty_tdktersedia; 
                    this.subTotal=parseInt(this.qty)*parseInt(this.listso[i].harga);
                    this.total += this.subTotal;
                    this.lstatus="Tidak Tersedia";  
                    this.tampil=true;  
                    this.statuspo=false;
                }
                });
            }
            
            $("#modal-po").modal("hide"); 
        },
        draftSo(){
            let keputusan=confirm("Simpan Draft?");
            this.banding2.jumlah="";
            this.banding2.penyama="";
            if(keputusan===true){
                if(this.listso.length>0){
                    if(this.statuspo===true){
                        this.up.statusso="tersedia";
                        axios.post("/api/so",this.up)
                            .then(res=>{
                                this.ubah.so_tersedia="Y";
                                for(let i=0;i<this.listso.length;i++){
                                    this.upload.id=this.listso[i].id;
                                        axios.put("/api/listrso/"+this.upload.id,this.ubah)
                                        .then(res=>{
                                            axios.get("/api/listrso/"+this.up.nomor_rso)
                                            .then(res=>{
                                                this.listrso=res.data.data;
                                                for(let i=0;i<this.listrso.length;i++){
                                                    this.banding2.so_tdktersedia=this.listrso[i].so_tdktersedia;
                                                    this.banding2.jumlah+=this.banding2.so_tdktersedia;
                                                    this.banding2.penyama+="Y";
                                                    this.banding2.nomor_rso=this.listrso[0].lno_rso;
                                                }
                                                if(this.banding2.jumlah===this.banding2.penyama){
                                                        this.updaterso.status="So"; 
                                                        axios.put("/api/rso/"+ this.banding2.nomor_rso,this.updaterso)
                                                        .then(res=>{
                                                            this.$router.push({name:'so'})
                                                        });
                                                    }
                                                this.$router.push({name:'so'})
                                            });
                                        });
                                };
                            });    
                    }else{
                        this.up.statusso="tidaktersedia";
                        axios.post("/api/so",this.up)
                            .then(res=>{
                                this.ubah.so_tdktersedia="Y";
                                for(let i=0;i<this.listso.length;i++){
                                    this.upload.id=this.listso[i].id;
                                        axios.put("/api/listrso/"+this.upload.id,this.ubah)
                                        .then(res=>{
                                            axios.get("/api/listrso/"+this.up.nomor_rso)
                                            .then(res=>{
                                                this.listrso=res.data.data;
                                                for(let i=0;i<this.listrso.length;i++){
                                                    this.banding2.so_tersedia=this.listrso[i].so_tersedia;
                                                    this.banding2.jumlah+=this.banding2.so_tersedia;
                                                    this.banding2.penyama+="Y";
                                                    this.banding2.nomor_rso=this.listrso[0].lno_rso;
                                                }
                                                if(this.banding2.jumlah===this.banding2.penyama){
                                                        this.updaterso.status="So"; 
                                                        axios.put("/api/rso/"+ this.banding2.nomor_rso,this.updaterso)
                                                        .then(res=>{
                                                            this.$router.push({name:'so'})
                                                        });
                                                    }
                                                this.$router.push({name:'so'})
                                            });
                                        });
                                };
                            }); 
                    }
                }else{
                    this.gagal=true;
                }
            }
        },
        submitSo(){
            let keputusan=confirm("Simpan Draft?");
            this.banding2.jumlah="";
            this.banding2.penyama="";
            if(keputusan===true){
                if(this.listso.length>0){
                    if(this.statuspo===true){
                        this.up.statusso="tersedia";
                        this.up.status="Sent";
                        axios.post("/api/so",this.up)
                            .then(res=>{
                                this.ubah.so_tersedia="Y";
                                for(let i=0;i<this.listso.length;i++){
                                    this.upload.id=this.listso[i].id;
                                        axios.put("/api/listrso/"+this.upload.id,this.ubah)
                                        .then(res=>{
                                            axios.get("/api/listrso/"+this.up.nomor_rso)
                                            .then(res=>{
                                                this.listrso=res.data.data;
                                                for(let i=0;i<this.listrso.length;i++){
                                                    this.banding2.so_tdktersedia=this.listrso[i].so_tdktersedia;
                                                    this.banding2.jumlah+=this.banding2.so_tdktersedia;
                                                    this.banding2.penyama+="Y";
                                                    this.banding2.nomor_rso=this.listrso[0].lno_rso;
                                                }
                                                if(this.banding2.jumlah===this.banding2.penyama){
                                                        this.updaterso.status="So"; 
                                                        axios.put("/api/rso/"+ this.banding2.nomor_rso,this.updaterso)
                                                        .then(res=>{
                                                            this.$router.push({name:'so'})
                                                        });
                                                    }
                                                this.$router.push({name:'so'})
                                            });
                                        });
                                };
                            });    
                    }else{
                        this.up.statusso="tidaktersedia";
                        this.up.status="Sent";
                        axios.post("/api/so",this.up)
                            .then(res=>{
                                this.ubah.so_tdktersedia="Y";
                                for(let i=0;i<this.listso.length;i++){
                                    this.upload.id=this.listso[i].id;
                                        axios.put("/api/listrso/"+this.upload.id,this.ubah)
                                        .then(res=>{
                                            axios.get("/api/listrso/"+this.up.nomor_rso)
                                            .then(res=>{
                                                this.listrso=res.data.data;
                                                for(let i=0;i<this.listrso.length;i++){
                                                    this.banding2.so_tersedia=this.listrso[i].so_tersedia;
                                                    this.banding2.jumlah+=this.banding2.so_tersedia;
                                                    this.banding2.penyama+="Y";
                                                    this.banding2.nomor_rso=this.listrso[0].lno_rso;
                                                }
                                                if(this.banding2.jumlah===this.banding2.penyama){
                                                        this.updaterso.status="So"; 
                                                        axios.put("/api/rso/"+ this.banding2.nomor_rso,this.updaterso)
                                                        .then(res=>{
                                                            this.$router.push({name:'so'})
                                                        });
                                                    }
                                                this.$router.push({name:'so'})
                                            });
                                        });
                                };
                            }); 
                    }
                }else{
                    this.gagal=true;
                }
            }
        },
        hapuslistSo(ls){
            let keputusan=confirm("Yakin ingin menghapus item ini?");
            if(keputusan===true){
                if(this.status==="Tersedia"){
                    if(ls.status==="Tersedia"){
                        axios.delete("/api/listrso/"+ls.id)
                        .then(res=>{
                            this.pilihStatus();
                        });

                    }else if(ls.status==="Tidak Tersedia"){
                        axios.delete("/api/listrso/"+ls.id)
                        .then(res=>{
                            this.pilihStatus();
                        });
                        
                    }else if(ls.status==="Tersedia Sebagian"){
                        this.listdel.qty=parseInt(ls.qty)-parseInt(ls.qty_tersedia);
                        this.listdel.qty_tersedia="";
                        this.listdel.status="Tidak Tersedia";
                        this.listdel.so_tersedia="Y";
                        axios.put("/api/listrso/"+ls.id,this.listdel)
                        .then(res=>{
                            this.pilihStatus();
                            this.listdel={};
                        });
                    }
                }else if(this.status==="TidakTersedia"){
                    if(ls.status==="Tersedia"){
                        axios.delete("/api/listrso/"+ls.id)
                        .then(res=>{
                            this.pilihStatus();
                        });
                    }else if(ls.status==="Tidak Tersedia"){
                        axios.delete("/api/listrso/"+ls.id)
                        .then(res=>{
                            this.pilihStatus();
                        });
                    }else if(ls.status==="Tersedia Sebagian"){
                        this.listdel.qty=parseInt(ls.qty)-parseInt(ls.qty_tdktersedia);
                        this.listdel.qty_tdktersedia="";
                        this.listdel.status="Tersedia";
                        this.listdel.so_tdktersedia="Y";
                        this.listdel.tanggal_datang="";
                        this.listdel.acc_purch="N";
                        axios.put("/api/listrso/"+ls.id,this.listdel)
                        .then(res=>{
                            this.pilihStatus();
                            this.listdel={};
                        });
                    }
                }
            }
        },
        timer() {
                setInterval(() => {
                    this.gagal=false;
                }, 5000);
            },

    }   
} 
</script>

<style>

</style>