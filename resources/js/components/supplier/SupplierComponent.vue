<template>
    <div class="container"  @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Supplier</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Supplier</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(br , index) in filteredCustomer" :key="br.kode_supplier">
                <td style="text-align:center">{{index+1}}</td>
                <td style="text-align:center">{{br.kode_supplier}}</td>
                <td>{{br.nama}}</td>
                <td style="text-align:center">
                    <button @click="updateSupplier(br)" class="btn btn-primary">Edit</button>
                    <button @click="deleteSupplier(br)" class="btn btn-danger">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1"  data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Supplier</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Supplier</label>
                        <input v-model="form.kode_supplier" type="text" name="form.kode" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.nama" type="text" name="form.nama"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" v-model="form.alamat"></textarea>
                    </div>
                    <div class="form-group">
                        <label>PIC</label>
                        <input v-model="form.pic" type="text" name="form.qty" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Kontak</label>
                        <input v-model="form.telepon" type="text" name="form.qty" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="form.email" type="email" name="form.qty" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="form.status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Non Aktif">Non Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="CreateSupplier()" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            search  : '',
            supplier:[],
            form:{},
            errors:[],
            edit:false,
            links:{}
        }
    },
    created(){
        this.getSupplier()
    },
    computed:{
        filteredCustomer(){
            return this.supplier.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            
            });
        }
    },
    methods:{
        getSupplier(){
            axios.get("/api/supplier")
            .then(res=>this.supplier=res.data.data)
        },
        showmodal(){
            this.errors=[];
            $("#modal-form").modal("show");
        },
        CreateSupplier(){
            if(this.edit===false){
                axios.post("/api/supplier",this.form)
                .then((response)=>{
                    this.getSupplier();
                    this.$router.push({name:'supplier'})
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })
                
            }else{
                axios.put('/api/supplier/'+  this.links.kode_supplier,this.form)
                .then((response)=>{
                    this.getSupplier();
                    this.$router.push({name:'supplier'})
                    $("#modal-form").modal("hide");
                    this.edit=false
                    this.resetForm()
                })
            }
        },
        updateSupplier(supplier){
            this.getSupplier()
            this.form.kode_supplier=supplier.kode_supplier
            this.links.kode_supplier=supplier.kode_supplier
            this.form.nama=supplier.nama
            this.form.alamat=supplier.alamat
            this.form.pic=supplier.pic
            this.form.telepon=supplier.telepon
            this.form.email=supplier.email
            this.form.status=supplier.status
            this.edit=true
            this.showmodal();
        },
        deleteSupplier(supplier){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/supplier/" + supplier.kode_supplier)
                .then(response=>{
                    this.getSupplier();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.getSupplier();
            this.edit=false
            this.form.kode_supplier=""
            this.form.nama=""
            this.form.alamat=""
            this.form.pic=""
            this.form.telepon=""
            this.form.email=""
            this.links.kode_supplier=""
            this.form.status=""
        },
    }
}
</script>


<style>
    #overflow{
        width: 100%;
    height: 440px;
    overflow-y: scroll;
    }

    #thead thead tr th{
        text-align: center;
        border-bottom: none;
        position: sticky; top: 0; 
        background-color: #fff;
        top: -1px;
        border-collapse: collapse;
        box-shadow: inset 0 0 0 #dee2e6,
        inset 0 -1px 0 #dee2e6;
    }

    #modal-width{
    width: 120%;
    height: auto;
    right: 13%;
    }
    
</style>