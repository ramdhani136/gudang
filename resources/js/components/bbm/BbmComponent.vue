<template>
<div class="container">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="filter.nomor" type="text" class="form-control" placeholder="PO/BCM/BBM/Nopol">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="filter.request" class="form-control">
            <option value="Accedit">Draft</option>
            <option value="N">Open</option>
            <option value="Edit">Request Perbaikan</option>
            <option value="Batal">Request Pembatalan</option>
        </select>
    </div>
    <div class="row" v-if="ambiluser.incoming===1">
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
                        <button v-if="ambiluser.incoming===1 && bm.request==='Acchapus'" @click="batalkan(bm)" class="btn btn-danger">Batalkan</button>
                        <router-link v-if="ambiluser.incoming===1 && bm.request==='Accedit'" :to="{name:'viewbbm',params:{nomor:bm.bbm}}" class="btn btn-orange">
                            Edit BBM
                        </router-link>
                        <button v-if="ambiluser.incoming===1 && (bm.request!=='Batal' && bm.request!=='Acchapus' && bm.request!=='Tolakhapus' && bm.request!=='Accedit') " @click="showbatal(bm)" class="btn btn-orange">Request Batal</button>
                        <button v-if="ambiluser.incoming===1 && bm.request==='Batal' " @click="batalrequest(bm)" class="btn btn-none">Batal Request</button>
                        <button v-if="ambiluser.incoming===1 && bm.request==='Tolakhapus' " @click="detailtolak(bm)" class="btn btn-danger">Detail Penolakan</button>
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
    <div class="modal fade" id="modal-batal" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form pemintaan pembatalan BBM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="keteranganedit" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimbatal()" class="btn btn-success">Request Batal</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-detailtolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Penolakan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="alastolak" class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="requlangbatal()" class="btn btn-success">Request Ulang</button>
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
            bbm: [],
            history: {},
            historyview: {},
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
                request: "N"
            },
            user: {},
            ket: {
                nama: "Pilih Supplier"
            },
            listbbm: {},
            listpo: {},
            updatebbm: 0,
            history: {},
            closepo: '',
            sisapo: 0,
            keteranganedit: '',
            pilihbatal: {},
            alastolak: '',
            ambilbbm: ''
        }
    },
    created() {
        this.getBbm();
    },
    computed: {
        FilteredBBM() {
            var vm = this,
                lists = vm.bbm
            return _.filter(lists, function (query) {
                var tanggal = query.tanggal >= vm.filter.mulaitanggal && query.tanggal <= vm.filter.sampaitanggal,
                    supplier = vm.filter.kode_supplier ? (query.kode_supplier == vm.filter.kode_supplier) : true,
                    user = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    nomorpo = vm.filter.nomor ? (query.nomor_po.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    bcm = vm.filter.nomor ? (query.nomor_bcm.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    bbm = vm.filter.nomor ? (query.bbm.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    nopol = vm.filter.nomor ? (query.nopol.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true,
                    status = vm.filter.request ? (query.request == vm.filter.request) : true;
                return tanggal && supplier && user && (nomorpo || bcm || nopol || bbm) && status;
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
        getBbm() {
            axios.get("/api/bbm")
                .then(res => {
                    this.bbm = res.data.data;
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
        batalkan(bm) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus BBM ini! , BCM akan ikut di batalkan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    axios.get("/api/listbbm/" + bm.bbm)
                        .then(res => {
                            this.listbbm = res.data.data;
                            this.updatebbm = 0;
                            this.closepo = '';
                            this.sisapo = 0;
                            for (let i = 0; i < this.listbbm.length; i++) {
                                axios.get("/api/listpo/data/" + bm.nomor_po + "/" + this.listbbm[i].kode_barang)
                                    .then(res => {
                                        this.listpo = res.data.data;
                                        this.updatebbm = parseFloat(this.listpo[0].bbm) - parseFloat(this.listbbm[i].qty);
                                        this.sisapo = parseFloat(this.listpo[0].qty) - parseFloat(this.updatebbm);

                                        if (this.sisapo < 1) {
                                            this.closepo = "Y";
                                        } else {
                                            this.closepo = "N";
                                        }
                                        axios.put("/api/listpo/" + this.listpo[0].id, {
                                            bbm: this.updatebbm,
                                            sisapo: this.sisapo,
                                            closepo: this.closepo,
                                        })
                                    })
                            }
                            axios.delete("/api/bbm/" + bm.bbm)
                                .then(res => {
                                    axios.put("/api/po/" + bm.nomor_po, {
                                            status: 'Acc',
                                            poselesai: 'N',
                                        })
                                        .then(res => {
                                            axios.delete("/api/bcm/" + bm.nomor_bcm)
                                                .then(res => {
                                                    axios.get("/api/history/" + bm.nomor_bcm)
                                                        .then(res => {
                                                            this.history = res.data.data;
                                                            for (let y = 0; y < this.history.length; y++) {
                                                                axios.delete("/api/history/" + this.history[y].id);
                                                            }
                                                        }).then(res => {
                                                            axios.get("/api/history/" + bm.bbm)
                                                                .then(res => {
                                                                    this.history = res.data.data;
                                                                    for (let y = 0; y < this.history.length; y++) {
                                                                        axios.delete("/api/history/" + this.history[y].id);
                                                                    }
                                                                }).then(res => {
                                                                    axios.post("/api/history", {
                                                                        nomor_dok: bm.nomor_po,
                                                                        nomor_ref: bm.nomor_bcm,
                                                                        id_user: this.ambiluser.id,
                                                                        notif: "BCM dihapus",
                                                                        keterangan: "Membatalkan BCM nomor : " + bm.nomor_bcm,
                                                                        jenis: "Po",
                                                                        tanggal: this.DateTime(),
                                                                    }).then(res => {
                                                                        axios.post("/api/history", {
                                                                            nomor_dok: bm.nomor_po,
                                                                            nomor_ref: bm.bbm,
                                                                            id_user: this.ambiluser.id,
                                                                            notif: "BBM dihapus",
                                                                            keterangan: "Membatalkan BBM nomor : " + bm.bbm,
                                                                            jenis: "Po",
                                                                            tanggal: this.DateTime(),
                                                                        }).then(res => {
                                                                            this.getBbm();
                                                                            this.load = false;
                                                                            swalWithBootstrapButtons.fire(
                                                                                'Deleted!',
                                                                                'BBM berhasil di hapus.',
                                                                                'success'
                                                                            )
                                                                        })
                                                                    })
                                                                });
                                                        })
                                                })
                                        })
                                })
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus form BBM :)',
                        'error'
                    )
                }
            })
        },
        showbatal(bm) {
            $("#modal-batal").modal("show");
            this.pilihbatal = bm;
        },
        kirimbatal() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin meminta pembatalan BBM!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    if (this.keteranganedit === "") {
                        this.load = false;
                        $("#modal-batal").modal("hide");
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Keterangan wajib di isi!',
                        })
                    } else {
                        axios.put("/api/bbm/" + this.pilihbatal.bbm, {
                            request: 'Batal',
                            keteranganedit: this.keteranganedit,
                            alastolak: ''
                        }).then(res => {
                            axios.post("/api/history", {
                                nomor_dok: this.pilihbatal.bbm,
                                nomor_ref: this.pilihbatal.bbm,
                                id_user: this.ambiluser.id,
                                notif: "request batal bbm",
                                keterangan: "Mengirim permintaan pembatalan nomor : " + this.pilihbatal.bbm,
                                jenis: "Bbm",
                                tanggal: this.DateTime(),
                            }).then(res => {
                                axios.post("/api/history", {
                                    nomor_dok: this.pilihbatal.nomor_bcm,
                                    nomor_ref: this.pilihbatal.bbm,
                                    id_user: this.ambiluser.id,
                                    notif: "request batal bbm",
                                    keterangan: "Mengirim permintaan pembatalan nomor : " + this.pilihbatal.bbm,
                                    jenis: "Bcm",
                                    tanggal: this.DateTime(),
                                }).then(res => {
                                    this.load = false;
                                    $("#modal-batal").modal("hide");
                                    swalWithBootstrapButtons.fire(
                                        'Sukses!',
                                        'Permintaan berhasil di kirim.',
                                        'success'
                                    )
                                    this.getBbm();
                                })
                            })
                        })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim permintaan pembatalan :)',
                        'error'
                    )
                }
            })
        },
        batalrequest(bm) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin membatalkan permintaan pembatalan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    axios.put("/api/bbm/" + bm.bbm, {
                        request: 'N',
                        keteranganedit: '',
                        alastolak: ''
                    }).then(res => {
                        axios.post("/api/history", {
                            nomor_dok: bm.bbm,
                            nomor_ref: bm.bbm,
                            id_user: this.ambiluser.id,
                            notif: "batal melakukan request pembatalan",
                            keterangan: "Batal melakukan request pembatalan : " + bm.bbm,
                            jenis: "Bbm",
                            tanggal: this.DateTime(),
                        }).then(res => {
                            axios.post("/api/history", {
                                nomor_dok: bm.nomor_bcm,
                                nomor_ref: bm.bbm,
                                id_user: this.ambiluser.id,
                                notif: "batal melakukan request pembatalan",
                                keterangan: "Batal melakukan request pembatalan : " + bm.bbm,
                                jenis: "Bcm",
                                tanggal: this.DateTime(),
                            }).then(res => {
                                this.load = false;
                                swalWithBootstrapButtons.fire(
                                    'Sukses!',
                                    'Permintaan berhasil di batalkan.',
                                    'success'
                                )
                                this.getBbm();
                            })
                        })
                    })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan pembatalan :)',
                        'error'
                    )
                }
            })
        },
        detailtolak(bm) {
            this.alastolak = bm.alastolak;
            $("#modal-detailtolak").modal("show");
            this.pilihbatal = bm;
        },
        requlangbatal() {
            $("#modal-detailtolak").modal("hide");
            this.showbatal(this.pilihbatal);
        }
    }
}
</script>

<style>

</style>
