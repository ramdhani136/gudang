<template>
<div class="container" @keyup.esc="resetForm()">
    <button @click="showmodal()" class="btn btn-success my-3">+ Tambah User</button>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(usr , index) in Filtereduser" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td>{{usr.name}}</td>
                    <td>{{usr.username}}</td>
                    <td>{{usr.email}}</td>
                    <td style="text-align:center">
                        <button @click="updateUser(usr)" class="btn btn-primary">Edit</button>
                        <button @click="deleteUser(usr)" class="btn btn-danger">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form User</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input v-model="form.username" type="text" name="username" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input v-model="form.name" type="text" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="form.email" type="email" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input v-model="form.password" type="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Pilih Bagian</label>
                        <select v-model="bagian" class="form-control">
                            <option value="1">Accounting</option>
                            <option value="2">DIC</option>
                            <option value="3">Purchasing</option>
                            <option value="4">Sales</option>
                            <option value="5">Superadmin</option>
                            <option value="6">Warehouse</option>
                        </select>
                    </div>
                    <div v-if="bagian==='4'" class="form-group">
                        <label>Menu Sales</label>
                        <select v-model="form.sales" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='4'" class="form-group">
                        <label>Kordinator</label>
                        <select v-model="form.kordisales" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='4'" class="form-group">
                        <label>Supervisor</label>
                        <select v-model="form.susales" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='4'" class="form-group">
                        <label>Group</label>
                        <select v-model="form.kode_groupso" class="form-control">
                            <option :value="gs.kode" v-for="(gs,index) in groupso" :key="index">{{gs.area}}</option>
                        </select>
                    </div>
                    <div v-if="bagian==='2'" class="form-group">
                        <label>Inventory Control</label>
                        <select v-model="form.inventory" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='2'" class="form-group">
                        <label>Incoming Goods</label>
                        <select v-model="form.incoming" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='2'" class="form-group">
                        <label>Incoming ACC</label>
                        <select v-model="form.incomingspv" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='3'" class="form-group">
                        <label>Staff</label>
                        <select v-model="form.purch" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='3'" class="form-group">
                        <label>Supervisor</label>
                        <select v-model="form.suppurch" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='2'" class="form-group">
                        <label>Distribusi Staff</label>
                        <select v-model="form.distribusi" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='1'" class="form-group">
                        <label>Staff</label>
                        <select v-model="form.acc" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='6'" class="form-group">
                        <label>Warehouse</label>
                        <select v-model="form.warehouse" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div v-if="bagian==='5'" class="form-group">
                        <label>Status</label>
                        <select v-model="form.superadmin" class="form-control">
                            <option value="0">Tidak Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="tambahuser()" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import {
    Circle5
} from 'vue-loading-spinner'
export default {
    components: {
        Circle5
    },
    data() {
        return {
            search: '',
            user: [],
            form: {
                sales: 0,
                susales: 0,
                purch: 0,
                suppurch: 0,
                inventory: 0,
                incoming: 0,
                superadmin: 0,
                acc: 0,
                warehouse: 0,
                distribusi: 0,
                kordisales: 0,
                incomingspv: 0,
                password: '',
                kode_groupso: 'A1',
            },
            edit: false,
            load: true,
            groupso: {},
            bagian: null
        }
    },
    created() {
        this.getuser()
    },
    computed: {
        Filtereduser() {
            return this.user.filter(elem => {
                return elem.name.toLowerCase().includes(this.search.toLowerCase());
            });
        }
    },
    methods: {
        getuser() {
            axios.get("/api/user")
                .then(res => {
                    this.user = res.data.data;
                    axios.get("/api/groupso")
                        .then(res => {
                            this.groupso = res.data.data;
                            this.load = false;
                        })
                })
        },
        deleteUser(usr) {
            let keputusan = confirm('Apakah anda yakin?');
            if (keputusan === true) {
                axios.delete("/api/user/" + usr.id)
                    .then(response => {
                        this.getuser();
                    })
                    .catch(error => {
                        console.log(error)
                    })
            }
        },
        showmodal() {
            $("#modal-form").modal("show");
        },
        tambahuser() {
            axios.post("/api/user", this.form)
                .then(res => {
                    this.getuser();
                    $("#modal-form").modal("hide");
                    this.resetform();
                })
        },
        resetform() {
            this.form = {
                sales: 0,
                susales: 0,
                purch: 0,
                suppurch: 0,
                inventory: 0,
                incoming: 0,
                superadmin: 0,
                acc: 0,
                distribusi: 0,
                password: '',
                warehouse: 0,
                kordisales: 0,
                incomingspv: 0,
            };
        }
        /*  
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
         resetForm(){
             this.form.id=""
             this.form.nama=""
             this.form.keterangan=""
             this.edit=false;
         }, */
    }
}
</script>

<style>
</style>
