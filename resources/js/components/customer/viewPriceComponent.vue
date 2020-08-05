<template>
    <div class="container">
        <div >
            <button @click="showmodal()" class="btn btn-success mb-4 mt-3 ml-n1">+ Input Harga Baru</button>
        </div>
        <div id="viewprice" class="row row-cols-2 ml-n3">
            <div class="col-4">
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input v-for="br in barang" :key="br.kode" type="text" class="form-control" :value="br.nama"  disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Satuan :</label>
                    <input v-for="br in barang" :key="br.kode" type="text" class="form-control" :value="br.satuan"  disabled>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer :</label>
                    <input v-for="cs in customer" :key="cs.kode" type="text" class="form-control" :value="cs.nama"  disabled>
                </div>
            </div>
        </div>
        <div id="overflow" class="border-top tinggi">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Harga</th>
                <th>Sales</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
                <tr v-for="(it,index) in item" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td style="text-align:center">{{it.created_at}}</td>
                <td style="text-align:center">{{it.harga | currency}}</td>
                <td style="text-align:center">{{it.sales}}</td>
                <td>
                    <div class="form-group">
                        <textarea v-model="it.keterangan" class="form-control" disabled></textarea>
                    </div>
                </td>
                <td style="text-align:center">
                    <div v-if="status=='Aktif'" class="btn btn-orange">Aktif</div>
                    <div v-if="status!=='Aktif'" class="btn btn-none">Non Aktif</div>
                </td>
            </tr>
            <div v-if="tampil" id="nocontent">Tidak ada data yang tersimpan</div>
        </tbody>
    </table>
    <Circle5 id="load6" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div id="modal-width" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form Harga</h5>
                        <button  type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input v-for="br in barang" :key="br.kode" type="text" class="form-control" :value="br.nama"  disabled>
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <input v-for="br in barang" :key="br.kode" type="text" class="form-control" :value="br.satuan"  disabled>
                        </div>
                        <div class="form-group">
                            <label>Sales</label>
                            <select class="form-control" v-model="up.nip_sales">
                                <option :value="sl.nip" v-for="sl in sales" :key="sl.nip">{{sl.nama}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Harga</label>
                            <input v-model="up.harga"  type="number" name="harga"  autocomplete="off" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea v-model="up.keterangan" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" @click="createItem()" class="btn btn-primary">Save changes</button>
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
        return{
            customer:[],
            load:true,
            status:'Aktif',
            item:{},
            barang:{},
            customer:{},
            tampil:false,
            up:{},
            sales:{},
        }
    },
    created(){
        this.getItem();
    },
    methods:{
        getItem(){
            axios.get("/api/data/price/"+this.$route.params.customer+'/'+this.$route.params.barang)
            .then(res=>{this.item=res.data.data
                
                axios.get("/api/barang/"+this.$route.params.barang) 
                .then(res=>this.barang=res.data.data);

                axios.get("/api/customer/"+this.$route.params.customer) 
                .then(res=>this.customer=res.data.data);

                axios.get("/api/sales/")
                .then(res=>this.sales=res.data.data);

                if(this.item.length<1){
                    this.tampil=true;
                }
                this.load=false;
            });
        },
        showmodal(){
            this.errors=[];
            $("#modal-form").modal("show");
        },
        resetForm(){
            this.getItem();
            this.up.no_reg="";
            this.up.nip_sales="";
            this.up.harga="";
            this.up.keterangan="";
        },
        createItem(){
            this.up.kode_customer=this.$route.params.customer;
            this.up.kode_barang=this.$route.params.barang;
            axios.post("/api/custprice",this.up)
            .then(res=>{
                this.getItem();
                this.resetForm();
                this.tampil=false;
                $("#modal-form").modal("hide");    
            })
        }
    }
}
</script>

<style>
    .btn-none{
        background-color:#666;
        border:solid 1px rgb(85, 85, 85);
        color:#fff;
    }

    #viewprice{
        width: 100%;
    }

    .tinggi{
        max-height: 350px;
    }

    #load6{
        position: relative;
        margin:0 auto;
        margin-top: 100px;
    }

    #nocontent{
        position: absolute;
        width: 90%;
        text-align: center;
        color: rgb(197, 196, 196);
        top: 350px;
        letter-spacing: 0.5px;
    }
</style>
