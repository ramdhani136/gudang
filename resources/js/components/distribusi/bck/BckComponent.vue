<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="open">Open</option>
            <option value="close">Close</option>
        </select>
    </div>
    <div class="row">
        <router-link to="/bck/create" class="btn btn-success my-3">+ Create Bukti Checker</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Checker Keluar</th>
                    <th>Tanggal</th>
                    <th>Nomor SO</th>
                    <th>Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(bk,index) in FilteredBck" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewbck',params:{bck:bk.bck}}" class="btn btn-none">
                            {{bk.bck}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{bk.tanggal}}</td>
                    <td style="text-align:center">{{bk.nomor_so}}</td>
                    <td>{{bk.customer}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(bk)" class="btn btn-primary">
                            Lihat History
                        </button>
                        <button @click="batalkan(bk)" class="btn btn-danger">Batalkan</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History RSO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor RSO</th>
                                <th>keterangan</th>
                                <th>Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(hs,index) in historyview" :key="index">
                                <td>{{hs.tanggal}}</td>
                                <td>{{hs.nomor_dok}}</td>
                                <td>{{hs.keterangan}}</td>
                                <td>{{hs.user}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            load: true,
            status: 'open',
            bck: [],
            historyview: {},
            history: {},
            listbck: {},
            listso: {},
            statusbck: '',
            qtybck: 0,
        }
    },
    created() {
        this.getBck();
    },
    computed: {
        FilteredBck() {
            if (this.search === "") {
                if (this.status === "open") {
                    return this.bck.filter(elem => elem.status === "open")
                } else if (this.status === "close") {
                    return this.bck.filter(elem => elem.status === "close")
                }
            } else {
                return this.bck.filter(elem => {
                    return elem.nomor_so.toLowerCase().includes(this.search).toLowerCase();
                });
            }
        },
    },
    methods: {
        getBck() {
            axios.get("/api/bck")
                .then(res => {
                    this.bck = res.data.data;
                    this.load = false;
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
        },
        showhistory(bk) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + bk.bck + "/Bck")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
        batalkan(bk) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin membatalkan form checker ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                this.load = true;
                if (result.isConfirmed) {
                    /* edit status bck so */
                    axios.put("/api/so/" + bk.nomor_so, {
                            closebck: "N"
                        })
                        .then(res => {
                            /* edit listso */
                            axios.get("/api/listbck/" + bk.bck)
                                .then(res => {
                                    this.listbck = res.data.data;
                                    for (let i = 0; i < this.listbck.length; i++) {
                                        this.qtybck = 0;
                                        axios.get("/api/listso/data/" + bk.nomor_so + "/" + this.listbck[i].kode_barang)
                                            .then(res => {
                                                this.listso = res.data.data;
                                                this.qtybck = parseInt(this.listso[0].bck) - parseInt(this.listbck[i].qty);
                                                axios.put("/api/listso/" + this.listso[0].id, {
                                                    bck: this.qtybck,
                                                    closeso: "N"
                                                })
                                            })
                                    }
                                })
                        }).then(res => {
                            axios.delete("/api/bck/" + bk.bck)
                                .then(res => {
                                    axios.get("/api/history/" + bk.bck)
                                        .then(res => {
                                            this.history = res.data.data;
                                            for (let i = 0; i < this.history.length; i++) {
                                                axios.delete("/api/history/" + this.history[i].id);
                                            }
                                        }).then(res => {
                                            axios.post("/api/history", {
                                                nomor_dok: bk.nomor_so,
                                                id_user: this.ambiluser.id,
                                                notif: "BCK " + bk.bck + " di batalkan",
                                                keterangan: "Membatalkan BCK nomor : " + bk.bck,
                                                jenis: "So",
                                                tanggal: this.DateTime(),
                                            }).then(res => {
                                                this.load = false;
                                                swalWithBootstrapButtons.fire(
                                                    'Deleted!',
                                                    'Bukti Checker Keluar di batalkan.',
                                                    'success'
                                                )
                                                this.getBck();
                                            })
                                        })

                                });
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus BCK :)',
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
