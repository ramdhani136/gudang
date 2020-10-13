<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="Kordinator">Request Kordinator</option>
            <option value="Supervisor">Request Supervisor</option>
            <option value="Dic">Request DIC</option>
            <option value="Acc">Open</option>
            <option value="Tolak">Reject</option>
            <option value="Selesai">Selesai</option>
        </select>
    </div>
    <div class="row">
        <router-link to="/retur/create" class="btn btn-success my-3">+ Create Retur</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Retur</th>
                    <th>Tanggal</th>
                    <th>Nomor SO</th>
                    <th>Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rt,index) in FilteredRetur" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewretur',params:{nomor:rt.nomor_retur}}" class="btn btn-none">
                            {{rt.nomor_retur}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{rt.tanggal}}</td>
                    <td style="text-align:center">{{rt.nomor_so}}</td>
                    <td>{{rt.customer}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(rt)" class="btn btn-primary">
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
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History Retur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor Retur</th>
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
            status: 'Acc',
            retur: [],
            historyview: {}
        }
    },
    created() {
        this.getretur();
    },
    computed: {
        FilteredRetur() {
            if (this.search === "") {
                if (this.status === "Kordinator") {
                    return this.retur.filter(elem => elem.status === "Kordinator")
                } else if (this.status === "Supervisor") {
                    return this.retur.filter(elem => elem.status === "Supervisor")
                } else if (this.status === "Dic") {
                    return this.retur.filter(elem => elem.status === "Dic")
                } else if (this.status === "Acc") {
                    return this.retur.filter(elem => elem.status === "Acc")
                } else if (this.status === "Tolak") {
                    return this.retur.filter(elem => elem.status === "Tolak")
                } else if (this.status === "Selesai") {
                    return this.retur.filter(elem => elem.status === "Selesai")
                }
            } else {
                return this.retur.filter(elem => {
                    return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        },
    },
    methods: {
        getretur() {
            if (this.ambiluser.susales === 1) {
                axios.get("/api/retur")
                    .then(res => {
                        this.retur = res.data.data;
                        this.load = false;
                    })
            } else {
                axios.get("/api/retur/akses/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.retur = res.data.data;
                        this.load = false;
                    })
            }
        },
        showhistory(rt) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + rt.nomor_retur + "/Rt")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
    }
}
</script>

<style>

</style>
