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
            listbck: {}
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
            /* Ubah status so */
            axios.put("/api/so/" + bk.nomor_so, {
                status: 'Acc',
                closebck: 'N'
                /* end */
            }).then(res => {
                /* status open bck */
                axios.put("/api/bck/" + bk.nomor_bck, {
                    status: 'open'
                    /* end */
                }).then(res => {
                    this.closesoplus = [];
                    this.banding = []
                    axios.get("/api/listbbk/" + bk.bbk)
                        .then(res => {
                            this.listbbk = res.data.data;
                            this.qtybbk = 0;
                            this.closeso = '';
                            for (let i = 0; i < this.listbbk.length; i++) {
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
                                        }).then(res => {
                                            axios.get("/api/listbck/" + bk.nomor_bck + "/" + this.listbbk[i].kode_barang)
                                                .then(res => {
                                                    this.listbck = res.data.data;
                                                    axios.put("/api/listbck/" + this.listbck[0].id, {
                                                        qty: (parseInt(this.listso[0].qty) - parseInt(this.listso[0].bbk)) + parseInt(this.listbbk[i].qty),
                                                    })
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

                                    })
                                })
                        })
                })
            })
        }
    }
}
</script>

<style>

</style>
