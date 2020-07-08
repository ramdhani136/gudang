<template>
    <div class="container" @keyup.esc="resetForm()">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Divisi</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>nama</th>
                <th>keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(dv , index) in FilteredDivisi" :key="dv">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{dv.nama}}</td>
                <td>{{dv.keterangan}}</td>
                <td style="text-align:center">
                    <button @click="updateDivisi(dv)" class="btn btn-primary">Edit</button>
                    <button @click="deleteDivisi(dv)" class="btn btn-danger">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form divisi</h5>
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
                        <label>Kode Customer</label>
                        <input v-model="form.nama" type="text" name="nama" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.keterangan" type="text" name="keterangan"  autocomplete="off" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="createDivisi()" class="btn btn-primary">Save changes</button>
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
            divisi:[],
            form:{
                id:'',
                nama:'',
                keterangan  :'',
            },
            errors:[],
            edit:false,
        }
    },
    created(){
        this.getDivisi()
    },
    computed:{
        FilteredDivisi(){
            return this.divisi.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getDivisi(){
            axios.get("/api/divisi")
            .then(res=>this.divisi=res.data.data)
        },
        showmodal(){
            this.errors=[];
            $("#modal-form").modal("show");
        },
        createDivisi(){
            if(this.edit===false){
                axios.post("/api/divisi",this.form)
                .then((response)=>{
                    this.getDivisi();
                    this.$router.push({name:'divisi'})
                    $("#modal-form").modal("hide");
                    this.resetForm();
                })
                .catch(error=>{
                    this.errors=[];
                    if(error.response.data.errors.nama){
                        this.errors.push(error.response.data.errors.nama[0])
                    }
                    if(error.response.data.errors.keterangan){
                        this.errors.push(error.response.data.errors.keterangan[0])
                    }
            })
            }else{
                axios.put("/api/divisi/"+  this.form.id,this.form)
                .then((response)=>{
                    this.getDivisi();
                    this.$router.push({name:'divisi'})
                    $("#modal-form").modal("hide");
                    this.resetForm()
                })
                .catch(error=>{
                    this.errors=[];
                    if(error.response.data.errors.nama){
                        this.errors.push(error.response.data.errors.nama[0])
                    }
                    if(error.response.data.errors.keterangan){
                        this.errors.push(error.response.data.errors.keterangan[0])
                    }
            })
            }
        },
        updateDivisi(divisi){
            this.form.id=divisi.id
            this.form.nama=divisi.nama
            this.form.keterangan=divisi.keterangan
            this.edit=true;
            this.showmodal();
        },
        deleteDivisi(divisi){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/divisi/" + divisi.id)
                .then(response=>{
                    this.getDivisi();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        resetForm(){
            this.form.id=""
            this.form.nama=""
            this.form.keterangan=""
            this.edit=false;
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