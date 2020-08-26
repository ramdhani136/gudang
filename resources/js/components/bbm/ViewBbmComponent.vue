<template>
    <div class="container">
        <div class="row row-cols-2" v-for="(up,index) in bbm" :key="index">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor BBM :</label>
                    <input v-model="up.bbm" type="text" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
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
                    <input v-model="ket.nomor_po" @click="showPo()" type="text" class="form-control" placeholder="Pilih Purchase Order" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Plat.No :</label>
                    <input v-model="up.nopol" type="text" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
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
                            <th>Checker</th>
                            <th>Jumlah Real</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(listbbm,index) in checker" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{listbbm.kode_barang}}</td>
                            <td>{{listbbm.nama_barang}}</td>
                            <td style="text-align:center">{{listbbm.satuan}}</td>
                            <td style="text-align:center">{{hitung.qty[index]}}</td>
                            <td  style="text-align:center">
                                <input  v-model="listbbm.qty" type="number" class="form-control" disabled>
                            </td>
                            <td style="text-align:center">
                                <textarea v-model="listbbm.keterangan" class="form-control" disabled></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2">
                <button @click="kembali()" class="btn-primary btn ml-4" >
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
                                <td style="text-align:center">{{ls.jumlah}}</td>
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
            bbm:{},
            poaktif:{},
            ket:{
                qty:[],
            },
            aktif:{},
            listsisa:{},
            checker:[],
            listbbm:{},
            uploadlist:[],
            hitung:{
                qty:[]
            },
            uploood:{},
            daftarbbm:{},
            masukan:{},
            listqty:[],
        }
    },
    created(){
        this.getPoAktif();
        this.getBbm();
        this.getlistbm();
        this.resetForm();
        this.default();
    },  
    computed:{  

    },
    methods:{
        getlistbm(){
            axios.get("/api/listbbm/"+this.$route.params.nomor)
            .then(res=>{
                this.checker=res.data.data;
                for(let i=0;i<this.checker.length;i++){
                    axios.get("/api/bcm/"+this.checker[i].bcm+"/"+this.checker[i].kode_barang)
                    .then(res=>{
                        this.daftarbbm=res.data.data;
                        this.masukan={qty:this.daftarbbm[0].qty};
                        this.listqty.push(this.masukan);
                        this.hitung.qty[i]=this.listqty[i].qty; 
                        this.checker.push([{qty:this.hitung.qty[i]}])
                        this.checker.splice(this.checker.length-1);
                        this.load=false;
                    });
                }
            }); 
            
        },
        getBbm(){
            axios.get("/api/bbm/"+this.$route.params.nomor)
            .then(res=>{
                this.bbm=res.data.data;
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
        bbm_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "BBM-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
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
            this.ket.nomor_po=aktif.nomor_po;
            axios.get("/api/listrso/data/listpo/"+aktif.nomor_po)
            .then(res=>{
                this.listsisa=res.data.data;
            });
        },
        checklist(){
            $("#modal-po").modal("hide");
        },
        default(){
            axios.get("/api/bbm/"+this.$route.params.nomor)
            .then(res=>{
                this.bbm=res.data.data;
                this.ket.nomor_po=this.bbm[0].nomor_po;
                this.ket.supplier=this.bbm[0].supplier;
            });
        },
        kembali(){
            this.$router.push({name:'ingoods'});
        },
    },
} 
</script>

<style>

</style>