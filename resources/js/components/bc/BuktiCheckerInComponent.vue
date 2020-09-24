<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="sent">Menunggu Acc</option>
            <option value="open">Open</option>
            <option value="tolak">Tolak</option>
            <option value="close">Close</option>
        </select>
    </div>
    <div class="row" v-if="ambiluser.incoming===1 || ambiluser.superadmin===1">
        <router-link to="/dic/bcm/view" class="btn btn-success my-3">+ Create Bukti Checker</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Checker Masuk</th>
                    <th>Tanggal</th>
                    <th>Nomor PO</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(bm,index) in FilteredBCM" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewbcm',params:{nomor:bm.bcm}}" class="btn btn-none">
                            {{bm.bcm}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{bm.tanggal}}</td>
                    <td style="text-align:center">{{bm.nomor_po}}</td>
                    <td>{{bm.supplier}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(bm)" class="btn btn-primary">Lihat History</button>
                        <button v-if="bm.status!=='close' && (ambiluser.incoming===1 || ambiluser.superadmin===1)" @click="batal(bm)" class="btn btn-danger">Batalkan</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History BCM</h5>
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
            load: false,
            status: '',
            bcm: [],
            historyview: {},
            listbcm: {},
            listpo: {},
            history: {}
        }
    },
    created() {
        this.getBcm();
    },
    computed: {
        FilteredBCM() {
            if (this.search === "") {
                if (this.status === "draft") {
                    return this.bcm.filter(elem => elem.status === "draft")
                } else if (this.status === "open") {
                    return this.bcm.filter(elem => elem.status === "open")
                } else if (this.status === "close") {
                    return this.bcm.filter(elem => elem.status === "close")
                } else if (this.status === "sent") {
                    return this.bcm.filter(elem => elem.status === "sent")
                } else if (this.status === "tolak") {
                    return this.bcm.filter(elem => elem.status === "tolak")
                }
            } else {
                return this.bcm.filter(elem => {
                    return elem.nomor_po.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        },
    },
    methods: {
        getBcm() {
            axios.get("/api/bcm")
                .then(res => {
                    this.bcm = res.data.data;
                    if (this.ambiluser.incoming === 1) {
                        this.status = "open";
                    } else {
                        this.status = "sent";
                    }
                })
        },
        showhistory(bm) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + bm.bcm + "/Bcm")
                .then(res => {
                    this.historyview = res.data.data;
                    console.log(this.historyview);
                })
        },
        batal(bm) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus checker masuk ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    /* batalin selesai po */
                    axios.put("/api/po/" + bm.nomor_po, {
                            poselesai: "N"
                        })
                        .then(res => {
                            /* Kembalikan sisaso dan status po & polist */
                            axios.get("/api/listbcm/" + bm.bcm)
                                .then(res => {
                                    this.listbcm = res.data.data;
                                    this.sisapo = 0;
                                    for (let i = 0; i < this.listbcm.length; i++) {
                                        axios.get("/api/listpo/data/" + bm.nomor_po + "/" + this.listbcm[i].kode_barang)
                                            .then(res => {
                                                this.listpo = res.data.data;
                                                this.sisapo = parseInt(this.listpo[0].sisapo) + parseInt(this.listbcm[i].sj);
                                                axios.put("/api/listpo/" + this.listpo[0].id, {
                                                    sisapo: this.sisapo,
                                                    closepo: "N"
                                                })
                                            })
                                    }
                                })
                            /* End */
                            /* Menghapus bcm */
                            axios.delete("/api/bcm/" + bm.bcm)
                                .then(res => {
                                    axios.get("/api/history/" + bm.bcm)
                                        .then(res => {
                                            this.history = res.data.data;
                                            for (let k = 0; k < this.history.length; k++) {
                                                axios.delete("/api/history/" + this.history[k].id);
                                            }
                                        })
                                    axios.post("/api/history", {
                                        nomor_dok: bm.nomor_po,
                                        nomor_ref: bm.bcm,
                                        id_user: this.ambiluser.id,
                                        notif: "BCM nomor" + bm.bcm + " Di tarik DIC",
                                        keterangan: "Membatalkan Form Checker Keluar " + bm.bcm,
                                        jenis: "Po",
                                        tanggal: this.DateTime(),
                                    })
                                    swalWithBootstrapButtons.fire(
                                        'Deleted!',
                                        'Berhasil menghapus BCK.',
                                        'success'
                                    )
                                    this.getBcm();
                                })
                            /* End */
                        })
                    /* end */
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
    }
}
</script>

<style>

</style>
