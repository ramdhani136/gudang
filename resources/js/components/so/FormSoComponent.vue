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
            <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{ket.subtotal | currency}}</div>
            <div class="row mt-1 mx-auto col-12">
                <Circle5 id="load3" v-if="load"></Circle5>
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr v-for="(lso,index) in so" :key="index">
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th>Status</th>
                            <th v-if="ket.status=='Tidak Tersedia'">Estimasi Tersedia</th>
                            <th v-if="lso.status=='Draft'">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ts,index) in listso" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{ts.nama_barang}}</td>
                            <td style="text-align:center" v-if="ket.status=='Tersedia'">{{ts.qty_tersedia}}</td>
                            <td style="text-align:center" v-if="ket.status=='Tidak Tersedia'">{{ts.qty_tdktersedia}}</td>
                            <td style="text-align:center">{{ts.satuan}}</td>
                            <td style="text-align:center">{{ts.harga | currency}}</td>
                            <td style="text-align:center" v-if="ket.status=='Tersedia'">{{ts.harga*ts.qty_tersedia | currency}}</td>
                            <td style="text-align:center" v-if="ket.status=='Tidak Tersedia'">{{ts.harga*ts.qty_tdktersedia | currency}}</td>
                            <td style="text-align:center">{{ket.status}}</td>
                            <td style="text-align:center" v-if="ket.status=='Tidak Tersedia'">{{ts.tgl_datang}}</td>
                            <td style="text-align:center" v-for="(lso,index) in so" :key="index" v-if="lso.status=='Draft'" >
                                <button @click="hapusListSo(ts)" class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(lso,index) in so" :key="index">
                <button v-if="lso.status=='Draft'" class="btn-orange btn ml-3" >
                    Simpan Draft
                </button>
                <button v-if="lso.status=='Draft'"  class="btn-success btn ml-1" >
                    Kirim SO
                </button>
        </div>
        <div v-if="vso.status=='Tolak'" v-for="vso in so" :key="vso.nomor_so" id="alastolak">
            <div v-for="(lso,index) in so" :key="index">
                <b>{{lso.alastolak}}</b> 
            </div>    
        </div>
        <div class="modal fade" id="modal-form" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Penolakan SO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="up.alastolak" class="form-control"></textarea>
                    </div>               
                </div>
                <div v-for="(lso,index) in so" :key="index" class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="tolakSo(lso)" class="btn btn-primary">Konfirmasi Tolak</button>
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
            so:{},
            vso:{},
            listso:{},
            tujuan:'',
            up:{},
            load:true,
            totalt:0,
            subTotalt:0,
            totald:0,
            subTotald:0,
            statusSo: {'so_open': 'Y'},
            ket:{
                qtypesan:0,
                subtotal:0,
            },
            listrso:{}
        }
    },
    created(){
        this.getSo();
        this.getlistso()
    },  
    computed:{       
    },
    methods:{
        showModal(){
            $("#modal-form").modal("show");
        },
        getSo(){
            axios.get(`/api/so/${this.$route.params.id}`)
            .then(res=>this.so=res.data.data);
        },
        getlistso(){
            axios.get("/api/so/"+this.$route.params.id)
            .then(res=>{
                this.so=res.data.data;
                if(this.so[0].statusso==="tersedia"){
                    axios.get(`/api/listrso/data/sotersedia/`+this.so[0].nomor_rso)
                    .then(res=>{
                        this.listso=res.data.data;
                        this.load=false;
                        for(let i=0;i<this.listso.length;i++){
                            this.ket.qtypesan=this.listso[i].qty_tersedia;
                            this.ket.subtotal+=parseInt(this.listso[i].qty_tersedia)*parseInt(this.listso[i].harga);
                            this.ket.status="Tersedia";
                        }     
                    });
                }else{
                    axios.get(`/api/listrso/data/sott/`+this.so[0].nomor_rso)
                    .then(res=>{
                        this.listso=res.data.data;
                        this.load=false;
                        for(let i=0;i<this.listso.length;i++){
                            this.ket.qtypesan=this.listso[i].qty_tdktersedia;
                            this.ket.subtotal+=parseInt(this.ket.qtypesan)*parseInt(this.listso[i].harga);
                            this.ket.status="Tidak Tersedia";
                            this.ket.tanggal=this.listso[i].tgl_datang;
                        }  
                    });
                }
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
        hapusListSo(lso){
            let keputusan=confirm("Yakin ingin menghapus item ini dari SO?");
            if(keputusan===true){
                axios.get(`/api/so/${this.$route.params.id}`)
                .then(res=>this.so=res.data.data);
                if(this.so[0].statusso==="tersedia"){
                    alert(lso.id);
                }else{
                    alert(lso.id);
                }
            }
        }
    },
}
</script>

<style>

</style>