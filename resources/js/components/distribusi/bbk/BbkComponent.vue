<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="open">List BBK</option>
        </select>
    </div>
    <div class="row">
        <router-link to="/distribusi/bbk/create" class="btn btn-success my-3">+ Create BBK</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor BBK</th>
                    <th>Tanggal</th>
                    <th>Nomor BCK</th>
                    <th>Nomor SO</th>
                    <th>Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(bk,index) in FilteredBbk" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewbbk',params:{bbk:bk.bbk}}" class="btn btn-none">
                            {{bk.bbk}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{bk.tanggal}}</td>
                    <td style="text-align:center">{{bk.nomor_bck}}</td>
                    <td style="text-align:center">{{bk.nomor_so}}</td>
                    <td>{{bk.customer}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(bk)" class="btn btn-primary">
                            Lihat History
                        </button>
                        <button @click="batalkan(bk)" class="btn btn-danger mt-1">
                            Batalkan
                        </button>
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
                                <th>Nomor BBK</th>
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
            bbk: [],
            history: {},
            historyview: {},
            listbbk: {},
            listso: {},
            qtybbk: 0,
            closesoplus: [],
            banding: [],
            tclose: '',
            tband: '',
            listsofix: {},
            listbck: {},
            history: {},
            listsonya: {},
            qtybck: 0,
            listmu: [],
            lebih: 0
        }
    },
    created() {
        this.getBbk();
    },
    computed: {
        FilteredBbk() {
            if (this.search === "") {
                if (this.status === "open") {
                    return this.bbk.filter(elem => elem.status === "open")
                } else if (this.status === "close") {
                    return this.bbk.filter(elem => elem.status === "close")
                }
            } else {
                return this.bbk.filter(elem => {
                    return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        },
    },
    methods: {
        getBbk() {
            axios.get("/api/bbk")
                .then(res => {
                    this.bbk = res.data.data;
                    this.load = false;
                })
        },
        showhistory(bk) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + bk.bbk + "/Bbk")
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
                text: "ingin membatalkan form BBK ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    /* Ubah status so */
                    axios.put("/api/so/" + bk.nomor_so, {
                        status: 'Acc',
                        closebck: 'N'
                        /* end */
                    }).then(res => {
                        /* status open bck */
                        axios.put("/api/bck/" + bk.nomor_bck, {
                            status: 'draft'
                            /* end */
                        }).then(res => {
                            this.closesoplus = [];
                            this.banding = []
                            axios.get("/api/listbbk/" + bk.bbk)
                                .then(res => {
                                    this.listbbk = res.data.data;
                                    this.qtybbk = 0;
                                    this.closeso = '';
                                    this.qtybck = 0;
                                    this.lebih = 0;
                                    for (let i = 0; i < this.listbbk.length; i++) {
                                        axios.get("/api/listbck/view/" + bk.nomor_bck + "/" + this.listbbk[i].kode_barang)
                                            .then(res => {
                                                this.listbck = res.data.data;
                                                axios.get("/api/listso/data/" + bk.nomor_so + "/" + this.listbbk[i].kode_barang)
                                                    .then(res => {
                                                        this.listsonya = res.data.data;
                                                        this.listmu.push(this.listsonya[0]);
                                                        axios.get("/api/listbck/view/" + bk.nomor_bck + "/" + this.listbbk[i].kode_barang)
                                                            .then(res => {
                                                                this.listbck = res.data.data;
                                                                if ((parseInt(this.listmu[i].bbk) - parseInt(this.listmu[i].qty)) > 0) {
                                                                    this.lebih = parseInt(this.listmu[i].bbk) - parseInt(this.listmu[i].qty)
                                                                } else {
                                                                    this.lebih = 0;
                                                                }
                                                                this.qtybck = parseInt(this.listbbk[i].qty) - parseInt(this.lebih);
                                                                console.log(this.qtybck);
                                                                axios.put("/api/listbck/" + this.listbck[0].id, {
                                                                    qty: this.qtybck,
                                                                })
                                                            })
                                                    })
                                            }).then(res => {
                                                this.a = '';
                                                axios.get("/api/listso/data/" + bk.nomor_so + "/" + this.listbbk[i].kode_barang)
                                                    .then(res => {
                                                        this.listso = res.data.data;
                                                        this.qtybbk = parseInt(this.listso[0].bbk) - parseInt(this.listbbk[i].qty);
                                                        if (this.listso[0].bck >= this.listso[0].qty) {
                                                            this.closeso = "Y";
                                                        } else {
                                                            this.closeso = "N";
                                                        }
                                                        axios.put("/api/listso/" + this.listso[0].id, {
                                                            bbk: this.qtybbk,
                                                            closeso: this.closeso
                                                        })
                                                    })
                                            })
                                    };
                                }).then(res => {
                                    axios.get("/api/listso/" + bk.nomor_so)
                                        .then(res => {
                                            this.listsofix = res.data.data;
                                            this.a = "";
                                            this.aplus = "";
                                            this.aband = "";
                                            for (let i = 0; i < this.listsofix.length; i++) {
                                                if (this.listsofix[i].bck >= this.listsofix[i].qty) {
                                                    this.a = "Y";
                                                } else {
                                                    this.a = "N";
                                                }
                                                this.aplus += this.a;
                                                this.aband += "Y";
                                            }
                                            if (this.aplus === this.aband) {
                                                axios.put("/api/so/" + bk.nomor_so, {
                                                    closebck: "Y"
                                                });
                                            } else {
                                                axios.put("/api/so/" + bk.nomor_so, {
                                                    closebck: "N"
                                                });
                                            }
                                        }).then(res => {
                                            axios.delete("/api/bbk/" + bk.bbk).then(res => {
                                                axios.post("/api/history", {
                                                    nomor_dok: bk.nomor_bck,
                                                    id_user: this.ambiluser.id,
                                                    notif: "Membatalkan BBK!",
                                                    keterangan: "form BBK nomor : " + bk.bbk + " di batalkan",
                                                    jenis: "Bck",
                                                    tanggal: this.DateTime(),
                                                }).then(res => {
                                                    axios.get("/api/history/" + bk.bbk)
                                                        .then(res => {
                                                            this.history = res.data.data
                                                            for (let l = 0; l < this.history.length; l++) {
                                                                axios.delete("/api/history/" + this.history[l].id)
                                                            }
                                                        }).then(res => {
                                                            this.load = false;
                                                            swalWithBootstrapButtons.fire(
                                                                'Deleted!',
                                                                'Berhasil menghasil form BBK.',
                                                                'success'
                                                            )
                                                            this.getBbk();
                                                        })
                                                })

                                            })
                                        })
                                })
                        })
                    })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus form bbk :)',
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
