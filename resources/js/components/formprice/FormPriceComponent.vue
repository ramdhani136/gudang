<template>
<div class="container">
    <router-link to="/formprice/create" class="btn btn-success my-3">+ Input Harga</router-link>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 float-right">
        <select name="status" v-model="status" class="form-control">
            <option value="Draft">Draft</option>
            <option value="Kordinator">Request Kordinator</option>
            <option value="Supervisor">Request SPV</option>
            <option value="Confirm">Confirmed</option>
            <option value="Tolak">Di tolak</option>
        </select>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Permintaan</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Sales</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(fp , index) in Filterform" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewformprice',params:{nomor:fp.nomor_price}}" class="btn btn-none">
                            {{fp.nomor_price}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{fp.tanggal}}</td>
                    <td>{{fp.customer}}</td>
                    <td>{{fp.user}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(fp)" class="btn btn-primary">Lihat History</button>
                        <button @click="deleteFrom(fp)" class="btn btn-danger">Batalkan</button>
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
            formprice: [],
            status: 'Kordinator',
            load: true,
            tampil: false,
            history: {},
            historyview: {},
            statusnya: ''
        }
    },
    created() {
        this.getform();
    },
    computed: {
        Filterform() {
            if (this.search === "") {
                if (this.status === "Draft") {
                    return this.formprice.filter(elem => elem.status === "Draft")
                } else if (this.status === "Kordinator") {
                    return this.formprice.filter(elem => elem.status === "Kordinator")
                } else if (this.status === "Supervisor") {
                    return this.formprice.filter(elem => elem.status === "Supervisor")
                } else if (this.status === "Confirm") {
                    return this.formprice.filter(elem => elem.status === "Confirm")
                } else if (this.status === "Tolak") {
                    return this.formprice.filter(elem => elem.status === "Tolak")
                }

            } else {
                return this.formprice.filter(elem => {
                    return elem.nomor_price.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        },
    },
    methods: {
        getform() {
            if (this.ambiluser.sales === 1) {
                axios.get("/api/formprice/")
                    .then(res => {
                        this.formprice = res.data.data;
                        this.load = false;
                    })
            } else {
                axios.get("/api/formprice/data/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.formprice = res.data.data;
                        this.load = false;
                    })
            }
        },
        showhistory(fr) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + fr.nomor_price + "/Cp")
                .then(res => {
                    this.historyview = res.data.data;
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
        deleteFrom(fp) {
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
                text: "Ingin menghapus permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete("/api/formprice/" + fp.nomor_price)
                        .then(res => {
                            this.load = false;
                            this.getform();
                            swalWithBootstrapButtons.fire(
                                'Deleted!',
                                'Berhasil menghapus permintaan.',
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
                        'Batal menghapus permintaan :)',
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
