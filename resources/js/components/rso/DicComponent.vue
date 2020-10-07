<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="Sent">Request Masuk</option>
            <option value="Purch">Sent Purchase</option>
            <option value="Confirmed">Sent Marketing</option>
        </select>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor RSO</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="rs.status!=='Draft'" v-for="(rs , index) in FilterKategori" :key="rs.nomor_rso">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'dicform',params:{id:rs.nomor_rso}}" class="btn btn-none">
                            {{rs.nomor_rso}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{rs.tanggal_rso}}</td>
                    <td>{{rs.customer}}</td>
                    <td v-if="rs.status=='Sent'" style="text-align:center">
                        <button @click="showhistory(rs)" class="btn btn-primary">
                            Lihat History
                        </button>
                    </td>
                    <td v-if="rs.status!=='Sent'" style="text-align:center">
                        <router-link :to="{name:'dicform',params:{id:rs.nomor_rso}}" class="btn btn-primary">
                            Lihat RSO
                        </router-link>
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
    components: {
        Circle5
    },
    data() {
        return {
            search: '',
            rso: [],
            status: 'Sent',
            urso: {},
            ya: false,
            load: true,
            historyview: {},
        }
    },
    created() {
        this.getRso()
    },
    computed: {
        FilterKategori() {
            if (this.search === "") {
                if (this.status === "Sent") {
                    return this.rso.filter(elem => elem.status === "Sent")
                } else if (this.status === "Confirmed") {
                    return this.rso.filter(elem => elem.status === "Confirmed")
                } else if (this.status === "Purch") {
                    return this.rso.filter(elem => elem.status === "Purch")
                }
            } else {
                return this.rso.filter(elem => {
                    return elem.nomor_rso.toLowerCase().includes(this.search);
                });
            }
        }
    },
    methods: {
        getRso() {
            axios.get("/api/rso")
                .then(res => {
                    this.rso = res.data.data
                    this.load = false;
                });
        },
        ConfirmRso(rs) {
            let keputusan = confirm('Apakah anda yakin ingin mengkonfirmasi RSO ini?');
            if (keputusan === true) {
                this.getRso()
                this.urso.kode_customer = rs.kode_customer
                this.urso.status = "Confirmed"
                this.urso.nomor_rso = rs.nomor_rso
                this.urso.id_user = rs.id_user
                this.urso.tanggal_rso = rs.tanggal_rso
                axios.put(`/api/rso/` + this.urso.nomor_rso, this.urso)
                    .then((response) => {
                        this.getRso()
                    })
            }
        },
        showhistory(rs) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + rs.nomor_rso + "/RSO")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
    }
}
</script>

<style>
#overflow {
    width: 100%;
    height: 440px;
    overflow-y: scroll;
}

#thead thead tr th {
    text-align: center;
    border-bottom: none;
    position: sticky;
    top: 0;
    background-color: #fff;
    top: -1px;
    border-collapse: collapse;
    box-shadow: inset 0 0 0 #dee2e6,
        inset 0 -1px 0 #dee2e6;
}

#modal-width {
    width: 120%;
    height: auto;
    right: 13%;
}
</style>
