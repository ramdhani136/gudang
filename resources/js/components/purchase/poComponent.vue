<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="Draft">Draft</option>
                <option value="Request">Request</option>
                <option value="Acc">Open</option>
                <option value="Tolak">Rejected</option>
                <option value="Selesai">Selesai</option>
                <option value="Di Selesaikan">Di Selesaikan</option>
            </select>
        </div> 
        <div class="row">
            <button  @click="showmodalPO()" class="btn btn-success my-3">+ Create PO</button>
        </div>   
            <div id="overflow" class="border-top">
            <table id="thead" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Estimasi Kedatangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(pl , index) in FilterKategori" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{pl.nomor_po}}</td>
                            <td style="text-align:center">{{pl.tanggal_po}}</td>
                            <td>{{pl.supplier}}</td>
                            <td style="text-align:center">{{pl.tanggal_datang}}</td>
                            <td  style="text-align:center">
                                <router-link :to="{name:'poCreateView',params:{nomor:pl.nomor_po}}" class="btn btn-primary" >
                                    Lihat Detail
                                </router-link>
                                <button @click="bukalagi(pl)" v-if="pl.status=='Di Selesaikan'" class="btn btn-orange">Reopen PO</button>
                                <button @click="getHapus(pl)" v-if="pl.status=='Draft'" class="btn btn-danger">Hapus</button>
                                <button @click="requestSelesai(pl)" v-if="pl.status=='Acc' && pl.rs==='N'" class="btn btn-orange">Request Selesai</button>
                                <button @click="batalselesai(pl)" v-if="pl.status=='Acc' && pl.rs==='Y'" class="btn btn-none">Batal Selesai</button>
                                <button  @click="infoTolak(pl)" v-if="pl.status=='Acc' && pl.rs==='T'" class="btn btn-danger">R. Selesai di tolak</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Circle5 id="load" v-if="load"></Circle5>
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
            <div class="modal fade" id="modal-selesai" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Permintaan PO Selesai</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="input.alasselesai" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimselesai()" class="btn btn-primary">kirim permintaan</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informasi Tolak Selesai PO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="alastolak" class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="requlang()" type="button" class="btn btn-orange">Request Ulang Selesai</button>
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
            status:'Acc',
            po:[],
            load:true,
            visible:false,
            query:'',
            selected:0,
            supply:null,
            itemHeight:39,
            supplier:[],
            purchasing:{},
            upload:{
                nomor_po:this.po_nomor(),
                tanggal_po:this.now(),
                tanggal_datang:this.now(),
            },
            file:{},
            input:{},
            tujuanid:0,
            alastolak:null,
        }
    },
    created(){
        this.getPo();
        this.getSupplier();
        this.getPurchasing();
    },
    computed:{
        FilterKategori(){
            if(this.search===""){
                if(this.status==="Draft"){
                    return this.po.filter(elem=> elem.status==="Draft")
                }else if(this.status==="Request"){
                    return this.po.filter(elem=> elem.status==="Request")
                }else if(this.status==="Acc"){
                    return this.po.filter(elem=> elem.status==="Acc")
                }else if(this.status==="Tolak"){
                    return this.po.filter(elem=> elem.status==="Tolak")
                }else if(this.status==="Selesai"){
                    return this.po.filter(elem=> elem.status==="Selesai")
                }else if(this.status==="Di Selesaikan"){
                    return this.po.filter(elem=> elem.status==="Di Selesaikan")
                }
            }else{
                return this.po.filter(elem => {
                return elem.nomor_po.toLowerCase().includes(this.search.toLowerCase());
            });
            }
        },
        matches(){
            if(this.query==''){
                return [];
            }
            return this.supplier.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods:{
        getPo(){
            axios.get("/api/po")
            .then(res=>{this.po=res.data.data
                this.load=false;
            });
        },
        getSupplier(){
            axios.get("/api/supplier")
            .then(res=>{this.supplier=res.data.data;
            });
        },
        getPurchasing(){
            axios.get("/api/purchasing")
            .then(res=>{this.purchasing=res.data.data;
            });
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
        },
        getHapus(pl){
            let jawab= confirm("Apakah anda yakin ingin menghapus PO Ini?");
            if(jawab===true){
                axios.delete("/api/po/"+pl.nomor_po)
                .then(res=>{
                    this.file.open_po="N";
                    this.file.harga_supplier=0;  
                    this.file.nomor_po="";
                        axios.put("/api/listrso/data/deletePo/"+pl.nomor_po,this.file)
                        .then(res=>{
                            this.getPo();
                            this.getSupplier();
                            this.getPurchasing();
                        });
                });
            }
        },
        requestSelesai(pl){
            this.tujuanid=pl.nomor_po;
            $("#modal-selesai").modal("show");
        },
        kirimselesai(){
            let tanya=confirm("Apakah anda yakin?");
            if(tanya===true){
                this.input.rs="Y";
                axios.put("/api/po/"+this.tujuanid,this.input)
                .then(res=>{
                    this.getPo();
                    this.getSupplier();
                    this.getPurchasing();
                    $("#modal-selesai").modal("hide");
                });
            }
        },
        batalselesai(pl){
            let tanya=confirm("Apakah anda yakin?");
            if(tanya===true){
            this.input.rs="N";
            this.input.alasselesai="";
                axios.put("/api/po/"+pl.nomor_po,this.input)
                .then(res=>{
                    this.getPo();
                    this.getSupplier();
                    this.getPurchasing();
                });
            }
        },
        infoTolak(pl){
            this.tujuanid=pl.nomor_po;
            this.alastolak=pl.alastolakrequest;
            $("#modal-tolak").modal("show");
        },
        requlang(){
            $("#modal-tolak").modal("hide");
            $("#modal-selesai").modal("show");
        },
        bukalagi(pl){
            let tanya=confirm("Apakah anda yakin");
            if(tanya===true){
                axios.put("/api/po/"+pl.nomor_po,{status:'Acc',rs:'N'})
                .then(res=>{
                    this.getPo();
                    this.getSupplier();
                    this.getPurchasing();
                })
            }
        }
    }
}
</script>


<style>

</style>