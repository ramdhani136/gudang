<template>
<div class="container">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="filter.nomorpo" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="filter.status" class="form-control">
            <option v-if="ambiluser.purch===1 || ambiluser.suppurch===1" value=" Draft">Draft</option>
            <option v-if="ambiluser.purch===1 || ambiluser.suppurch===1" value="Request">Request</option>
            <option value="Acc">Open</option>
            <option v-if="ambiluser.purch===1 || ambiluser.suppurch===1" value="Tolak">Rejected</option>
            <option value="Selesai">Selesai</option>
            <option value="Di Selesaikan">Di Selesaikan</option>
        </select>
    </div>
    <div class="row" v-if="ambiluser.purch===1 || ambiluser.superadmin===1">
        <router-link to="/purchase/po/create" class="btn btn-success my-3">+ Create PO</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor PO</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Estimasi Kedatangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(pl , index) in FilterKategori" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'poView',params:{nomor:pl.nomor_po}}" class="btn btn-none">
                            {{pl.nomor_po}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{pl.tanggal_po}}</td>
                    <td>{{pl.supplier}}</td>
                    <td style="text-align:center">{{pl.tanggal_datang}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(pl)" class="btn btn-primary">
                            Lihat Rincian
                        </button>
                        <button @click="getHapus(pl)" v-if="(pl.status=='Draft' || pl.status=='Request' || pl.status=='Tolak')  && (ambiluser.superadmin===1 || ambiluser.purch===1)" class="btn btn-danger">Batalkan</button>
                        <button @click="requestSelesai(pl)" v-if="pl.status=='Acc' && pl.rs==='N' && (ambiluser.superadmin===1 || ambiluser.purch===1)" class="btn btn-orange">Request Selesai</button>
                        <button @click="batalselesai(pl)" v-if="pl.status=='Acc' && pl.rs==='Y' &&  (ambiluser.superadmin===1 || ambiluser.purch===1)" class="btn btn-none">Batal Selesai</button>
                        <button @click="infoTolak(pl)" v-if="pl.status=='Acc' && pl.rs==='T' &&  (ambiluser.superadmin===1 || ambiluser.purch===1)" class="btn btn-none">R. Selesai di tolak</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-selesai" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Permintaan PO Selesai</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="input.alasselesai" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimselesai()" class="btn btn-primary">kirim permintaan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informasi Tolak Selesai PO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="alastolak" class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="requlang()" type="button" class="btn btn-orange">Request Ulang Selesai</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-history" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History PO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor PO</th>
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
                        <label>Purchasing</label>
                        <select v-model="filter.id_user" class="form-control">
                            <option value="">Semua Data</option>
                            <option :value="us.id" v-for="(us,index) in user" :key="index">{{us.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Berdasarkan Tanggal</label>
                        <select @change="cektanggal()" v-model="filter.listtanggal" class="form-control">
                            <option value="po">Penerbitan PO</option>
                            <option value="rd">Rencana Datang</option>
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
                    <button @click="cek()" type="button" class="btn btn-success" data-dismiss="modal">Save Change</button>
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
            po: [],
            load: true,
            supplier: [],
            purchasing: {},
            upload: {
                nomor_po: this.po_nomor(),
                tanggal_po: this.now(),
                tanggal_datang: this.now(),
            },
            file: {},
            input: {},
            tujuanid: 0,
            alastolak: null,
            listpo: {},
            listhapus: {},
            sisapo: 0,
            openpo: 0,
            statuspo: '',
            historyview: {},
            history: {},
            ket: {
                nama: "Pilih Supplier"
            },
            filter: {
                kode_supplier: '',
                id_user: this.ambiluser.id,
                jenistanggal: 'Y',
                mulaitanggal: this.FirstDate(),
                sampaitanggal: this.today(),
                status: 'Acc',
                nomorpo: '',
                listtanggal: 'po',
            },
            user: {},
            visible: false,
            query: '',
            selected: 0,
            custom: null,
            itemHeight: 39,
        }
    },
    created() {
        this.getPo();
        this.getSupplier();
        this.getPurchasing();
    },
    computed: {
        FilterKategori() {
            var vm = this,
                lists = vm.po
            return _.filter(lists, function (query) {
                // var tanggal = query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.sampaitanggal,
                var tanggal = vm.filter.listtanggal === 'po' ? query.tanggal_po >= vm.filter.mulaitanggal && query.tanggal_po <= vm.filter.sampaitanggal : query.tanggal_datang >= vm.filter.mulaitanggal && query.tanggal_datang <= vm.filter.sampaitanggal,
                    supplier = vm.filter.kode_supplier ? (query.kode_supplier == vm.filter.kode_supplier) : true,
                    user = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    nomorpo = vm.filter.nomorpo ? (query.nomor_po.toLowerCase().includes(vm.filter.nomorpo.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true;
                return tanggal && supplier && user && nomorpo && status;
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
        getPo() {
            axios.get("/api/po")
                .then(res => {
                    this.po = res.data.data
                    if (this.ambiluser.incoming === 1) {
                        this.filter.status = "Acc";
                    } else {
                        this.filter.status = "Request";
                    }
                    this.load = false;
                });
        },
        getSupplier() {
            axios.get("/api/supplier")
                .then(res => {
                    this.supplier = res.data.data;
                    axios.get("/api/user/data/purch")
                        .then(res => {
                            this.user = res.data.data;
                        })
                });
        },
        getPurchasing() {
            axios.get("/api/purchasing")
                .then(res => {
                    this.purchasing = res.data.data;
                });
        },
        now() {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') + day;
            return output
        },
        validate() {
            if (this.form.tanggal_rso < this.now()) {
                this.form.tanggal_rso = this.now();
            }
        },
        po_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "PO-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        resetForm() {
            this.upload = {
                nomor_po: this.po_nomor(),
                tanggal_po: this.now(),
                tanggal_datang: this.now(),
            };
            this.supply = null;
            this.visible = false;
        },
        CreatePo() {
            this.upload.status = "Draft";
            axios.post('/api/po', this.upload)
                .then(res => {
                    $("#modal-po").modal("hide");
                    this.$router.push({
                        name: 'poView',
                        params: {
                            nomor: this.upload.nomor_po
                        }
                    })
                });
        },
        getHapus(pl) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus PO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.get("/api/listpo/" + pl.nomor_po)
                        .then(res => {
                            this.listpo = res.data.data;
                            this.sisapo = 0;
                            this.openpo = 0;
                            this.statuspo = "";
                            for (let i = 0; i < this.listpo.length; i++) {
                                axios.get("/api/listso/data/kembalikanpo/" + this.listpo[i].kode_barang)
                                    .then(res => {
                                        this.listhapus = res.data.data;
                                        for (let k = 0; k < this.listhapus.length; k++) {
                                            if (this.listpo[i].qty >= this.listhapus[k].openpo) {
                                                this.sisapo = this.listhapus[k].openpo + this.listhapus[k].sisapo;
                                                this.openpo = 0;
                                                this.statuspo = "N";
                                                this.listpo[i].qty = parseInt(this.listpo[i].qty) - parseInt(this.listhapus[k].openpo);
                                            } else {
                                                this.openpo = parseInt(this.listhapus[k].openpo) - parseInt(this.listpo[i].qty);
                                                if (this.listpo[i].qty > this.listhapus[k].qty) {
                                                    this.sisapo = parseInt(this.listhapus[k].sisapo) + parseInt(this.listhapus[k].open);
                                                } else {
                                                    this.sisapo = parseInt(this.listhapus[k].sisapo) + parseInt(this.listpo[i].qty);
                                                }
                                                if (this.sisapo < 1) {
                                                    this.statuspo = "Y";
                                                } else {
                                                    this.statuspo = "N";
                                                }
                                                this.listpo[i].qty = parseInt(this.listpo[i].qty) - parseInt(this.listhapus[k].openpo);
                                                if (this.listpo[i].qty < 1) {
                                                    this.listpo[i].qty = 0;
                                                }
                                            }
                                            axios.put("/api/listso/" + this.listhapus[k].id, {
                                                sisapo: this.sisapo,
                                                openpo: this.openpo,
                                                statuspo: this.statuspo
                                            })
                                        }
                                    })
                            }
                            axios.delete("/api/po/" + pl.nomor_po)
                                .then(res => {
                                    axios.get("/api/history/" + pl.nomor_po)
                                        .then(res => {
                                            this.history = res.data.data;
                                            for (let o = 0; 0 < this.history.length; o++) {
                                                axios.delete("/api/history/" + this.history[o].id)
                                            }
                                        })
                                    swalWithBootstrapButtons.fire(
                                        'Deleted!',
                                        'PO berhasil di hapus.',
                                        'success'
                                    )
                                    this.getPo();
                                })
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapis PO ini :)',
                        'error'
                    )
                }
            })
        },
        requestSelesai(pl) {
            this.tujuanid = pl.nomor_po;
            $("#modal-selesai").modal("show");
        },
        kirimselesai() {
            let tanya = confirm("Apakah anda yakin?");
            if (tanya === true) {
                this.input.rs = "Y";
                axios.put("/api/po/" + this.tujuanid, this.input)
                    .then(res => {
                        this.getPo();
                        this.getSupplier();
                        this.getPurchasing();
                        $("#modal-selesai").modal("hide");
                    });
            }
        },
        batalselesai(pl) {
            let tanya = confirm("Apakah anda yakin?");
            if (tanya === true) {
                this.input.rs = "N";
                this.input.alasselesai = "";
                axios.put("/api/po/" + pl.nomor_po, this.input)
                    .then(res => {
                        this.getPo();
                        this.getSupplier();
                        this.getPurchasing();
                    });
            }
        },
        infoTolak(pl) {
            this.tujuanid = pl.nomor_po;
            this.alastolak = pl.alastolakrequest;
            $("#modal-tolak").modal("show");
        },
        requlang() {
            $("#modal-tolak").modal("hide");
            $("#modal-selesai").modal("show");
        },
        bukalagi(pl) {
            let tanya = confirm("Apakah anda yakin");
            if (tanya === true) {
                axios.put("/api/po/" + pl.nomor_po, {
                        status: 'Acc',
                        rs: 'N'
                    })
                    .then(res => {
                        this.getPo();
                        this.getSupplier();
                        this.getPurchasing();
                    })
            }
        },
        showhistory(pl) {
            $("#modal-history").modal("show");
            axios.get("/api/history/data/" + pl.nomor_po + "/Po")
                .then(res => {
                    this.historyview = res.data.data;
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
            this.filter.kode_supplier = this.custom.kode_supplier
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
        cek() {
            console.log(this.filter);
        }
    }
}
</script>

<style>

</style>
