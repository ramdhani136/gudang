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
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Supplier</label>
                    <select v-model="ket.supplier" name="customer" class="col-12 form-control" disabled>
                        <option :value="ket.supplier">{{ket.supplier}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor PO</label>
                    <input v-model="up.nomor_po" @click="showPo()" type="text" class="form-control" placeholder="Pilih Purchase Order">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Kendaraan </label>
                    <input type="text" v-model="up.nopol" class="form-control">
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
                            <th>Sisa PO</th>
                            <th>Qty SJ</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(listbcm,index) in checker" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{listbcm.kode_barang}}</td>
                            <td>{{listbcm.nama}}</td>
                            <td style="text-align:center">{{listbcm.satuan}}</td>
                            <td style="text-align:center">{{listbcm.sisapo}}</td>
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
                <button v-if="jenbutton" @click="submitBCM()" class="btn-success btn ml-2" >
                    Kirim Warehouse
                </button>
                <button v-if="!jenbutton" @click="requestBcm()" class="btn-primary btn ml-2" >
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
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih PO</label>
                        <select @change="pilihPo(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in poaktif" :key="index">{{aktif.nomor_po}}</option>
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
                                <td>{{ls.nama}}</td>
                                <td style="text-align:center">{{ls.satuan}}</td>
                                <td style="text-align:center">{{ls.sisapo}}</td>
                                <td style="text-align:center">
                                    <input v-model="checker" type="checkbox" :value="ls">
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
                bcm:this.bcm_nomor(),
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
                qty_masuk:0,
            },
            uplist:{},
            sipo:0,
            uppo:{},
            listpo:{},
            persen:0,
            listrso:{},
            coba:[],
        }
    },
    created(){
        this.getPoAktif();
        this.resetForm();
    },  
    computed:{  

    },
    methods:{
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
        bcm_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "BCM-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showPo(){
            $("#modal-po").modal("show");
        },
        resetForm(){
            this.checker=[];
        },
        pilihPo(aktif){
            this.checker=[];
            this.ket.supplier=aktif.supplier;
            this.up.nomor_po=aktif.nomor_po;
            axios.get("/api/listrso/data/pilihpo/"+aktif.nomor_po)
            .then(res=>{
                this.listsisa=res.data.data;
            });
        },
        checklist(){
            $("#modal-po").modal("hide");
        },
        /* draftBcm(){
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
        }, */
        submitBCM(){
        console.log(this.checker);
        /*  let jawab=confirm("Yakin ingin mengirim Bukti Checker Masuk ini?");
        if(jawab===true){
            this.up.status="open";
            axios.post("/api/bcm",this.up)
            .then(res=>{
                for(let i=0;i<this.checker.length;i++){
                    this.uploadlist.kode_barang=this.checker[i].kode_barang;
                    this.uploadlist.qty=this.hitung.qty[i];
                    this.uploadlist.keterangan=this.hitung.keterangan[i];
                    this.uploadlist.nomor_bcm=this.up.bcm;
                    this.uploadlist.status="open";
                    this.uploood={kode_barang:this.uploadlist.kode_barang,nomor_bcm:this.uploadlist.nomor_bcm,
                    qty:this.uploadlist.qty,keterangan:this.uploadlist.keterangan,nomor_po:this.up.nomor_po,sisapo:this.checker[i].sisapo,
                    };
                    axios.post("/api/listbcm",this.uploood)
                    .then(res=>{ 
                    });
                }
                for(let h=0;h<this.checker.length;h++){
                axios.get("/api/view/detailpo/"+this.checker[h].nomor_po+"/"+this.checker[h].kode_barang)
                        .then(res=>{
                            this.listpo=res.data.data;
                            for(let j=0;j<this.listpo.length;j++){  
                                if(this.hitung.qty[h]<=(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk))){
                                    this.hasil=this.hitung.qty[h];
                                    this.hitung.qty[h]=parseInt(this.hitung.qty[h])-(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk));
                                }else if(this.hitung.qty[h]>(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk))){
                                    this.hasil=parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk);
                                    this.hitung.qty[h]=parseInt(this.hitung.qty[h])-(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk));
                                }
                                if(this.hitung.qty[h]<0){
                                    this.hitung.qty[h]=0;
                                }
                                    this.uplistsisa={qty_masuk:parseInt(this.hasil)+parseInt(this.listpo[j].qty_masuk)};
                                    axios.put("/api/listrso/"+this.listpo[j].id,this.uplistsisa)
                                    .then(res=>{
                                    axios.get("/api/listrso/data/listpo/"+this.aktif.nomor_po)
                                        .then(res=>{
                                            this.listsisa=res.data.data;
                                            for(let k=0;k<this.listsisa.length;k++){
                                                if(this.listsisa[k].sisapo<1){
                                                    this.uplist.po_close="Y";
                                                    axios.put("/api/listrso/data/"+this.listsisa[k].nomor_po+"/"+this.listsisa[k].kode_barang,this.uplist)
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
                                            for(let l=0;l<this.listsisa.length;l++){
                                                this.sipo+=parseInt(this.listsisa[l].sisapo);  
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
        )} */
        },
       /*  requestBcm(){   
        let jawab=confirm("Yakin ingin mengirim Bukti Checker Masuk ini?");
        if(jawab===true){
            this.up.status="sent";
            axios.post("/api/bcm",this.up)
            .then(res=>{
                for(let i=0;i<this.checker.length;i++){
                    this.uploadlist.kode_barang=this.checker[i].kode_barang;
                    this.uploadlist.qty=this.hitung.qty[i];
                    this.uploadlist.keterangan=this.hitung.keterangan[i];
                    this.uploadlist.nomor_bcm=this.up.bcm;
                    this.uploadlist.status="open";
                    this.uploood={kode_barang:this.uploadlist.kode_barang,nomor_bcm:this.uploadlist.nomor_bcm,
                    qty:this.uploadlist.qty,keterangan:this.uploadlist.keterangan,nomor_po:this.up.nomor_po,sisapo:this.checker[i].sisapo,
                    };
                    axios.post("/api/listbcm",this.uploood)
                    .then(res=>{ 
                    });
                }
                for(let h=0;h<this.checker.length;h++){
                axios.get("/api/view/detailpo/"+this.checker[h].nomor_po+"/"+this.checker[h].kode_barang)
                        .then(res=>{
                            this.listpo=res.data.data;
                            for(let j=0;j<this.listpo.length;j++){  
                                if(this.hitung.qty[h]<=(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk))){
                                    this.hasil=this.hitung.qty[h];
                                    this.hitung.qty[h]=parseInt(this.hitung.qty[h])-(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk));
                                }else if(this.hitung.qty[h]>(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk))){
                                    this.hasil=parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk);
                                    this.hitung.qty[h]=parseInt(this.hitung.qty[h])-(parseInt(this.listpo[j].qty_tdktersedia)-parseInt(this.listpo[j].qty_masuk));
                                }
                                if(this.hitung.qty[h]<0){
                                    this.hitung.qty[h]=0;
                                }
                                    this.uplistsisa={qty_masuk:parseInt(this.hasil)+parseInt(this.listpo[j].qty_masuk)};
                                    axios.put("/api/listrso/"+this.listpo[j].id,this.uplistsisa)
                                    .then(res=>{
                                    axios.get("/api/listrso/data/listpo/"+this.aktif.nomor_po)
                                        .then(res=>{
                                            this.listsisa=res.data.data;
                                            for(let k=0;k<this.listsisa.length;k++){
                                                if(this.listsisa[k].sisapo<1){
                                                    this.uplist.po_close="Y";
                                                    axios.put("/api/listrso/data/"+this.listsisa[k].nomor_po+"/"+this.listsisa[k].kode_barang,this.uplist)
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
                                            for(let l=0;l<this.listsisa.length;l++){
                                                this.sipo+=parseInt(this.listsisa[l].sisapo);  
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
        }, */
        validqty(index){
                this.pembanding="";
                this.aksicek="";
                if(parseInt(this.hitung.qty[index])>parseInt(this.checker[index].sisapo)){
                    this.cek[index]="error"
                }else{
                    this.cek[index]="tidak"
                }
                for(let i=0;i<this.checker.length;i++){
                    this.aksicek+=this.cek[i];
                    this.pembanding+="tidak";
                }
                if(this.aksicek===this.pembanding){
                    this.jenbutton=true;
                }else{
                    this.jenbutton=false;
                }

        }
    },
} 
</script>

<style>

</style>