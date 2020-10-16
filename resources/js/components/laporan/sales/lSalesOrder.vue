<template>
<div class="wrapper" @keyup.esc="getso()">
    <div class="row diam" v-scroll="handleScroll">
        <div class="col-11 font-weight-bold title mt-4 mb-3">
            List Sales Order<br>
            <b class="ltanggal">{{filter.mulaitanggal}} - {{filter.akhirtanggal}} ({{" "+statusnya+" "}})</b>
        </div>
        <div class="col-1 rig  mt-4">
            <button @click="showFillter()" class="btn btn-orange">Filter</button>
        </div>
    </div>
    <div class="panelbawah">
        <div class="col-2">
            <button class="btn-sm btn-none">Cetak Laporan</button>
        </div>
    </div>
    <div v-for=" (dso,index) in filterList" :key="index">
        <div class="row font-weight-bold bc align-items-center py-1">
            <div class="col-2">{{dso.tanggal_so}}
            </div>
            <div class="col-5">{{dso.customer}}</div>
            <div class="col-3">{{dso.nomor_so}}</div>
            <div class="col-2">{{dso.sales}}</div>
        </div>
        <div class="row">
            <table class="table">
                <thead>
                    <th>Kode Barang</th>
                    <th>Item</th>
                    <th>Di pesan</th>
                    <th>Di terima</th>
                    <th>Harga</th>
                    <th>Sisa Pesanan</th>
                </thead>
                <tbody v-for="(lso,index) in listso[index]" :key="index">
                    <td>{{lso.kode_barang}}</td>
                    <td>{{lso.nama_barang}}</td>
                    <td>{{lso.qty}}</td>
                    <td>{{lso.bbk}}</td>
                    <td>{{lso.harga | currency}}</td>
                    <td style="font-size:0.9em; font-weight:bold;">{{lso.qty-lso.bbk}} {{lso.satuan}}</td>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal fade" id="modal-search" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fillter Pencarian</h5>
                    <button type="button" @click="getso()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="filter.status" class="form-control">
                            <option value="all">Semua</option>
                            <option value="acc">Open</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible2" v-text="custom2 ? custom2.nama:''"></div>
                        <div class="placeholder" v-if="custom2==null" v-text="ket2.nama">Semua Customer</div>
                        <div class="popover" v-show="visible2">
                            <input type="text" @keydown.up="up2" @keydown.down="down2" @keydown.enter="selectItem2" v-model="query2" placeholder="Masukan nama customer ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match2,index) in matches2" :key="match2.kode" v-text="match2.nama" :class="{'selected':(selected2==index)}" @click="itemClicked2(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sales</label>
                        <select v-model="filter.sales" class="form-control" :disabled="ambiluser.susales===0 && ambiluser.kordisales===0">
                            <option value="all">Semua Sales</option>
                            <option v-for="(sl,index) in sales" :key="index" :value="sl.id">{{sl.name}}</option>
                        </select>
                    </div>
                    <div class=" form-group">
                        <label>Group Sales</label>
                        <select v-model="filter.group" class="form-control" :disabled="ambiluser.susales===0">
                            <option value="all">Pilih Semua</option>
                            <option :value="gr.kode" v-for="(gr,index) in groupnya" :key="index">{{gr.area}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <select @change="cekjenistanggal()" v-model="jenistanggal" class="form-control">
                            <option value="N">Semua Tanggal</option>
                            <option value="Y">Filter Tanggal</option>
                        </select>
                    </div>
                    <div v-if="jenistanggal==='Y'" class="form-group">
                        <label>Mulai Tanggal</label>
                        <input v-model="filter.mulaitanggal" type="date" class="form-control">
                    </div>
                    <div v-if="jenistanggal==='Y' && filter.mulaitanggal!==undifined" class="form-group">
                        <label>Sampai Tanggal</label>
                        <input v-model="filter.akhirtanggal" type="date" class="form-control" :min="filter.mulaitanggal">
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="getso()" type="button" class="btn btn-primary" data-dismiss="modal">Fillter Data</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: ['ambiluser'],
    data() {
        return {
            listso: [],
            so: [],
            jenistanggal: 'N',
            visible2: false,
            query2: '',
            selected2: 0,
            itemHeight2: 39,
            custom2: null,
            ket2: {
                nama: "Semua Customer",
                kode: ''
            },
            customer: [],
            filter: {
                group: this.ambiluser.kode_groupso,
                sales: this.ambiluser.id,
                status: 'all',
                mulaitanggal: this.Datesebulan(),
                akhirtanggal: this.DateTime(),
            },
            sales: {},
            groupnya: {},
            statusnya: '',
            load: true
        }
    },
    created() {
        this.auth();
        this.getso();
    },
    computed: {
        matches2() {
            if (this.query2 == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query2.toLowerCase()))
        },
        filterList() {
            var vm = this,
                lists = vm.so
            return _.filter(lists, function (query) {
                var tanggal = query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.akhirtanggal,
                    // nomorso = vm.nomorso ? (query.nomor_so.toLowerCase().includes(vm.nomorso.toLowerCase())) : true,
                    customer = vm.ket2.kode ? (query.kode_customer == vm.ket2.kode) : true,
                    sales = vm.ambiluser.sales === 1 ? (query.id_user == vm.ambiluser.id) : vm.filter.sales !== 'all' ? (query.id_user == vm.filter.sales) : true,
                    groupin = vm.ambiluser.sales === 1 || vm.ambiluser.kordisales === 1 ? (query.kode_groupso == vm.ambiluser.kode_groupso) : vm.filter.group !== 'all' ? (query.kode_groupso == vm.filter.group) : true,
                    status = vm.filter.status === 'acc' ? (query.status == 'Acc') : vm.filter.status === 'selesai' ? (query.status == 'Selesai' || query.status == 'Di Selesaikan') : true;
                return tanggal && customer && status && sales && groupin
            })
        }
    },

    methods: {
        getso() {
            this.listso = [];
            axios.get("/api/so/laporan/data")
                .then(res => {
                    this.so = res.data.data;
                    for (let i = 0; i < this.filterList.length; i++) {
                        axios.get("/api/listso/" + this.filterList[i].nomor_so)
                            .then(res => {
                                this.listso.push(res.data.data);
                                axios.get("/api/customer")
                                    .then(res => {
                                        this.customer = res.data.data;
                                        axios.get("/api/groupso")
                                            .then(res => {
                                                this.groupnya = res.data.data;
                                                if (this.filter.status === "all") {
                                                    this.statusnya = "Semua SO";
                                                } else if (this.filter.status === "acc") {
                                                    this.statusnya = "Open";
                                                } else {
                                                    this.statusnya = "Selesai";
                                                }
                                            })
                                    })
                            })
                    }
                })
        },
        showFillter() {
            $("#modal-search").modal("show");
        },
        handleScroll: function (evt, el) {
            if (window.scrollY > 50) {
                el.setAttribute(
                    'style',
                    'top: 160px; '
                )
            } else {
                el.setAttribute(
                    'style',
                    'top: 190px; '
                )
            }
            // return window.scrollY > 100
        },
        toggleVisible2() {
            this.visible2 = !this.visible2;
        },
        itemClicked2(index) {
            this.selected2 = index;
            this.selectItem2();
        },
        selectItem2() {
            this.custom2 = this.matches2[this.selected2];
            this.ket2.kode = this.custom2.kode;
            this.ket2.nama = this.custom2.nama;
            this.item = true;
            this.visible2 = false;
        },
        up2() {
            if (this.selected2 == 0) {
                return;
            }
            this.selected2 -= 1;
            this.scrollToItem2();
        },
        down2() {
            if (this.selected2 >= this.matches2.length - 1) {
                return;
            }
            this.selected2 += 1;
            this.scrollToItem2();
        },
        scrollToItem2() {
            this.$refs.optionList.scrollTop = this.selected2 * this.itemHeight2;
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
            this.datetimes = this.dates + " "
            //  + this.times;
            return this.datetimes;
        },
        Datesebulan() {
            this.date = new Date();
            this.month = this.date.getMonth();
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
            this.datetimes = this.dates + " "
            //  + this.times;
            return this.datetimes;
        },
        auth() {
            if (this.ambiluser.sales === 1) {
                axios.get("/api/user/view/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.sales = res.data.data;
                        this.filter.sales = this.ambiluser.id;
                        this.filter.group = this.ambiluser.kode_groupso;
                    })
            } else if (this.ambiluser.kordisales === 1) {
                axios.get("/api/user/view/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.sales = res.data.data;
                        this.filter.sales = "all";
                        this.filter.group = this.ambiluser.kode_groupso;
                    })
            } else if (this.ambiluser.susales === 1) {
                axios.get("/api/user/data/all")
                    .then(res => {
                        this.sales = res.data.data;
                        this.filter.sales = "all";
                        this.filter.group = 'all';
                    })
            }
        },
        cekjenistanggal() {
            if (this.jenistanggal === 'N') {
                this.filter.mulaitanggal = this.Datesebulan();
                this.filter.akhirtanggal = this.DateTime();
            }
        }
    }
}
</script>

<style scoped>
.wrapper {
    width: 84%;
    margin-left: 8%;
    font-size: 1em;
    margin-top: 100px;
}

.title {
    font-size: 1.5em;
}

.bc {
    background-color: rgb(233, 250, 233);
}

.btn-grey {
    border: solid 1px rgb(158, 158, 158);
    background-color: rgb(202, 202, 202);
}

.diam {
    margin-top: -105px;
    position: fixed;
    width: 71%;
    background-color: #fff;
    z-index: 1000;
    height: auto;
}

.panelbawah {
    border-top: solid 1px rgb(231, 229, 229);
    width: 84%;
    height: 50px;
    background-color: #fff;
    bottom: 0;
    position: fixed;
    z-index: 1001;
    padding-top: 10px;
    margin-left: -8%;
    padding-left: 6%;
}

.ltanggal {
    font-size: 0.65em;
    color: mediumseagreen;
}
</style>
