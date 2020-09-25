<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor BBM :</label>
                <input v-model="up.bbm" type="text" class="form-control col-12" autocomplete="disabled">
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
                <label>Nomor Bukti Checker</label>
                <input v-model="up.nomor_bcm" @click="showBcm()" type="text" class="form-control" placeholder="Pilih Nomor BCM" autocomplete="disabled">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Polisi</label>
                <input type="text" v-model="ket.nopol" class="form-control" disabled autocomplete="disabled">
            </div>
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12" autocomplete="disabled"></textarea>
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
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Qty</th>
                        <th>Jumlah Real</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(listbbm,index) in checker" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{listbbm.nama_barang}}</td>
                        <td style="text-align:center">{{listbbm.satuan}}</td>
                        <td style="text-align:center">{{listbbm.bongkar}}</td>
                        <td style="text-align:center">
                            <input @input="validasiqty()" v-model="hitung.qty[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">
                            <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <!-- <button @click="draftBbm()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button> -->
        <button @click="submitBBM()" class="btn-success btn ml-2">
            Kirim BBM
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
                        <label>Pilih Bukti Checker</label>
                        <select @change="pilihBcm(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in bcmaktif" :key="index">{{aktif.bcm}}</option>
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
                                    <td style="text-align:center">{{ls.bongkar}}</td>
                                    <td style="text-align:center">
                                        <input v-model="checker" type="checkbox" :value="ls" disabled>
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
                bbm: this.bbm_nomor(),
                tanggal: this.now(),
            },
            bcmaktif: {},
            ket: {},
            aktif: {},
            listsisa: {},
            checker: [],
            listbbm: {},
            uploadlist: [],
            hitung: {
                qty: [],
                keterangan: [],
                bagi: []
            },
            uploood: {},
            dtime: this.DateTime(),
            totalmasuk: {},
            listpo: {},
            uplist: {},
            uppo: {},
            uplistsisa: {
                qty_masuk: 0,
            },
            uplistsisa2: {
                qty_masuk: 0,
            },
            listpost: {},
            listsisas: {},
            listbcm: {},
            comp: {},
            input: {},
            listso: {},
            diterima: 0,
            statusso: '',
            nomorpo: '',
            listtutuppo: {},
            bbm: 0,
            listbagi: {}
        }
    },
    created() {
        this.getBcmAktif();
        this.resetForm();
    },
    computed: {

    },
    methods: {
        getBcmAktif() {
            axios.get("/api/bcm/data/open")
                .then(res => {
                    this.bcmaktif = res.data.data;
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
        bbm_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "BBM-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        showBcm() {
            $("#modal-po").modal("show");
        },
        resetForm() {
            this.checker = [];
        },
        pilihBcm(aktif) {
            this.checker = [];
            this.ket.supplier = aktif.supplier;
            this.up.nomor_bcm = aktif.bcm;
            this.ket.nopol = aktif.nopol;
            this.nomorpo = aktif.nomor_po;
            axios.get("/api/listbcm/data/" + aktif.bcm)
                .then(res => {
                    this.listsisa = res.data.data;
                    for (let i in this.listsisa) {
                        this.checker.push(this.listsisa[i]);
                    }
                });
        },
        checklist() {
            $("#modal-po").modal("hide");
        },
        submitBBM() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengirim BBM ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.checker.length > 0) {
                        this.a = '';
                        this.b = '';
                        this.banding = '';
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === '' || this.hitung.qty[i] < 1) {
                                this.a = "N";
                            } else {
                                this.a = "Y";
                            }
                            this.b += this.a;
                            this.banding += "Y";
                        }
                        if (this.b === this.banding) {
                            axios.post("/api/bbm", this.up)
                                .then(res => {
                                    for (let i = 0; i < this.checker.length; i++) {
                                        /* input list bbm */
                                        axios.post("/api/listbbm", {
                                            nomor_bbm: this.up.bbm,
                                            kode_barang: this.checker[i].kode_barang,
                                            qty: this.hitung.qty[i],
                                            keterangan: this.hitung.keterangan[i]
                                        })
                                        /* end */

                                        /* update listso */
                                        axios.get("/api/listso/data/bbmbagi/" + this.checker[i].kode_barang)
                                            .then(res => {
                                                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] < 1 || this.hitung.qty[i] === '') {
                                                    this.hitung.bagi[i] = 0;
                                                } else {
                                                    this.hitung.bagi[i] = this.hitung.qty[i];
                                                }
                                                this.listso = res.data.data;
                                                this.diterima = 0;
                                                this.statusso = '';
                                                for (let j = 0; j < this.listso.length; j++) {
                                                    if (this.hitung.bagi[i] <= this.listso[j].blmdatang) {
                                                        this.diterima = parseInt(this.listso[j].tersedia + parseInt(this.hitung.bagi[i]));
                                                        if (this.diterima < this.listso[j].blmdatang) {
                                                            this.statusso = "Y";
                                                        } else {
                                                            this.statusso = "N";
                                                        }
                                                        this.hitung.bagi[i] = 0;
                                                    } else {
                                                        this.statusso = "N";
                                                        this.diterima = parseInt(this.listso[j].blmdatang + parseInt(this.listso[j].tersedia));
                                                        this.hitung.bagi[i] = parseInt(this.hitung.bagi[i]) - parseInt(this.listso[j].blmdatang);
                                                    }
                                                    axios.put("/api/listso/" + this.listso[j].id, {
                                                        tersedia: this.diterima,
                                                        openso: this.statusso,
                                                    })
                                                }
                                                axios.get("/api/listpo/data/" + this.nomorpo + "/" + this.checker[i].kode_barang)
                                                    .then(res => {
                                                        this.listbagi = res.data.data;
                                                        this.sisapo = 0;
                                                        this.bbm = 0;
                                                        for (let l = 0; l < this.listbagi.length; l++) {
                                                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] < 1 || this.hitung.qty[i] === '') {
                                                                this.hitung.qty[i] = 0;
                                                            }
                                                            this.sisapo = (parseInt(this.checker[i].sj + this.listbagi[l].sisapo)) - parseInt(this.hitung.qty[i]);
                                                            this.bbm = parseInt(this.listbagi[l].bbm) + parseInt(this.hitung.qty[i]);
                                                            if (this.sisapo > 0) {
                                                                this.closepo = "N";
                                                            } else {
                                                                this.closepo = "Y";
                                                            }
                                                            axios.put("/api/listpo/" + this.listbagi[l].id, {
                                                                bbm: this.bbm,
                                                                sisapo: this.sisapo,
                                                                closepo: this.closepo
                                                            })
                                                        }
                                                    })
                                                axios.get("/api/listpo/" + this.nomorpo)
                                                    .then(res => {
                                                        this.listtutuppo = res.data.data;
                                                        this.aplus = '';
                                                        this.banding = '';
                                                        for (let y = 0; y < this.listtutuppo.length; y++) {
                                                            this.aplus += this.listtutuppo[y].closepo;
                                                            this.banding += "Y";
                                                        }
                                                        if (this.aplus === this.banding) {
                                                            axios.put("/api/po/" + this.nomorpo, {
                                                                poselesai: "Y",
                                                                status: "Selesai",
                                                            })
                                                        } else {
                                                            axios.put("/api/po/" + this.nomorpo, {
                                                                poselesai: "N",
                                                                status: "Acc",
                                                            })
                                                        }
                                                    })
                                            })
                                    }
                                    /* tutup bcm */
                                    axios.put("/api/bcm/" + this.up.nomor_bcm, {
                                        status: 'close'
                                    }).then(res => {
                                        axios.post("/api/history", {
                                            nomor_dok: this.up.nomor_bcm,
                                            nomor_ref: this.up.bbm,
                                            id_user: this.ambiluser.id,
                                            notif: "Anda mendapatkan permintaan BBM baru",
                                            keterangan: "Membuka form BBM nomor : " + this.up.bbm,
                                            jenis: "Bcm",
                                            tanggal: this.DateTime(),
                                        }).then(res => {
                                            axios.post("/api/history", {
                                                nomor_dok: this.up.bbm,
                                                nomor_ref: this.up.nomor_bcm,
                                                id_user: this.ambiluser.id,
                                                notif: "Anda mendapatkan permintaan BBM baru",
                                                keterangan: "Membuat BBM nomor : " + this.up.bbm,
                                                jenis: "Bbm",
                                                tanggal: this.DateTime(),
                                            })
                                        }).then(res => {
                                            swalWithBootstrapButtons.fire(
                                                'Sukses!',
                                                'Berhasil mengirim BBM.',
                                                'success'
                                            )
                                            this.$router.push({
                                                name: 'ingoods'
                                            });
                                        })
                                    })
                                    /* end */
                                }).catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali rincian BBM anda!',
                                    })
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Cek kembali qty barang anda, tidak boleh kosong!',
                            })
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'And belum menginput item apapun!',
                        })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim bbm :)',
                        'error'
                    )
                }
            })
        },
        validasiqty() {
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] > this.checker[i].bongkar) {
                    this.hitung.qty[i] = this.checker[i].bongkar
                }
            }
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
        }
    },
}
</script>

<style>

</style>
