<template>
    <div class="container" @keyup.esc="resetForm()">
        <div class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Rso :</label>
                    <input  v-model="rlist.nomor_rso" type="text" class="form-control col-12" disabled>
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input  v-model="rlist.tanggal_rso"  type="date" class="form-control col-12" disabled>
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
                    <select   v-model="rlist.nip_sales" name="marketing" class="col-12 form-control" disabled>
                        <option :value="rlist.nip_sales">{{rlist.sales}}</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea  v-model="rlist.keterangan"  name="keterangan" class="form-control col-12" disabled></textarea>
                </div>
            </div>
        </div>
        <div id="dicoverflow" class="row mt-2 mx-auto">
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Estimasi Kedatangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(list,index) in listrso" :key="list.nomor_rso">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{list.nama_barang}}</td>
                        <td style="text-align:center">{{list.qty_tdktersedia}}</td>
                        <td style="text-align:center">{{list.satuan}}</td>
                        <td >
                            <div class="form-group">
                                <input style="text-align:center" v-model="list.tgl_datang" type="text" class="form-control" disabled>
                            </div>
                        </td>
                        <td>
                            <button @click="showModal(list)" class="btn btn-primary">Update</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-for="rs in form" :key="rs.nomor_id">
        <button @click="ConfirmRso(rs)" class="btn btn-success mt-2" >Konfirmasi</button>
        </div>
        <div v-for="list in listrso" :key="list.nomor_rso">
        <div class="modal fade" id="modal-form" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Status</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input v-model="dic.nama_barang" type="text"   autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Permintaan</label>
                        <input v-model="dic.jumlahrso" type="number"   autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="dic.satuan" type="text"   autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea v-model="dic.catatan" name="catatan" class="form-control" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label>Estimasi Datang</label>
                        <input v-model="dic.tanggal_datang" type="date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="updateStatusklik()" class="btn btn-primary">Save changes</button>
                </div>
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
            customers:{},  
            listrso:{},
            dic:{},
            konfirm:{},
            update:{},
            urso:{}
        }
    },
    created(){
        this.getRso();
        this.getlistRso();
    },  
    mounted(){
        axios.get("/api/customer")
        .then(res=>this.customers=res.data.data)
    },
    methods:{
        getRso(){
            axios.get(`/api/rso/${this.$route.params.id}`)
            .then(res=>this.form=res.data.data)
        },
        getlistRso(){
            axios.get(`/api/listrso/data/purch/${this.$route.params.id}`)
            .then(res=>this.listrso=res.data.data)
        },
        showModal(list){
            this.getlistRso()
            this.dic.catatan=list.catatan
            this.dic.id=list.id
            this.dic.nama_barang=list.nama_barang
            this.dic.jumlahrso=list.qty_tdktersedia
            this.dic.satuan=list.satuan
            this.dic.tanggal_datang=list.tgl_datang
            this.update.nomor_rso=list.nomor_rso
            this.update.tanggal_rso=list.tanggal_rso
            this.update.kode_barang=list.kode_barang
            this.update.qty=list.qty
            this.update.qty_tersedia=list.qty_tersedia
            this.update.qty_tdktersedia=list.qty_tdktersedia
            this.update.status=list.status
            $("#modal-form").modal("show");
        },
        updateStatusklik(){
            this.update.tanggal_datang=this.dic.tanggal_datang
            axios.put(`/api/listrso/`+this.dic.id,this.update)
            .then((response)=>{
                this.resetForm()
                $("#modal-form").modal("hide");
            })     
        },
        ConfirmRso(rs){
            this.konfirm.status='Confirmed';
            this.konfirm.tanggal_rso=rs.tanggal_rso;
            this.konfirm.nip_sales=rs.nip_sales;
            this.konfirm.kode_customer=rs.kode_customer;
            this.konfirm.keterangan=rs.keterangan;
            this.konfirm.status="Confirmed";


            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.put(`/api/rso/`+rs.nomor_rso,this.konfirm)
                    .then((response)=>{
                    this.getRso();
                    this.$router.push({name:'purchase'});
            })
                .catch(error=>{
                    console.log(error)
                })
            }  
        },
        resetForm(){
            this.getlistRso()
            this.dic.catatan=""
            this.dic.id=""
            this.dic.nama_barang=""
            this.dic.jumlahrso=""
            this.dic.satuan=""
            this.dic.tanggal_datang=""
        },
    },
}
</script>

<style>
    #dicoverflow{
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
        z-index: 10;
    }
    
</style>