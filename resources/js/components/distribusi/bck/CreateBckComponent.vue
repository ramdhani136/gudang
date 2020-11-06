<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Checker :</label>
                <input @input="cekbck()" v-model="up.bck" type="text" class="form-control col-12" maxlength="16" :class="{ 'is-valid': aktif, 'is-invalid': !aktif }">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
            <div class="form-group">
                <label>Kendaraan :</label>
                <input v-model="ket.nopol" class="form-control" @click="showkendaraan()" autocomplete="off" placeholder="Pilih Kendaraan">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Nomor SO</label>
                <input v-model="up.nomor_so" @click="showSo()" type="text" class="form-control" placeholder="Pilih Sales Order">
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
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{total | currency}}</div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
        <div class="row mt-1 mx-auto col-12">
            <Circle5 id="load3" v-if="load"></Circle5>
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th style="width:20%;">Nama Barang</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Diskon</th>
                        <th>Sisa SO</th>
                        <th style="width:12%;">Rencana Kirim</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(listbcm,index) in checker" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{listbcm.kode_barang}}</td>
                        <td>{{listbcm.nama_barang}}</td>
                        <td style="text-align:center">{{listbcm.satuan}}</td>
                        <td style="text-align:center">{{listbcm.harga | currency}}</td>
                        <td style="text-align:center">{{listbcm.diskon | currency}}</td>
                        <td style="text-align:center">{{listbcm.qty-listbcm.bck}}</td>
                        <td style="text-align:center">
                            <input @input="validqty(index)" v-model="hitung.qty[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">
                            <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                        </td>
                        <td style="text-align:center">
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
        <button @click="batal()" class="btn-orange btn ml-2">
            Batal
        </button>
        <button @click="submitBck()" class="btn-success btn ml-1">
            Simpan
        </button>
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
                        <select @change="pilihSo(ambil)" v-model="ambil" class="form-control">
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
    <div v-if="gagal" class="row mt-2">
        <div id="alastolak">
            <div>
                <b>Belum ada list Request Sales Order Yang di pilih! </b>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-kendaraan" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Kendaraan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select @change="cekopsi()" v-model="opsikendaraan" class="form-control">
                            <option value="ambil">Daftar Kendaraan</option>
                            <option value="baru">Input Kendaraan</option>
                        </select>
                    </div>
                    <div class="form-group" v-if="opsikendaraan==='ambil'">
                        <label>Nomor Kendaraan</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="custom ? custom.nopol:''"></div>
                        <div class="placeholder" v-if="custom==null" v-text="ket.nama">Pilih Kendaraan</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="upin" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nomor kendaraan ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.kode" v-text="match.nopol" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" v-if="opsikendaraan!=='ambil'">
                        <label>Nomor Kendaraan</label>
                        <input @input="ceknopol()" v-model="ketup.nopol" type="text" class="form-control" placeholder="Contoh : F.2617.HG" :class="{ 'is-valid': nyala, 'is-invalid': !nyala }">
                    </div>
                    <div class="form-group">
                        <label>Merk</label>
                        <input v-model="ketup.nama" type="text" class="form-control" :disabled="opsikendaraan==='ambil'" placeholder="Contoh: Canter, Dyna 105 ">
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select v-model="ketup.id_jenis" class="form-control" :disabled="opsikendaraan==='ambil'">
                            <option :value="jn.id" v-for="(jn,index) in jeniskendaraan" :key="index">{{jn.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kubikasi (M3)</label>
                        <input v-model="ketup.kubikasi" type="number" class="form-control" :disabled="opsikendaraan==='ambil'">
                    </div>
                    <div class="form-group">
                        <label>Tonase (KG)</label>
                        <input v-model="ketup.tonase" type="number" class="form-control" :disabled="opsikendaraan==='ambil'">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="savekendaraan()" type="button" class="btn btn-primary">Save changes</button>
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
            up: {
                bck: this.bck_nomor(),
                tanggal: this.now(),
            },
            ket: {
                sisaso: [],
                sisasopilih: [],
                tersedia: [],
                ltersedia: [],
                nama: "Pilih Kendaraan",
            },
            listsisa: {},
            checker: [],
            listbcm: {},
            uploadlist: [],
            hitung: {
                qty: [],
                keterangan: [],
                jumlah: []
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
            listso: {},
            qtymasuk: 0,
            sisanya: 0,
            listsoall: {},
            aktif: false,
            adabck: {},
            ambil: {},
            opsikendaraan: 'ambil',
            visible: false,
            query: '',
            selected: 0,
            custom: null,
            itemHeight: 39,
            kendaraan: [],
            ketup: {},
            jeniskendaraan: {},
            nyala: false,
            adakendaraan: {},
            inken: {},
        }
    },
    created() {
        this.getSoAktif();
        this.resetForm();
        this.timer();
    },
    computed: {
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.kendaraan.filter((item) => item.nopol.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods: {
        getSoAktif() {
            axios.get("/api/so/data/aktif")
                .then(res => {
                    this.soaktif = res.data.data;
                    axios.get("/api/kendaraan")
                        .then(res => {
                            this.kendaraan = res.data.data;
                            axios.get("/api/jeniskendaraan")
                                .then(res => {
                                    this.jeniskendaraan = res.data.data;
                                    this.load = false;
                                })
                        })
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
        pilihSo(ambil) {
            this.validqty();
            this.hitung.qty = [];
            this.checker = [];
            this.ket.customer = ambil.customer;
            this.ket.lokasi = ambil.lokasi;
            this.ket.alamat = ambil.alamat;
            this.ket.distribusi = ambil.distribusi;
            this.up.nomor_so = ambil.nomor_so;
            this.ket.status = ambil.statusso;
            this.ket.nomor_rso = ambil.nomor_rso;
            this.up.keterangan = ambil.keterangan;
            axios.get("/api/listso/data/listbck/" + ambil.nomor_so)
                .then(res => {
                    this.listso = res.data.data;
                    for (let i = 0; i < this.listso.length; i++) {
                        this.checker.push(this.listso[i]);
                        this.ket.sisaso[i] = parseFloat(this.listso[i].qty) - parseFloat(this.listso[i].bck)
                        this.ket.tersedia[i] = parseFloat(this.listso[i].tersedia) - parseFloat(this.listso[i].bck)
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
            this.load = true;
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
                                            diskon: this.checker[i].diskon,
                                            keterangan: this.hitung.keterangan[i],
                                        }
                                        axios.post("/api/listbck", this.uploadlist)
                                            .then(res => {
                                                axios.get("/api/listso/data/" + this.up.nomor_so + "/" + this.checker[i].kode_barang)
                                                    .then(res => {
                                                        this.listso = res.data.data;
                                                        this.qtymasuk = parseFloat(this.listso[0].bck) + parseFloat(this.hitung.qty[i]);
                                                        this.sisanya = parseFloat(this.listso[0].qty) - (parseFloat(this.listso[0].bck) + parseFloat(this.hitung.qty[i]));
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
                                                                    this.bplus = '';
                                                                    this.bban = '';
                                                                    for (let i = 0; i < this.listsoall.length; i++) {
                                                                        this.bplus += this.listsoall[i].closeso;
                                                                        this.bban += "Y";
                                                                    }
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
                                            this.load = false;
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
                                    this.load = false;
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
                            this.load = false;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum memasukkan item apapun!',
                        })
                        this.load = false;
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
            this.total = 0;
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] > (parseFloat(this.checker[i].qty) - parseFloat(this.checker[i].bck))) {
                    this.hitung.qty[i] = (parseFloat(this.checker[i].qty) - parseFloat(this.checker[i].bck));
                }
                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                    this.hitung.jumlah[i] = 0;
                } else {
                    this.hitung.jumlah[i] = this.hitung.qty[i];
                }
                this.subtotal = (parseFloat(this.checker[i].harga) - parseFloat(this.checker[i].diskon)) * parseFloat(this.hitung.jumlah[i]);
                this.total += parseFloat(this.subtotal);
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
            this.ket.sisasopilih.splice(index, 1);
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
        cekbck() {
            axios.get("/api/bck/" + this.up.bck)
                .then(res => {
                    this.adabck = res.data.data;
                    if (this.adabck.length < 1 && this.up.bck.length === 16) {
                        this.aktif = true;
                    } else {
                        this.aktif = false;
                    }
                })
        },
        showkendaraan() {
            this.getSoAktif();
            $("#modal-kendaraan").modal("show");
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
            this.up.id_kendaraan = this.custom.id;
            this.ketup.nama = this.custom.nama;
            this.ketup.id_jenis = this.custom.id_jenis;
            this.ketup.kubikasi = this.custom.kubikasi;
            this.ketup.tonase = this.custom.tonase;
            this.ket.nopol = this.custom.nopol;
            this.visible = false;
        },
        upin() {
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
        cekopsi() {
            this.ketup = {};
            this.ket.nopol = "";
            this.up.id_kendaraan = "";
            this.ket.nama = "Pilih Kendaraan";
            this.query = "";
            this.custom = null;
            this.up.id_kendaraan = "";
        },
        ceknopol() {
            axios.get("/api/kendaraan/view/" + this.ketup.nopol)
                .then(res => {
                    this.adakendaraan = res.data.data;
                    if (this.ketup.nopol.length > 0 && this.adakendaraan.length < 1) {
                        this.nyala = true;
                    } else {
                        this.nyala = false;
                    }
                })
        },
        savekendaraan() {
            // console.log(this.ketup);
            if (this.opsikendaraan === 'ambil') {
                $("#modal-kendaraan").modal("hide");
            } else {
                axios.post("/api/kendaraan", this.ketup)
                    .then(res => {
                        axios.get("/api/kendaraan/view/" + this.ketup.nopol)
                            .then(res => {
                                this.inken = res.data.data;
                                this.up.id_kendaraan = this.inken[0].id;
                                this.ket.nopol = this.inken[0].nopol;
                                this.ket.nama = this.inken[0].nopol;
                                this.getSoAktif();
                                this.opsikendaraan = "ambil";
                                this.visible = false;
                                $("#modal-kendaraan").modal("hide");
                            })
                    })
            }
        }
    },
}
</script>

<style>

</style>
