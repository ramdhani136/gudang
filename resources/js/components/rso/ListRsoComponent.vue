<template>
    <div class="container" @keyup.esc="resetform()">
        <div class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
            <div class="col-4">
                    <input v-if="disabled"  v-model="rlist.status" type="hidden"  :disabled="disabled == 1">
                    <input v-if="!disabled"  v-model="inprso.status" type="hidden" >
                <div class="form-group">
                    <label>Nomor Rso :</label>
                    <input v-if="disabled"  v-model="rlist.nomor_rso" type="text" class="form-control col-12" :disabled="disabled == 1">
                    <input v-if="!disabled"  v-model="inprso.nomor_rso" type="text" class="form-control col-12">
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-if="disabled" v-model="rlist.tanggal_rso"  type="date" class="form-control col-12" :disabled="disabled == 1">
                    <input v-if="!disabled" v-model="inprso.tanggal_rso"  type="date" class="form-control col-12" :disabled="disabled == 0" >
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select v-if="disabled"  v-model="rlist.kode_customer" name="customer" class="col-12 form-control" :disabled="disabled == 1">
                        <option v-for="custom in customers" :key="custom.kode" :value="custom.kode" >{{custom.nama}}</option>
                    </select>
                    <div v-if="!disabled">
                    <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisiblecust" v-text="custom2 ? custom2.nama:''"></div>
                        <div class="placeholder" v-if="custom2==null" v-text="ketcust.customer">Pilih Customer</div>
                        <div class="popovercs" v-show="visiblecust">
                            <input type="text"
                            @keydown.up="upcust"
                            @keydown.down="downcust"
                            @keydown.enter="selectItemCust"
                            v-model="query2"
                            placeholder="Masukan nama customer .."
                            >
                            <div class="optionbr" ref="optionListcust">
                                <ul>
                                    <li v-for="(match,index) in custmatches" 
                                    :key="match.kode"
                                    v-text="match.nama"
                                    :class="{'selected':(selected==index)}"
                                    @click="itemClickedCust(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Marketing</label>
                    <select  v-if="disabled"  v-model="rlist.nip_sales" name="marketing" class="col-12 form-control" :disabled="disabled == 1">
                        <option v-for="sl in sales" :key="sl.nip" :value="sl.nip">{{sl.nama}}</option>
                    </select>
                    <select v-if="!disabled" v-model="inprso.nip_sales" name="marketing" class="col-12 form-control" >
                        <option v-for="sl in sales" :key="sl.nip" :value="sl.nip">{{sl.nama}}</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-if="disabled" v-model="rlist.keterangan"  name="keterangan" class="form-control col-12" :disabled="disabled == 1"></textarea>
                    <textarea v-if="!disabled" v-model="inprso.keterangan"  name="keterangan" class="form-control col-12" ></textarea>
                </div>
                <div v-if="rlist.status=='Draft'" class="form-group">
                    <button @click="getdisabled(rlist)" class="btn btn-primary col-4 ">{{tombol}}</button>
                    <button @click="updateRso()" v-if="tbsukses" class="btn btn-success col-4 ml-1">Update</button>
                </div>
            </div>
        </div>
        <div class="row m-auto" v-for="rlist in form" :key="rlist.id">
            <button v-if="rlist.status=='Draft'" @click="showmodal()" type="button" class="btn btn-success mt-3">+ Tambah Barang</button>
        </div>
        <div id="rsoverflow" class="row mt-2 mx-auto" v-for="rlist in form" :key="rlist.id">
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Diminta</th>
                        <th>Satuan</th>
                        <th v-if="rlist.status=='Sent'">Catatan</th>
                        <th v-if="rlist.status=='Confirmed'">Status</th>
                        <th v-if="rlist.status=='Confirmed'">Tersedia</th>
                        <th v-if="rlist.status=='Confirmed'">Tidak Tersedia</th>
                        <th v-if="rlist.status=='Confirmed'">Estimasi Kedatangan</th>
                        <th v-if="rlist.status=='Draft'">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(list,index) in listrso" :key="list.nomor_rso">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{list.nama_barang}}</td>
                        <td style="text-align:center">{{list.qty}}</td>
                        <td style="text-align:center">{{list.satuan}}</td>
                        <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.status}}</td>
                        <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.qty_tersedia}}</td>
                        <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.qty_tdktersedia}}</td>
                        <td style="text-align:center" v-if="rlist.status=='Confirmed'" >
                            {{list.tgl_datang}}
                            <button v-if="list.acc_purch=='N'" @click="aksiDetail(list)" class="btn btn-orange">Detail Penolakan</button>
                        </td>
                        <td v-if="rlist.status=='Sent'" style="text-align:center">{{list.catatan}}</td>
                        <td  v-if="rlist.status=='Draft'" style="text-align:center">
                            <button @click="editListRso(list)"  class="btn btn-primary">Edit</button>
                            <button @click="deleteListRso(list)"  class="btn btn-danger">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Circle5 id="load2" v-if="load"></Circle5>
        </div>
        <div  class="row mt-2"  v-for="rlist in form" :key="rlist.id">
                <router-link :to="{name:'createso',params:{id:rlist.nomor_rso}}" v-if="rlist.status=='Confirmed'" class="btn-success btn ml-3" >
                    Proses SO
                </router-link>
        </div>
        <div  class="row mt-2"  v-for="rlist in form" :key="rlist.id">
                <router-link :to="{name:'createso',params:{id:rlist.nomor_rso}}" v-if="rlist.status=='So'" class="btn-orange btn ml-3" >
                    Lihat Sales Order
                </router-link>
        </div>
        <div class="row mt-2"  v-for="rlist in form" :key="rlist.id">
            <button v-if="rlist.status=='Draft'"  @click="updateStatus()" class="btn-orange btn ml-3">Kirim RSO</button>
        </div>
        <div class="modal fade" id="modal-form" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="custom ? custom.nama:''"></div>
                        <div class="placeholder" v-if="custom==null" v-text="ket.nama">Pilih Barang</div>
                        <div class="popover" v-show="visible">
                            <input type="text"
                            @keydown.up="up"
                            @keydown.down="down"
                            @keydown.enter="selectItem"
                            v-model="query"
                            placeholder="Masukan nama barang .."
                            >
                            <div class="optionbr" ref="optionList">
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
                        <label>Jumlah</label>
                        <input v-model="inputlrso.qty"  type="number" name="qty"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="ket.satuan"  type="text"  class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea v-model="inputlrso.catatan" name="catatan"  class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer" v-for="rlist in form" :key="rlist.nomor_rso">
                    <button type="button" @click="resetform()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button"  @click="createListRso(rlist)" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div>
        


        <div  v-for="list in listrso" :key="list.nomor_rso">
        <div class="modal fade" id="modal-detail" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Penolakan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input v-model="detail.nama_barang" type="text"  class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input  v-model="detail.qty"  type="number" name="qty"  autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="detail.satuan" type="text"  class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="detail.alastolak" name="catatan"  class="form-control" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label>Aksi</label>
                        <select v-model="aksi" class="form-control">
                            <option value="N">Tidak Ada</option>
                            <option value="Y">Request Ulang</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label v-if="aksi=='Y'">Jumlah</label>
                        <input v-model="upDetail.qty_tdktersedia"  v-if="aksi=='Y'" type="number" name="qty"  autocomplete="off" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-if="aksi=='Y'" type="button" @click="kirimPurch(list)"  class="btn btn-primary">Kirim Permintaan</button>
                </div>
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
            form:{},
            tbsukses:false,
            tombol:'Edit RSO',
            disabled:1,
            customers:{},  
            listrso:{},
            inputlrso:{
                nomor_rso:this.$route.params.id,
            },
            barang:{},
            edit:false,
            inprso:{},
            sales:{},
            urso:{},
            ket:{
                nama:"Pilih Barang"
            },
            visible:false,
            visiblecust:false,
            query:'',
            query2:'',
            selected:0,
            custom:null,
            custom2:null,
            itemHeight:39,
            ketcust:{},
            aksi:'N',
            detail:{},
            upDetail:{
                qty:0,
            },
            qtyupdate:0,
            statusup:{},
            load:true,
        }
    },
    created(){
        this.getRso();
        this.getCustomer();
        this.getSales();
        this.getlistRso();
        this.getBarang();
        this.getSales();
    },  
    computed:{
        matches(){
            if(this.query==''){
                return [];
            }
            return this.barang.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
        },
        custmatches(){
            if(this.query2==''){
                return [];
            }
            return this.customers.filter((item)=> item.nama.toLowerCase().includes(this.query2.toLowerCase()))
        }
        
    },
    methods:{
        getdisabled(rlist){
            this.disabled = (this.disabled + 1) % 2;
            if(this.disabled==1){
                this.tombol="Edit Rso";
                this.tbsukses=false;
                this.getRso()
                this.inprso.nomor_rso=rlist.nomor_rso
                this.inprso.tanggal_rso=rlist.tanggal_rso  
                this.inprso.nip_sales=rlist.nip_sales 
                this.inprso.kode_customer=rlist.kode_customer 
                this.inprso.keterangan=rlist.keterangan 
    
            }else{
                this.tombol="Close";
                this.tbsukses=true;
                this.getRso()
                this.inprso.status=rlist.status
                this.inprso.nomor_rso=rlist.nomor_rso
                this.inprso.tanggal_rso=rlist.tanggal_rso  
                this.inprso.nip_sales=rlist.nip_sales 
                this.inprso.kode_customer=rlist.kode_customer 
                this.inprso.keterangan=rlist.keterangan 
                this.inprso.kode_customer=rlist.kode_customer
                this.ketcust.customer=rlist.customer
                this.visiblecust=false
            }
        },
        getRso(){
            axios.get(`/api/rso/${this.$route.params.id}`)
            .then(res=>this.form=res.data.data);
        },
        updateRso(){
            this.getRso()
            axios.put(`/api/rso/${this.$route.params.id}`,this.inprso)
            .then((response)=>{
                    this.getRso()
                    this.getlistRso()
                    this.getBarang()
                    this.tbsukses=false
                    this.disabled=1
                    this.tombol="Edit RSO"
                    this.$router.push({name:'rso'})
                })
        },
        showmodal(){
            $("#modal-form").modal("show");
        },
        showDetail(){
            $("#modal-detail").modal("show");
        },
        getlistRso(){
            axios.get(`/api/listrso/${this.$route.params.id}`)
            .then(res=>{this.listrso=res.data.data
                this.load=false;
            });
        },
        getBarang(){
            axios.get("/api/barang/")
            .then(res=>this.barang=res.data.data)
        },
        createListRso(rlist){
            if(this.edit===false){
                this.inputlrso.tanggal_rso=rlist.tanggal_rso
                axios.post("/api/listrso",this.inputlrso)
                .then((response)=>{
                    this.getRso();
                    this.getlistRso();
                    this.getBarang();
                    this.resetform();
                    $("#modal-form").modal("hide");
                })
                
            }else{
                this.inputlrso.tanggal_rso=rlist.tanggal_rso
                axios.put("/api/listrso/"+  this.inputlrso.id,this.inputlrso)
                .then((response)=>{
                    this.resetform()
                    this.getRso();
                    this.getlistRso();
                    this.getBarang(); 
                    $("#modal-form").modal("hide");
                })
            }
        },
        editListRso(list){
            this.getlistRso();
            this.inputlrso.id=list.id
            this.ket.satuan=list.satuan
            this.ket.nama=list.nama_barang
            this.inputlrso.nomor_rso=list.lno_rso
            this.inputlrso.kode_barang=list.lkode_barang
            this.inputlrso.qty=list.qty
            this.inputlrso.catatan=list.catatan
            this.edit=true
            this.showmodal()
        },
        deleteListRso(list){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/listrso/" + list.id)
                .then(response=>{
                    this.getRso();
                    this.getlistRso();
                    this.getBarang();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        getSales(){
            axios.get("/api/sales")
            .then(res=>this.sales=res.data.data)
        },
        updateStatus(){
            let tanya=confirm('Apakah yakin ingin mengirim RSO ini ke DIC?');
            if(tanya===true){
                this.urso.status="Sent"
                axios.put(`/api/rso/${this.$route.params.id}`,this.urso)
                .then((response)=>{
                    this.$router.push({name:'rso'}) 
                })
            }
        },
        resetform(){
            this.getlistRso()
            this.inputlrso.id=""
            this.ket.satuan=""
            this.inputlrso.kode_barang=""
            this.inputlrso.qty=""
            this.inputlrso.catatan=""
            this.ket.nama="Pilih Barang"
            this.custom=null
            this.edit=false
            this.visible=false
        },
        toggleVisible(){
            this.visible = !this.visible;
        },
        toggleVisiblecust(){
            this.visiblecust = !this.visiblecust;
        },
        itemClicked(index){
            this.selected=index;
            this.selectItem();
        },
        selectItem(){
            this.custom = this.matches[this.selected];
            this.inputlrso.kode_barang= this.custom.kode;
            this.ket.satuan= this.custom.satuan;
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
        
        itemClickedCust(index){
            this.selected=index;
            this.selectItemCust();
        },
        selectItemCust(){
            this.custom2 = this.custmatches[this.selected];
            this.inprso.kode_customer=this.custom2.kode
            this.visiblecust=false;
        },
        upcust(){
            if(this.selected==0){
                return;
            }
            this.selected -= 1;
            this.scrollToItem();
        },
        downcust(){
            if(this.selected >= this.custmatches.length -1 ){
                return;
            }
            this.selected += 1;
            this.scrollToItem();
        },
        scrollToItemcust(){
            this.$refs.optionListCust.scrollTop = this.selected * this.itemHeight;
        },
        getCustomer(){
            axios.get("/api/customer")
            .then(res=>this.customers=res.data.data)
        },
        aksiDetail(list){
            this.getlistRso();
            this.upDetail.id=list.id
            this.upDetail.nomor_rso=list.lno_rso;
            this.upDetail.tanggal_rso=list.tanggal_rso;
            this.upDetail.kode_barang=list.lkode_barang;
            this.upDetail.qty_tersedia=list.qty_tersedia;
            this.upDetail.status=list.status;
            this.upDetail.alastolak="";
            this.upDetail.acc_purch="";
            this.upDetail.booking="N";
            this.detail.nama_barang=list.nama_barang;
            this.detail.qty=list.qty_tdktersedia;
            this.detail.satuan=list.satuan;
            this.detail.kode_barang=list.lkode_barang;
            this.detail.alastolak=list.alastolak;
            this.statusup.status="Sent";
            this.statusup.nomor_rso=list.lno_rso;
            this.statusup.tanggal_rso=list.tanggal_rso;
            this.statusup.nip_sales=list.nip_sales;
            this.statusup.kode_customer=list.kode_customer;
            this.statusup.keterangan=list.so_ket;
            this.showDetail(); 
        },
        kirimPurch(list){
            let tanya=confirm('Yakin kirim ulang permintaan?');
            if(tanya==true){
                if(this.upDetail.qty_tersedia===null){
                this.qtyupdate=0;
                }else{
                this.qtyupdate=this.upDetail.qty_tersedia;
                }
                this.upDetail.qty=parseInt(this.upDetail.qty_tdktersedia)+parseInt(this.qtyupdate);
            
                axios.put("/api/listrso/"+this.upDetail.id,this.upDetail)
                .then(response=>{
                    axios.put("/api/rso/"+this.upDetail.nomor_rso,this.statusup)
                    .then(response=>{
                        $("#modal-detail").modal("hide");
                        this.$router.push({name:'rso'})
                    })
                })
            }
        }
    },

}
</script>

<style>
    #load2{
        position: relative;
        margin: 0 auto; 
        margin-top:60px ;  
    }

    #rsoverflow{
    width: 100%;
    max-height: 240px;
    overflow-y: scroll;
    border-top:solid 1px #dee2e6;
    }

    #rsthead thead tr th{
        text-align: center;
        border-bottom: none;
        position: sticky; top: 0; 
        background-color: #fff;
        top: -1px;
        border-collapse: collapse;
        box-shadow: inset 0 0 0 #dee2e6,
        inset 0 -1px 0 #dee2e6;
    }

    .btn-orange{
        background-color: lightsalmon;
        border:solid 1px rgb(247, 141, 99);
        color: white;
    }

    .btn-orange:hover{
        background-color: rgb(253, 143, 100);
        border:solid 1px rgb(243, 127, 81);
        color: white;
    }


    .optionbr{
        max-height: 350px;
        overflow-y: scroll;
        margin-top: 5px;
    }

    .optionbr ul{
        list-style-type:none;
        text-align: left;
        padding-left:0;
    }

    .optionbr ul li{
        border-bottom: 1px solid lightgrey;
        padding:10px;
        cursor: pointer;
        background-color: #f1f1f1;
    }

    .optionbr ul li.selected{
        background-color: #58bd4c;
        color: #fff;
    }

    .popovercs{
        max-width: 92%;
        min-height: 50px;
        border:1px solid lightgray;
        position: absolute;
        z-index: 800;
        top:70px;
        left: 4%;
        right: 0;
        background-color: #fff;
        border-radius: 3px;
        text-align: center;
    }

    .popovercs input{
        width:95%;
        margin-top: 5px;
        height: 40px;
        font-size: 14px;
        border-radius: 3px;
        border:solid 1px lightgray;
        padding-left: 8px;
    }

    
</style>