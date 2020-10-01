<template>
<div class="container" @keyup.esc="resetForm()">
    <button @click="showmodal()" class="btn btn-success my-3">+ Tambah Group</button>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Area</th>
                    <th>keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(gr , index) in FilteredGroup" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">{{gr.kode}}</td>
                    <td>{{gr.area}}</td>
                    <td>{{gr.keterangan}}</td>
                    <td style="text-align:center">{{gr.status}}</td>
                    <td style="text-align:center">
                        <button @click="updateGroup(gr)" class="btn btn-primary">Edit</button>
                        <button @click="deleteGroup(gr)" class="btn btn-danger">Hapus</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Form Group SO</h5>
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
                        <label>Kode</label>
                        <input v-model="form.kode" type="text" name="nama" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Area</label>
                        <input v-model="form.area" type="text" name="keterangan" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" v-model="form.keterangan"></textarea>
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
                    <button type="button" @click="createGroup()" class="btn btn-primary">Save changes</button>
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
            groupso: [],
            form: {
                status: 'Aktif'
            },
            errors: [],
            edit: false,
            load: true,
            kode: ''
        }
    },
    created() {
        this.getGroup()
    },
    computed: {
        FilteredGroup() {
            return this.groupso.filter(elem => {
                return elem.area.toLowerCase().includes(this.search.toLowerCase());
            });
        }
    },
    methods: {
        getGroup() {
            axios.get("/api/groupso")
                .then(res => {
                    this.groupso = res.data.data;
                    this.load = false;
                })
        },
        showmodal() {
            this.errors = [];
            $("#modal-form").modal("show");
        },
        createGroup() {
            this.load = true;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menambahkan/merubah data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.edit === false) {
                        axios.post("/api/groupso", this.form)
                            .then((response) => {
                                this.getGroup();
                                $("#modal-form").modal("hide");
                                swalWithBootstrapButtons.fire(
                                    'Sukses!',
                                    'Berhasil menambah/merubah data.',
                                    'success'
                                )
                                this.load = false;
                                this.resetForm();
                            })
                            .catch(error => {
                                this.load = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Pastikan kembali inputan anda!',
                                })
                            })
                    } else {
                        axios.put("/api/groupso/" + this.kode, this.form)
                            .then((response) => {
                                this.getGroup();
                                $("#modal-form").modal("hide");
                                swalWithBootstrapButtons.fire(
                                    'Sukses!',
                                    'Berhasil menambah/merubah data.',
                                    'success'
                                )
                                this.load = false;
                                this.resetForm()
                            })
                            .catch(error => {
                                this.load = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Pastikan kembali inputan anda!',
                                })
                            })
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan input/perubahan :)',
                        'error'
                    )
                }
            })

        },
        updateGroup(gr) {
            this.form.kode = gr.kode
            this.kode = gr.kode;
            this.form.area = gr.area
            this.form.keterangan = gr.keterangan
            this.form.status = gr.status
            this.edit = true;
            this.showmodal();
        },
        deleteGroup(gr) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengahapus ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete("/api/groupso/" + gr.kode)
                        .then(response => {
                            this.getGroup();
                            swalWithBootstrapButtons.fire(
                                'Deleted!',
                                'Berhasil menghapus data.',
                                'success'
                            )
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal menghapus data!',
                            })
                        })

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus data ini :)',
                        'error'
                    )
                }
            })
        },
        resetForm() {
            this.form.kode = "";
            this.form.area = "";
            this.form.keterangan = "";
            this.form.status = "Aktif";
            this.edit = false;
        },
    }
}
</script>

<style>

</style>
