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
                    <input v-if="!disabled" v-model="inprso.tanggal_rso"  type="date" class="form-control col-12" >
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select v-if="disabled"  v-model="rlist.kode_customer" name="customer" class="col-12 form-control" :disabled="disabled == 1">
                        <option v-for="custom in customers" :key="custom.kode" :value="custom.kode" >{{custom.nama}}</option>
                    </select>
                    <select v-if="!disabled" v-model="inprso.kode_customer" name="customer" class="col-12 form-control">
                        <option v-for="custom in customers" :key="custom.kode" :value="custom.kode" >{{custom.nama}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Marketing</label>
                    <select  v-if="disabled"  v-model="rlist.id_user" name="marketing" class="col-12 form-control" :disabled="disabled == 1">
                        <option value="1">Rotamba</option>
                        <option value="2">Miana</option>
                    </select>
                    <select v-if="!disabled" v-model="inprso.id_user" name="marketing" class="col-12 form-control" >
                        <option value="1">Rotamba</option>
                        <option value="2">Miana</option>
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
                        <th>Booking</th>
                        <th>Satuan</th>
                        <th v-if="rlist.status=='Sent'">Catatan</th>
                        <th v-if="rlist.status=='Confirmed'">Status</th>
                        <th v-if="rlist.status=='Confirmed'">Tersedia</th>
                        <th v-if="rlist.status=='Confirmed'">Tidak Tersedia</th>
                        <th v-if="rlist.status=='Confirmed'">Estimasi Kedatangan</th>
                        <th v-if="rlist.status!=='Sent'">Aksi</th>
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
                        <td style="text-align:center" v-if="rlist.status=='Confirmed'" >{{list.tgl_datang}}</td>
                        <td v-if="rlist.status=='Sent'" style="text-align:center">{{list.catatan}}</td>
                        <td  v-if="rlist.status!=='Sent'" style="text-align:center">
                            <button @click="editListRso(list)"  class="btn btn-primary">Edit</button>
                            <button @click="deleteListRso(list)"  class="btn btn-danger">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
                        <select v-model="inputlrso.kode_barang" name="barang"  class="form-control">
                            <option v-for="br in barang" :key="br.kode" :value="br.kode">{{br.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input v-model="inputlrso.qty"  type="number" name="qty"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea v-model="inputlrso.catatan" name="catatan"  class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetform()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button"  @click="createListRso()" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div>
    </div>   
</template>

<script>
export default {
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
            inprso:{
            }
        }
    },
    created(){
        this.getRso();
        this.getlistRso();
        this.getBarang();
    },  
    mounted(){
        axios.get("/api/customer")
        .then(res=>this.customers=res.data.data)
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
                this.inprso.id_user=rlist.id_user 
                this.inprso.kode_customer=rlist.kode_customer 
                this.inprso.keterangan=rlist.keterangan 
    
            }else{
                this.tombol="Close";
                this.tbsukses=true;
                this.getRso()
                this.inprso.status=rlist.status
                this.inprso.nomor_rso=rlist.nomor_rso
                this.inprso.tanggal_rso=rlist.tanggal_rso  
                this.inprso.id_user=rlist.id_user 
                this.inprso.kode_customer=rlist.kode_customer 
                this.inprso.keterangan=rlist.keterangan 
            }
        },
        getRso(){
            axios.get(`/api/rso/${this.$route.params.id}`)
            .then(res=>this.form=res.data.data)
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
        getlistRso(){
            axios.get(`/api/listrso/${this.$route.params.id}`)
            .then(res=>this.listrso=res.data.data)
        },
        getBarang(){
            axios.get("/api/barang/")
            .then(res=>this.barang=res.data.data)
        },
        createListRso(){
            if(this.edit===false){
                axios.post("/api/listrso",this.inputlrso)
                .then((response)=>{
                    this.getRso();
                    this.getlistRso();
                    this.getBarang();
                    this.resetform();
                    $("#modal-form").modal("hide");
                })
                
            }else{
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
        resetform(){
            this.getlistRso()
            this.inputlrso.status=""
            this.inputlrso.id=""
            this.inputlrso.kode_barang=""
            this.inputlrso.qty=""
            this.inputlrso.catatan=""
            this.edit=false
        },
    },
}
</script>

<style>
    #rsoverflow{
    width: 100%;
    max-height: 280px;
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
    
</style>