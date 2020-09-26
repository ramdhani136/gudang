<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Checker :</label>
                <input v-model="up.bck" type="text" class="form-control col-12" :disabled="statusbck!=='draft' || ambiluser.distribusi===0">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" :disabled="statusbck!=='draft' || ambiluser.distribusi===0">
            </div>
            <div class="form-group">
                <label>Nopol Kendaraan :</label>
                <select v-model="up.id_kendaraan" name="kendaraan" class="form-control" :disabled="statusbck!=='draft' || ambiluser.distribusi===0">
                    <option :value="up.id_kendaraan" v-for="(kd,index) in kendaraan" :key="index">{{kd.nopol}}</option>
                </select>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Nomor SO</label>
                <input v-model="up.nomor_so" @click="showSo()" type="text" class="form-control" placeholder="Pilih Sales Order" :disabled="statusbck!=='draft' || ambiluser.distribusi===0">
            </div>
            <div class="form-group">
                <label>Customer</label>
                <select v-model="ket.customer" name="customer" class="col-12 form-control" disabled>
                    <option :value="ket.customer">{{ket.customer}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Distribusi</label>
                <input v-model="ket.distribusi" type="text" class="form-control" disabled>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Lokasi</label>
                <input v-model="ket.lokasi" type="text" class="form-control" disabled>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea v-model="ket.alamat" name="keterangan" class="form-control col-12" disabled></textarea>
            </div>
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12" :disabled="statusbck!=='draft' || ambiluser.distribusi===0"></textarea>
            </div>
        </div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
        <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{total | currency}}</div>
        <div class="row mt-1 mx-auto col-12">
            <Circle5 id="load3" v-if="load"></Circle5>
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Sisa SO</th>
                        <th>Qty Tersedia</th>
                        <th>Rencana Kirim</th>
                        <th>Keterangan</th>
                        <th v-if="statusbck==='draft'">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(listbcm,index) in checker" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{listbcm.kode_barang}}</td>
                        <td>{{listbcm.nama_barang}}</td>
                        <td style="text-align:center">{{listbcm.satuan}}</td>
                        <td style="text-align:center">{{listbcm.harga | currency}}</td>
                        <td style="text-align:center">{{hitung.sisaso[index]}}</td>
                        <td style="text-align:center">{{hitung.tersedia[index]}}</td>
                        <td style="text-align:center">
                            <input @input="validqty(index)" v-model="hitung.qty[index]" type="number" class="form-control" :disabled="statusbck!=='draft' || ambiluser.distribusi===0">
                        </td>
                        <td style="text-align:center">
                            <textarea v-model="hitung.keterangan[index]" class="form-control" :disabled="statusbck!=='draft' || ambiluser.distribusi===0"></textarea>
                        </td>
                        <td v-if="statusbck==='draft'" style="text-align:center">
                            <button @click="hapusitem(index)" class="btn btn-danger">Hapus</button>
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
        <button class="btn btn-primary ml-3">Kembali</button>
        <button class="btn btn-danger ml-1">Batalkan BCK</button>
    </div>
    <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form SO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih SO</label>
                        <select @change="pilihSo(aktif)" v-model="aktif" class="form-control">
                            <option :value="aktif" v-for="(aktif,index) in soaktif" :key="index">{{aktif.nomor_so}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <input v-model="ket.customer" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody">
                        <table class="table mt-2 table-striped table-bordered" style="width:100%">
                            <thead id="rsthead">
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Kode</th>
                                    <th style="text-align:center">Item</th>
                                    <th style="text-align:center">Satuan</th>
                                    <th style="text-align:center">Sisa SO</th>
                                    <th style="text-align:center">Qty Tersedia</th>
                                    <th style="text-align:center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(ls,index) in listso" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td>{{ls.kode_barang}}</td>
                                    <td>{{ls.nama_barang}}</td>
                                    <td style="text-align:center">{{ls.satuan}}</td>
                                    <td style="text-align:center">{{ket.sisaso[index]}}</td>
                                    <td style="text-align:center">{{ket.tersedia[index]}}</td>
                                    <td style="text-align:center">
                                        <input @change="pilihlistchecker()" v-model="checker" type="checkbox" :value="ls" :disabled="ket.ltersedia[index]===0">
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
                <div class="error-actions">
                    <router-link to="/so" class="btn btn-primary btn-lg">
                        Lihat Data SO
                    </router-link>
                </div>
            </div>
        </div>
    </div>
    <div v-if="gagal" class="row mt-2">
        <div id="alastolak">
            <div>
                <b>Belum ada list Request Sales Order Yang di pilih! </b>
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
            up: {},
            ket: {
                sisaso: [],
                sisasopilih: [],
                tersedia: [],
                ltersedia: [],
            },
            aktif: {},
            listsisa: {},
            checker: [],
            listbcm: {},
            uploadlist: [],
            hitung: {
                qty: [],
                keterangan: [],
                jumlah: [],
                sisaso: [],
                tersedia: []
            },
            uplistsisa: {
                qty_masuk: 0,
            },
            soaktif: {},
            listso: {},
            gagal: false,
            uplist: {},
            qty: 0,
            listrso: {},
            nilai: null,
            harga: 0,
            subtotal: 0,
            total: 0,
            kendaraan: {},
            listso: {},
            qtymasuk: 0,
            sisanya: 0,
            listsoall: {},
            bck: {},
            statusbck: '',
            listbck: {},
            listsonya: {}
        }
    },
    created() {
        this.getSoAktif();
        this.resetForm();
        this.timer();
    },
    computed: {

    },
    methods: {
        getSoAktif() {
            axios.get("/api/bck/" + this.$route.params.bck)
                .then(res => {
                    this.bck = res.data.data;
                    this.up = this.bck[0];
                    this.ket = this.bck[0];
                    this.statusbck = this.bck[0].status;
                    axios.get("/api/listbck/" + this.$route.params.bck)
                        .then(res => {
                            this.listbck = res.data.data;
                            for (let i = 0; i < this.listbck.length; i++) {
                                this.checker.push({
                                    kode_barang: this.listbck[i].kode_barang,
                                    nama_barang: this.listbck[i].nama_barang,
                                    satuan: this.listbck[i].satuan,
                                    harga: this.listbck[i].harga,
                                })
                                this.hitung.qty[i] = this.listbck[i].qty;
                                axios.get("/api/listso/data/" + this.up.nomor_so + "/" + this.listbck[i].kode_barang)
                                    .then(res => {
                                        this.listsonya = res.data.data;
                                        this.hitung.sisaso[i] = (parseInt(this.listsonya[0].qty) - parseInt(this.listsonya[0].bck)) + parseInt(this.listbck[i].qty);
                                        this.hitung.tersedia[i] = (parseInt(this.listsonya[0].tersedia) - parseInt(this.listsonya[0].bck)) + parseInt(this.listbck[i].qty)
                                        this.hitung.keterangan[i] = this.listbck[i].keterangan;
                                    })
                            }
                            axios.get("/api/so/data/aktif")
                                .then(res => {
                                    this.validqty();
                                    this.soaktif = res.data.data;
                                    axios.get("/api/kendaraan")
                                        .then(res => {
                                            this.kendaraan = res.data.data;
                                            this.load = false;
                                        })
                                });
                        })

                })
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
        bck_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "BCK-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        showSo() {
            $("#modal-po").modal("show");
        },
        resetForm() {
            this.checker = [];
        },
        pilihSo(aktif) {
            this.validqty();
            this.hitung.qty = [];
            this.checker = [];
            this.ket.customer = aktif.customer;
            this.ket.lokasi = aktif.lokasi;
            this.ket.alamat = aktif.alamat;
            this.ket.distribusi = aktif.distribusi;
            this.up.nomor_so = aktif.nomor_so;
            this.ket.status = aktif.statusso;
            this.ket.nomor_rso = aktif.nomor_rso;
            axios.get("/api/listso/data/listbck/" + aktif.nomor_so)
                .then(res => {
                    this.listso = res.data.data;
                    for (let i = 0; i < this.listso.length; i++) {
                        this.ket.sisaso[i] = parseInt(this.listso[i].qty) - parseInt(this.listso[i].bck)
                        this.ket.tersedia[i] = parseInt(this.listso[i].tersedia) - parseInt(this.listso[i].bck)
                    }

                })
        },
        checklist() {
            $("#modal-po").modal("hide");
        },
        pilihlistchecker() {
            for (let i = 0; i < this.checker.length; i++) {
                this.ket.sisasopilih[i] = this.ket.sisaso[i];
                this.ket.tersedia[i] = this.ket.tersedia[i];
            }
        },
        submitBck() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengirim BCK ini ke Warehouse!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.checker.length > 0) {
                        this.a = '';
                        this.aplus = '';
                        this.banding = '';
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] < 1 || this.hitung.qty[i] === '') {
                                this.hitung.qty[i] = 0;
                            }
                            if (this.hitung.qty[i] < 1) {
                                this.a = "N";
                            } else {
                                this.a = "Y";
                            }
                            this.aplus += this.a;
                            this.banding += "Y";
                        }
                        if (this.aplus === this.banding) {
                            axios.post("/api/bck", this.up)
                                .then(res => {
                                    this.qtymasuk = 0;
                                    this.sisanya = 0;
                                    this.closepo = '';
                                    for (let i = 0; i < this.checker.length; i++) {
                                        this.uploadlist = {
                                            nomor_bck: this.up.bck,
                                            kode_barang: this.checker[i].kode_barang,
                                            qty: this.hitung.qty[i],
                                            harga: this.checker[i].harga,
                                            keterangan: this.hitung.keterangan[i],
                                        }
                                        axios.post("/api/listbck", this.uploadlist)
                                            .then(res => {
                                                axios.get("/api/listso/data/" + this.up.nomor_so + "/" + this.checker[i].kode_barang)
                                                    .then(res => {
                                                        this.listso = res.data.data;
                                                        this.qtymasuk = parseInt(this.listso[0].bck) + parseInt(this.hitung.qty[i]);
                                                        this.sisanya = parseInt(this.listso[0].qty) - (parseInt(this.listso[0].bck) + parseInt(this.hitung.qty[i]));
                                                        if (this.sisanya < 1) {
                                                            this.closepo = "Y";
                                                        } else {
                                                            this.closepo = "N";
                                                        }
                                                        axios.put("/api/listso/" + this.listso[0].id, {
                                                            bck: this.qtymasuk,
                                                            closeso: this.closepo
                                                        }).then(res => {
                                                            axios.get("/api/listso/" + this.up.nomor_so)
                                                                .then(res => {
                                                                    this.listsoall = res.data.data;
                                                                    console.log(this.listsoall)
                                                                    this.bplus = '';
                                                                    this.bban = '';
                                                                    for (let i = 0; i < this.listsoall.length; i++) {
                                                                        this.bplus += this.listsoall[i].closeso;
                                                                        this.bban += "Y";
                                                                    }
                                                                    console.log(this.bplus);
                                                                    console.log(this.bban);
                                                                    if (this.bplus === this.bban) {
                                                                        axios.put("/api/so/" + this.up.nomor_so, {
                                                                            closebck: "Y"
                                                                        })
                                                                    }
                                                                })
                                                        })
                                                    })
                                            })
                                    }
                                    this.$nextTick(() => {
                                        axios.post("/api/history", {
                                            nomor_dok: this.up.nomor_so,
                                            id_user: this.ambiluser.id,
                                            notif: "Anda mendapatkan BCK baru!",
                                            keterangan: "Membuka BCK nomor : " + this.up.bck,
                                            jenis: "So",
                                            tanggal: this.DateTime(),
                                        })
                                        axios.post("/api/history", {
                                            nomor_dok: this.up.bck,
                                            id_user: this.ambiluser.id,
                                            notif: "Anda mendapatkan BCK baru!",
                                            keterangan: "Mengirim form bck ke warehouse",
                                            jenis: "Bck",
                                            tanggal: this.DateTime(),
                                        }).then(res => {
                                            swalWithBootstrapButtons.fire(
                                                'Sukses!',
                                                'BCK berhasil di kirim.',
                                                'success'
                                            )
                                            this.$router.push({
                                                name: 'bck'
                                            })
                                        })
                                    })
                                }).catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali rincian BCK anda!',
                                    })
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Periksa kembali qty item, pastikan sudah terisi!',
                            })
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum memasukkan item apapun!',
                        })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim BCK :)',
                        'error'
                    )
                }
            })
        },
        validqty(index) {
            if (parseInt(this.hitung.qty[index]) > parseInt(this.hitung.tersedia[index])) {
                this.hitung.qty[index] = this.hitung.tersedia[index];
            }
            this.total = 0;
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                    this.hitung.jumlah[i] = 0;
                } else {
                    this.hitung.jumlah[i] = this.hitung.qty[i];
                }
                this.subtotal = parseInt(this.checker[i].harga) * parseInt(this.hitung.jumlah[i]);
                this.total += parseInt(this.subtotal);
            }
        },
        batal() {
            this.$router.push({
                name: 'bck'
            });
        },
        timer() {
            setInterval(() => {
                this.gagal = false;
            }, 5000);
        },
        hapusitem(index) {
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
    },
}
</script>

<style>

</style>
