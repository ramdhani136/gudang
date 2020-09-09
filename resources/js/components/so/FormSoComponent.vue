<template>
    <div class="container">
        <div  class="row row-cols-2" v-for="(vso,index) in so" :key="index">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor SO :</label>
                    <input v-model="vso.nomor_so" type="text" class="form-control col-12" :disabled="disabled == 1">
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="vso.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Tanggal Kirim :</label>
                    <input v-model="vso.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" :disabled="disabled == 1">
                </div>
                <button @click="getdisabled()" v-if="vso.status==='Draft' || vso.status==='Tolak'" class="btn btn-primary">{{tombol}}</button>
                <button @click="updateSO(vso)" v-if="tbsukses && vso.status==='Draft' || tbsukses && vso.status==='Tolak'" class="btn btn-success col-4 ml-1">Update</button>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select v-model="vso.kode_customer" name="customer" class="col-12 form-control" disabled>
                        <option :value="vso.kode_customer">{{vso.customer}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sales</label>
                    <select v-model="vso.nip_sales" name="marketing" class="col-12 form-control" disabled>
                        <option :value="vso.nip_sales">{{vso.sales}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="vso.keterangan"  name="keterangan" class="form-control col-12" :disabled="disabled == 1"></textarea>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Distribusi :</label>
                    <select class="form-control" v-model="vso.distribusi" @change="aksidistribusi(vso)" :disabled="disabled == 1">
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
                    <textarea  v-model="vso.alamat" name="alamat" class="form-control col-12" disabled></textarea>
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
                            <th>Sudah kirim</th>
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
                            <td style="text-align:center">{{sk.qty[index]}}</td>
                            <td style="text-align:center" v-for="(lso,index) in so" :key="index" v-if="lso.status=='Draft'" >
                                <button @click="hapusListSo(ts)" class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>   
        <div class="row mt-2" v-for="(lso,index) in so" :key="index">
                <button @click="submitDraft()" v-if="lso.status=='Draft'" class="btn-orange btn ml-3" >
                    Simpan Draft
                </button>
                <button @click="submitSend()" v-if="lso.status=='Draft'"  class="btn-success btn ml-1" >
                    Kirim SO
                </button>
                <button @click="submitSend()" v-if="lso.status=='Tolak'"  class="btn-orange btn ml-3" >
                    Request Ulang
                </button>
        </div>
        <div v-if="vso.status=='Tolak'" v-for="vso in so" :key="vso.nomor_so" id="alastolak" class="mt-3">
            <div v-for="(lso,index) in so" :key="index">
                <b>{{lso.alastolak}}</b> 
            </div>    
        </div>
        <div class="modal fade" id="modal-lokasi" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Tujuan Pengiriman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label>Tujuan</label>
                            <select class="form-control" v-model="lokasi">
                                <option value="default">Default</option>
                                <option value="ekspedisi">Ekspedisi</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group" v-if="lokasi==='ekspedisi'">
                        <label>Lokasi</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="eks ? eks.nama:''"></div>
                        <div class="placeholder" v-if="eks==null">Pilih Ekspedisi</div>
                        <div class="popover" v-show="visible">
                            <input type="text"
                            @keydown.up="up"
                            @keydown.down="down"
                            @keydown.enter="selectItem"
                            v-model="query"
                            placeholder="Masukan nama ekspedisi.."
                            >
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" 
                                    :key="match.id"
                                    v-text="match.nama"
                                    :class="{'selected':(selected==index)}"
                                    @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                        <div v-if="lokasi==='ekspedisi'" class="form-group mt-3" >
                            <label>Alamat</label>
                            <textarea v-model="eksal" class="form-control" disabled></textarea>
                        </div>
                        <div v-if="lokasi==='default'" class="form-group">
                            <label>Alamat</label>
                            <textarea  v-for="vso in so" :key="vso.nomor_so" v-model="vso.alamatcustomer" class="form-control" disabled></textarea>
                        </div>
                        <div v-if="lokasi==='lainnya'"  class="form-group">
                            <label>Tempat/Gedung</label>
                            <input  type="text" class="form-control" v-model="lainlok">
                        </div>
                        <div v-if="lokasi==='lainnya'" class="form-group">
                            <label>Alamat</label>
                            <textarea v-model="lainal" class="form-control"></textarea>
                        </div>
                        
                </div>
                <div class="modal-footer"  v-for="vso in so" :key="vso.nomor_so">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="selectLokasi(vso)" class="btn btn-primary" data-dismiss="modal">Save Change</button>
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
            listrso:{},
            updel:{qty:0,},
            ada:0,
            ubah:{},
            disabled:1,
            tombol:'Edit SO',
            tbsukses:false,
            gagal:false,
            distribusi:'default',
            lokasi:'default',
            dataekspedisi:{},
            visible:false,
            visiblecust:false,
            query:'',
            selected:0,
            eks:null,
            itemHeight:39,
            disabled:1,
            dataekspedisi:{},
            eksal:'',
            sk:{
                qty:[],
            },
        }
    },
    created(){
        this.getSo();
        this.getlistso();
        this.getekspedisi();
    },  
    computed:{  
        matches(){
            if(this.query==''){
                return [];
            }
            return this.dataekspedisi.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
        },     
    },
    methods:{
            getekspedisi(){
            axios.get("/api/ekspedisi")
            .then(res=>{
                this.dataekspedisi=res.data.data;
            });
        },
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
                            this.sk.qty[i]=this.listso[i].bbk_tdktersedia ;
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
        hapusListSo(ts){
            let keputusan=confirm("Yakin ingin menghapus item ini dari SO? #Akan otomatis menghapus item in pada RSO");
            if(keputusan===true){
                axios.get(`/api/so/${this.$route.params.id}`)
                .then(res=>this.so=res.data.data);
                if(this.so[0].statusso==="tersedia"){
                    axios.get("/api/listrso/"+ts.id+"/edit")
                    .then(res=>{
                        this.listrso=res.data.data;
                        this.updel.qty_tersedia="";
                        this.updel.so_tersedia="Y";
                        this.updel.qty=parseInt(this.listrso[0].qty)-parseInt(this.listrso[0].qty_tersedia);
                        if(this.updel.qty<1){
                            axios.delete("/api/listrso/"+this.listrso[0].id)
                            .then(res=>{
                                this.getSo();
                                this.getlistso()
                            }); 
                        }else{
                            axios.put("/api/listrso/"+this.listrso[0].id,this.updel)
                            .then(res=>{
                                this.getSo();
                                this.getlistso()
                            });
                        };
                        axios.get(`/api/listrso/data/sotersedia/`+this.so[0].nomor_rso)
                        .then(res=>{
                            this.ada=0;
                            this.listso=res.data.data;
                            for(let i=0;i<this.listso.length;i++){
                                this.ada += parseInt(this.listso[i].qty_tersedia);
                            }
                            if(this.ada<1){
                                    axios.get("/api/listrso/"+this.so[0].nomor_rso)
                                    .then(res=>{
                                        this.listrso=res.data.data;
                                        if(this.listrso.length>0){
                                                axios.delete("/api/so/"+this.$route.params.id)
                                                .then(res=>{
                                                    this.$router.push({name:'so'})
                                                });  
                                        }else{
                                            axios.delete("/api/rso/"+this.so[0].nomor_rso)
                                            .then(res=>{
                                                axios.delete("/api/so/"+this.$route.params.id)
                                                .then(res=>{
                                                    this.$router.push({name:'so'})
                                                }); 
                                            });                  
                                        }
                                });
                            }else{
                                this.getSo();
                                this.getlistso();
                            }
                        });
                    });
                }else{
                    axios.get("/api/listrso/"+ts.id+"/edit")
                    .then(res=>{
                        this.listrso=res.data.data;
                        this.updel.qty_tdktersedia="";
                        this.updel.so_tdktersedia="Y";
                        this.updel.qty=parseInt(this.listrso[0].qty)-parseInt(this.listrso[0].qty_tdktersedia);
                        if(this.updel.qty<1){
                            axios.delete("/api/listrso/"+this.listrso[0].id)
                            .then(res=>{
                                this.getSo();
                                this.getlistso()
                            }); 
                        }else{
                            axios.put("/api/listrso/"+this.listrso[0].id,this.updel)
                            .then(res=>{
                                this.getSo();
                                this.getlistso()
                            });
                        };
                        axios.get(`/api/listrso/data/sott/`+this.so[0].nomor_rso)
                        .then(res=>{
                            this.ada=0;
                            this.listso=res.data.data;
                            for(let i=0;i<this.listso.length;i++){
                                this.ada += parseInt(this.listso[i].qty_tdktersedia);
                            }
                            if(this.ada<1){
                                    axios.get("/api/listrso/"+this.so[0].nomor_rso)
                                    .then(res=>{
                                        this.listrso=res.data.data;
                                        if(this.listrso.length>0){
                                                axios.delete("/api/so/"+this.$route.params.id)
                                                .then(res=>{
                                                    this.$router.push({name:'so'})
                                                });
                                        }else{
                                            axios.delete("/api/rso/"+this.so[0].nomor_rso)
                                            .then(res=>{
                                                axios.delete("/api/so/"+this.$route.params.id)
                                                .then(res=>{
                                                    this.$router.push({name:'so'})
                                                });
                                            });
                                        }
                                    });
                            }else{
                                this.getSo();
                                this.getlistso();
                            }
                        });
                    });
                }
            }
        },
        submitDraft(){
            this.$router.push({name:'so'})
        },
        submitSend(){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ml-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin mengirim SO ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya, Yakin!',
            cancelButtonText: 'Tidak!',
            reverseButtons: true
            }).then((result) => {
            if (result.value) {
                 this.ubah.status="Sent";
                axios.put("/api/so/"+this.$route.params.id,this.ubah)
                .then(res=>{
                    swalWithBootstrapButtons.fire(
                    'Sukses!',
                    'Berhasil mengirim SO.',
                    'success'
                    )
                    this.$router.push({name:'so'})
                });
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Cancelled',
                'Batal melakukan permintaan :)',
                'error'
                )
            }
            })
        },
        getdisabled(){
            this.disabled = (this.disabled + 1) % 2;
            if(this.disabled==1){
                this.tombol="Edit Rso";
                this.tbsukses=false;
    
            }else{
                this.tombol="Close";
                this.tbsukses=true;
            }
        },
        updateSO(vso){
            this.dataso={
                tanggal_kirim:vso.tanggal_kirim,
                keterangan:vso.keterangan,
                lokasi:vso.lokasi,
                alamat:vso.alamat,
                id_ekspedisi:vso.id_ekspedisi,
                distribusi:vso.distribusi,
                nomor_so:vso.nomor_so,
            };

            axios.put("/api/so/"+this.$route.params.id,this.dataso)
            .then(res=>{
                this.tbsukses=false;
                this.disabled=1;
                this.tombol="Edit Rso";
                this.$router.push({path:'/so/form/'+vso.nomor_so});
            });
        },
        aksidistribusi(vso){
            if(vso.distribusi==="kirim"){
                this.lokasi="default";
                if(this.lokasi==='default'){
                }
                $("#modal-lokasi").modal("show");  
            }else if(vso.distribusi==='ambil'){
                vso.lokasi="PT. Ekatunggal Tunas Mandiri";
                vso.alamat="Jl. Pahlawan No.29A, RT.003/005 Citeureup, Kab. Bogor, Jawa Barat";
            }else if(vso.distribusi==="default"){
                vso.alamat="";
                vso.lokasi="";
            }
        },
        selectLokasi(vso){
            vso.distribusi="default";
            vso.distribusi="kirim";
            if(this.lokasi==='ekspedisi'){
                this.lok=this.ekslok;
                this.al=this.eksal;
                this.ide=this.eksid;
            }else if(this.lokasi==='lainnya'){
                this.lok=this.lainlok;
                this.al=this.lainal;
            }else if(this.lokasi==='default'){
                this.lok=vso.customer;
                this.al=vso.alamatcustomer;
            } 
            vso.lokasi=this.lok;
            vso.alamat=this.al;
            vso.id_ekspedisi=this.ide;
        },
        toggleVisible(){
            this.visible = !this.visible;
        },
        itemClicked(index){
            this.selected=index;
            this.selectItem();
        },
        selectItem(){
            this.eks = this.matches[this.selected];  
            this.eksal= this.eks.alamat;
            this.ekslok= this.eks.nama;
            this.eksid= this.eks.id;
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

    },
}
</script>

<style>

</style>