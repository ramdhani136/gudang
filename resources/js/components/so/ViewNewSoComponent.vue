<template>
<div class="container">
    <div class="row row-cols-2" v-for="(vso,index) in so" :key="index">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor SO :</label>
                <input v-model="vso.nomor_so" type="text" class="form-control col-12" :disabled="ket.statusnya!=='Draft'">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="vso.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Tanggal Kirim :</label>
                <input v-model="vso.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" :disabled="ket.statusnya!=='Draft'">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Customer</label>
                <select v-model="ket.customer" name="customer" class="col-12 form-control" disabled>
                    <option :value="ket.customer">{{ket.customer}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nomor RSO</label>
                <input v-model="vso.nomor_rso" type="text" class="form-control" placeholder="Pilih RSO" disabled>
            </div>
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="vso.keterangan" name="keterangan" class="form-control col-12" :disabled="ket.statusnya!=='Draft'"></textarea>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Distribusi :</label>
                <select class="form-control" v-model="vso.distribusi" @change="aksidistribusi(vso)" :disabled="ket.statusnya!=='Draft'">
                    <option value="default">- Masukan pilihan anda -</option>
                    <option value="kirim">Di Kirim</option>
                    <option value="ambil">Ambil Sendiri</option>
                </select>
            </div>
            <div class="form-group">
                <label>Lokasi</label>
                <input @click="clikdistribusi()" v-model="vso.lokasi" name="alamat" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea v-model="vso.alamat" name="alamat" class="form-control col-12" disabled></textarea>
            </div>
        </div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
        <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{invoice | currency}}</div>
        <div class="row mt-1 mx-auto col-12">
            <Circle5 id="load3" v-if="load"></Circle5>
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Diskon</th>
                        <th>Status</th>
                        <th v-if="ket.status==='Tidak Tersedia'">Estimasi</th>
                        <th v-if="ket.statusnya!=='Draft' && ket.statusnya!=='Tolak'">Sudah Kirim</th>
                        <th v-if="ket.statusnya==='Draft' || ket.statusnya==='Tolak'">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(ch,index) in listnewso" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{ch.kode_barang}}</td>
                        <td>{{ch.nama_barang}}</td>
                        <td style="text-align:center">
                            <input style="text-align:center" @input="validasiqty" v-model="hitung.qty[index]" type="number" class="form-control" :disabled="ket.statusnya!=='Draft'">
                        </td>
                        <td style="text-align:center">{{ch.satuan}}</td>
                        <td style="text-align:center">{{ch.harga |currency}}</td>
                        <td style="text-align:center">{{ch.diskon |currency}}</td>
                        <td style="text-align:center">{{ket.status}}</td>
                        <td v-if="ket.status==='Tidak Tersedia'" style="text-align:center">{{ch.tgl_datang}}</td>
                        <td v-if="ket.statusnya!=='Draft' && ket.statusnya!=='Tolak'" style="text-align:center">{{ch.bbk}}</td>
                        <td v-if="ket.statusnya==='Draft' || ket.statusnya==='Tolak'" style="text-align:center">
                            <button @click="hapuslistSo(index)" class="btn btn-danger">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2" v-for="(vso,index) in so" :key="index">
        <!-- <button v-if="ket.statusnya==='Di Selesaikan' || ket.statusnya==='Draft' || ket.statusnya==='Tolak' || ket.statusnya==='Selesai'" @click="kembali()" class="btn-primary btn ml-4 mt-2" >
                    Kembali
                </button> -->
        <button v-if="ket.statusnya==='Draft'" @click="submitSo(vso)" class="btn-success btn ml-4 mt-2">
            Kirim SO
        </button>
        <button v-if="(ket.statusnya==='Dic' || ket.statusnya==='Sent' || ket.statusnya==='Acc'  || ket.statusnya==='Tolak') && (ambiluser.sales===1 || ambiluser.superadmin===1)" @click="reqedit(vso)" class="btn-orange btn ml-4 mt-2">
            Request Edit SO
        </button>
        <button v-if="ket.statusnya==='Tolak'" @click="submitSo(vso)" class="btn-success btn ml-2 mt-2">
            Kirim Ulang SO
        </button>
        <button v-if="ket.statusnya==='Sent' || ket.statusnya==='Draft' " @click="reqbatal(vso)" class="btn-none btn ml-1 mt-2">
            Batalkan SO
        </button>
        <button v-if="ambiluser.distribusi===1 && ket.statusnya==='Dic' " @click="formtolak(vso)" class="btn-none btn ml-4 mt-2">
            Tolak SO
        </button>
        <button v-if="ambiluser.distribusi===1  && ket.statusnya==='Dic'" @click="AccDic(vso)" class="btn-success btn ml-2 mt-2">
            Terima SO
        </button>
    </div>
    <div v-if="vso.status=='Tolak'" v-for="(vso,index) in so" :key="index" id="alastolak" class="mt-3">
        <div v-for="(lso,index) in so" :key="index">
            <b>{{lso.alastolak}}</b>
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
    <div v-if="gagal" class="row mt-2">
        <div id="alastolak">
            <div>
                <b>Belum ada list Request Sales Order Yang di pilih! </b>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-lokasi" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Tujuan Pengiriman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tujuan</label>
                        <select class="form-control" v-model="lokasi">
                            <option value="default">Default</option>
                            <option value="ekspedisi">Ekspedisi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group" v-if="lokasi==='ekspedisi'">
                        <label>Lokasi</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="eks ? eks.nama:''"></div>
                        <div class="placeholder" v-if="eks==null">Pilih Ekspedisi</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="up" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama ekspedisi..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.id" v-text="match.nama" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div v-if="lokasi==='ekspedisi'" class="form-group mt-3">
                        <label>Alamat</label>
                        <textarea v-model="eksal" class="form-control" disabled></textarea>
                    </div>
                    <div v-if="lokasi==='default'" class="form-group">
                        <label>Alamat</label>
                        <textarea v-model="defaultal" class="form-control" disabled></textarea>
                    </div>
                    <div v-if="lokasi==='lainnya'" class="form-group">
                        <label>Tempat/Gedung</label>
                        <input type="text" class="form-control" v-model="lainlok">
                    </div>
                    <div v-if="lokasi==='lainnya'" class="form-group">
                        <label>Alamat</label>
                        <textarea v-model="lainal" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer" v-for="vso in so" :key="vso.nomor_so">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="selectLokasi(vso)" class="btn btn-primary" data-dismiss="modal">Save Change</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Penolakan SO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="up.alastolak" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="tolakSo()" class="btn btn-primary">Konfirmasi Tolak</button>
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
            ket: {
                qty: [],
                coba: []
            },
            gagal: false,
            distribusi: 'default',
            lokasi: 'default',
            dataekspedisi: {},
            visible: true,
            visiblecust: false,
            query: '',
            selected: 0,
            eks: null,
            itemHeight: 39,
            disabled: 1,
            hitung: {
                qty: []
            },
            sub: 0,
            invoice: 0,
            lisallrso: {},
            listnewso: {},
            so: {},
            uploadso: {},
            uplist: {},
            ada: 0,
            listfull: {},
            diisi: '',
            banding: '',
            aksi: '',
            akses: true,
            bbk: 0,
            up: {},
            listsoup: {},
            history: {},
            nomorso: ''
        }
    },
    created() {
        this.timer();
        this.getekspedisi();
        this.getso();
    },
    computed: {
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.dataekspedisi.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        },
    },
    methods: {
        getso() {
            axios.get("/api/so/" + this.$route.params.id)
                .then(res => {
                    this.so = res.data.data;
                    this.ket.customer = this.so[0].customer;
                    this.ket.statusnya = this.so[0].status;
                    this.nomorso = this.so[0].nomor_so;
                    if (this.so[0].statusso === "tersedia") {
                        this.ket.status = "Tersedia"
                    } else {
                        this.ket.status = "Tidak Tersedia"
                    }
                    axios.get("/api/listso/" + this.$route.params.id)
                        .then(res => {
                            this.listnewso = res.data.data;
                            this.invoice = 0;
                            for (let i = 0; i < this.listnewso.length; i++) {
                                this.hitung.qty[i] = this.listnewso[i].qty;
                                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                                    this.sub = 0;
                                } else {
                                    this.sub = this.hitung.qty[i];
                                }

                                this.invoice += parseInt(this.sub) * (parseInt(this.listnewso[i].harga) - parseInt(this.listnewso[i].diskon));
                                console.log(this.invoice)
                            }
                            this.load = false;
                        })
                })
        },
        getekspedisi() {
            axios.get("/api/ekspedisi")
                .then(res => {
                    this.dataekspedisi = res.data.data;
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
        so_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "SO-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        submitSo(vso) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengirim SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.diisi = "";
                    this.banding = "";
                    this.aksi = "";
                    for (let c = 0; c < this.listnewso.length; c++) {
                        if (this.hitung.qty[c] === undefined || this.hitung.qty[c] === "" || this.hitung.qty[c] < 1) {
                            this.diisi = "N";
                        } else {
                            this.diisi = "Y";
                        }
                        this.aksi += this.diisi;
                        this.banding += "Y";
                    }
                    if (this.aksi === this.banding) {
                        if (vso.nomor_so === this.listnewso[0].nomor_so) {
                            this.uploadso = {
                                nomor_so: vso.nomor_so,
                                tanggal_kirim: vso.tanggal_kirim,
                                keterangan: vso.keterangan,
                                distribusi: vso.distribusi,
                                alamat: vso.alamat,
                                lokasi: vso.lokasi,
                                status: 'Kordinator',
                                alastolak: '',
                                id_ekspedisi: vso.id_ekspedisi
                            };
                            axios.put("/api/so/" + this.listnewso[0].nomor_so, this.uploadso)
                                .then(res => {
                                    axios.get("/api/listso/" + this.$route.params.id)
                                        .then(res => {
                                            this.listfull = res.data.data
                                            for (let n = 0; n < this.listfull.length; n++) {
                                                axios.delete("/api/listso/" + this.listfull[n].id)
                                                    .then(res => {})
                                            }
                                            this.ada = 0;
                                            for (let i = 0; i < this.listnewso.length; i++) {
                                                if (vso.statusso === "tersedia") {
                                                    this.ada = this.hitung.qty[i];
                                                } else {
                                                    this.ada = 0;
                                                }
                                                this.uplist = {
                                                    tanggal_datang: this.listnewso[i].tgl_datang,
                                                    nomor_so: vso.nomor_so,
                                                    kode_barang: this.listnewso[i].kode_barang,
                                                    harga: this.listnewso[i].harga,
                                                    id_custprice: this.listnewso[i].id_custprice,
                                                    qty: this.hitung.qty[i],
                                                    qtyrso: 0,
                                                    tersedia: this.ada,
                                                    statusso: this.ket.status,
                                                    diskon: this.listnewso[i].diskon,
                                                }
                                                axios.post("/api/listso", this.uplist)
                                            }
                                        })
                                    axios.post("/api/history", {
                                        nomor_dok: vso.nomor_so,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan SO baru",
                                        keterangan: "So di kirim ke Sales Kordinator",
                                        jenis: "So",
                                        tanggal: this.DateTime(),
                                    })
                                    this.$router.push({
                                        name: 'so'
                                    })
                                    swalWithBootstrapButtons.fire(
                                        'Terkirim!',
                                        'So berhasil di kirim.',
                                        'success'
                                    )
                                })
                        } else {
                            axios.delete("/api/so/" + this.$route.params.id)
                                .then(res => {
                                    this.uploadso = {
                                        nomor_so: vso.nomor_so,
                                        tanggal_kirim: vso.tanggal_kirim,
                                        keterangan: vso.keterangan,
                                        distribusi: vso.distribusi,
                                        alamat: vso.alamat,
                                        lokasi: vso.lokasi,
                                        status: 'Sent',
                                        id_ekspedisi: vso.id_ekspedisi,
                                        statusso: vso.statusso,
                                        tanggal_so: vso.tanggal_so,
                                        nomor_rso: vso.nomor_rso,
                                    };
                                    axios.post("/api/so", this.uploadso)
                                        .then(res => {
                                            this.ada = 0;
                                            for (let i = 0; i < this.listnewso.length; i++) {
                                                if (vso.statusso === "tersedia") {
                                                    this.ada = this.hitung.qty[i];
                                                } else {
                                                    this.ada = 0;
                                                }
                                                this.uplist = {
                                                    nomor_so: vso.nomor_so,
                                                    kode_barang: this.listnewso[i].kode_barang,
                                                    harga: this.listnewso[i].harga,
                                                    id_custprice: this.listnewso[i].id_custprice,
                                                    qty: this.hitung.qty[i],
                                                    qtyrso: 0,
                                                    tersedia: this.ada,
                                                    diskon: this.listnewso[i].diskon,
                                                }
                                                axios.post("/api/listso", this.uplist)
                                            }
                                        })
                                    this.history = {};
                                    axios.get("/api/history/" + this.$route.params.id)
                                        .then(res => {
                                            this.history = res.data.data;
                                            for (let j = 0; j < this.history.length; j++) {
                                                axios.put("/api/history/" + this.history[j].id, {
                                                    nomor_dok: vso.nomor_so
                                                })
                                            }
                                        })
                                    axios.post("/api/history", {
                                        nomor_dok: vso.nomor_so,
                                        nomor_ref: vso.nomor_rso,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan SO baru",
                                        keterangan: "Merubah nomor SO " + this.$route.params.id + " dengan " + vso.nomor_so,
                                        jenis: "So",
                                        tanggal: this.DateTime(),
                                    })
                                    axios.post("/api/history", {
                                        nomor_dok: vso.nomor_so,
                                        nomor_ref: vso.nomor_rso,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan SO baru",
                                        keterangan: "Mengirim SO ke Sales Supervisor",
                                        jenis: "So",
                                        tanggal: this.DateTime(),
                                    })
                                    axios.post("/api/history", {
                                        nomor_dok: vso.nomor_rso,
                                        nomor_ref: vso.nomor_so,
                                        id_user: this.ambiluser.id,
                                        notif: "Anda mendapatkan permintaan SO baru",
                                        keterangan: "Merubah nomor SO " + this.$route.params.id + " dengan " + vso.nomor_so,
                                        jenis: "RSO",
                                        tanggal: this.DateTime(),
                                        aktif: "N",
                                    })
                                    swalWithBootstrapButtons.fire(
                                        'Terkirim!',
                                        'So berhasil di kirim.',
                                        'success'
                                    )
                                    this.$router.push({
                                        name: 'so'
                                    })
                                })
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Qty item tidak boleh kosong',
                        })
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim SO :)',
                        'error'
                    )
                }
            })
        },
        kembali() {
            this.$router.push({
                name: 'so'
            })
        },
        reqedit(vso) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin melakukan permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin!',
                cancelButtonText: 'Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.get("/api/listso/" + this.$route.params.id)
                        .then(res => {
                            this.listfull = res.data.data;
                            for (let i = 0; i < this.listfull.length; i++) {
                                if (this.listfull[i].bbk === null || this.listfull[i].bbk === "") {
                                    this.listfull[i].bbk = 0;
                                }
                                this.bbk += this.listfull[i].bbk;
                            }
                            if (this.bbk < 1) {
                                axios.put("/api/so/" + this.$route.params.id, {
                                        alastolak: '',
                                        status: 'Draft'
                                    })
                                    .then(res => {
                                        axios.post("/api/history", {
                                            nomor_dok: this.$route.params.id,
                                            id_user: this.ambiluser.id,
                                            notif: "Rso nomor : " + this.$route.params.id + "di tarik sales",
                                            keterangan: "Sales menarik kembali SO (Edit SO)",
                                            jenis: "So",
                                            tanggal: this.DateTime(),
                                        })
                                        this.$router.push({
                                            name: 'so'
                                        })
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil...',
                                            text: 'Silahkan edit SO anda di list draft SO',
                                        })
                                    });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Tidak dapat melakukan permintaan karena sebagian Item di SO ini sudah terkirim',
                                })
                            }
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan permintaan :)',
                        'error'
                    )
                }
            })
        },
        reqbatal(vso) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Semua item pada SO ini akan di unbooking",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin!',
                cancelButtonText: 'Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.get("/api/listso/" + this.$route.params.id)
                        .then(res => {
                            this.listfull = res.data.data;
                            for (let i = 0; i < this.listfull.length; i++) {
                                if (this.listfull[i].bbk === null || this.listfull[i].bbk === "") {
                                    this.listfull[i].bbk = 0;
                                }
                                this.bbk += this.listfull[i].bbk;
                            }
                            if (this.bbk < 1) {
                                axios.delete("/api/so/" + this.$route.params.id)
                                    .then(res => {
                                        axios.get("/api/history/data/" + this.$route.params.id + "/So")
                                            .then(res => {
                                                this.history = res.data.data;
                                                for (let i = 0; i < this.history.length; i++) {
                                                    axios.delete("/api/history/" + this.history[i].id);
                                                }
                                            });
                                        axios.post("/api/history", {
                                            nomor_dok: vso.nomor_rso,
                                            nomor_ref: this.$route.params.id,
                                            id_user: this.ambiluser.id,
                                            notif: "So di hapus",
                                            keterangan: "Membatalkan SO nomor : " + this.$route.params.id,
                                            jenis: "RSO",
                                            tanggal: this.DateTime(),
                                            aktif: "N",
                                        })
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Sukses...',
                                            text: 'SO berhasil dihapus dari database',
                                        })
                                        this.$router.push({
                                            name: 'so'
                                        })
                                    });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Tidak dapat melakukan permintaan karena sebagian Item di SO ini sudah terkirim',
                                })
                            }
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan permintaan :)',
                        'error'
                    )
                }
            })
        },
        validasiqty() {
            this.invoice = 0;
            for (let i = 0; i < this.listnewso.length; i++) {
                if (this.hitung.qty[i] > this.listnewso[i].qty) {
                    this.hitung.qty[i] = this.listnewso[i].qty;
                }

                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                    this.sub = 0;
                } else {
                    this.sub = this.hitung.qty[i];
                }

                this.invoice += parseInt(this.sub) * (parseInt(this.listnewso[i].harga) - parseInt(this.listnewso[i].diskon));
            }
        },
        hapuslistSo(index) {
            if (this.listnewso.length < 2) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "tidak ada item lain jika anda menghapus item ini dan SO akan otomatis dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6 ml-2',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.listnewso.splice(index, 1);
                        this.ket.coba.splice(index, 1);
                        this.hitung.qty.splice(index, 1);
                        this.validasiqty();
                        axios.delete("/api/so/" + this.$route.params.id)
                            .then(res => {
                                Swal.fire({
                                    icon: 'Terhapus',
                                    title: 'Oops...',
                                    text: 'SO sudah terhapus dari database',
                                })
                                this.$router.push({
                                    name: 'so'
                                })
                            })
                    }
                })
            } else {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "item akan di unbooking setelah anda mengirim SO ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6 ml-2',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.listnewso.splice(index, 1);
                        this.ket.coba.splice(index, 1);
                        this.hitung.qty.splice(index, 1);
                        this.validasiqty();
                    }
                })
            }

        },
        timer() {
            setInterval(() => {
                this.gagal = false;
            }, 5000);
        },
        aksidistribusi(vso) {
            if (vso.distribusi === "kirim") {
                this.lokasi = "default";
                if (this.lokasi === 'default') {}
                $("#modal-lokasi").modal("show");
            } else if (vso.distribusi === 'ambil') {
                vso.lokasi = "PT. Ekatunggal Tunas Mandiri";
                vso.alamat = "Jl. Pahlawan No.29A, RT.003/005 Citeureup, Kab. Bogor, Jawa Barat";
            } else if (vso.distribusi === "default") {
                vso.alamat = "";
                vso.lokasi = "";
            }
        },
        selectLokasi(vso) {
            vso.distribusi = "default";
            vso.distribusi = "kirim";
            if (this.lokasi === 'ekspedisi') {
                this.lok = this.ekslok;
                this.al = this.eksal;
                this.ide = this.eksid;
            } else if (this.lokasi === 'lainnya') {
                this.lok = this.lainlok;
                this.al = this.lainal;
            } else if (this.lokasi === 'default') {
                this.lok = vso.customer;
                this.al = vso.alamat;
            }
            vso.lokasi = this.lok;
            vso.alamat = this.al;
            vso.id_ekspedisi = this.ide;

        },
        toggleVisible() {
            this.visible = !this.visible;
        },
        itemClicked(index) {
            this.selected = index;
            this.selectItem();
        },
        selectItem() {
            this.eks = this.matches[this.selected];
            this.eksal = this.eks.alamat;
            this.ekslok = this.eks.nama;
            this.eksid = this.eks.id;
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
        AccDic(vso) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin Menerima SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put("/api/so/" + vso.nomor_so, {
                        status: "Acc"
                    }).then(res => {
                        axios.get("/api/listso/" + vso.nomor_so).then(res => {
                            this.listsoup = res.data.data;
                            for (let y = 0; y < this.listsoup.length; y++) {
                                axios.put("/api/listso/" + this.listsoup[y].id, {
                                    openso: 'Y',
                                    accdate: this.DateTime(),
                                })
                            }
                        })
                        axios.post("/api/history", {
                            nomor_dok: vso.nomor_so,
                            id_user: this.ambiluser.id,
                            notif: "So nomor : " + vso.nomor_so + " di terima DIC",
                            keterangan: "SO di terima DIC",
                            jenis: "So",
                            tanggal: this.DateTime(),
                        })
                        this.$router.push({
                            name: 'distribusiso'
                        })
                        swalWithBootstrapButtons.fire(
                            'Sukses!',
                            'SO berhasil di terima.',
                            'success'
                        )
                    })

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menerima SO :)',
                        'error'
                    )
                }
            })
        },
        formtolak() {
            $("#modal-tolak").modal("show");
        },
        tolakSo() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin',
                text: "Ingin menolak SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya,Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put("/api/so/" + this.$route.params.id, {
                        status: "Tolak",
                        alastolak: this.up.alastolak,
                    }).then(res => {
                        axios.get("/api/listso/" + this.$route.params.id).then(res => {
                            this.listsoup = res.data.data;
                            for (let y = 0; y < this.listsoup.length; y++) {
                                axios.put("/api/listso/" + this.listsoup[y].id, {
                                    openso: 'N'
                                })
                            }
                        })
                        axios.post("/api/history", {
                            nomor_dok: this.nomorso,
                            id_user: this.ambiluser.id,
                            notif: "So nomor : " + this.nomorso + " di tolak DIC",
                            keterangan: "SO di tolak DIC",
                            jenis: "So",
                            tanggal: this.DateTime(),
                        })
                        swalWithBootstrapButtons.fire(
                            'Sukses!',
                            'So berhasil di tolak.',
                            'success'
                        )
                        $("#modal-tolak").modal("hide");
                        this.$router.push({
                            name: 'distribusiso'
                        })
                    })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan penolakan :)',
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
        }
    }
}
</script>

<style>

</style>
