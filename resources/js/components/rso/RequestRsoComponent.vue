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
                    <select   v-model="rlist.id_user" name="marketing" class="col-12 form-control" disabled>
                        <option value="1">Rotamba</option>
                        <option value="2">Miana</option>
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
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        <th>Jumlah Tersedia</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(list,index) in listrso" :key="list.nomor_rso">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{list.nama_barang}}</td>
                        <td>{{list.qty}}</td>
                        <td style="text-align:center">{{list.satuan}}</td>
                        <td style="text-align:center">
                            <select  v-model="list.status" name="change" class="form-control" disabled>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
                            </select>
                        </td>
                        <td>
                            <div class="form-group">
                                <input v-model="list.qty_tersedia" type="number" class="form-control col-12 z1 " disabled>
                            </div>
                        </td>
                        <td>
                            <button @click="showModal(list)" class="btn btn-primary">Update</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-success mt-2 ">Konfirmasi RSO</button>
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
                        <label>Status Barang</label>
                        <select @change="updateStatus()" v-model="update.status" name="status" class="form-control">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Tidak Tersedia">Tidak Tersedia</option>
                        </select>
                    </div>
                    <div v-if="update.status=='Tersedia'" class="form-group">
                        <label>Jumlah Tersedia</label>
                        <input  v-model="update.qty_tersedia" type="number"   autocomplete="off" class="form-control">
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
</template>

<script>
export default {
    data(){
        return {
            form:{},
            customers:{},  
            listrso:{},
            inputlrso:{
                status:"Confirmed"
            },
            inprso:{
                status:'Draft'
            },
            dic:{},
            status:'',
            update:{}
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
        showModal(){
            $("#modal-form").modal("show");
        },
        getlistRso(){
            axios.get(`/api/listrso/${this.$route.params.id}`)
            .then(res=>this.listrso=res.data.data)
        },
        showModal(list){
            this.getlistRso()
            this.dic.catatan=list.catatan
            this.dic.id=list.id
            this.dic.nama_barang=list.nama_barang
            this.update.status=list.status
            this.update.qty_tersedia=list.qty_tersedia
            this.dic.jumlahrso=list.qty
            this.dic.satuan=list.satuan
            $("#modal-form").modal("show");
        },
        updateStatus(){
            this.getlistRso()
            axios.put(`/api/listrso/`+this.dic.id,this.update)
            .then((response)=>{
                if(this.update.status==="Tidak Tersedia"){
                    this.update.qty_tersedia=''
                    axios.put(`/api/listrso/`+this.dic.id,this.update)
                }
            })

            
        },
        updateStatusklik(){
            this.getlistRso()
            axios.put(`/api/listrso/`+this.dic.id,this.update)
            .then((response)=>{
                this.resetForm()
                this.getlistRso()
                $("#modal-form").modal("hide");
            })
            
        },
        resetForm(){
            this.getlistRso()
            this.dic.catatan=""
            this.dic.id=""
            this.dic.nama_barang=""
            this.update.status=""
            this.update.qty_tersedia=""
            this.dic.jumlahrso=""
            this.dic.satuan=""
        }
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