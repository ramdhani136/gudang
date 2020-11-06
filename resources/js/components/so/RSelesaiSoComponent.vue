<template>
<div class="container" @keyup.esc="resetForm()">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor SO</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(ln,index) in Filteredlist" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td>
                        <router-link :to="{name:'viewso',params:{id:ln.nomor_so}}" class="btn btn-none">
                            {{ln.nomor_so}}
                        </router-link>
                    </td>
                    <td>{{ln.tanggal_so}}</td>
                    <td style="text-align:center">{{ln.customer}}</td>
                    <td>
                        <textarea v-model="ln.alasselesai" class="form-control" disabled></textarea>
                    </td>
                    <td style="text-align:center">
                        <button v-if="ambiluser.susales===1" @click="terima(ln)" class="btn btn-success">Terima</button>
                        <button v-if="ambiluser.kordisales===1" @click="kirimspv(ln)" class="btn btn-success">Terima</button>
                        <button @click="tolak(ln)" class="btn btn-danger">Tolak</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Customer</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="ket.tolak" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimTolak()" class="btn btn-primary">Kirim Penolakan</button>
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
    props: ['ambiluser'],
    components: {
        Circle5
    },
    data() {
        return {
            search: '',
            list: [],
            form: {
                kode: '',
                nama: '',
                status: 'Aktif'
            },
            errors: [],
            edit: false,
            target: '',
            load: true,
            up: {},
            ket: {},
            listso: {},
            bck: {},
            qtybck: 0,
            viewlist: {},
            ambil: {
                qty: []
            },
            bckfix: 0,
            history: {}
        }
    },
    created() {
        this.getlist()
    },
    computed: {
        Filteredlist() {
            return this.list.filter(elem => {
                return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
            });
        }
    },
    methods: {
        getlist() {
            if (this.ambiluser.kordisales === 1) {
                axios.get("/api/so/selesai/kordinator/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.list = res.data.data
                        this.load = false;
                    });
            } else if (this.ambiluser.susales === 1) {
                axios.get("/api/so/selesai/supervisor")
                    .then(res => {
                        this.list = res.data.data
                        this.load = false;
                    });
            }

        },
        terima(ln) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menerima permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    axios.put("/api/so/" + ln.nomor_so, {
                            status: 'Di Selesaikan'
                        })
                        .then(res => {
                            axios.get('/api/listso/' + ln.nomor_so)
                                .then(res => {
                                    this.listso = res.data.data;
                                    this.qtybck = 0;
                                    this.bckfix = 0;
                                    for (let a = 0; a < this.listso.length; a++) {
                                        this.qtybck = parseFloat(this.listso[a].bck) - parseFloat(this.listso[a].bbk);
                                        if (this.qtybck < 1) {
                                            this.qtybck = 0;
                                        }
                                        this.bckfix = parseFloat(this.listso[a].qty) - parseFloat(this.qtybck);
                                        this.ambil.qty.push(
                                            this.bckfix
                                        )
                                        axios.get("/api/listso/data/" + this.listso[a].nomor_so + "/" + this.listso[a].kode_barang)
                                            .then(res => {
                                                this.viewlist = res.data.data;
                                                for (let m = 0; m < this.viewlist.length; m++) {
                                                    axios.put("/api/listso/" + this.viewlist[m].id, {
                                                        bck: this.ambil.qty[a],
                                                    })
                                                }
                                            })
                                    }
                                })
                                .then(res => {
                                    axios.get("/api/bck/data/so/" + ln.nomor_so)
                                        .then(res => {
                                            this.bck = res.data.data;
                                            for (let y = 0; y < this.bck.length; y++) {
                                                axios.delete("/api/bck/" + this.bck[y].bck)
                                                    .then(res => {
                                                        axios.get("/api/history/" + this.bck[y].bck)
                                                            .then(res => {
                                                                this.history = res.data.data;
                                                                for (let s = 0; s < this.history.length; s++) {
                                                                    axios.delete("/api/history/" + this.history[s].id);
                                                                }
                                                            })
                                                    })
                                            }
                                        })
                                })
                        }).then(res => {
                            axios.post("/api/history", {
                                nomor_dok: ln.nomor_so,
                                nomor_ref: ln.nomor_rso,
                                id_user: this.ambiluser.id,
                                notif: "permintaan penyelesaian di Acc ",
                                keterangan: "SO di Selesaikan",
                                jenis: "So",
                                tanggal: this.DateTime(),
                            })
                            axios.post("/api/history", {
                                nomor_dok: ln.nomor_rso,
                                nomor_ref: ln.nomor_so,
                                id_user: this.ambiluser.id,
                                notif: "permintaan penyelesaian di Acc ",
                                keterangan: "So nomor : " + ln.nomor_so + " Di Selesaikan",
                                jenis: "RSO",
                                tanggal: this.DateTime(),
                            })
                            axios.post("/api/history", {
                                nomor_dok: ln.nomor_so,
                                nomor_ref: ln.nomor_rso,
                                id_user: this.ambiluser.id,
                                notif: "semua open Bck di batalkan",
                                keterangan: "semua open Bck di batalkan",
                                jenis: "So",
                                tanggal: this.DateTime(),
                            })
                            swalWithBootstrapButtons.fire(
                                'Sukses!',
                                'Berhasil menerima permintaan.',
                                'success'
                            )
                            this.getlist();
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menerima permintaan :)',
                        'error'
                    )
                }
            })
        },
        kirimspv(ln) {
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
                text: "Ingin menerima permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put("/api/so/" + ln.nomor_so, {
                            rss: 'Y'
                        })
                        .then(res => {
                            this.getlist();
                            axios.post("/api/history", {
                                nomor_dok: ln.nomor_so,
                                nomor_ref: ln.nomor_rso,
                                id_user: this.ambiluser.id,
                                notif: "permintaan penyelesaian di kirim ke SPV ",
                                keterangan: "permintaan penyelesaian di kirim ke SPV",
                                jenis: "So",
                                tanggal: this.DateTime(),
                            })
                            axios.post("/api/history", {
                                nomor_dok: ln.nomor_rso,
                                nomor_ref: ln.nomor_so,
                                id_user: this.ambiluser.id,
                                notif: "permintaan di kirim ke Sales SPV ",
                                keterangan: "So nomor : " + ln.nomor_so + " Di kirim ke SPV",
                                jenis: "RSO",
                                tanggal: this.DateTime(),
                            })
                            this.load = false;
                            swalWithBootstrapButtons.fire(
                                'Berhasil!',
                                'Permintaan di kirim ke supervisor.',
                                'success'
                            )
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menerima permintaan :)',
                        'error'
                    )
                }
            })
        },
        tolak(ln) {
            this.ket.id = ln.nomor_so;
            this.ket.rso = ln.nomor_rso;
            $("#modal-tolak").modal("show");
        },
        kirimTolak() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menolak permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.up.alastolakselesai = this.ket.tolak;
                    this.up.rs = "T";
                    axios.put("/api/so/" + this.ket.id, this.up)
                        .then(res => {
                            axios.post("/api/history", {
                                nomor_dok: this.ket.id,
                                nomor_ref: this.ket.rso,
                                id_user: this.ambiluser.id,
                                notif: "Permintaan penyelesaian di tolak ",
                                keterangan: "Permintaan penyelesaian di tolak",
                                jenis: "So",
                                tanggal: this.DateTime(),
                            });
                            swalWithBootstrapButtons.fire(
                                'Sukses!',
                                'Berhasil menolak permintaan ini.',
                                'success'
                            )
                            this.getlist()
                            $("#modal-tolak").modal("hide");
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menolak permintaan ini :)',
                        'error'
                    )
                }
            })
        },
        DateTime() {
            this.date = new Date();
            this.month = this.date.getMonth() + 1;
            this.year = this.date.getFullYear();
            this.hours = this.date.getHours();
            this.minute = this.date.getMinutes();
            this.seconds = this.date.getSeconds();
            if (this.month > 12) {
                this.month = 12;
            }
            this.day = this.date.getDate();
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + this.day;
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
            this.datetimes = this.dates + " " + this.times;
            return this.datetimes;
        }
    }
}
</script>

<style>

</style>
