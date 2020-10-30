<template>
<div class="container" @keyup.esc="resetForm()">
    <router-link to="/purchase/po/create" class="btn btn-success my-3">+ Create PO</router-link>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Diminta</th>
                    <th>PR Open</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(prlist , index) in FilteredPr" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td>{{prlist.kode_barang}}</td>
                    <td>{{prlist.nama}}</td>
                    <td style="text-align:center">{{prlist.jumlah}}</td>
                    <td style="text-align:center">{{prlist.potersedia}}</td>
                    <td style="text-align:center">{{prlist.satuan}}</td>
                    <td style="text-align:center">
                        <button @click="showmodal(prlist)" class="btn btn-primary">Lihat Rincian</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-pr" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="scrollList">
                        <div class="form-group mr-3">
                            <label>Kode Barang</label>
                            <input v-model="ket.kode" type="text" class="form-control" disabled>
                        </div>
                        <div class="form-group mr-3">
                            <label>Nama Barang</label>
                            <input v-model="ket.nama" type="text" class="form-control" disabled>
                        </div>
                        <div class="form-group mr-3">
                            <label>Jumlah Permintaan</label>
                            <input v-model="ket.total" type="text" class="form-control" disabled>
                        </div>
                        <table id="thead" class="table table-striped table-bordered" style="width:97%">
                            <thead>
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Nomor SO</th>
                                    <th>Customer</th>
                                    <th style="text-align:center">Jumlah</th>
                                    <th style="text-align:center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prl,index) in viewlistpr" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td style="text-align:center">{{prl.nomor_so}}</td>
                                    <td>{{prl.customer}}</td>
                                    <td style="text-align:center">{{prl.jumlah}}</td>
                                    <td style="text-align:center">{{prl.satuan}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
            pr: [],
            load: true,
            listPr: {},
            tujuan: {},
            prlist: {},
            upload: {
                nomor_po: this.po_nomor(),
                tanggal_po: this.now(),
                tanggal_datang: this.now(),
            },
            viewlistpr: {},
            ket: {}
        }
    },
    created() {
        this.getPr();
        this.getSupplier();
        this.getPurchasing();
    },
    computed: {
        FilteredPr() {
            return this.pr.filter(elem => {
                return elem.nama.toLowerCase().includes(this.search);
            });
        },
    },
    methods: {
        getSupplier() {
            axios.get("/api/supplier")
                .then(res => {
                    this.supplier = res.data.data;
                });
        },
        getPr() {
            axios.get("/api/listso/data/group")
                .then(res => {
                    this.pr = res.data.data
                    this.load = false;
                });
        },
        getPurchasing() {
            axios.get("/api/purchasing")
                .then(res => {
                    this.purchasing = res.data.data;
                });
        },
        showmodal(prlist) {
            this.tujuan.kode = prlist.kode_barang;
            axios.get("/api/listso/data/group/" + this.tujuan.kode)
                .then(res => {
                    this.viewlistpr = res.data.data;
                    this.ket.kode = this.viewlistpr[0].kode;
                    this.ket.nama = this.viewlistpr[0].nama_barang;
                    this.ket.total = prlist.jumlah;
                });
            $("#modal-pr").modal("show");
        },
        now() {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') + day;
            return output
        },
        validate() {
            if (this.form.tanggal_rso < this.now()) {
                this.form.tanggal_rso = this.now();
            }
        },
        po_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "PO-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        resetForm() {
            this.upload = {
                nomor_po: this.po_nomor(),
                tanggal_po: this.now(),
                tanggal_datang: this.now(),
            };
        },

    }
}
</script>

<style>
#scrollList {
    width: 100%;
    max-height: 400px;
    overflow-y: scroll;
    font-size: 0.9em;
}
</style>
