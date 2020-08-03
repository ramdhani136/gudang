<template>
    <div class="container" @keyup.esc="resetForm()">
        <div class="row row-cols-2" v-for="(lpo,index) in po" :key="index">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor PO :</label>
                    <input v-model="lpo.nomor_po"  type="text" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="lpo.tanggal_po" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Supplier</label>
                    <select  v-model="lpo.kode_supplier" name="customer" class="col-12 form-control" disabled>
                        <option :value="lpo.kode_supplier">{{lpo.supplier}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purchasing</label>
                    <select v-model="lpo.nip_purchasing"  class="col-12 form-control" disabled>
                        <option :value="lpo.nip_purchasing">{{lpo.purchasing}}</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Tanggal Datang :</label>
                    <input v-model="lpo.tanggal_datang" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="lpo.keterangan"   name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
            </div>
        </div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <button @click="showmodal()" class="row float-left  ml-3 mt-4 label">Ambil Permintaan</button>
            <div id="totalpo" class="mt-3 ml-auto mr-3">Total Invoice &nbsp; : &nbsp;{{totalPrice | currency}}</div>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(list,index) in listpo" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{list.nama}}</td>
                            <td style="text-align:center">{{list.jumlah}}</td>
                            <td style="text-align:center">{{list.satuan}}</td>
                            <td style="text-align:center">{{list.harga_supplier | currency}}</td>
                            <td style="text-align:center">{{list.subtotal | currency}}</td>
                            <td  style="text-align:center">
                                <button @click="showDetail(list)" class="btn btn-primary">Lihat Rincian</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div  class="row mt-2">
                <button   class="btn-orange btn ml-3" >
                    Kirim PO
                </button>
        </div>
        <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Customer</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Barang</label>
                        <select @change="getList()" v-model="chooseItem" class="form-control">
                            <option v-for="(prl,index) in pr" :key="index" :value="prl.kode_barang">{{prl.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-if="aktif" v-for="(fill,index) in FilteredSatuan" :key="index" v-model="fill.satuan" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody">
                    <table class="table mt-2 table-striped table-bordered" style="width:100%">
                        <thead  id="rsthead">
                            <tr>
                                <th style="text-align:center">No</th>
                                <th style="text-align:center">Nomor SO</th>
                                <th style="text-align:center">Customer</th>
                                <th style="text-align:center">Jumlah</th>
                                <th style="text-align:center">Tandai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(choosepr,index) in prlist" :key="index">
                                <td style="text-align:center">{{index+1}}</td>
                                <td style="text-align:center">{{choosepr.nomor_so}}</td>
                                <td>{{choosepr.nama_customer}}</td>
                                <td style="text-align:center">{{choosepr.qty_tdktersedia}}</td>
                                <td style="text-align:center">
                                    <input v-model="checked" type="checkbox" :value="choosepr.id">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input v-model="file.harga_supplier" type="number" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="TambahItem()" class="btn btn-primary">Save changes</button>
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
                                <tr v-for="(prl,index) in pOpen" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td style="text-align:center">{{prl.nomor_so}}</td>
                                    <td>{{prl.nama_customer}}</td>
                                    <td style="text-align:center">{{prl.qty_tdktersedia}}</td>
                                    <td style="text-align:center">{{prl.satuan}}</td>
                                    <td>
                                        <button @click="destroy(prl)" type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
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
            checked:[],
            pr:{},
            chooseItem:'',
            prlist:{},
            aktif:false,
            po:{},
            file:{},
            listpo:{},
            totalPrice:0,
            pOpen:{}
        }
    },
    created(){
        this.getPr();
        this.getPo();
        this.getListPo();
    },  
    computed:{  
            FilteredSatuan(){
            return this.pr.filter(elem => {
            return elem.kode_barang.toLowerCase().includes(this.chooseItem);
            })
        }
    },
    methods:{
        getListPo(){
            axios.get("/api/listrso/data/listpo/"+this.$route.params.nomor)
            .then(res=>{this.listpo=res.data.data;
                this.totalPrice=0;
                for (let i = 0; i < this.listpo.length; i++) {
                    this.subTotal=parseInt(this.listpo[i].subtotal);
                    this.totalPrice += this.subTotal;     
                }
                this.load=false;
            });
        },
        getPr(){
            axios.get("/api/listrso/data/group")
            .then(res=>{this.pr=res.data.data
            });
        },
        getList(){
            axios.get("/api/listrso/data/group/"+this.chooseItem)
            .then(res=>{this.prlist=res.data.data;
            this.aktif=true;
            })
        },
        getPo(){
            axios.get("/api/po/"+ this.$route.params.nomor)
            .then(res=>this.po=res.data.data);
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
        po_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "PO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        showmodal(){
            this.errors=[];
            $("#modal-form").modal("show");
        },
        resetForm(){
            this.aktif=false;
            this.chooseItem='';
            this.prlist={};
            this.checked=[];
            this.file={};
        },
        TambahItem(){
            this.file.nomor_po=this.$route.params.nomor;
            this.file.open_po="Y";
            for(let i=0;i<this.checked.length;i++){
                axios.put("/api/listrso/"+ this.checked[i],this.file)
                .then(res=>{
                    this.getPr();
                    this.getListPo();
                    this.resetForm();
                    $("#modal-form").modal("hide");
                });
            }
        },
        showDetail(list){
            this.tujuan=list.kode_barang;
            this.npo=this.$route.params.nomor;
            axios.get("/api/listrso/data/groupopen/"+this.npo+"/"+this.tujuan)
            .then(res=>{this.pOpen=res.data.data;
            });
            $("#modal-pr").modal("show");
        },
        destroy(prl){
            let keputusan = confirm("Yakin igin hapus ini?");
            if(keputusan===true){
                this.file.nomor_po="";
                this.file.open_po="N";
                this.file.harga_supplier=0;
                axios.put("/api/listrso/"+ prl.id,this.file)
                .then(res=>{
                    this.getPr();
                    this.getListPo();
                    this.resetForm();
                    $("#modal-form").modal("hide");
                });
            }
        }
    },
}
</script>

<style>
    #totalpo{
        width:30%;
        height: 40px;
        padding: 1%;
        border:solid 1px rgb(209, 209, 209);
        text-align: center;
        align-items: center;
        font-size: 1.1em;
        border-radius: 3px;
        color: #666;
        background-color:#fff ;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    #overflowBody{
        max-height: 220px;
        font-size: 0.9em;
        overflow-y: scroll;
    }
</style>