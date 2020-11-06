<template>
<div class="container">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="filter.nomorso" type="text" class="form-control" placeholder="Input Nomor SO">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="filter.status" class="form-control">
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
                    <th>Sales</th>
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
                    <td style="text-align:center">{{rs.sales}}</td>
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
                            <option value="">Semua Sales</option>
                            <option :value="us.id" v-for="(us,index) in user" :key="index">{{us.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Group</label>
                        <select v-model="filter.kode_groupso" class="form-control" :disabled="ambiluser.susales!==1">
                            <option value="">Semua Group</option>
                            <option :value="gr.kode" v-for="(gr,index) in groupso" :key="index">{{gr.area}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Berdasarkan Tanggal</label>
                        <select @change="cektanggal()" v-model="filter.listtanggal" class="form-control">
                            <option value="so">Penerbitan SO</option>
                            <option value="rk">Rencana Kirim</option>
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
    props: ['ambiluser'],
    components: {
        Circle5
    },
    data() {
        return {
            so: [],
            load: true,
            historyview: {},
            filter: {
                kode_customer: '',
                id_user: "",
                kode_groupso: this.ambiluser.kode_groupso,
                jenistanggal: 'Y',
                mulaitanggal: this.FirstDate(),
                sampaitanggal: this.today(),
                status: 'Acc',
                nomorso: '',
                listtanggal: 'so',
            },
            groupso: {},
            user: {},
            visible: false,
            query: '',
            selected: 0,
            custom: null,
            itemHeight: 39,
            customer: [],
            ket: {
                nama: "Pilih Customer"
            },
        }
    },
    created() {
        this.getSo();
    },
    computed: {
        FilterKategori() {
            var vm = this,
                lists = vm.so
            return _.filter(lists, function (query) {
                // var tanggal = query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.sampaitanggal,
                var tanggal = vm.filter.listtanggal === 'so' ? query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.sampaitanggal : query.tanggal_kirim >= vm.filter.mulaitanggal && query.tanggal_kirim <= vm.filter.sampaitanggal,
                    customer = vm.filter.kode_customer ? (query.kode_customer == vm.filter.kode_customer) : true,
                    sales = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    groupin = vm.filter.kode_groupso ? (query.kode_groupso == vm.filter.kode_groupso) : true,
                    nomorso = vm.filter.nomorso ? (query.nomor_so.toLowerCase().includes(vm.filter.nomorso.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true;
                return tanggal && customer && sales && groupin && nomorso && status;
            })
        },
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods: {
        getSo() {
            if (this.ambiluser.susales === 1) {
                axios.get("/api/so/data/realso")
                    .then(res => {
                        this.so = res.data.data
                        if (this.ambiluser.susales === 1) {
                            this.filter.status = "Sent"
                            axios.get("/api/user/data/all")
                                .then(res => {
                                    this.user = res.data.data;
                                    this.filter.kode_groupso = "";
                                })
                        } else {
                            this.filter.status = "Kordinator"
                        }
                        this.load = false;
                    });
            } else {
                axios.get("/api/so/data/view/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.so = res.data.data
                        if (this.ambiluser.susales === 1) {
                            this.filter.status = "Sent"
                        } else {
                            this.filter.status = "Kordinator"
                            axios.get("/api/user/view/" + this.ambiluser.kode_groupso)
                                .then(res => {
                                    this.user = res.data.data;
                                })
                        }
                        this.load = false;
                    });
            }
            axios.get("/api/groupso")
                .then(res => {
                    this.groupso = res.data.data;
                    axios.get("/api/customer")
                        .then(res => {
                            this.customer = res.data.data;
                        })
                })
        },
        showhistory(rs) {
            $("#modal-history").modal("show");
            axios.get("/api/history/data/" + rs.nomor_so + "/So")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
        showfilter() {
            $("#modal-filter").modal("show");
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
        cekjenis() {
            this.filter.mulaitanggal = this.FirstDate();
            this.filter.sampaitanggal = this.today();
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
            this.filter.kode_customer = this.custom.kode
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
    }
}
</script>

<style>

</style>
