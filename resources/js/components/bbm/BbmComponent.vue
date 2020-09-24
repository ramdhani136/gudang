<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="open">Selesai</option>
            <!-- <option value="close">Close</option> -->
        </select>
    </div>
    <div class="row">
        <router-link to="/dic/incoming/view" class="btn btn-success my-3">+ Create BBM</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor BBM</th>
                    <th>Tanggal</th>
                    <th>Nomor PO</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(bm,index) in FilteredBBM" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewbbm',params:{nomor:bm.bbm}}" class="btn btn-none">
                            {{bm.bbm}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{bm.tanggal}}</td>
                    <td style="text-align:center">{{bm.nomor_po}}</td>
                    <td>{{bm.supplier}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(bm)" class="btn btn-primary">
                            Lihat History
                        </button>
                        <button class="btn btn-danger">Batalkan</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History BBm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor BBM</th>
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
            bbm: [],
            history: {},
            historyview: {}
        }
    },
    created() {
        this.getBbm();
    },
    computed: {
        FilteredBBM() {
            if (this.search === "") {
                if (this.status === "open") {
                    return this.bbm.filter(elem => elem.status === "open")
                } else if (this.status === "close") {
                    return this.bbm.filter(elem => elem.status === "close")
                }
            } else {
                return this.bbm.filter(elem => {
                    return elem.nomor_po.toLowerCase().includes(this.search);
                });
            }
        },
    },
    methods: {
        getBbm() {
            axios.get("/api/bbm")
                .then(res => {
                    this.bbm = res.data.data;
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
        showhistory(bm) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + bm.bbm + "/Bbm")
                .then(res => {
                    this.historyview = res.data.data;
                    console.log(this.historyview)
                })
        },
    }
}
</script>

<style>

</style>
