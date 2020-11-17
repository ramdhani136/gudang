<template>
<div class="container">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="filter.nomor" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="filter.status" class="form-control">
            <option value="Sent">Request Masuk</option>
            <option value="Purch">Sent Purchase</option>
            <option value="Confirmed">Sent Marketing</option>
            <option value="So">Open SO</option>
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
                    <th>Sales</th>
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
                    <td style="text-align:center">{{rs.namauser}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(rs)" class="btn btn-primary">
                            Lihat History
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
    <div class="modal fade" id="modal-filter" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="custom ? custom.nama:''"></div>
                        <div class="placeholder" v-if="custom==null" v-text="ket.nama">Pilih Customer</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="up" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama customer ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.kode" v-text="match.nama" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sales</label>
                        <select v-model="filter.id_user" class="form-control">
                            <option value="">Semua user</option>
                            <option :value="us.id" v-for="(us,index) in user" :key="index">{{us.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih Tanggal</label>
                        <select @change="cekjenis()" v-model="filter.jenistanggal" class="form-control">
                            <option value="Y">Bulan Berjalan</option>
                            <option value="N">Filter Tanggal</option>
                        </select>
                    </div>
                    <div v-if="filter.jenistanggal==='N'" class="form-group">
                        <label>Mulai Tanggal</label>
                        <input v-model="filter.mulaitanggal" type="date" class="form-control">
                    </div>
                    <div v-if="filter.jenistanggal==='N'&& filter.mulaitanggal!==undifined" class="form-group">
                        <label>Sampai Tanggal</label>
                        <input v-model="filter.sampaitanggal" type="date" class="form-control" :min="filter.mulaitanggal">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Save Change</button>
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
            rso: [],
            urso: {},
            ya: false,
            load: true,
            historyview: {},
            visible: false,
            query: '',
            selected: 0,
            custom: null,
            itemHeight: 39,
            customer: [],
            filter: {
                kode_customer: '',
                id_user: "",
                jenistanggal: 'Y',
                mulaitanggal: this.FirstDate(),
                sampaitanggal: this.today(),
                status: 'Sent',
                nomor: '',
            },
            user: {},
            ket: {
                nama: "Pilih Customer"
            },
        }
    },
    created() {
        this.getRso()
    },
    computed: {
        FilterKategori() {
            var vm = this,
                lists = vm.rso
            return _.filter(lists, function (query) {
                var tanggal = query.tanggal_rso >= vm.filter.mulaitanggal && query.tanggal_rso <= vm.filter.sampaitanggal,
                    customer = vm.filter.kode_customer ? (query.kode_customer == vm.filter.kode_customer) : true,
                    user = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    nomorrso = vm.filter.nomor ? (query.nomor_rso.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true;
                return user && customer && tanggal && nomorrso && status;
            })
        },
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    showfilter() {
        $("#modal-filter").modal("show");
    },

    methods: {
        getRso() {
            axios.get("/api/rso/data/realrso/")
                .then(res => {
                    this.rso = res.data.data
                }).then(res => {
                    axios.get("/api/customer").then(res => {
                        this.customer = res.data.data;
                    }).then(res => {
                        axios.get("/api/user/data/all").then(res => {
                            this.user = res.data.data;
                            this.load = false;
                        })
                    })
                })
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
        FirstDate() {
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
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + "01";
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
            this.datetimes = this.dates;
            return this.datetimes;
        },
        today() {
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
            this.datetimes = this.dates;
            return this.datetimes;
        },
        toggleVisible() {
            this.visible = !this.visible;
        },
        itemClicked(index) {
            this.selected = index;
            this.selectItem();
        },
        selectItem() {
            this.custom = this.matches[this.selected];
            this.filter.kode_customer = this.custom.kode;
            this.visible = false;
        },
        up() {
            if (this.selected == 0) {
                return;
            }
            this.selected -= 1;
            this.scrollToItem();
        },
        down() {
            if (this.selected >= this.matches.length - 1) {
                return;
            }
            this.selected += 1;
            this.scrollToItem();
        },
        scrollToItem() {
            this.$refs.optionList.scrollTop = this.selected * this.itemHeight;
        },
        showfilter() {
            $("#modal-filter").modal("show");
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
