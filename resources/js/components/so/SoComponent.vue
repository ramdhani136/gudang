<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="Draft">Draft SO</option>
                <option value="Sent">Waiting list</option>
                <option value="Acc">Open</option>
                <option value="Tolak">Rejected</option>
                <option value="Selesai">Selesai</option>
                <option value="Di Selesaikan">Di Selesaikan</option>
            </select>
        </div>
        <!-- <div class="row">
            <router-link to="/so/create/" class="btn btn-success my-3" >+ Create SO</router-link>
        </div>  -->
            <div class="row">
                <router-link to="/so/new" class="btn btn-success my-3" >+ Create SO</router-link>
            </div> 
            <div id="overflow" class="border-top">
            <table id="thead" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor SO</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Tanggal Kirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rs , index) in FilterKategori" :key="rs.nomor_so">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">
                                <router-link :to="{name:'formso',params:{id:rs.nomor_so,rso:rs.nomor_rso}}" class="btn btn-none " >
                                    {{rs.nomor_so}}
                                </router-link>
                            </td>
                            <td style="text-align:center">{{rs.tanggal_so}}</td>
                            <td>{{rs.customer}}</td>
                            <td style="text-align:center">{{rs.tanggal_kirim}}</td>
                            <td  style="text-align:center">
                                <button @click="batalSo(rs)" v-if="rs.status=='Acc' || rs.status=='Sent'" class="btn btn-danger">Batalkan</button>
                                <button @click="deleteSo(rs)" v-if="rs.status=='Draft' || rs.status=='Tolak'" class="btn btn-danger">Hapus</button>
                                <button @click="requestselesai(rs)"  v-if="rs.status==='Acc' && rs.rs==='N'" class="btn btn-orange">Request Selesai</button>
                                <button @click="batalselesai(rs)"  v-if="rs.status==='Acc' && rs.rs==='Y'" class="btn btn-none">Batal Request</button>
                                <button @click="bukalagi(rs)" v-if="rs.status=='Di Selesaikan'" class="btn btn-orange">Reopen SO</button>
                                <button  @click="infoTolak(rs)" v-if="rs.status=='Acc' && rs.rs==='T'" class="btn btn-danger">R. Selesai di tolak</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Circle5 id="load" v-if="load"></Circle5>
            </div>
            <div class="modal fade" id="modal-selesai" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Permintaan Selesai SO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="input.alasselesai" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimselesai()" class="btn btn-primary">kirim permintaan</button>
                </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informasi Penolakan SO Selesai</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="alastolak" class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="requlang()" type="button" class="btn btn-orange">Request Ulang Selesai</button>
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
            search  : '',
            status:'Acc',
            so:[],
            load:true,
            update:{},
            listrso:{},
            uplsoneg:{},
            uplsopos:{},
            nso:null,
            input:{},
            alastolak:null,
        }
    },
    created(){
        this.getSo();
    },
    computed:{
        FilterKategori(){
            if(this.search===""){
                if(this.status==="Draft"){
                    return this.so.filter(elem=> elem.status==="Draft")
                }else if(this.status==="Acc"){
                    return this.so.filter(elem=> elem.status==="Acc")
                }else if(this.status==="Sent"){
                    return this.so.filter(elem=> elem.status==="Sent")
                }
                else if(this.status==="Tolak"){
                    return this.so.filter(elem=> elem.status==="Tolak")
                }else if(this.status==="Selesai"){
                    return this.so.filter(elem=> elem.status==="Selesai")
                }
                else if(this.status==="Di Selesaikan"){
                    return this.so.filter(elem=> elem.status==="Di Selesaikan")
                }
            }else{
                return this.so.filter(elem => {
                return elem.nomor_so.toLowerCase().includes(this.search);
            });
            }
        },
    },
    methods:{
        getSo(){
            axios.get("/api/so/data/realso")
            .then(res=>{this.so=res.data.data
                this.load=false;
            });
        },
        deleteSo(rs){
                const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin',
                cancelButtonText: 'Tidak',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    if(rs.statusso==="tersedia"){
                    this.update.status="Confirmed";
                    axios.put("/api/rso/"+rs.nomor_rso,this.update)
                    .then(res=>{
                        axios.get("/api/listrso/"+rs.nomor_rso)
                        .then(res=>{
                            this.listrso=res.data.data;
                            for(let i=0;i<this.listrso.length;i++){
                                if(this.listrso[i].qty_tersedia>0){
                                    this.uplsoneg.so_tersedia="N";
                                    axios.put("/api/listrso/"+this.listrso[i].id,this.uplsoneg)
                                        .then(res=>{
                                        });
                                    }else{
                                        this.uplsopos.so_tersedia="Y";
                                        axios.put("/api/listrso/"+this.listrso[i].id,this.uplsopos)
                                            .then(res=>{
                                        });
                                    }
                            }
                            axios.delete("/api/so/"+rs.nomor_so)
                            .then(res=>{
                                this.getSo();
                            })
                        });
                        swalWithBootstrapButtons.fire(
                        'Deleted!',
                        'SO berhasil di hapus.',
                        'success'
                        )
                    });
                }else{
                    this.update.status="Confirmed";
                    axios.put("/api/rso/"+rs.nomor_rso,this.update)
                    .then(res=>{
                        axios.get("/api/listrso/"+rs.nomor_rso)
                        .then(res=>{
                            this.listrso=res.data.data;
                            for(let i=0;i<this.listrso.length;i++){
                                if(this.listrso[i].qty_tdktersedia>0 && this.listrso[i].acc_purch==="Y"){
                                    this.uplsopos.so_tdktersedia="N";
                                    axios.put("/api/listrso/"+this.listrso[i].id,this.uplsopos)
                                }else if(this.listrso[i].qty_tdktersedia>0 && this.listrso[i].acc_purch==="N"){
                                    this.uplsoneg.so_tdktersedia="Y";
                                    axios.put("/api/listrso/"+this.listrso[i].id,this.uplsoneg)
                                }
                            }
                            axios.delete("/api/so/"+rs.nomor_so)
                            .then(res=>{
                                this.getSo();
                            })
                            swalWithBootstrapButtons.fire(
                            'Deleted!',
                            'SO berhasil di hapus.',
                            'success'
                            )
                        });
                    });
                    
                }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Batal menghapus SO ini :)',
                    'error'
                    )
                }
                })
        },
        requestselesai(rs){
            this.nso=rs.nomor_so;
            $("#modal-selesai").modal("show");
        },
        kirimselesai(){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ml-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin menyelesaikan SO ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya, Yakin!',
            cancelButtonText: 'Tidak',
            reverseButtons: true
            }).then((result) => {
            if (result.value) {
                this.input.rs="Y";
                axios.put("/api/so/"+this.nso,this.input)
                .then(res=>{
                    this.getSo();
                    this.input={};
                    swalWithBootstrapButtons.fire(
                    'Sukses!',
                    'Berhasil melakukan request pembatalan SO.',
                    'success'
                    )
                    $("#modal-selesai").modal("hide");
                });
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Cancelled',
                'Batal melakukan request pembatalan:)',
                'error'
                )
            }
            })
        },
        batalselesai(rs){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ml-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin membatalkan request Selesai SO ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya, Yakin!',
            cancelButtonText: 'Tidak!',
            reverseButtons: true
            }).then((result) => {
            if (result.value) {
                this.input.rs="N";
                this.input.alasselesai="";
                    axios.put("/api/so/"+rs.nomor_so,this.input)
                    .then(res=>{
                        this.getSo();
                        swalWithBootstrapButtons.fire(
                        'Sukses!',
                        'Request Selesai berhasil dibatalkan.',
                        'success'
                        )
                    });
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Cancelled',
                'Batalkan melakukan permintaan :)',
                'error'
                )
            }
            })
        },
        infoTolak(rs){
            this.nso=rs.nomor_so;
            this.alastolak=rs.alastolakselesai;
            $("#modal-tolak").modal("show");
        },
        requlang(){
            $("#modal-tolak").modal("hide");
            $("#modal-selesai").modal("show");
        },
        bukalagi(rs){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ml-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin membuka kembali SO ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Iya, Yakin!',
            cancelButtonText: 'Tidak!',
            reverseButtons: true
            }).then((result) => {
            if (result.value) {
                 axios.put("/api/so/"+rs.nomor_so,{status:'Acc',rs:'N',alasselesai:'',alastolakselesai:''})
                .then(res=>{
                    this.getSo();
                    swalWithBootstrapButtons.fire(
                    'Sukses!',
                    'Berhasil membuka ulang SO.',
                    'success'
                    )
                })
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Cancelled',
                'Batal membuka ulang SO :)',
                'error'
                )
            }
            })
        }
    }
}
</script>


<style>

</style>