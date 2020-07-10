<template>
    <div class="container">
        <div class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Rso :</label>
                    <input v-model="rlist.nomor_rso" type="text" class="form-control col-12" :disabled="disabled == 1">
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="rlist.tanggal_rso"  type="date" class="form-control col-12" :disabled="disabled == 1">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer</label>
                    <select   v-model="rlist.kode_customer" name="customer" class="col-12 form-control" :disabled="disabled == 1">
                        <option v-for="custom in customers" :key="custom.kode" :value="custom.kode" >{{custom.nama}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Marketing</label>
                    <select v-model="rlist.id_user" name="marketing" class="col-12 form-control" :disabled="disabled == 1">
                        <option value="1">Rotamba</option>
                        <option value="2">Miana</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="rlist.keterangan"  name="keterangan" class="form-control col-12" :disabled="disabled == 1"></textarea>
                </div>
                <div class="form-group">
                    <button @click="getdisabled()" class="btn btn-primary col-4 ">{{tombol}}</button>
                    <button @click="updateRso()" v-if="tbsukses" class="btn btn-success col-4 ml-1">Update</button>
                </div>
            </div>
        </div>
        <div class="row m-auto">
            <button @click="showmodal()" type="button" class="btn btn-success mt-3">+ Tambah Barang</button>
        </div>
        <div id="rsoverflow" class="row mt-2 mx-auto">
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center">1</td>
                        <td>KAWAT ULIR 1.7</td>
                        <td>20.000</td>
                        <td style="text-align:center">KG</td>
                        <td style="text-align:center">
                            <button @click="updateBarang(br)" class="btn btn-primary">Edit</button>
                            <button @click="deleteBarang(br)" class="btn btn-danger">Hapus</button>
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
                        <label>Kode Barang</label>
                        <input  type="text" name="form.kode" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input  type="text" name="form.nama"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Stok</label>
                        <input type="number" name="form.qty" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <select name="satuan"  class="form-control">
                            <option value="PCS">PCS</option>
                            <option value="ROLL">ROLL</option>
                            <option value="LBR">LBR</option>
                            <option value="KG">KG</option>
                            <option value="MTR">MTR</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button"  class="btn btn-primary">Save changes</button>
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
        }
    },
    created(){
        this.getRso()
    },  
    mounted(){
        axios.get("/api/customer")
        .then(res=>this.customers=res.data.data)
    },
    methods:{
        getdisabled(){
            this.disabled = (this.disabled + 1) % 2;
            if(this.disabled==1){
                this.tombol="Edit Rso";
                this.tbsukses=false;
                this.getRso()
    
            }else{
                this.tombol="Close";
                this.tbsukses=true;
            }
        },
        getRso(){
            axios.get(`/api/rso/${this.$route.params.id}`)
            .then(res=>this.form=res.data.data)
        },
        updateRso(){
            axios.put(`/api/rso/${this.$route.params.id}`,this.form)
            .then((response)=>{
                    console.log(this.form)
                    this.getRso()
                })
        },
        showmodal(){
            $("#modal-form").modal("show");
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