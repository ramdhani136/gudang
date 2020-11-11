<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Checker :</label>
                <input @input="ceknomorbcm()" v-model="up.bcm" type="text" maxlength="16" class="form-control col-12" autocomplete="disabled" :class="{ 'is-valid': nyala, 'is-invalid': !nyala }">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Supplier</label>
                <select v-model="ket.supplier" name="customer" class="col-12 form-control" disabled>
                    <option :value="ket.supplier">{{ket.supplier}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nomor PO</label>
                <input v-model="up.nomor_po" @click="showPo()" type="text" class="form-control" placeholder="Pilih Purchase Order" autocomplete="disabled">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Kendaraan </label>
                <input type="text" v-model="up.nopol" class="form-control" autocomplete="disabled">
            </div>
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12"></textarea>
            </div>
        </div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
        <div class="row mt-1 mx-auto col-12">
            <Circle5 id="load3" v-if="load"></Circle5>
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Sisa PO</th>
                        <th>Qty SJ</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(listbcm,index) in checker" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td style="text-align:center">{{listbcm.kode_barang}}</td>
                        <td>{{listbcm.nama_barang}}</td>
                        <td style="text-align:center">{{listbcm.satuan}}</td>
                        <td style="text-align:center">{{listbcm.sisapo}}</td>
                        <td style="text-align:center">
                            <input @input="validqty(index)" v-model="hitung.qty[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">
                            <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                        </td>
                        <td style="text-align:center">
                            <button @click="hapuslist(index)" class="btn btn-danger">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <!-- <button @click="draftBcm()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button> -->
        <button v-if="jenbutton" @click="submitBCM()" class="btn-success btn ml-2">
            Kirim Warehouse
        </button>
        <button v-if="!jenbutton" @click="requestBcm()" class="btn-primary btn ml-2">
            Request Acc
        </button>
    </div>
    <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form PO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih PO</label>
                        <select @change="pilihPo(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in poaktif" :key="index">{{aktif.nomor_po}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input v-model="ket.supplier" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody">
                        <table class="table mt-2 table-striped table-bordered" style="width:100%">
                            <thead id="rsthead">
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Item</th>
                                    <th style="text-align:center">Satuan</th>
                                    <th style="text-align:center">Sisa PO</th>
                                    <th style="text-align:center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(ls,index) in listsisa" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td>{{ls.nama_barang}}</td>
                                    <td style="text-align:center">{{ls.satuan}}</td>
                                    <td style="text-align:center">{{ls.sisapo}}</td>
                                    <td style="text-align:center">
                                        <input v-model="checker" type="checkbox" :value="ls">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="resetForm()" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="checklist()" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-pr" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="scrollList">
                        <table id="thead" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Nomor SO</th>
                                    <th>Customer</th>
                                    <th style="text-align:center">Jumlah</th>
                                    <th style="text-align:center">Satuan</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align:center"></td>
                                    <td style="text-align:center"></td>
                                    <td></td>
                                    <td style="text-align:center"></td>
                                    <td style="text-align:center"></td>
                                    <td>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
            load: false,
            up: {
                bcm: this.bcm_nomor(),
                tanggal: this.now(),
            },
            poaktif: {},
            ket: {},
            aktif: {},
            listsisa: {},
            checker: [],
            listbcm: {},
            uploadlist: [],
            hitung: {
                qty: [],
                keterangan: [],
                jumlahnya: [],
                sisanya: []
            },
            uploood: {},
            aksicek: 'benar',
            jenbutton: true,
            cek: {},
            uplistsisa: {
                qty_masuk: 0,
            },
            uplist: {},
            sipo: 0,
            uppo: {},
            listpo: {},
            persen: 0,
            listrso: {},
            coba: [],
            ok: '',
            nilai: '',
            banding: '',
            sisanya: 0,
            jumlahnya: 0,
            listbck: {},
            sisapo: 0,
            poselesai: '',
            tutuppo: '',
            bandingtutup: '',
            nyala: false,
            adabcm: {},
        }
    },
    created() {
        this.getPoAktif();
        this.resetForm();
    },
    computed: {

    },
    methods: {
        getPoAktif() {
            axios.get("/api/poaktif/")
                .then(res => {
                    this.poaktif = res.data.data;
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
            if (this.so.tanggal_so < this.now()) {
                this.so.tanggal_so = this.now();
            }
        },
        tglKirim() {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate() + 2;

            var output = d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') + day;
            return output
        },
        bcm_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "BCM-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        showPo() {
            $("#modal-po").modal("show");
        },
        resetForm() {
            this.checker = [];
            this.hitung.qty = [];
            this.hitung.keterangan = [];
        },
        pilihPo(aktif) {
            this.hitung.qty = [];
            this.hitung.keterangan = [];
            this.checker = [];
            this.ket.supplier = aktif.supplier;
            this.up.nomor_po = aktif.nomor_po;
            axios.get("/api/listpo/data/aktif/" + aktif.nomor_po)
                .then(res => {
                    this.listsisa = res.data.data;
                });
        },
        checklist() {
            $("#modal-po").modal("hide");
        },
        draftBcm() {
            let jawab = confirm("Simpan Draft?");
            if (jawab === true) {
                this.up.status = "Draft";
                axios.post("/api/bcm", this.up)
                    .then(res => {
                        for (let i = 0; i < this.checker.length; i++) {
                            this.uploadlist.kode_barang = this.checker[i].kode_barang;
                            this.uploadlist.qty = this.hitung.qty[i];
                            this.uploadlist.keterangan = this.hitung.keterangan[i];
                            this.uploadlist.nomor_bcm = this.up.bcm;
                            this.uploadlist.status = "open";
                            this.uploood = {
                                kode_barang: this.uploadlist.kode_barang,
                                nomor_bcm: this.uploadlist.nomor_bcm,
                                qty: this.uploadlist.qty,
                                keterangan: this.uploadlist.keterangan
                            };
                            axios.post("/api/listbcm", this.uploood)
                                .then(res => {});
                        }
                        for (let h = 0; h < this.checker.length; h++) {
                            axios.get("/api/view/detailpo/" + this.checker[h].nomor_po + "/" + this.checker[h].kode_barang)
                                .then(res => {
                                    this.listpo = res.data.data;
                                    this.persen = 0
                                    for (let j = 0; j < this.listpo.length; j++) {
                                        this.persen += parseInt(this.listpo[j].qty_tdktersedia);
                                        this.coba = [];
                                        this.coba = [{
                                            qty: this.persen / 100,
                                        }];
                                    }
                                    for (let z = 0; z < this.listpo.length; z++) {
                                        this.uplistsisa = {
                                            qty_masuk: ((((this.listpo[z].qty_tdktersedia / this.coba[0].qty) / 100) * (this.hitung.qty[h])) + this.listpo[z].qty_masuk)
                                        };
                                        axios.put("/api/listrso/" + this.listpo[z].id, this.uplistsisa)
                                            .then(res => {
                                                axios.get("/api/listrso/data/listpo/" + this.aktif.nomor_po)
                                                    .then(res => {
                                                        this.listsisa = res.data.data;
                                                        for (let i = 0; i < this.listsisa.length; i++) {
                                                            if (this.listsisa[i].sisapo < 1) {
                                                                this.uplist.po_close = "Y";
                                                                axios.put("/api/listrso/data/" + this.listsisa[i].nomor_po + "/" + this.listsisa[i].kode_barang, this.uplist)
                                                                    .then(res => {});
                                                            }
                                                        }
                                                    })
                                                this.sipo = 0;
                                                axios.get("/api/listrso/data/listall/" + this.aktif.nomor_po)
                                                    .then(res => {
                                                        this.sisasemua = 0;
                                                        this.listsisa = res.data.data;
                                                        for (let i = 0; i < this.listsisa.length; i++) {
                                                            this.sipo += parseInt(this.listsisa[i].sisapo);
                                                        }
                                                        if (this.sipo < 1) {
                                                            this.uppo.status = "Selesai";
                                                            axios.put("/api/po/" + this.aktif.nomor_po, this.uppo)
                                                                .then(res => {
                                                                    this.$router.push({
                                                                        name: 'bcmcomponent'
                                                                    });
                                                                });
                                                        } else {
                                                            this.$router.push({
                                                                name: 'bcmcomponent'
                                                            });
                                                        }
                                                    });
                                            });

                                    }
                                });
                        }
                    }, )
            }
        },
        submitBCM() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengirim bukti checker ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    if (this.checker.length < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput barang apapun!',
                        })
                    } else {
                        this.ok = '';
                        this.banding = '';
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === '' || this.hitung.qty[i] === 0 || this.hitung.qty[i] < 1) {
                                this.nilai = "N";
                            } else {
                                this.nilai = "Y";
                            }
                            this.ok += this.nilai;
                            this.banding += "Y";
                        }
                        if (this.ok === this.banding) {
                            this.up.status = "open";
                            axios.post("/api/bcm", this.up)
                                .then(res => {
                                    for (let i = 0; i < this.checker.length; i++) {
                                        this.uploadlist.kode_barang = this.checker[i].kode_barang;
                                        this.uploadlist.qty = this.hitung.qty[i];
                                        this.uploadlist.keterangan = this.hitung.keterangan[i];
                                        this.uploadlist.nomor_bcm = this.up.bcm;
                                        this.uploadlist.status = "open";
                                        this.uploood = {
                                            kode_barang: this.uploadlist.kode_barang,
                                            nomor_bcm: this.uploadlist.nomor_bcm,
                                            sj: this.uploadlist.qty,
                                            bongkar: this.uploadlist.qty,
                                            keterangan: this.uploadlist.keterangan,
                                            nomor_po: this.up.nomor_po,
                                            sisapo: this.checker[i].sisapo,
                                        };
                                        axios.post("/api/listbcm", this.uploood);
                                    };
                                    this.sisanya = 0;
                                    this.poselesai = '';
                                    this.tutuppo = '';
                                    this.bandingtutup = '';
                                    for (let h = 0; h < this.checker.length; h++) {
                                        this.sisapo = parseInt(this.checker[h].sisapo) - parseInt(this.hitung.qty[h]);
                                        if (this.sisapo < 1) {
                                            this.poselesai = "Y";
                                        } else {
                                            this.poselesai = "N";
                                        }
                                        axios.put("/api/listpo/" + this.checker[h].id, {
                                            sisapo: this.sisapo,
                                            closepo: this.poselesai
                                        })
                                        this.tutuppo += this.poselesai;
                                        this.bandingtutup += "Y";
                                    }
                                    if (this.tutuppo === this.bandingtutup) {
                                        axios.put("/api/po/" + this.up.nomor_po, {
                                            poselesai: "Y"
                                        })
                                    }
                                    axios.post("/api/history", {
                                        nomor_dok: this.up.nomor_po,
                                        nomor_ref: this.up.bcm,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan BCM baru",
                                        keterangan: "Membuka form checker masuk nomor : " + this.up.bcm,
                                        jenis: "Po",
                                        tanggal: this.DateTime(),
                                    })
                                    axios.post("/api/history", {
                                        nomor_dok: this.up.bcm,
                                        nomor_ref: this.up.nomor_po,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan BCM baru",
                                        keterangan: "Mengirim form checker ke gudang bongkar",
                                        jenis: "Bcm",
                                        tanggal: this.DateTime(),
                                    })
                                    this.$router.push({
                                        name: 'bcmcomponent'
                                    });
                                    this.load = false;
                                    swalWithBootstrapButtons.fire(
                                        'Terkirim!',
                                        'Bukti Checker Masuk berhasil di kirim.',
                                        'success'
                                    )
                                }).catch(error => {
                                    this.load = false;
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali rincian BCM anda!',
                                    })
                                })
                        } else {
                            this.load = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty barang tidak boleh kosong!',
                            })
                        }
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim bukti checker :)',
                        'error'
                    )
                }
            })
        },
        requestBcm() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin meminta konfirmasi BCM ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.checker.length < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput barang apapun!',
                        })
                    } else {
                        this.ok = '';
                        this.banding = '';
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === '' || this.hitung.qty[i] === 0 || this.hitung.qty[i] < 1) {
                                this.nilai = "N";
                            } else {
                                this.nilai = "Y";
                            }
                            this.ok += this.nilai;
                            this.banding += "Y";
                        }
                        if (this.ok === this.banding) {
                            this.up.status = "sent";
                            axios.post("/api/bcm", this.up)
                                .then(res => {
                                    for (let i = 0; i < this.checker.length; i++) {
                                        this.uploadlist.kode_barang = this.checker[i].kode_barang;
                                        this.uploadlist.qty = this.hitung.qty[i];
                                        this.uploadlist.keterangan = this.hitung.keterangan[i];
                                        this.uploadlist.nomor_bcm = this.up.bcm;
                                        this.uploadlist.status = "sent";
                                        this.uploood = {
                                            kode_barang: this.uploadlist.kode_barang,
                                            nomor_bcm: this.uploadlist.nomor_bcm,
                                            sj: this.uploadlist.qty,
                                            bongkar: this.uploadlist.qty,
                                            keterangan: this.uploadlist.keterangan,
                                            nomor_po: this.up.nomor_po,
                                            sisapo: this.checker[i].sisapo,
                                        };
                                        axios.post("/api/listbcm", this.uploood);
                                    };
                                    this.sisanya = 0;
                                    this.poselesai = '';
                                    this.tutuppo = '';
                                    this.bandingtutup = '';
                                    for (let h = 0; h < this.checker.length; h++) {
                                        this.sisapo = parseInt(this.checker[h].sisapo) - parseInt(this.hitung.qty[h]);
                                        if (this.sisapo < 1) {
                                            this.poselesai = "Y";
                                        } else {
                                            this.poselesai = "N";
                                        }
                                        axios.put("/api/listpo/" + this.checker[h].id, {
                                            sisapo: this.sisapo,
                                            closepo: this.poselesai
                                        })
                                        this.tutuppo += this.poselesai;
                                        this.bandingtutup += "Y";
                                    }
                                    if (this.tutuppo === this.bandingtutup) {
                                        axios.put("/api/po/" + this.up.nomor_po, {
                                            poselesai: "Y"
                                        })
                                    }
                                    axios.post("/api/history", {
                                        nomor_dok: this.up.nomor_po,
                                        nomor_ref: this.up.bcm,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan BCM baru",
                                        keterangan: "Membuka form checker masuk nomor : " + this.up.bcm,
                                        jenis: "Po",
                                        tanggal: this.DateTime(),
                                    })
                                    axios.post("/api/history", {
                                        nomor_dok: this.up.bcm,
                                        nomor_ref: this.up.nomor_po,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan BCM baru",
                                        keterangan: "Meminta konfirmasi Form BCM",
                                        jenis: "Bcm",
                                        tanggal: this.DateTime(),
                                    })
                                    this.$router.push({
                                        name: 'bcmcomponent'
                                    });
                                    swalWithBootstrapButtons.fire(
                                        'Terkirim!',
                                        'Bukti Checker Masuk berhasil di kirim.',
                                        'success'
                                    )
                                }).catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali rincian BCM anda!',
                                    })
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty barang tidak boleh kosong!',
                            })
                        }
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim bukti checker :)',
                        'error'
                    )
                }
            })
        },
        validqty() {
            this.pembanding = "";
            this.aksicek = "";
            this.sisanya = 0;
            this.jumlahnya = 0;
            this.a = '';
            this.aplus = '';
            this.bandingnya = '';
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === '') {
                    this.hitung.jumlahnya[i] = 0;
                } else {
                    this.hitung.jumlahnya[i] = this.hitung.qty[i];
                }

                if (this.hitung.jumlahnya[i] > this.checker[i].sisapo) {
                    this.a = "N";
                } else {
                    this.a = "Y";
                }
                this.aplus += this.a;
                this.bandingnya += "Y";
            }
            if (this.aplus === this.bandingnya) {
                this.jenbutton = true;
            } else {
                this.jenbutton = false;
            }
        },
        hapuslist(index) {
            this.checker.splice(index, 1);
            this.hitung.qty.splice(index, 1);
            this.hitung.keterangan.splice(index, 1);
            this.validqty();
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
        ceknomorbcm() {
            axios.get("/api/bcm/" + this.up.bcm)
                .then(res => {
                    this.adabcm = res.data.data;
                    if (this.up.bcm.length === 16 && this.adabcm.length === 0) {
                        this.nyala = true;
                    } else {
                        this.nyala = false;
                    };
                })
        }
    },
}
</script>

<style>

</style>
