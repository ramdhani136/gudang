<template>
    <div class="container">
        <div v-if="rlist.status=='Confirmed'" class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor SO :</label>
                    <input v-model="so.nomor_so" type="text" class="form-control col-12">
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="so.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select  v-model="rlist.kode_customer" name="customer" class="col-12 form-control" disabled>
                        <option v-for="custom in customers" :key="custom.kode" :value="custom.kode" >{{custom.nama}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Marketing</label>
                    <select  v-model="rlist.nip_sales" name="marketing" class="col-12 form-control" disabled>
                        <option v-for="sl in sales" :key="sl.nip" :value="sl.nip">{{sl.nama}}</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Tanggal Kirim :</label>
                    <input v-model="so.tanggal_kirim" type="date" @change="validateKirim()" :min="tglKirim()" class="form-control col-12">
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="so.keterangan"  name="keterangan" class="form-control col-12"></textarea>
                </div>
            </div>
        </div>
        <div v-if="rlist.status=='Confirmed'" v-for="rlist in form" :key="rlist.id" id="rsoverflowso" class="row mt-2 mx-auto">
            <div class="row float-left  ml-3 mt-4 label">Item Tersedia</div>
            <div v-if="rlist.status=='Confirmed'" id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{totalt+totald | currency}}</div>
            <div class="row mt-1 mx-auto col-12" v-for="rlist in form" :key="rlist.id">
                <Circle5 id="load3" v-if="load"></Circle5>
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th v-if="rlist.status=='Confirmed'">Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th v-if="rlist.status=='Sent'">Catatan</th>
                            <th v-if="rlist.status=='Confirmed'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(list,index) in listrso" :key="list.nomor_rso">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{list.nama_barang}}</td>
                            <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.qty_tersedia}}</td>
                            <td style="text-align:center">{{list.satuan}}</td>
                            <td style="text-align:center">{{list.harga | currency}}</td>
                            <td style="text-align:center">{{list.qty_tersedia*list.harga | currency}}</td>
                            <td v-if="rlist.status=='Sent'" style="text-align:center">{{list.catatan}}</td>
                            <td  v-if="rlist.status=='Confirmed'" style="text-align:center">
                                <button @click="deleteTersedia(list)"  class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                <div class="row float-left ml-3 mt-2 labelt">Item Tidak Tersedia</div>
                <div class="row mt-1 mx-auto  col-12" v-for="rlist in form" :key="rlist.id">
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th v-if="rlist.status=='Confirmed'">Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th v-if="rlist.status=='Sent'">Catatan</th>
                            <th v-if="rlist.status=='Confirmed'">Estimasi Kedatangan</th>
                            <th v-if="rlist.status=='Confirmed'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(list,index) in tidaktersedia" :key="list.nomor_rso">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{list.nama_barang}}</td>
                            <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.qty_tdktersedia}}</td>
                            <td style="text-align:center">{{list.satuan}}</td>
                            <td style="text-align:center">{{list.harga | currency}}</td>
                            <td style="text-align:center">{{list.qty_tdktersedia*list.harga | currency}}</td>
                            <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.tgl_datang}}</td>
                            <td v-if="rlist.status=='Sent'" style="text-align:center">{{list.catatan}}</td>
                            <td  v-if="rlist.status=='Confirmed'" style="text-align:center">
                                <button @click="deleteTdkTersedia(list)"  class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div  class="row mt-2"  v-for="rlist in form" :key="rlist.id">
                <button  @click="CreateSo(rlist)" v-if="rlist.status=='Confirmed'" class="btn-orange btn ml-3" >
                    Create SO
                </button>
        </div>
        <div class="row"  v-if="rlist.status!=='Confirmed'" v-for="rlist in form" :key="rlist.id">
        <div class="col-md-12">
            <div class="error-template">
                <h1>
                    Oops!</h1>
                <h2>
                    Gak bisa buka halaman ini broh</h2>
                <div class="error-details">
                    Data RSO ini belum di Konfirmasi atau sudah digunakan untuk penerbitan SO Lainnya
                </div>
                <div class="error-actions">
                        <router-link to="/so"  class="btn btn-primary btn-lg">
                                Lihat Data SO
                        </router-link>
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
            customers:{},  
            listrso:{},
            inputlrso:{
                nomor_rso:this.$route.params.id,
            },
            inprso:{},
            sales:{},
            urso:{},
            tidaktersedia:{},
            so:{
                nomor_so:this.so_nomor(),
                tanggal_so:this.now(),
                tanggal_kirim:this.tglKirim(),
            },
            statusso:{},
            nomorrso:'',
            load:true,
            totalt:0,
            subTotalt:0,
            totald:0,
            subTotald:0
        }
    },
    created(){
        this.getRso();
        this.getCustomer();
        this.getSales();
        this.getlistRso();
        this.getlistTdktersedia();
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
        getRso(){
            axios.get(`/api/rso/${this.$route.params.id}`)
            .then(res=>this.form=res.data.data)
        },
        getlistRso(){
            axios.get(`/api/listrso/data/dic/${this.$route.params.id}`)
            .then(res=>{this.listrso=res.data.data
                this.totalt=0;
                for (let i = 0; i < this.listrso.length; i++) {
                    this.subTotalt=parseInt(this.listrso[i].qty_tersedia)*parseInt(this.listrso[i].harga);
                    this.totalt += this.subTotalt;     
                }
            });
        },
        getlistTdktersedia(){
            axios.get(`/api/listrso/data/acc/${this.$route.params.id}`)
            .then(res=>{this.tidaktersedia=res.data.data
                this.totald=0;
                for (let i = 0; i < this.tidaktersedia.length; i++) {
                    this.subTotald=parseInt(this.tidaktersedia[i].qty_tdktersedia)*parseInt(this.tidaktersedia[i].harga);
                    this.totald += this.subTotald;     
                }
                this.load=false;
            });
        },
        deleteTersedia(list){
            let keputusan=confirm('Apakah anda yakin ingin menghapus barang ini?');
            if(keputusan===true){
                if(list.qty_tdktersedia>0){
                    this.urso.id=list.id;
                    this.urso.nomor_rso=list.nomor_rso;
                    this.urso.tanggal_rso=list.tanggal_rso;
                    this.urso.kode_barang=list.kode_barang;
                    this.urso.qty=list.qty-list.qty_tersedia;
                    this.urso.qty_tersedia="";
                    this.urso.qty_tdktersedia=list.qty_tdktersedia;
                    this.urso.tangga_datang=list.tangga_datang;
                    this.urso.acc_purch=list.acc_purch;
                    this.urso.status="Tidak Tersedia";
                    this.urso.catatan=list.catatan;
                    this.urso.booking=list.booking;
                    axios.put("/api/listrso/"+  this.urso.id,this.urso)
                    .then((response)=>{
                        this.getlistRso();
                        this.getlistTdktersedia();
                    })
                }else{
                    axios.delete("/api/listrso/" + list.id)
                    .then(response=>{
                    this.getlistRso();
                    this.getlistTdktersedia();
                })
                } 
            }
        },
        deleteTdkTersedia(list){
            let keputusan=confirm('Apakah anda yakin ingin menghapus barang ini?');
            if(keputusan===true){
                if(list.qty_tersedia>0){
                    this.urso.id=list.id;
                    this.urso.nomor_rso=list.nomor_rso;
                    this.urso.tanggal_rso=list.tanggal_rso;
                    this.urso.kode_barang=list.kode_barang;
                    this.urso.qty=list.qty-list.qty_tdktersedia;
                    this.urso.qty_tersedia=list.qty_tersedia;
                    this.urso.qty_tdktersedia="";
                    this.urso.tanggal_datang="";
                    this.urso.acc_purch="";
                    this.urso.status="Tersedia";
                    this.urso.catatan=list.catatan;
                    this.urso.booking=list.booking;
                    axios.put("/api/listrso/"+  this.urso.id,this.urso)
                    .then((response)=>{
                        this.getlistRso();
                        this.getlistTdktersedia();
                    })
                }else{
                    axios.delete("/api/listrso/" + list.id)
                    .then(response=>{
                    this.getlistRso();
                    this.getlistTdktersedia();
                })
                }       
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
        getCustomer(){
            axios.get("/api/customer")
            .then(res=>this.customers=res.data.data)
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
        validateKirim(){
            if(this.so.tanggal_kirim < this.tglKirim()){
                this.so.tanggal_kirim=this.tglKirim();
            }
        },
        so_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "SO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        CreateSo(rlist){
            let tanya=confirm('Apakah yakin ingin membuat SO ini?');
            if(tanya===true){
            this.so.nomor_rso=rlist.nomor_rso;
            axios.post("/api/so",this.so)
            .then((response)=>{
                this.nomorrso=rlist.nomor_rso;
                this.statusso.nomor_rso=rlist.nomor_rso;
                this.statusso.tanggal_rso=rlist.tanggal_rso;
                this.statusso.nip_sales=rlist.nip_sales;
                this.statusso.kode_customer=rlist.kode_customer;
                this.statusso.keterangan=rlist.keterangan;
                this.statusso.status="So";
                
                axios.put("/api/rso/"+  this.nomorrso,this.statusso)
                .then((response)=>{
                    this.$router.push({name:'rso'}) 
                })        
            })
            }
        },   
    },
}
</script>

<style>
    #rsoverflowso{
    width: 100%;
    max-height: 275px;
    overflow-y: scroll;
    border-top:solid 1px #dee2e6;
    }

    #load3{
        position: relative;
        margin: 0 auto; 
        margin-bottom:10px ; 
    }

    .label{
        border:solid 1px rgb(42, 199, 42);
        background-color: rgb(104, 226, 104);
        font-size: 16px;
        border-radius: 3px;
        padding: 5px;
        padding-left:1%;
        padding-right: 1%;
        color:#fff;
    }

    .labelt{
        border:solid 1px rgb(40, 131, 206);
        background-color: rgb(75, 161, 231);
        font-size: 16px;
        border-radius: 3px;
        padding: 5px;
        padding-left:1%;
        padding-right: 1%;
        color:#fff;
    }

    .error-template {padding: 40px 15px;text-align: center;}
    .error-actions {margin-top:15px;margin-bottom:15px;}
    .error-actions .btn { margin-right:10px; }



</style>