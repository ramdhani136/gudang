<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor BBM :</label>
                    <input v-model="up.bbm" type="text" class="form-control col-12" >
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" >
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Supplier</label>
                    <select v-model="ket.supplier" name="customer" class="col-12 form-control" disabled>
                        <option :value="ket.supplier">{{ket.supplier}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Bukti Checker</label>
                    <input v-model="up.nomor_bcm" @click="showBcm()" type="text" class="form-control" placeholder="Pilih Purchase Order">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Polisi</label>
                    <input type="text" v-model="ket.nopol" class="form-control" disabled>
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
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Qty</th>
                            <th>Jumlah Real</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(listbbm,index) in checker" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{listbbm.nama_barang}}</td>
                            <td style="text-align:center">{{listbbm.satuan}}</td>
                            <td style="text-align:center">{{listbbm.qty}}</td>
                            <td  style="text-align:center">
                                <input @input="validasiqty()" v-model="hitung.qty[index]" type="number" class="form-control">
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
                <!-- <button @click="draftBbm()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button> -->
                <button @click="submitBBM()" class="btn-success btn ml-2" >
                    Kirim BBM
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
                        <label>Pilih Bukti Checker</label>
                        <select @change="pilihBcm(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in bcmaktif" :key="index">{{aktif.bcm}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input v-model="ket.supplier" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody">
                    <table class="table mt-2 table-striped table-bordered" style="width:100%">
                        <thead  id="rsthead">
                            <tr>
                                <th style="text-align:center">No</th>
                                <th style="text-align:center">Item</th>
                                <th style="text-align:center">Satuan</th>
                                <th style="text-align:center">Sisa PO</th>
                                <th style="text-align:center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(ls,index) in listsisa" :key="index">
                                <td style="text-align:center">{{index+1}}</td>
                                <td>{{ls.nama_barang}}</td>
                                <td style="text-align:center">{{ls.satuan}}</td>
                                <td style="text-align:center">{{ls.qty}}</td>
                                <td style="text-align:center">
                                    <input v-model="checker" type="checkbox"  :value="ls" disabled>
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
                bbm:this.bbm_nomor(),
                tanggal:this.now(),
            },
            bcmaktif:{},
            ket:{},
            aktif:{},
            listsisa:{},
            checker:[],
            listbbm:{},
            uploadlist:[],
            hitung:{
                qty:[],
                keterangan:[],
            },
            uploood:{},
            dtime:this.DateTime(),
            totalmasuk:{},
            listpo:{},
            uplist:{},
            uppo:{},
            uplistsisa:{
                qty_masuk:0,
            },
            uplistsisa2:{
                qty_masuk:0,
            },
            listpost:{},
            listsisas:{},
            listbcm:{},
            comp:{},
            input:{}
        }
    },
    created(){
        this.getBcmAktif();
        this.resetForm();
    },  
    computed:{  

    },
    methods:{
        getBcmAktif(){
            axios.get("/api/bcm/data/open")
            .then(res=>{
                this.bcmaktif=res.data.data;
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
        bbm_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "BBM-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showBcm(){
            $("#modal-po").modal("show");
        },
        resetForm(){
            this.checker=[];
        },  
        pilihBcm(aktif){
            this.checker=[];
            this.ket.supplier=aktif.supplier;
            this.up.nomor_bcm=aktif.bcm;
            this.ket.nopol=aktif.nopol;
            axios.get("/api/listbcm/data/"+aktif.bcm)
            .then(res=>{
                this.listsisa=res.data.data;
                for (let i in this.listsisa) {
					this.checker.push(this.listsisa[i]);
				}
            });
        },
        checklist(){
            $("#modal-po").modal("hide");
        },
        /* draftBbm(){
            let keputusan=confirm("Simpan Draft?");
            if(keputusan===true){
            axios.post("/api/bbm",this.up)
            .then(res=>{
                for(let i=0;i<this.checker.length;i++){
                    this.uploadlist.kode_barang=this.checker[i].kode_barang;
                    this.uploadlist.qty=this.hitung.qty[i];
                    this.uploadlist.keterangan=this.hitung.keterangan[i];
                    this.uploadlist.nomor_bbm=this.up.bbm;
                    this.uploood={kode_barang:this.uploadlist.kode_barang,nomor_bbm:this.uploadlist.nomor_bbm,
                    qty:this.uploadlist.qty,keterangan:this.uploadlist.keterangan
                    };
                    axios.post("/api/listbbm",this.uploood)
                    .then(res=>{
                        this.$router.push({name:'ingoods'});
                    });
            }
        },
        )
            }
        }, */
        submitBBM(){ 
            let keputusan=confirm("yakin ingin mengirim Bukti Barang Masuk ini?");
            if(keputusan===true){
                axios.post("/api/bbm/",this.up)
                .then(res=>{
                    for(let i=0;i<this.checker.length;i++){
                this.uplistrso={close_bcm:'Y'};
                axios.put("/api/listbcm/"+this.checker[i].id,this.uplistrso)
                .then(res=>{
                    this.input={qty:this.hitung.qty[i],kode_barang:this.checker[i].kode_barang,nomor_bbm:this.up.bbm,keterangan:this.hitung.keterangan[i]}
                    axios.post("/api/listbbm/",this.input)
                    .then(res=>{
                    
                    });
                    if(this.hitung.qty[i]<this.checker[i].qty){
                    axios.get("/api/view/accpo/"+this.checker[i].nomor_po+"/"+this.checker[i].kode_barang)
                    .then(res=>{
                        this.listpo=res.data.data;
                        for(let j=0;j<this.listpo.length;j++){ 
    
                            if(this.listpo[j].qty_masuk<this.checker[i].qty){
                                this.hasil=0;
                                this.checker[i].qty=parseInt(this.checker[i].qty)-parseInt(this.listpo[j].qty_masuk);
                            }else{
                                this.hasil=parseInt(this.listpo[j].qty_masuk)-parseInt(this.checker[i].qty);
                                this.checker[i].qty=parseInt(this.checker[i].qty)-parseInt(this.listpo[j].qty_masuk);
                            }
                            if(this.checker[i].qty<0){
                                this.checker[i].qty=0;
                            }
                            this.uplistsisa={qty_masuk:this.hasil};
                            axios.put("/api/listrso/"+this.listpo[j].id, this.uplistsisa)
                            .then(res=>{
                            axios.get("/api/listrso/data/listpo/"+this.checker[i].nomor_po)
                                .then(res=>{
                                    this.listsisa=res.data.data;
                                    for(let k=0;k<this.listsisa.length;k++){
                                        if(this.listsisa[k].sisapo>0){
                                            this.uplist.po_close="N";
                                            axios.put("/api/listrso/data/"+this.listsisa[k].nomor_po+"/"+this.listsisa[k].kode_barang,this.uplist)
                                            .then(res=>{
                                            });
                                            }
                                            }
                                })
                            this.sipo=0;
                            axios.get("/api/listrso/data/listall/"+this.checker[i].nomor_po)
                            .then(res=>{
                                this.sisasemua=0;
                                this.listsisa=res.data.data;
                                for(let l=0;l<this.listsisa.length;l++){
                                    this.sipo+=parseInt(this.listsisa[l].sisapo);  
                                }
                                if(this.sipo>0){
                                this.uppo.status="Acc";
                                axios.put("/api/po/"+this.checker[i].nomor_po,this.uppo)
                                    .then(res=>{
                                    
                                    });
                                }
                                });
                        });
                        } 
                        axios.get("/api/view/detailpo/"+this.checker[i].nomor_po+"/"+this.checker[i].kode_barang)
                        .then(res=>{
                            this.listpost=res.data.data;
                            for(let m=0;m<this.listpost.length;m++){  
                                if(this.hitung.qty[i]<=(parseInt(this.listpost[m].qty_tdktersedia)-parseInt(this.listpost[m].qty_masuk))){
                                    this.hasil=this.hitung.qty[i];
                                    this.hitung.qty[i]=parseInt(this.hitung.qty[i])-(parseInt(this.listpost[m].qty_tdktersedia)-parseInt(this.listpost[m].qty_masuk));
                                }else if(this.hitung.qty[i]>(parseInt(this.listpost[m].qty_tdktersedia)-parseInt(this.listpost[m].qty_masuk))){
                                    this.hasil=parseInt(this.listpost[m].qty_tdktersedia)-parseInt(this.listpost[m].qty_masuk);
                                    this.hitung.qty[i]=parseInt(this.hitung.qty[m])-(parseInt(this.listpost[m].qty_tdktersedia)-parseInt(this.listpost[m].qty_masuk));
                                }
                                if(this.hitung.qty[i]<0){
                                    this.hitung.qty[i]=0;
                                }
                                    this.uplistsisa2={qty_masuk:parseInt(this.hasil)+parseInt(this.listpost[m].qty_masuk)};
                                    console.log(this.uplistsisa2);
                                    axios.put("/api/listrso/"+this.listpost[m].id,this.uplistsisa2)
                                    .then(res=>{
                                    axios.get("/api/listrso/data/listpo/"+this.checker[i].nomor_po)
                                        .then(res=>{
                                            this.listsisas=res.data.data;
                                            for(let k=0;k<this.listsisas.length;k++){
                                                if(this.listsisas[k].sisapo<1){
                                                    this.uplist.po_close="Y";
                                                    axios.put("/api/listrso/data/"+this.checker[i].nomor_po+"/"+this.listsisas[k].kode_barang,this.uplist)
                                                    .then(res=>{
                                                    });
                                                    }
                                            }
                                        })
                                    }); 
                            }
                        });
                        this.$router.push({name:'ingoods'});
                    });
                }else{
                    this.$router.push({name:'ingoods'});
                }
                this.statusbcm=""; 
                this.bandingkan="";
                this.pembandingbcm="Y";
                axios.get("/api/listbcm/"+this.up.nomor_bcm)
                .then(res=>{
                    this.listbcm=res.data.data;
                    for(let s=0;s<this.listbcm.length;s++){
                        this.statusbcm+=this.listbcm[s].close_bcm;
                        this.bandingkan+=this.pembandingbcm;
                    }
                    if(this.statusbcm===this.bandingkan){
                        this.comp={status:"close"};
                        axios.put("/api/bcm/"+this.up.nomor_bcm,this.comp)
                        .then(res=>{
                            console.log("sudah diupdate");
                        });
                    }
                });
                })

            }
                });
            }
        },
        validasiqty(){
            for(let i=0;i<this.checker.length;i++){
                if(this.hitung.qty[i]>this.checker[i].qty){
                    this.hitung.qty[i]=this.checker[i].qty
                }   
            }
        },
        DateTime(){
            this.date = new Date(); 
            this.month = this.date.getMonth()+1;
            this.year=this.date.getFullYear();
            this.hours=this.date.getHours();
            this.minute=this.date.getMinutes();
            this.seconds=this.date.getSeconds();
            if(this.month>12){
                this.month=12;
            }
            this.day = this.date.getDate();
            this.dates=this.year+"-"+(this.month<10 ? '0' : '')+this.month+"-"+this.day;
            this.times=this.hours+":"+this.minute+":"+(this.seconds<10 ? '0' : '')+this.seconds;
            this.datetimes=this.dates+" "+this.times;
            return this.datetimes;
        }
    },
} 
</script>

<style>

</style>