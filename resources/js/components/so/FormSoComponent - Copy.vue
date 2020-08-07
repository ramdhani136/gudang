<template>
    <div class="container">
        <div  class="row row-cols-2" v-for="vso in so" :key="vso.nomor_so">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor SO :</label>
                    <input v-model="vso.nomor_so" type="text" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="vso.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select v-model="vso.kode_customer" name="customer" class="col-12 form-control" disabled>
                        <option :value="vso.kode_customer">{{vso.customer}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Marketing</label>
                    <select v-model="vso.nip_sales" name="marketing" class="col-12 form-control" disabled>
                        <option :value="vso.nip_sales">{{vso.sales}}</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Tanggal Kirim :</label>
                    <input v-model="vso.tanggal_kirim" type="date" @change="validateKirim()" :min="tglKirim()" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="vso.keterangan"  name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
            </div>
        </div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <div class="row float-left  ml-3 mt-4 label">Item Tersedia</div>
            <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{totalt+totald | currency}}</div>
            <div class="row mt-1 mx-auto col-12">
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
                            <th v-for="vso in so" :key="vso.nomor_so"  v-if="vso.status=='Draft' || vso.status=='Tolak'" >Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ts,index) in Ltersedia" :key="ts.id">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{ts.nama_barang}}</td>
                            <td style="text-align:center">{{ts.qty_tersedia}}</td>
                            <td style="text-align:center">{{ts.satuan}}</td>
                            <td style="text-align:center">{{ts.harga | currency}}</td>
                            <td style="text-align:center">{{ts.harga*ts.qty_tersedia | currency}}</td>
                            <td v-if="vso.status=='Draft' || vso.status=='Tolak'"  v-for="vso in so" :key="vso.nomor_so" style="text-align:center">
                                <button @click="deleteTersedia(ts)" class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                <div class="row float-left ml-3 mt-2 labelt">Item Tidak Tersedia</div>
                <div class="row mt-1 mx-auto  col-12">
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th>Estimasi Kedatangan</th>
                            <th v-for="vso in so" :key="vso.nomor_so"  v-if="vso.status=='Draft' || vso.status=='Tolak'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ltt,index) in LTTersedia " :key="ltt.id">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{ltt.nama_barang}}</td>
                            <td style="text-align:center">{{ltt.qty_tdktersedia}}</td>
                            <td style="text-align:center">{{ltt.satuan}}</td>
                            <td style="text-align:center">{{ltt.harga | currency}}</td>
                            <td style="text-align:center">{{ltt.harga*ltt.qty_tdktersedia | currency}}</td>
                            <td style="text-align:center">{{ltt.tgl_datang}}</td>
                            <td v-for="vso in so" :key="vso.nomor_so"  v-if="vso.status=='Draft' || vso.status=='Tolak'" style="text-align:center">
                                <button @click="deleteTdkTersedia(ltt)"  class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(lso,index) in so" :key="index">
            <div v-if="vso.status=='Tolak'" v-for="vso in so" :key="vso.nomor_so" id="alastolak">
                <div v-for="(lso,index) in so" :key="index">
                    <b>{{lso.alastolak}}</b> 
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
            so:{},
            vso:{},
            Ltersedia:{},
            LTTersedia:{},
            tujuan:'',
            load:true,
            totalt:0,
            subTotalt:0,
            totald:0,
            subTotald:0,
            urso:{}
        }
    },
    created(){
        this.getSo();
        this.ListTersedia();
        this.ListTdkTersedia();
    },  
    computed:{       
    },
    methods:{
        getSo(){
            axios.get(`/api/so/${this.$route.params.id}`)
            .then(res=>this.so=res.data.data);
        },
        ListTersedia(){
            axios.get(`/api/so/${this.$route.params.id}`)
            .then(res=>{
                this.so=res.data.data;
                this.tujuan=this.so[0].nomor_rso;
                axios.get(`/api/listrso/data/dic/`+this.tujuan)
                .then(res=>{this.Ltersedia=res.data.data
                    this.totalt=0;
                    for (let i = 0; i < this.Ltersedia.length; i++) {
                    this.subTotalt=parseInt(this.Ltersedia[i].qty_tersedia)*parseInt(this.Ltersedia[i].harga);
                    this.totalt += this.subTotalt;     
                    }
                })
            });  
        },
        ListTdkTersedia(){
            axios.get(`/api/so/${this.$route.params.id}`)
            .then(res=>{
                this.so=res.data.data;
                this.tujuan=this.so[0].nomor_rso;
                axios.get(`/api/listrso/data/acc/`+this.tujuan)
                .then(res=>{this.LTTersedia=res.data.data
                    this.totald=0;
                    for (let i = 0; i < this.LTTersedia.length; i++) {
                    this.subTotald=parseInt(this.LTTersedia[i].qty_tdktersedia)*parseInt(this.LTTersedia[i].harga);
                    this.totald += this.subTotald;     
                    }
                    this.load=false;
                });
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
        validateKirim(){
            if(this.so.tanggal_kirim < this.tglKirim()){
                this.so.tanggal_kirim=this.tglKirim();
            }
        },
        deleteTersedia(ts){
            let keputusan=confirm('Apakah anda yakin ingin menghapus barang ini?');
            if(keputusan===true){
                if(ts.qty_tdktersedia>0){
                    this.urso.id=ts.id;
                    this.urso.nomor_rso=ts.nomor_rso;
                    this.urso.tanggal_rso=ts.tanggal_rso;
                    this.urso.kode_barang=ts.kode_barang;
                    this.urso.qty=ts.qty-ts.qty_tersedia;
                    this.urso.qty_tersedia="";
                    this.urso.qty_tdktersedia=ts.qty_tdktersedia;
                    this.urso.tangga_datang=ts.tangga_datang;
                    this.urso.acc_purch=ts.acc_purch;
                    this.urso.status="Tidak Tersedia";
                    this.urso.catatan=ts.catatan;
                    this.urso.booking=ts.booking;
                    axios.put("/api/listrso/"+  this.urso.id,this.urso)
                    .then((response)=>{
                        this.ListTersedia();
                        this.ListTdkTersedia();
                    })
                }else{
                    axios.delete("/api/listrso/" + ts.id)
                    .then(response=>{
                    this.ListTersedia();
                    this.ListTdkTersedia();
                })
                } 
            }
        },
        deleteTdkTersedia(ltt){
            let keputusan=confirm('Apakah anda yakin ingin menghapus barang ini?');
            if(keputusan===true){
                if(ltt.qty_tersedia>0){
                    this.urso.id=ltt.id;
                    this.urso.nomor_rso=ltt.nomor_rso;
                    this.urso.tanggal_rso=ltt.tanggal_rso;
                    this.urso.kode_barang=ltt.kode_barang;
                    this.urso.qty=ltt.qty-ltt.qty_tdktersedia;
                    this.urso.qty_tersedia=ltt.qty_tersedia;
                    this.urso.qty_tdktersedia="";
                    this.urso.tanggal_datang="";
                    this.urso.acc_purch="";
                    this.urso.status="Tersedia";
                    this.urso.catatan=ltt.catatan;
                    this.urso.booking=ltt.booking;
                    axios.put("/api/listrso/"+  this.urso.id,this.urso)
                    .then((response)=>{
                        this.ListTersedia();
                        this.ListTdkTersedia();
                    })
                }else{
                    axios.delete("/api/listrso/" + ltt.id)
                    .then(response=>{
                    this.ListTersedia();
                    this.ListTdkTersedia();
                })
                }       
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