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
                <div class="form-group">
                    <label>Tanggal Kirim :</label>
                    <input v-model="vso.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
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
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="vso.keterangan"  name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Distribusi :</label>
                    <select class="form-control" v-model="vso.distribusi" @click="ifkirim()" @change="aksidistribusi()" disabled>
                        <option value="default">- Masukan pilihan anda -</option>
                        <option value="kirim">Di Kirim</option>
                        <option value="ambil">Ambil Sendiri</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lokasi</label>
                    <input @click="clikdistribusi()" v-model="vso.lokasi" name="alamat" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea v-model="vso.alamat" name="alamat" class="form-control col-12" disabled></textarea>
                </div>
            </div>
        </div>
        <div id="rsoverflowso" class="row mt-2 mx-auto">
            <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{ket.subtotal | currency}}</div>
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
                            <th>Status</th>
                            <th>Sudah kirim</th>
                            <th v-if="ket.status=='Tidak Tersedia'">Estimasi Tersedia</th>
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
                            <td style="text-align:center">{{sk.qty[index]}}</td>
                            <td style="text-align:center" v-if="ket.status=='Tidak Tersedia'">{{ts.tgl_datang}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(lso,index) in so" :key="index">
                <button v-if="lso.status=='Sent'" @click="confirmSO(lso)" class="btn-orange btn ml-3" >
                    Terima SO
                </button>
                <button @click="showModal()" v-if="lso.status=='Sent'"  class="btn-danger btn ml-1" >
                    Tolak SO
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
            uplist:[],
            aksiup:{},
            sk:{
                qty:[],
            },
        }
    },
    created(){
        this.getSo();
        this.getlistso();
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
                            this.sk.qty[i]=this.listso[i].bbk_tersedia ;
                            this.uplist[i]={
                                id:this.listso[i].id,
                                statusso:this.listso[i].statusso,
                            };     
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
                             this.sk.qty[i]=this.listso[i].bbk_tdktersedia ;
                            this.ket.tanggal=this.listso[i].tgl_datang;
                            this.uplist[i]={
                                id:this.listso[i].id,
                                statusso:"Y",
                            };
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
        confirmSO(lso){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ml-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin menerima SO ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya, Yakin!',
            cancelButtonText: 'Tidak!',
            reverseButtons: true
            }).then((result) => {
            if (result.value) {
                this.up.nomor_so=lso.nomor_so;
                this.up.tanggal_so=lso.tanggal_so;
                this.up.tanggal_kirim=lso.tanggal_kirim;
                this.up.nomor_rso=lso.nomor_rso;
                this.up.keterangan=lso.keterangan;
                this.up.status="Acc";
                axios.put("/api/so/"+this.up.nomor_so,this.up)
                .then((response)=>{
                    this.statusSo={'so_open': 'Y'};
                    axios.put("/api/listrso/data/sopen/"+ this.up.nomor_rso,this.statusSo)
                    .then(res=>{
                        this.$router.push({name:'soconfirm'})
                    });
                    for(let d=0;d<this.uplist.length;d++){
                        this.aksiup={statusso:this.uplist[d].statusso,dateaccso:this.DateTime(),};
                        axios.put("/api/listrso/"+this.uplist[d].id,this.aksiup)
                        .then(res=>{
                        });
                    }
                    swalWithBootstrapButtons.fire(
                    'Selesai!',
                    'SO Berhasil di terima !',
                    'success'
                    )
                })  
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Batal',
                'SO Batal diterima :)',
                'error'
                )
            }
            })
        },
        tolakSo(lso){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ml-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin menolak SO ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya, Yakin!',
            cancelButtonText: 'Tidak!',
            reverseButtons: true
            }).then((result) => {
            if (result.value) {
                this.up.nomor_so=lso.nomor_so;
                this.up.tanggal_so=lso.tanggal_so;
                this.up.tanggal_kirim=lso.tanggal_kirim;
                this.up.nomor_rso=lso.nomor_rso;
                this.up.keterangan=lso.keterangan;
                this.up.status="Tolak";
                axios.put("/api/so/"+this.up.nomor_so,this.up)
                .then((response)=>{
                        $("#modal-form").modal("hide");
                        swalWithBootstrapButtons.fire(
                        'Sukses!',
                        'SO berhasil di tolak !',
                        'success'
                         )
                        this.$router.push({name:'soconfirm'})
                })
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Cancelled',
                'Batal melakukan penolakan :)',
                'error'
                )
            }
            })
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
            console.log(this.datetimes);
            return this.datetimes;
        }
    },
}
</script>

<style>
    #alastolak{
        width: 100%;
        padding: 1%;
        border:solid 1px rgb(253, 98, 98);
        text-align: center;
        align-content: center;
        margin-top: 5px;
        background-color: rgb(252, 156, 156) ;
        border-radius: 3px;
        font-size: 1.1em;
        color: rgb(255, 68, 68);
    }

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