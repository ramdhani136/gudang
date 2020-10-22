<template>
<div class="container">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="filter.nomor" type="text" class="form-control" placeholder="PO/BCM/No.Kendaraan">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="filter.status" class="form-control">
            <option value="sent">Menunggu Acc</option>
            <option value="open">Open</option>
            <option value="close">Close</option>
        </select>
    </div>
    <div class="row" v-if="ambiluser.incoming===1 || ambiluser.superadmin===1">
        <router-link to="/dic/bcm/view" class="btn btn-success my-3">+ Create Bukti Checker</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Checker Masuk</th>
                    <th>Tanggal</th>
                    <th>Nomor PO</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(bm,index) in FilteredBCM" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewbcm',params:{nomor:bm.bcm}}" class="btn btn-none">
                            {{bm.bcm}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{bm.tanggal}}</td>
                    <td style="text-align:center">{{bm.nomor_po}}</td>
                    <td>{{bm.supplier}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(bm)" class="btn btn-primary">Lihat History</button>
                        <button v-if="bm.status!=='close' && (ambiluser.incoming===1 || ambiluser.superadmin===1)" @click="batal(bm)" class="btn btn-danger">Batalkan</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History BCM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor BCM</th>
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
                        <label>Supplier</label>
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
                        <label>Purchasing</label>
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
    props: ['ambiluser'],
    components: {
        Circle5
    },
    data() {
        return {
            load: true,
            bcm: [],
            historyview: {},
            listbcm: {},
            listpo: {},
            history: {},
            visible: false,
            query: '',
            selected: 0,
            custom: null,
            itemHeight: 39,
            supplier: [],
            filter: {
                kode_supplier: '',
                id_user: "",
                jenistanggal: 'Y',
                mulaitanggal: this.FirstDate(),
                sampaitanggal: this.today(),
                status: 'open',
                nomor: '',
            },
            user: {},
            ket: {
                nama: "Pilih Supplier"
            },
        }
    },
    created() {
        this.getBcm();
    },
    computed: {
        FilteredBCM() {
            var vm = this,
                lists = vm.bcm
            return _.filter(lists, function (query) {
                var tanggal = query.tanggal >= vm.filter.mulaitanggal && query.tanggal <= vm.filter.sampaitanggal,
                    supplier = vm.filter.kode_supplier ? (query.kode_supplier == vm.filter.kode_supplier) : true,
                    user = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    nomorpo = vm.filter.nomor ? (query.nomor_po.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    bcm = vm.filter.nomor ? (query.bcm.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    nopol = vm.filter.nomor ? (query.nopol.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true;
                return tanggal && supplier && user && (nomorpo || bcm || nopol) && status;
            })
        },
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.supplier.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods: {
        getBcm() {
            axios.get("/api/bcm")
                .then(res => {
                    this.bcm = res.data.data;
                    if (this.ambiluser.incoming === 1) {
                        this.filter.status = "open";
                    } else {
                        this.filter.status = "sent";
                    }
                    axios.get("/api/supplier")
                        .then(res => {
                            this.supplier = res.data.data;
                            axios.get("/api/user/data/purch")
                                .then(res => {
                                    this.user = res.data.data;
                                    this.load = false;
                                })
                        })
                })
        },
        showhistory(bm) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + bm.bcm + "/Bcm")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
        batal(bm) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus checker masuk ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    /* batalin selesai po */
                    axios.put("/api/po/" + bm.nomor_po, {
                            poselesai: "N"
                        })
                        .then(res => {
                            /* Kembalikan sisaso dan status po & polist */
                            axios.get("/api/listbcm/" + bm.bcm)
                                .then(res => {
                                    this.listbcm = res.data.data;
                                    this.sisapo = 0;
                                    for (let i = 0; i < this.listbcm.length; i++) {
                                        axios.get("/api/listpo/data/" + bm.nomor_po + "/" + this.listbcm[i].kode_barang)
                                            .then(res => {
                                                this.listpo = res.data.data;
                                                this.sisapo = parseInt(this.listpo[0].sisapo) + parseInt(this.listbcm[i].sj);
                                                axios.put("/api/listpo/" + this.listpo[0].id, {
                                                    sisapo: this.sisapo,
                                                    closepo: "N"
                                                })
                                            })
                                    }
                                })
                            /* End */
                            /* Menghapus bcm */
                            axios.delete("/api/bcm/" + bm.bcm)
                                .then(res => {
                                    axios.get("/api/history/" + bm.bcm)
                                        .then(res => {
                                            this.history = res.data.data;
                                            for (let k = 0; k < this.history.length; k++) {
                                                axios.delete("/api/history/" + this.history[k].id);
                                            }
                                        })
                                    axios.post("/api/history", {
                                        nomor_dok: bm.nomor_po,
                                        nomor_ref: bm.bcm,
                                        id_user: this.ambiluser.id,
                                        notif: "BCM nomor" + bm.bcm + " Di tarik DIC",
                                        keterangan: "Membatalkan Form Checker Keluar " + bm.bcm,
                                        jenis: "Po",
                                        tanggal: this.DateTime(),
                                    })
                                    swalWithBootstrapButtons.fire(
                                        'Deleted!',
                                        'Berhasil menghapus BCK.',
                                        'success'
                                    )
                                    this.getBcm();
                                })
                            /* End */
                        })
                    /* end */
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus BCK :)',
                        'error'
                    )
                }
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
            this.filter.kode_supplier = this.custom.kode_supplier;
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
