<template>
<div class="wrapper" @keyup.esc="getpo()">
    <div class="row diam" v-scroll="handleScroll">
        <div class="col-11 font-weight-bold title mt-4 mb-3">
            List Purchase Order<br>
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
    <div v-for=" (dpo,index) in filterList" :key="index">
        <div class="row font-weight-bold bc align-items-center py-1">
            <div class="col-2">{{dpo.tanggal_po}}
            </div>
            <div class="col-5">{{dpo.supplier}}</div>
            <div class="col-3">{{dpo.nomor_po}}</div>
            <div class="col-2">{{dpo.purchasing}}</div>
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
                <tbody v-for="(lpo,index) in listpo[index]" :key="index">
                    <td>{{lpo.kode_barang}}</td>
                    <td>{{lpo.nama_barang}}</td>
                    <td>{{lpo.qty}}</td>
                    <td>{{lpo.bbm}}</td>
                    <td>{{lpo.harga | currency}}</td>
                    <td style="font-size:0.9em; font-weight:bold;">{{lpo.qty-lpo.bbm}} {{lpo.satuan}}</td>
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
                    <button type="button" @click="getpo()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select v-model="filter.status" class="form-control">
                            <option value="">Semua</option>
                            <option value="Acc">Open</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible2" v-text="custom2 ? custom2.nama:''"></div>
                        <div class="placeholder" v-if="custom2==null" v-text="ket2.nama">Semua Supplier</div>
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
                        <label>Purchase</label>
                        <select v-model="filter.id_user" class="form-control">
                            <option value="">Semua User</option>
                            <option v-for="(sl,index) in purch" :key="index" :value="sl.id">{{sl.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Berdasarkan Tanggal</label>
                        <select v-model="filter.listtanggal" class="form-control">
                            <option value="po">Penerbitan PO</option>
                            <option value="rd">Rencana Datang</option>
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
                    <button @click="getpo()" type="button" class="btn btn-primary" data-dismiss="modal">Fillter Data</button>
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
            listpo: [],
            po: [],
            jenistanggal: 'N',
            visible2: false,
            query2: '',
            selected2: 0,
            itemHeight2: 39,
            custom2: null,
            ket2: {
                nama: "Semua Supplier",
                kode: ''
            },
            supplier: [],
            filter: {
                id_user: "",
                status: "",
                mulaitanggal: this.Datesebulan(),
                akhirtanggal: this.today(),
                listtanggal: 'po',
                kode_supplier: '',
            },
            purch: {},
            statusnya: '',
            load: true
        }
    },
    created() {
        this.getpo();
    },
    computed: {
        matches2() {
            if (this.query2 == '') {
                return [];
            }
            return this.supplier.filter((item) => item.nama.toLowerCase().includes(this.query2.toLowerCase()))
        },
        filterList() {
            var vm = this,
                lists = vm.po;
            return _.filter(lists, function (query) {
                // var tanggal = query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.akhirtanggal,
                var tanggal = vm.filter.listtanggal === 'po' ? query.tanggal_po >= vm.filter.mulaitanggal && query.tanggal_po <= vm.filter.akhirtanggal : query.tanggal_datang >= vm.filter.mulaitanggal && query.tanggal_datang <= vm.filter.akhirtanggal,
                    supplier = vm.filter.kode_supplier ? (query.kode_supplier == vm.filter.kode_supplier) : true,
                    status = vm.filter.status === 'Acc' ? (query.status == 'Acc') : vm.filter.status === 'Selesai' ? (query.status == 'Selesai' || query.status == 'Di Selesaikan') : true,
                    purch = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true;
                return tanggal && supplier && status && purch;
            })
        }
    },

    methods: {
        getpo() {
            if (this.filter.status === "") {
                this.statusnya = "Semua PO";
            } else if (this.filter.status === "Acc") {
                this.statusnya = "Open";
            } else {
                this.statusnya = "Selesai";
            }
            this.listpo = [];
            axios.get("/api/po/data/laporan")
                .then(res => {
                    this.po = res.data.data;
                    for (let i = 0; i < this.filterList.length; i++) {
                        axios.get("/api/listpo/" + this.filterList[i].nomor_po)
                            .then(res => {
                                this.listpo.push(res.data.data);
                                axios.get("/api/supplier")
                                    .then(res => {
                                        this.supplier = res.data.data;
                                        axios.get("/api/user/data/purch")
                                            .then(res => {
                                                this.purch = res.data.data;
                                            });
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
            this.filter.kode_supplier = this.custom2.kode_supplier;
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
            this.datetimes = this.dates + " " + times;
            //  + this.times;
            return this.datetimes;
        },
        Datesebulan() {
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
            //  + this.times;
            return this.datetimes;
        },
        cekjenistanggal() {
            if (this.jenistanggal === 'N') {
                this.filter.mulaitanggal = this.Datesebulan();
                this.filter.akhirtanggal = this.today();
            }
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
