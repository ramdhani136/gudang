<template>
    <div class="container">
        <div class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
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
                    <label>keterangan</label>
                    <textarea  name="keterangan" class="form-control col-12"></textarea>
                </div>
            </div>
        </div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <div class="row float-left  ml-3 mt-4 label">Item Tersedia</div>
            <div class="row mt-1 mx-auto col-12" v-for="rlist in form" :key="rlist.id">
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th v-if="rlist.status=='Confirmed'">Qty</th>
                            <th>Satuan</th>
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
                <button v-if="rlist.status=='Confirmed'" class="btn-orange btn ml-3" >
                    Kirim SO
                </button>
        </div>
    </div>   
</template>

<script>
export default {
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
            },
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
            axios.get(`/api/listrso/${this.$route.params.id}`)
            .then(res=>this.listrso=res.data.data)
        },
        getlistTdktersedia(){
            axios.get(`/api/listrso/data/acc/${this.$route.params.id}`)
            .then(res=>this.tidaktersedia=res.data.data)
        },
        deleteTersedia(list){
            let keputusan=confirm('Apakah anda yakin ingin mengkonfirmasi RSO ini?');
            if(keputusan===true){
                this.urso.id=list.id;
                this.urso.nomor_rso=list.nomor_rso;
                this.urso.tanggal_rso=list.tanggal_rso;
                this.urso.kode_barang=list.kode_barang;
                this.urso.qty=list.qty;
                this.urso.qty_tersedia="";
                this.urso.qty_tdktersedia=list.qty_tdktersedia;
                this.urso.tangga_datang=list.tangga_datang;
                this.urso.acc_purch=list.acc_purch;
                this.urso.status=list.status;
                this.urso.catatan=list.catatan;
                this.urso.booking=list.booking;
                axios.put("/api/listrso/"+  this.urso.id,this.urso)
                .then((response)=>{
                    this.getlistRso();
                    this.getlistTdktersedia();
                })
            }
        },
        deleteTdkTersedia(list){
            let keputusan=confirm('Apakah anda yakin ingin mengkonfirmasi RSO ini?');
            if(keputusan===true){
                this.urso.id=list.id;
                this.urso.nomor_rso=list.nomor_rso;
                this.urso.tanggal_rso=list.tanggal_rso;
                this.urso.kode_barang=list.kode_barang;
                this.urso.qty=list.qty;
                this.urso.qty_tersedia=list.qty_tersedia;
                this.urso.qty_tdktersedia=list.qty_tersedia;
                this.urso.tanggal_datang="";
                this.urso.acc_purch="";
                this.urso.status=list.status;
                this.urso.catatan=list.catatan;
                this.urso.booking=list.booking;
                axios.put("/api/listrso/"+  this.urso.id,this.urso)
                .then((response)=>{
                    this.getlistRso();
                    this.getlistTdktersedia();
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
        so_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "SO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        }    
    },
}
</script>

<style>
    #rsoverflowso{
    width: 100%;
    max-height: 290px;
    overflow-y: scroll;
    border-top:solid 1px #dee2e6;
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


</style>