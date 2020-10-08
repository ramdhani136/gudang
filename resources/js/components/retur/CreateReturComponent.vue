<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Retur :</label>
                <input @input="ceknomor()" v-model="up.nomor_retur" type="text" class="form-control col-12" maxlength="15" :class="{ 'is-valid': bisakirim, 'is-invalid': !bisakirim }">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
            <div class="form-group">
                <label>Tanggal Kirim :</label>
                <input v-model="ket.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
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
                <label>keterangan</label>
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12"></textarea>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Distribusi :</label>
                <input v-model="ket.distribusi" type="text" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Lokasi</label>
                <input v-model="ket.lokasi" type="text" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" v-model="ket.alamat" disabled></textarea>
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
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Diskon</th>
                        <th>Terkirim</th>
                        <th>Jumlah Retur</th>
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
                        <td style="text-align:center">{{listbcm.bbk}}</td>
                        <td style="text-align:center">
                            <input @input="validqty(index)" v-model="hitung.qty[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">
                            <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                        </td>
                        <td style="text-align:center">
                            <button @click="hapus(index)" class="btn btn-danger">Hapus</button>
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
        <button @click="batal()" class="btn-primary btn ml-4">
            Batalkan
        </button>
        <button @click="submitretur()" class="btn-success btn ml-1">
            Kirim Permintaan
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
                                    <th style="text-align:center">Sudah Kirim</th>
                                    <th style="text-align:center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(ls,index) in listso" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td>{{ls.kode_barang}}</td>
                                    <td>{{ls.nama_barang}}</td>
                                    <td style="text-align:center">{{ls.satuan}}</td>
                                    <td style="text-align:center">{{ls.bbk}}</td>
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
    components: {
        Circle5
    },
    data() {
        return {
            load: true,
            up: {
                nomor_retur: this.retur_nomor(),
                tanggal: this.now(),
            },
            ket: {
                sisaso: [],
                sisasopilih: [],
                tersedia: [],
                ltersedia: []
            },
            aktif: {},
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
            soaktif: [],
            listso: {},
            gagal: false,
            uplist: {},
            qty: 0,
            listrso: {},
            nilai: null,
            harga: 0,
            subtotal: 0,
            total: 0,
            bisakirim: false,
            retur: {},
            listaktif: {},
            sopilih: {},
            adagak: 0
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
            axios.get("/api/so/data/aktif")
                .then(res => {
                    this.sopilih = res.data.data;
                    for (let i = 0; i < this.sopilih.length; i++) {
                        axios.get("/api/listso/" + this.sopilih[i].nomor_so)
                            .then(res => {
                                this.listaktif = res.data.data;
                                this.adagak = 0;
                                for (let o = 0; o < this.listaktif.length; o++) {
                                    this.adagak += parseInt(this.listaktif[o].bbk);
                                }
                                if (this.adagak > 0) {
                                    this.soaktif.push(this.sopilih[i]);
                                }
                            })
                    }
                    this.load = false;
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
        retur_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "RT-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        showSo() {
            $("#modal-po").modal("show");
        },
        resetForm() {
            this.checker = [];
        },
        pilihSo(aktif) {
            this.hitung.qty = [];
            this.checker = [];
            this.ket.customer = aktif.customer;
            this.ket.lokasi = aktif.lokasi;
            this.ket.alamat = aktif.alamat;
            this.ket.tanggal_kirim = aktif.tanggal_kirim;
            this.ket.distribusi = aktif.distribusi;
            this.up.nomor_so = aktif.nomor_so;
            this.ket.status = aktif.statusso;
            axios.get("/api/listso/data/lretur/" + this.up.nomor_so)
                .then(res => {
                    this.listso = res.data.data;
                })

        },
        checklist() {
            $("#modal-po").modal("hide");
        },
        pilihlistchecker() {
            for (let i = 0; i < this.checker.length; i++) {
                if (this.aktif.statusso === "tersedia") {
                    this.ket.sisasopilih[i] = parseInt(this.checker[i].qty_tersedia) - parseInt(this.checker[i].keluar_tersedia);
                    this.ket.tersedia[i] = parseInt(this.checker[i].qty_tersedia) - parseInt(this.checker[i].keluar_tersedia);
                } else {
                    this.ket.sisasopilih[i] = parseInt(this.checker[i].qty_tdktersedia) - parseInt(this.checker[i].keluar_tdktersedia);
                    this.ket.tersedia[i] = parseInt(this.checker[i].qty_masuk) - parseInt(this.checker[i].keluar_tdktersedia);
                }

            }
        },
        submitretur() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengirim permintaan retur ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.checker.length > 0) {
                        this.aplus = '';
                        this.aband = '';
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.hitung.qty[i] === undefined || this.hitung.qty[i] < 1 || this.hitung.qty[i] === "") {
                                this.a = "N";
                            } else {
                                this.a = "Y";
                            }
                            this.aplus += this.a;
                            this.aband += "Y";
                        }
                        if (this.aplus === this.aband) {
                            axios.post("/api/retur", this.up)
                                .then(res => {
                                    /* lanjut */
                                }).catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali form retur anda!',
                                    })
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Periksa kembali qty retur, jumlah tidak boleh kosong!',
                            })
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput barang apapun!',
                        })
                    }
                    // swalWithBootstrapButtons.fire(
                    //     'Sukses!',
                    //     'Permintaan retur berhasil di kirim.',
                    //     'success'
                    // )
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan permintaan ini :)',
                        'error'
                    )
                }
            })
        },
        validqty(index) {
            if (parseInt(this.hitung.qty[index]) > parseInt(this.checker[index].bbk) || this.hitung.qty[index] < 0) {
                this.hitung.qty[index] = this.checker[index].bbk;
            }
            this.total = 0;
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
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
                name: 'returcomponent'
            });
        },
        timer() {
            setInterval(() => {
                this.gagal = false;
            }, 5000);
        },
        ceknomor() {
            axios.get("/api/retur/" + this.up.nomor_retur)
                .then(res => {
                    this.retur = res.data.data;
                    if (this.up.nomor_retur.length === 15 && this.retur.length < 1) {
                        this.bisakirim = true;
                    } else {
                        this.bisakirim = false;
                    }
                })
        },
        hapus(index) {
            this.checker.splice(index, 1);
            this.hitung.qty.splice(index, 1);
            this.hitung.keterangan.splice(index, 1);
            this.total = 0;
            for (let i = 0; i < this.checker.length; i++) {
                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                    this.hitung.jumlah[i] = 0;
                } else {
                    this.hitung.jumlah[i] = this.hitung.qty[i];
                }
                this.subtotal = (parseInt(this.checker[i].harga) - parseInt(this.checker[i].diskon)) * parseInt(this.hitung.jumlah[i]);
                this.total += parseInt(this.subtotal);
            }
        }
    },
}
</script>

<style>

</style>
