<template>
    <div class="container"  @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Barang</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Awal</th>
                <th>Booking</th>
                <th>Tersedia</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(br , index) in filteredMembers" :key="br">
                <td>{{index+1}}</td>
                <td>{{br.nama}}</td>
                <td>{{br.qty}}</td>
                <td>200</td>
                <td>800</td>
                <td>{{br.satuan}}</td>
                <td>
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
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="errors.length > 0 " class="alert alert-danger">
                        <ul>
                            <li v-for="(error , index) in errors" :key="index">
                                {{error}}
                            </li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input v-model="form.kode" type="text" name="nama" id="" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.nama" type="text" name="Alamat" id="" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Stok</label>
                        <input v-model="form.qty" type="number" name="pekerajaan" id="" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <select v-model="form.satuan" name="satuan" id="" class="form-control">
                            <option value="PCS">PCS</option>
                            <option value="ROLL">ROLL</option>
                            <option value="LBR">LBR</option>
                            <option value="KG">KG</option>
                            <option value="MTR">MTR</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="createBarang()" class="btn btn-primary">Save changes</button>
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
            barang:[],
            form:{
                kode:'',
                nama:'',
                satuan:'',
                qty:'',
            },
            errors:[],
            edit:false,
        }
    },
    created(){
        this.getBarang()
    },
    computed:{
        filteredMembers(){
            return this.barang.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getBarang(){
            axios.get("/api/barang")
            .then(res=>this.barang=res.data.data)
        },
        showmodal(){
            this.errors=[];
            $("#modal-form").modal("show");
        },
        createBarang(){
            if(this.edit===false){
                axios.post("/api/barang",this.form)
                .then((response)=>{
                    this.getBarang();
                    this.$router.push({name:'barang'})
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })
                .catch(error=>{
                    this.errors=[];
                    if(error.response.data.errors.nama){
                        this.errors.push(error.response.data.errors.nama[0])
                    }
                    if(error.response.data.errors.kode){
                        this.errors.push(error.response.data.errors.kode[0])
                    }
                    if(error.response.data.errors.qty){
                        this.errors.push(error.response.data.errors.qty[0])
                    }
                    if(error.response.data.errors.satuan){
                        this.errors.push(error.response.data.errors.satuan[0])
                    }
            })
            }else{
                axios.put("/api/barang/"+  this.form.kode,this.form)
                .then((response)=>{
                    this.getBarang();
                    this.$router.push({name:'barang'})
                    $("#modal-form").modal("hide");
                    this.edit=false
                    this.resetForm()
                })
                .catch(error=>{
                    this.errors=[];
                    if(error.response.data.errors.nama){
                        this.errors.push(error.response.data.errors.nama[0])
                    }
                    if(error.response.data.errors.kode){
                        this.errors.push(error.response.data.errors.kode[0])
                    }
                    if(error.response.data.errors.qty){
                        this.errors.push(error.response.data.errors.qty[0])
                    }
                    if(error.response.data.errors.satuan){
                        this.errors.push(error.response.data.errors.satuan[0])
                    }
            })
            }
        },
        updateBarang(barang){
            this.form.nama=barang.nama
            this.form.qty=barang.qty
            this.form.satuan=barang.satuan
            this.form.kode=barang.kode
            this.edit=true
            this.showmodal();
        },
        deleteBarang(barang){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/barang/" + barang.kode)
                .then(response=>{
                    this.getBarang();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.edit=false
            this.form.nama=""
            this.form.kode=""
            this.form.qty=""
            this.form.satuan=""

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