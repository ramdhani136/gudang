<template>
    <div class="container" @keyup.esc="resetForm()">
        <button @click="showmodalPO()" class="btn btn-success my-3">+ Create PO</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Diminta</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(prlist , index) in FilteredPr" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{prlist.kode_barang}}</td>
                <td>{{prlist.nama}}</td>
                <td style="text-align:center">{{prlist.jumlah}}</td>
                <td style="text-align:center">{{prlist.satuan}}</td>
                <td style="text-align:center">
                    <button @click="showmodal(prlist)" class="btn btn-primary">Lihat Rincian</button>
                </td>
            </tr>
        </tbody>
    </table>
    <Circle5 id="load" v-if="load"></Circle5>
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
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prl,index) in prlist" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td style="text-align:center">{{prl.nomor_so}}</td>
                                    <td>{{prl.nama_customer}}</td>
                                    <td style="text-align:center">{{prl.qty_tdktersedia}}</td>
                                    <td style="text-align:center">{{prl.satuan}}</td>
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
        <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Purchase</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nomor PO</label>
                        <input v-model="upload.nomor_po" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input @change="validate()" :min="now()" v-model="upload.tanggal_po" type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Suplier</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="supply ? supply.nama:''"></div>
                        <div class="placeholder" v-if="supply==null">Pilih Supplier</div>
                        <div class="popover" v-show="visible">
                            <input type="text"
                            @keydown.up="up"
                            @keydown.down="down"
                            @keydown.enter="selectItem"
                            v-model="query"
                            placeholder="Masukan nama Supplier .."
                            >
                            <div class="option" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" 
                                    :key="match.kode"
                                    v-text="match.nama"
                                    :class="{'selected':(selected==index)}"
                                    @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Purchasing</label>
                        <select v-model="upload.nip_purchasing" class="form-control">
                            <option v-for="(purch,index) in purchasing"  :value="purch.nip" :key="index">{{purch.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Datang</label>
                        <input @change="validate()" :min="now()" v-model="upload.tanggal_datang" type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="upload.keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="resetForm()" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="CreatePo()" type="button" class="btn btn-orange">Create PO</button>
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
        return{
            search  : '',
            pr:[],
            load:true,
            listPr:{},
            tujuan:{},
            prlist:{},
            upload:{
                nomor_po:this.po_nomor(),
                tanggal_po:this.now(),
                tanggal_datang:this.now(),
            },
            visible:false,
            query:'',
            selected:0,
            supply:null,
            itemHeight:39,
            supplier:[],
            purchasing:{}
        }
    },
    created(){
        this.getPr();
        this.getSupplier();
        this.getPurchasing();
    },
    computed:{
        FilteredPr(){
            return this.pr.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        },
        matches(){
            if(this.query==''){
                return [];
            }
            return this.supplier.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods:{
        getSupplier(){
            axios.get("/api/supplier")
            .then(res=>{this.supplier=res.data.data;
            });
        },
        getPr(){
            axios.get("/api/listrso/data/group")
            .then(res=>{this.pr=res.data.data
                this.load=false;
            });
        },
        getPurchasing(){
            axios.get("/api/purchasing")
            .then(res=>{this.purchasing=res.data.data;
            });
        },
        showmodal(prlist){
            this.tujuan.kode=prlist.kode_barang;
            axios.get("/api/listrso/data/group/"+this.tujuan.kode)
            .then(res=>{this.prlist=res.data.data;

            });
            $("#modal-pr").modal("show");
        },
        showmodalPO(){
            $("#modal-po").modal("show");
        },now(){
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" + (day<10 ? '0' : '') + day;
            return output
            },
        validate(){
            if(this.form.tanggal_rso < this.now()){
                this.form.tanggal_rso=this.now();
            }
        },
        po_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "PO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        toggleVisible(){
            this.visible = !this.visible;
        },
        itemClicked(index){
            this.selected=index;
            this.selectItem();
        },
        selectItem(){
            this.supply = this.matches[this.selected];
            this.upload.kode_supplier= this.supply.kode_supplier;
            this.visible=false;
        },
        up(){
            if(this.selected==0){
                return;
            }
            this.selected -= 1;
            this.scrollToItem();
        },
        down(){
            if(this.selected >= this.matches.length -1 ){
                return;
            }
            this.selected += 1;
            this.scrollToItem();
        },
        scrollToItem(){
            this.$refs.optionList.scrollTop = this.selected * this.itemHeight;
        },
        resetForm(){
            this.upload={
                nomor_po:this.po_nomor(),
                tanggal_po:this.now(),
                tanggal_datang:this.now(),
            };
            this.supply=null;
            this.visible=false;
        },
        CreatePo(){
            this.upload.status="Draft";
            axios.post('/api/po',this.upload)
            .then(res=>{
            $("#modal-po").modal("hide");
            this.$router.push({name:'poCreateView',params:{nomor:this.upload.nomor_po}})
            });
        }
    }
}
</script>


<style>
    #scrollList{
        width: 100%;
        max-height:400px ;
        overflow-y: scroll;
        font-size: 0.9em;
    }
</style>