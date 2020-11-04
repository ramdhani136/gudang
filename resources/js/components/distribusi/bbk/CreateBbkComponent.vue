<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor BBK :</label>
                <input @input="cekbbk()" v-model="up.bbk" type="text" class="form-control col-12" autocomplete="disabled" maxlength="16" :class="{ 'is-valid': aktif, 'is-invalid': !aktif }">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
            <div class="form-group">
                <label>Nomor BCK</label>
                <input v-model="up.nomor_bck" @click="showSo()" type="text" class="form-control" placeholder="Pilih BCK" autocomplete="disabled">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Nomor SO</label>
                <input v-model="ket.nomor_so" type="text" class="form-control" disabled>
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
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12" autocomplete="disabled"></textarea>
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
                        <th style="width:23%;">Nama Barang</th>
                        <th>Satuan</th>
                        <th style="width:12%;">Harga</th>
                        <th style="width:7%;">Diskon</th>
                        <th style="width:10%;">Qty BCK</th>
                        <th>Qty termuat</th>
                        <th>Keterangan</th>
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
                        <td style="text-align:center">{{listbcm.qty}}</td>
                        <td style="text-align:center">
                            <input @input="validqty(index)" v-model="hitung.qty[index]" type="number" class="form-control">
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
        <!-- <button @click="draftBcm()" class="btn-orange btn ml-4" >
                    Simpan Draft
                </button> -->
        <button @click="batal()" class="btn-orange btn ml-2">
            Batal
        </button>
        <button @click="submitBbk()" class="btn-success btn ml-1">
            Simpan
        </button>
    </div>
    <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form List Bukti Checker</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih BCK</label>
                        <select @change="pilihBck(ambil)" v-model="ambil" class="form-control">
                            <option :value="ambil" v-for="(ambil,index) in bckaktif" :key="index">{{ambil.bck}}</option>
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
                                    <th style="text-align:center">jumlah BCK</th>
                                    <th style="text-align:center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(ls,index) in listbck" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td>{{ls.kode_barang}}</td>
                                    <td>{{ls.nama_barang}}</td>
                                    <td style="text-align:center">{{ls.satuan}}</td>
                                    <td style="text-align:center">{{ls.qty}}</td>
                                    <td style="text-align:center">
                                        <input @change="pilihlistchecker()" v-model="checker" type="checkbox" :value="ls" disabled>
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
            load: false,
            up: {
                bbk: this.bbk_nomor(),
                tanggal: this.now(),
            },
            ket: {},
            ambil: {},
            checker: [],
            listbcm: {},
            hitung: {
                qty: [],
                keterangan: [],
                jumlah: []
            },
            bckaktif: {},
            gagal: false,
            qty: 0,
            nilai: null,
            harga: 0,
            subtotal: 0,
            total: 0,
            listbck: {},
            uplistbbk: {},
            listso: {},
            listsonya: {},
            qtybbk: 0,
            qtybck: 0,
            selesai: '',
            aktif: false,
            adabbk: {}
        }
    },
    created() {
        this.getBckAktif();
        this.resetForm();
        this.timer();
    },
    computed: {

    },
    methods: {
        getBckAktif() {
            axios.get("/api/bck/data/aktif")
                .then(res => {
                    this.bckaktif = res.data.data;
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
        bbk_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "BBK-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        showSo() {
            $("#modal-po").modal("show");
        },
        resetForm() {
            this.checker = [];
        },
        pilihBck(ambil) {
            this.hitung.qty = [];
            this.checker = [];
            this.ket.customer = ambil.customer;
            this.ket.nomor_so = ambil.nomor_so;
            this.ket.lokasi = ambil.lokasi;
            this.ket.alamat = ambil.alamat;
            this.ket.distribusi = ambil.distribusi;
            this.up.nomor_bck = ambil.bck;

            axios.get("/api/listbck/data/aktif/" + this.up.nomor_bck)
                .then(res => {
                    this.listbck = res.data.data;
                    for (let i in this.listbck) {
                        this.checker.push(this.listbck[i]);
                    }
                });
        },
        checklist() {
            $("#modal-po").modal("hide");
        },
        submitBbk() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menyimpan BBK ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    if (this.checker.length > 0) {
                        this.a = '';
                        this.aplus = '';
                        this.banding = '';
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] < 1 || this.hitung.qty[i] === '') {
                                this.a = "N";
                            } else {
                                this.a = "Y";
                            }
                            this.aplus += this.a;
                            this.banding += "Y";
                        }
                        if (this.aplus === this.banding) {
                            axios.post("/api/bbk", this.up)
                                .then(res => {
                                    /* input listbbk */
                                    this.qtybck = 0;
                                    this.qtybck = 0;
                                    for (let i = 0; i < this.checker.length; i++) {
                                        this.uplistbbk = {
                                            nomor_bbk: this.up.bbk,
                                            kode_barang: this.checker[i].kode_barang,
                                            qty: this.hitung.qty[i],
                                            harga: this.checker[i].harga,
                                            diskon: this.checker[i].diskon,
                                            keterangan: this.checker[i].keterangan
                                        }
                                        axios.post("/api/listbbk", this.uplistbbk)
                                            .then(res => {
                                                /* edit listsi */
                                                axios.get("/api/listso/data/" + this.checker[i].nomor_so + "/" + this.checker[i].kode_barang)
                                                    .then(res => {
                                                        this.listso = res.data.data;
                                                        this.qtybbk = parseInt(this.listso[0].bbk) + parseInt(this.hitung.qty[i]);
                                                        this.qtybck = (parseInt(this.listso[0].bck) - parseInt(this.checker[i].qty)) + parseInt(this.hitung.qty[i]);
                                                        if (this.qtybck >= this.listso[0].qty) {
                                                            this.a = "Y";
                                                            this.qtybck = this.listso[0].qty;
                                                        } else {
                                                            this.a = "N"
                                                        }
                                                        axios.put("/api/listso/" + this.listso[0].id, {
                                                            bbk: this.qtybbk,
                                                            bck: this.qtybck,
                                                            closeso: this.a
                                                        }).then(res => {
                                                            axios.get("/api/listso/" + this.checker[0].nomor_so)
                                                                .then(res => {
                                                                    this.listsonya = res.data.data;
                                                                    this.selesai = '';
                                                                    this.banding = '';
                                                                    for (let y = 0; y < this.listsonya.length; y++) {
                                                                        this.banding += "Y";
                                                                        this.selesai += this.listsonya[y].closeso;
                                                                    }
                                                                    if (this.selesai === this.banding) {
                                                                        axios.put("/api/so/" + this.checker[0].nomor_so, {
                                                                            status: "Selesai",
                                                                            closebck: 'Y',
                                                                        })
                                                                    } else if (this.selesai !== this.banding) {
                                                                        axios.put("/api/so/" + this.checker[0].nomor_so, {
                                                                            status: "Acc",
                                                                            closebck: 'N',
                                                                        })
                                                                    }

                                                                })
                                                        })
                                                    })
                                                /* end */
                                            })
                                    }

                                }).then(res => {
                                    axios.post("/api/history", {
                                        nomor_dok: this.ket.nomor_so,
                                        id_user: this.ambiluser.id,
                                        notif: "BBK dibuka!",
                                        keterangan: "membuat BBK nomor : " + this.up.bbk,
                                        jenis: "So",
                                        tanggal: this.DateTime(),
                                    }).then(res => {
                                        axios.put("/api/bck/" + this.up.nomor_bck, {
                                                status: "close"
                                            })
                                            .then(res => {
                                                axios.post("/api/history", {
                                                    nomor_dok: this.up.bbk,
                                                    id_user: this.ambiluser.id,
                                                    notif: "BBK dibuka!",
                                                    keterangan: "Membuat BBK nomor : " + this.up.bbk,
                                                    jenis: "Bbk",
                                                    tanggal: this.DateTime(),
                                                }).then(res => {
                                                    axios.post("/api/history", {
                                                        nomor_dok: this.up.nomor_bck,
                                                        id_user: this.ambiluser.id,
                                                        notif: "BBK dibuka!",
                                                        keterangan: "Bck selesai, membuka BBK nomor : " + this.up.bbk,
                                                        jenis: "Bck",
                                                        tanggal: this.DateTime(),
                                                    }).then(res => {
                                                        this.load = false;
                                                        swalWithBootstrapButtons.fire(
                                                            'Sukss!',
                                                            'Berhasil meyimpan BBK.',
                                                            'success'
                                                        )
                                                        this.$router.push({
                                                            name: 'distribusibbk'
                                                        })
                                                    })
                                                })
                                            })
                                    })
                                }).catch(error => {
                                    this.load = false;
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali rincian BBK anda!',
                                    })
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Periksa kembali qty barang, tidak boleh kosong!',
                            })
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput item apapun!',
                        })
                    }

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menyimpan BCK :)',
                        'error'
                    )
                }
            })
        },
        validqty(index) {
            // if (parseInt(this.hitung.qty[index]) > (parseInt(this.checker[index].qty) + (parseInt(this.checker[index].qty) / 100 * 10))) {
            //     this.hitung.qty[index] = this.checker[index].qty;
            // }
            this.total = 0;
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "" || this.hitung.qty[i] < 0) {
                    this.hitung.jumlah[i] = 0;
                } else {
                    this.hitung.jumlah[i] = this.hitung.qty[i];
                }
                this.subtotal = (parseInt(this.checker[i].harga) - parseInt(this.checker[i].diskon)) * parseInt(this.hitung.jumlah[i]);
                this.total += parseInt(this.subtotal);
            }
        },
        batal() {
            this.$router.push({
                name: 'distribusibbk'
            });
        },
        timer() {
            setInterval(() => {
                this.gagal = false;
            }, 5000);
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
        cekbbk() {
            axios.get("/api/bbk/" + this.up.bbk)
                .then(res => {
                    this.adabbk = res.data.data;
                    if (this.adabbk.length < 1 && this.up.bbk.length === 16) {
                        this.aktif = true;
                    } else {
                        this.aktif = false;
                    }
                })
        }
    },
}
</script>

<style>

</style>
