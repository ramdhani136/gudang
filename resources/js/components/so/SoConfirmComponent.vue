<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="Kordinator">Request Kordinator</option>
            <option value="Sent">Request SPV</option>
            <option value="Dic">Request DIC</option>
            <option value="Acc">Open</option>
            <option value="Tolak">Rejected</option>
            <option value="Selesai">Selesai</option>
            <option value="Di Selesaikan">Di Selesaikan</option>
        </select>
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
                        <router-link :to="{name:'viewso',params:{id:rs.nomor_so}}" class="btn btn-none">
                            {{rs.nomor_so}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{rs.tanggal_so}}</td>
                    <td>{{rs.customer}}</td>
                    <td style="text-align:center">{{rs.tanggal_kirim}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(rs)" class="btn btn-primary">
                            Lihat Rincian
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-history" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History SO</h5>
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
            status: '',
            so: [],
            load: true,
            historyview: {}
        }
    },
    created() {
        this.getSo();
    },
    computed: {
        FilterKategori() {
            if (this.search === "") {
                if (this.status === "Sent") {
                    return this.so.filter(elem => elem.status === "Sent")
                } else if (this.status === "Acc") {
                    return this.so.filter(elem => elem.status === "Acc")
                } else if (this.status === "Tolak") {
                    return this.so.filter(elem => elem.status === "Tolak")
                } else if (this.status === "Selesai") {
                    return this.so.filter(elem => elem.status === "Selesai")
                } else if (this.status === "Di Selesaikan") {
                    return this.so.filter(elem => elem.status === "Di Selesaikan")
                } else if (this.status === "Dic") {
                    return this.so.filter(elem => elem.status === "Dic")
                } else if (this.status === "Kordinator") {
                    return this.so.filter(elem => elem.status === "Kordinator")
                }
            } else {
                return this.so.filter(elem => {
                    return elem.nomor_so.toLowerCase().includes(this.search);
                });
            }
        },
    },
    methods: {
        getSo() {
            axios.get("/api/so")
                .then(res => {
                    this.so = res.data.data
                    if (this.ambiluser.susales === 1) {
                        this.status = "Sent"
                    } else {
                        this.status = "Kordinator"
                    }
                    console.log(this.status);
                    this.load = false;
                });
        },
        showhistory(rs) {
            $("#modal-history").modal("show");
            axios.get("/api/history/data/" + rs.nomor_so + "/So")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
    }
}
</script>

<style>

</style>
