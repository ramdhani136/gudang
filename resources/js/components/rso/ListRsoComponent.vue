<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor RSO :</label>
                    <input v-model="upload.nomor_rso" type="text" class="form-control col-12" :disabled="status!=='Draft' && status!=='Confirmed' " >
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="upload.tanggal_rso" type="date" :min="now()" class="form-control col-12" :disabled="status!=='Draft' && status!=='Confirmed' " >
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <input @click="showcustomer()" v-model="namacustomer" type="text" class="form-control" placeholder="Pilih Customer" :disabled="listpr.length > 0">
                </div>
                <div class="form-group">
                    <label>Sales</label>
                    <select class="form-control" v-model="upload.nip_sales" :disabled="status!=='Draft' && status!=='Confirmed' " >
                        <option v-for="(sl,index) in sales" :key="index" :value="sl.nip">{{sl.nama}}</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="upload.keterangan" name="keterangan" class="form-control col-12" :disabled="status!=='Draft' && status!=='Confirmed' " ></textarea>
                </div>
            </div>
        </div>
        <div  id="total" class="mt-3 ml-auto mr-4">Total Invoice :&nbsp; {{invoice | currency}}</div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <button v-if="status==='Draft' || status==='Confirmed'" @click="showmodal()" class="btn btn-orange mt-2">+ Tambah Item</button>
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
                            <th>Qty</th>
                            <th v-if="status==='Draft'">Catatan</th>
                            <th v-if="status==='So' || status==='Confirmed'">Status</th>
                            <th v-if="status==='So' || status==='Confirmed'">Tersedia</th>
                            <th v-if="status==='So' || status==='Confirmed'">Tidak Tersedia</th>
                            <th v-if="status==='So' || status==='Confirmed'">Estimasi Kedatangan</th>
                            <th v-if="status==='Draft' || status==='Confirmed'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(lp,index) in listpr" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{lp.lkode_barang}}</td>
                            <td>{{lp.nama_barang}}</td>
                            <td style="text-align:center">{{lp.satuan}}</td>
                            <td style="text-align:center">{{lp.harga | currency}}</td>
                            <td  style="text-align:center">
                                <input style="text-align:center" :disabled="status!=='Draft' && status!=='Confirmed'  " @input="hitunginvoice()" v-model="hitung.qty[index]" type="number" class="form-control">
                            </td>
                            <td  v-if="status==='Draft'" style="text-align:center">
                                <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                            </td>
                            <td style="text-align:center" v-if="status==='So' || status==='Confirmed'">{{lp.status}}</td>
                            <td style="text-align:center" v-if="status==='So' || status==='Confirmed'">{{lp.qty_tersedia}}</td>
                            <td style="text-align:center" v-if="status==='So' || status==='Confirmed'">{{lp.qty_tdktersedia}}</td>
                            <td style="text-align:center" v-if="status==='So' || status==='Confirmed'">
                                <div v-if="lp.acc_purch==='Y'">{{lp.tgl_datang}}</div>
                                <button @click="aksiDetail(lp)" v-if="lp.acc_purch==='N'" style="text-align:center" class="btn btn-primary">Lihat</button>
                            </td>
                            <td v-if="status==='Draft' || status==='Confirmed'" >
                                <button @click="hapus(index)" style="text-align:center" class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2">
                <button  v-if="status==='Draft'" class="btn btn-primary ml-3" @click="createDraft()">
                    Simpan Draft
                </button>
                <button  v-if="status==='Draft'" @click="kirimrso()" class="btn-success btn ml-2" >
                    Kirim Permintaan
                </button>
                <button  v-if="status==='Confirmed'"  @click="requlang()" class="btn-none btn ml-3" >
                    Request Ulang
                </button>   
                <router-link v-if="status==='Confirmed'" to="/so/new" class="btn btn-success ml-2" >+ Create SO</router-link>
                <button  v-if="status==='Sent'" @click="reqedit()" class="btn-orange btn ml-3" >
                    Request Edit
                </button>  
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
                        <label>kode Barang</label>
                        <input v-model="ket.kode" type="text" name="qty"  autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="ket.satuan" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group" v-if="hs">
                        <label>Jenis Harga</label>
                        <select @change="selectItem()" class="form-control" v-model="jenisharga">
                            <option value="N">Default</option>
                            <option value="Y">Harga Spesial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input v-model="ket.harga" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="modal-footer" >
                    <button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="tambahList()" type="button" class="btn btn-primary">Input Item</button>
                </div>
                </div>
            </div>
        </div> 
        <div class="modal fade" id="modal-customer" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible2" v-text="custom2 ? custom2.nama:''"></div>
                        <div class="placeholder" v-if="custom2==null" v-text="ket2.nama">Pilih Customer</div>
                        <div class="popover" v-show="visible2">
                            <input type="text"
                            @keydown.up="up2"
                            @keydown.down="down2"
                            @keydown.enter="selectItem2"
                            v-model="query2"
                            placeholder="Masukan nama customer .."
                            >
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match2,index) in matches2" 
                                    :key="match2.kode"
                                    v-text="match2.nama"
                                    :class="{'selected':(selected2==index)}"
                                    @click="itemClicked2(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kode Customer</label>
                        <input type="text" class="form-control" disabled v-model="ket2.kode">
                    </div>
                </div>
                <div class="modal-footer" >
                    <button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="savecustomer()" class="btn btn-primary">Save Change</button>
                </div>
                </div>
            </div>
        </div> 
        <div >
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
                        <input  v-model="detail.qty_tdktersedia"  type="number" name="qty"  autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="detail.satuan" type="text"  class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="detail.alastolak" name="catatan"  class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            load:true,
            upload:{},
            listpr:[],
            hitung:{
                qty:[],
                keterangan:[],
                barang:{},
            },
            visible:true,
            query:'',
            selected:0,
            itemHeight:39,
            custom:null,
            ket:{
                nama:"Pilih Barang"
            },
            /* Customer */
            visible2:true,
            query2:'',
            selected2:0,
            itemHeight2:39,
            custom2:null,
            ket2:{
                nama:"Pilih Customer"
            },
            /* End */
            barangs:{},
            sales:{},
            customer:{},
            namacustomer:null,
            kodecustomer:null,
            item:false,
            jenisharga:'N',
            hs:false,
            hargaSpecial:{},
            invoice:0,
            subtotal:0,
            inhitung:{
                qty:[]
            },
            cek:'',
            init:'',
            banding:'',
            rso:{},
            listrso:{},
            status:'',
            detail:{}
        }
    },
    created(){
        this.getBarang();
        this.getrso();
    },  
    computed:{  
        matches(){
            if(this.query==''){
                return [];
            }
            return this.barang.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
        },
        matches2(){
            if(this.query2==''){
                return [];
            }
            return this.customer.filter((item)=> item.nama.toLowerCase().includes(this.query2.toLowerCase()))
        },
        Filteredlist(){
            return this.listpr.filter(elem => {
            return elem.lkode_barang.toLowerCase().includes(this.ket.kode.toLowerCase());
            })
        }
    },
    methods:{
        getBarang(){
            axios.get("/api/barang/")
            .then(res=>{
                this.barang=res.data.data
                axios.get("/api/sales")
                .then(res=>{
                    this.sales=res.data.data;
                    axios.get("/api/customer")
                    .then(res=>{
                        this.customer=res.data.data;
                        this.load=false;
                    })
                })
                })
            
        },
        getrso(){
            axios.get("/api/rso/"+this.$route.params.id)
            .then(res=>{
                this.rso=res.data.data;
                this.upload.nomor_rso=this.rso[0].nomor_rso;
                this.upload.tanggal_rso=this.rso[0].tanggal_rso;
                this.upload.nip_sales=this.rso[0].nip_sales;
                this.namacustomer=this.rso[0].customer;
                this.kodecustomer=this.rso[0].kode_customer;
                this.upload.keterangan=this.rso[0].keterangan;
                this.status=this.rso[0].status;
                axios.get("/api/listrso/"+this.$route.params.id)
                .then(res=>{
                    this.listpr=res.data.data;
                    for(let i=0;i<this.listpr.length;i++){
                        this.hitung.qty[i]=this.listpr[i].qty;
                        this.hitung.keterangan[i]=this.listpr[i].catatan;
                    }
                    this.hitunginvoice();
                })
            });
        },
        now(){
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" + (day<10 ? '0' : '') + day;
            return output
            },
        rso_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "RSO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
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
            this.custom = this.matches[this.selected];  
            this.ket.kode=this.custom.kode;

            axios.get("/api/view/price/"+this.kodecustomer+"/"+this.custom.kode)
                .then(res=>{this.hargaSpecial=res.data.data;
                if(this.hargaSpecial.length<1){
                    this.hs=false;
                    this.ket.harga=this.custom.harga;
                }else{
                        this.hs=true;
                    if(this.jenisharga=='N'){
                        this.visible=true;
                        this.visible=false;
                        this.ket.harga=this.custom.harga;
                        this.ket.id_custprice="";
                    }else{
                        this.hs=true;
                        this.visible=true;
                        this.visible=false;
                        this.ket.harga=this.hargaSpecial[0].harga;
                        this.ket.id_custprice=this.hargaSpecial[0].id;
                    }
                    
                }
                });    
            this.ket.satuan=this.custom.satuan;
            this.ket.nama=this.custom.nama;
            this.ket.harga=this.custom.harga;
            this.hs=true;
            this.visible=false;
            this.ket.id_custprice="";
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
        showmodal(){
            this.hs=false;
            this.jenisharga='N';
            $("#modal-form").modal("show");
        },
        tambahList(){
            if(this.listpr.length==0){
                this.barangs={id_custprice:this.ket.id_custprice,harga:this.ket.harga,lkode_barang:this.ket.kode,nama_barang:this.ket.nama,satuan:this.ket.satuan};
                this.listpr.push(this.barangs);
            }else{
                if(this.Filteredlist.length>0){
                    Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Item sudah diinput sebelumnya, hapus terlebih dahulu untuk memperbarui item!',
                    })
                }else{
                    if(this.listpr.length<=5){
                        this.barangs={id_custprice:this.ket.id_custprice,harga:this.ket.harga,lkode_barang:this.ket.kode,nama_barang:this.ket.nama,satuan:this.ket.satuan};
                        this.listpr.push(this.barangs);
                    }else{
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Maksimal input 6 item!',
                        })
                    }
                }
            }
            this.visible=true,
            this.query='',
            this.selected=0,
            this.custom=null,
            this.ket={
                nama:"Pilih Barang"
            },
            $("#modal-form").modal("hide");
        },
        kirimrso(){
                const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Kirim RSO ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    if(this.listpr.length>0){
                        this.init="";
                        this.cek="";
                        this.banding="";
                        for(let j=0;j<this.listpr.length;j++){
                            if(this.hitung.qty[j]===undefined || this.hitung.qty[j]==="" ){
                                this.init="N";
                            }else{
                                this.init="Y";
                            }
                            this.cek+=this.init;
                            this.banding+="Y";
                        }
                        if(this.cek===this.banding){
                            this.upload.status="Sent";
                            this.upload.kode_customer=this.kodecustomer;
                            axios.put("/api/rso/"+this.$route.params.id,this.upload)
                            .then(res=>{
                                axios.get("/api/listrso/"+this.upload.nomor_rso)
                                .then(res=>{
                                    this.listrso=res.data.data;
                                        for(let o=0;o<this.listrso.length;o++){
                                            axios.delete("/api/listrso/"+this.listrso[o].id)
                                        } 
                                        for(let i=0;i<this.listpr.length;i++){
                                            if(this.hitung.keterangan[i]===undefined){
                                                this.hitung.keterangan[i]="";
                                            }
                                                this.uplist={nomor_rso:this.upload.nomor_rso,tanggal_rso:this.upload.tanggal_rso
                                                ,kode_barang:this.listpr[i].lkode_barang,harga:this.listpr[i].harga,id_custprice:this.listpr[i].id_custprice,qty:this.hitung.qty[i]
                                                ,catatan:this.hitung.keterangan[i],}
                                                axios.post("/api/listrso",this.uplist)
                                        }
                                })
                                this.$router.push({name:'rso'}) 
                                swalWithBootstrapButtons.fire(
                                        'Save!',
                                        'Berhasil mengirimkan RSO.',
                                        'success'
                                )
                            }).catch(error=>{
                                Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Cek kembali rincian rso anda!',
                                })
                            })
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty item tidak boleh kosong',
                                })
                        }      
                        }else{
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput item apapun!',
                        })
                        } 
                } else if (

                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Batal mengirim RSO :)',
                    'error'
                    )
                }
            })
        },
        reqedit(){
                const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin merubah RSO ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    if(this.listpr.length>0){
                        this.init="";
                        this.cek="";
                        this.banding="";
                        for(let j=0;j<this.listpr.length;j++){
                            if(this.hitung.qty[j]===undefined || this.hitung.qty[j]==="" ){
                                this.init="N";
                            }else{
                                this.init="Y";
                            }
                            this.cek+=this.init;
                            this.banding+="Y";
                        }
                        if(this.cek===this.banding){
                            this.upload.status="Draft";
                            this.upload.kode_customer=this.kodecustomer;
                            axios.put("/api/rso/"+this.$route.params.id,this.upload)
                            .then(res=>{
                                axios.get("/api/listrso/"+this.upload.nomor_rso)
                                .then(res=>{
                                    this.listrso=res.data.data;
                                        for(let o=0;o<this.listrso.length;o++){
                                            axios.delete("/api/listrso/"+this.listrso[o].id)
                                        } 
                                        for(let i=0;i<this.listpr.length;i++){
                                            if(this.hitung.keterangan[i]===undefined){
                                                this.hitung.keterangan[i]="";
                                            }
                                                this.uplist={nomor_rso:this.upload.nomor_rso,tanggal_rso:this.upload.tanggal_rso
                                                ,kode_barang:this.listpr[i].lkode_barang,harga:this.listpr[i].harga,id_custprice:this.listpr[i].id_custprice,qty:this.hitung.qty[i]
                                                ,catatan:this.hitung.keterangan[i],}
                                                axios.post("/api/listrso",this.uplist)
                                        }
                                })
                                this.$router.push({name:'rso'}) 
                                swalWithBootstrapButtons.fire(
                                        'Save!',
                                        'Silahkan edit RSO ini di list draft RSO.',
                                        'success'
                                )
                            }).catch(error=>{
                                Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Cek kembali rincian rso anda!',
                                })
                            })
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty item tidak boleh kosong',
                                })
                        }      
                        }else{
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput item apapun!',
                        })
                        } 
                } else if (

                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Permintaan ini di batalkan :)',
                    'error'
                    )
                }
            })
        },
        requlang(){
                const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Semua item pada RSO ini akan di unbooking dan melakukan konfirmasi ulang oleh Inventory Control",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    if(this.listpr.length>0){
                        this.init="";
                        this.cek="";
                        this.banding="";
                        for(let j=0;j<this.listpr.length;j++){
                            if(this.hitung.qty[j]===undefined || this.hitung.qty[j]==="" ){
                                this.init="N";
                            }else{
                                this.init="Y";
                            }
                            this.cek+=this.init;
                            this.banding+="Y";
                        }
                        if(this.cek===this.banding){
                            this.upload.status="Sent";
                            this.upload.kode_customer=this.kodecustomer;
                            axios.put("/api/rso/"+this.$route.params.id,this.upload)
                            .then(res=>{
                                axios.get("/api/listrso/"+this.upload.nomor_rso)
                                .then(res=>{
                                    this.listrso=res.data.data;
                                        for(let o=0;o<this.listrso.length;o++){
                                            axios.delete("/api/listrso/"+this.listrso[o].id)
                                        } 
                                        for(let i=0;i<this.listpr.length;i++){
                                            if(this.hitung.keterangan[i]===undefined){
                                                this.hitung.keterangan[i]="";
                                            }
                                                this.uplist={nomor_rso:this.upload.nomor_rso,tanggal_rso:this.upload.tanggal_rso
                                                ,kode_barang:this.listpr[i].lkode_barang,harga:this.listpr[i].harga,id_custprice:this.listpr[i].id_custprice,qty:this.hitung.qty[i]
                                                ,catatan:this.hitung.keterangan[i],}
                                                axios.post("/api/listrso",this.uplist)
                                        }
                                })
                                this.$router.push({name:'rso'}) 
                                swalWithBootstrapButtons.fire(
                                        'Save!',
                                        'Berhasil mengirimkan ulang RSO.',
                                        'success'
                                )
                            }).catch(error=>{
                                Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Cek kembali rincian rso anda!',
                                })
                            })
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty item tidak boleh kosong',
                                })
                        }      
                        }else{
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput item apapun!',
                        })
                        } 
                } else if (

                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Batal mengirim ulang RSO :)',
                    'error'
                    )
                }
            })
        },
        createDraft(){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Simpan RSO ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    if(this.listpr.length>0){
                        this.init="";
                        this.cek="";
                        this.banding="";
                        for(let j=0;j<this.listpr.length;j++){
                            if(this.hitung.qty[j]===undefined || this.hitung.qty[j]==="" ){
                                this.init="N";
                            }else{
                                this.init="Y";
                            }
                            this.cek+=this.init;
                            this.banding+="Y";
                        }
                        if(this.cek===this.banding){
                            this.upload.status="Draft";
                            this.upload.kode_customer=this.kodecustomer;
                            axios.put("/api/rso/"+this.$route.params.id,this.upload)
                            .then(res=>{
                                axios.get("/api/listrso/"+this.upload.nomor_rso)
                                .then(res=>{
                                    this.listrso=res.data.data;
                                        for(let o=0;o<this.listrso.length;o++){
                                            axios.delete("/api/listrso/"+this.listrso[o].id)
                                        } 
                                        for(let i=0;i<this.listpr.length;i++){
                                            if(this.hitung.keterangan[i]===undefined){
                                                this.hitung.keterangan[i]="";
                                            }
                                                this.uplist={nomor_rso:this.upload.nomor_rso,tanggal_rso:this.upload.tanggal_rso
                                                ,kode_barang:this.listpr[i].lkode_barang,harga:this.listpr[i].harga,id_custprice:this.listpr[i].id_custprice,qty:this.hitung.qty[i]
                                                ,catatan:this.hitung.keterangan[i],}
                                                axios.post("/api/listrso",this.uplist)
                                        }
                                })
                                this.$router.push({name:'rso'}) 
                                swalWithBootstrapButtons.fire(
                                        'Save!',
                                        'Berhasil menyimpan RSO.',
                                        'success'
                                )
                            }).catch(error=>{
                                Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Cek kembali rincian rso anda!',
                                })
                            })
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty item tidak boleh kosong',
                                })
                        }      
                        }else{
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput item apapun!',
                        })
                        } 
                } else if (

                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Batal menyimpan RSO :)',
                    'error'
                    )
                }
            })
        },
        hapus(index){
            this.listpr.splice(index,1);
            this.hitung.qty.splice(index,1);
            this.hitung.keterangan.splice(index,1);
            this.hitunginvoice();
        },
        aksiDetail(lp){
            $("#modal-detail").modal("show");
            this.detail=lp;
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
        },
        showcustomer(){
            $("#modal-customer").modal("show");
        },
        /* pilih barang customer */
        toggleVisible2(){
            this.visible2 = !this.visible2;
        },
        itemClicked2(index){
            this.selected2=index;
            this.selectItem2();
        },
        selectItem2(){
            this.custom2 = this.matches2[this.selected2];  
            this.ket2.kode=this.custom2.kode;
            this.ket2.nama=this.custom2.nama;
            this.item=true;
            this.visible2=false;
        },
        up2(){
            if(this.selected2==0){
                return;
            }
            this.selected2 -= 1;
            this.scrollToItem2();
        },
        down2(){
            if(this.selected2 >= this.matches2.length -1 ){
                return;
            }
            this.selected2 += 1;
            this.scrollToItem2();
        },
        scrollToItem2(){
            this.$refs.optionList.scrollTop = this.selected2 * this.itemHeight2;
        },
        /* End */
        savecustomer(){     
            this.namacustomer= this.ket2.nama;
            this.kodecustomer= this.ket2.kode;
            this.upload.kode_customer= this.ket2.kode;
            $("#modal-customer").modal("hide"); 
        },
        pilihjenisharga(){
            console.log(this.ket.harga);
        },
        hitunginvoice(){
            this.subtotal=0;
            this.invoice=0;
            for(let i=0;i<this.listpr.length;i++){
                if(this.hitung.qty[i]===""){
                    this.inhitung.qty[i]=0;
                }else if(this.hitung.qty[i]===undefined){
                    this.inhitung.qty[i]=0;
                }else{
                    this.inhitung.qty[i]=this.hitung.qty[i];
                }
                this.subtotal=parseInt(this.listpr[i].harga)*parseInt( this.inhitung.qty[i])
                this.invoice+=this.subtotal;
            }
        }
    },
} 
</script>

<style>
    #total{
        width:30%;
        height: 50px;
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