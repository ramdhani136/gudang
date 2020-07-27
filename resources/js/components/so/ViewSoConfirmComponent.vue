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
            <div class="row mt-1 mx-auto col-12">
                <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ts,index) in Ltersedia" :key="ts.id">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{ts.nama_barang}}</td>
                            <td style="text-align:center">{{ts.qty_tersedia}}</td>
                            <td style="text-align:center">{{ts.satuan}}</td>
                            <td style="text-align:center">{{ts.catatan}}</td>
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
                            <th>Estimasi Kedatangan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ltt,index) in LTTersedia " :key="ltt.id">
                            <td style="text-align:center">{{index+1}}</td>
                            <td>{{ltt.nama_barang}}</td>
                            <td style="text-align:center">{{ltt.qty_tersedia}}</td>
                            <td style="text-align:center">{{ltt.satuan}}</td>
                            <td style="text-align:center">{{ltt.tgl_datang}}</td>
                            <td style="text-align:center">{{ltt.catatan}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(lso,index) in so" :key="index">
                <button v-if="lso.status=='Draft'" @click="confirmSO(lso)" class="btn-orange btn ml-3" >
                    Konfirmasi SO
                </button>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            so:{},
            vso:{},
            Ltersedia:{},
            LTTersedia:{},
            tujuan:'',
            up:{}
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
                .then(res=>this.Ltersedia=res.data.data)});  
        },
        ListTdkTersedia(){

            axios.get(`/api/so/${this.$route.params.id}`)
            .then(res=>{
                this.so=res.data.data;
                this.tujuan=this.so[0].nomor_rso;
                axios.get(`/api/listrso/data/acc/`+this.tujuan)
                .then(res=>this.LTTersedia=res.data.data)
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
        confirmSO(lso){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                this.up.nomor_so=lso.nomor_so;
                this.up.tanggal_so=lso.tanggal_so;
                this.up.tanggal_kirim=lso.tanggal_kirim;
                this.up.nomor_rso=lso.nomor_rso;
                this.up.keterangan=lso.keterangan;
                this.up.status="Acc";
                axios.put("/api/so/"+this.up.nomor_so,this.up)
                .then((response)=>{
                    this.$router.push({name:'soconfirm'})
                })
            }
        }
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