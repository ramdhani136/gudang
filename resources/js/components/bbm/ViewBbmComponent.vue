<template>
<div class="container">
    <div class="row row-cols-2" v-for="(up,index) in bbm" :key="index">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor BBM :</label>
                <input v-model="up.bbm" type="text" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="up.tanggal" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled>
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
                <input v-model="ket.nomor_po" @click="showPo()" type="text" class="form-control" placeholder="Pilih Purchase Order" disabled>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Plat.No :</label>
                <input v-model="up.nopol" type="text" class="form-control col-12" disabled>
            </div>
            <div v-if="tutup===true" class="form-group">
                <label>keterangan</label>
                <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
            </div>
            <div v-if="tutup===false" class="form-group">
                <label>keterangan</label>
                <textarea v-model="bbmketerangan" name="keterangan" class="form-control col-12"></textarea>
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
                        <th>Checker</th>
                        <th>Jumlah Real</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(listbbm,index) in checker" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td style="text-align:center">{{listbbm.kode_barang}}</td>
                        <td>{{listbbm.nama_barang}}</td>
                        <td style="text-align:center">{{listbbm.satuan}}</td>
                        <td style="text-align:center">{{hitung.qty[index]}}</td>
                        <td style="text-align:center">
                            <input v-if="tutup===true" v-model="listbbm.qty" type="number" class="form-control" disabled>
                            <input @input="cekinput()" v-if="tutup===false" v-model="upl.qty[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">
                            <textarea v-if="tutup===true" v-model="listbbm.keterangan" class="form-control" disabled></textarea>
                            <textarea v-if="tutup===false" v-model="upl.keterangan[index]" class="form-control"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-if=" request==='Edit' || request==='Batal' " class=" row mt-2">
        <div id="alastolak" style="width:95%;margin-left:2.5%">
            <div>
                <b>
                    {{input.keteranganedit}}
                    ({{request}})
                </b>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <button @click="kembali()" class="btn-primary btn ml-4">
            Kembali
        </button>
        <button v-if="ambiluser.incoming===1&&request==='Accedit'" @click="simpanbbm()" class="btn-orange btn ml-1">
            Simpan BBM
        </button>
        <button v-if="ambiluser.purch===1&&(request==='Edit' || request==='Batal')" @click="terimarequest()" class="btn-success btn ml-1">
            Terima
        </button>
        <button v-if="ambiluser.purch===1&&(request==='Edit' || request==='Batal')" @click="showtolak()" class="btn-danger btn ml-1">
            Tolak
        </button>
        <button v-if="statusbbm==='open' && request==='N' && ambiluser.incoming===1" @click="reqedit()" class="btn-orange btn ml-1">
            Request Edit
        </button>
        <button v-if="statusbbm==='open' && request==='Edit'  && ambiluser.incoming===1" @click="bataledit()" class="btn-none btn ml-1">
            Batal Edit
        </button>
        <button v-if="statusbbm==='open' && request==='Tolakedit' && alastolak!==null  && ambiluser.incoming===1" @click="detailtolak()" class="btn-danger btn ml-1">
            Detail Penolakan
        </button>
        <button v-if="statusbbm==='open' &&  ambiluser.incoming===1" @click="print()" class="btn-success btn ml-1">
            Print
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
                                    <td>{{ls.nama}}</td>
                                    <td style="text-align:center">{{ls.satuan}}</td>
                                    <td style="text-align:center">{{ls.jumlah}}</td>
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
    <div class="modal fade" id="modal-edit" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form pemintaan perbaikan BBM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="input.keteranganedit" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimedit()" class="btn btn-primary">kirim permintaan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Penolakan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="input.alastolak" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimtolak()" class="btn btn-success">kirim Penolakan</button>
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
                    <button type="button" @click="reqedit()" class="btn btn-success">Request Ulang</button>
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
            bbm: {},
            poaktif: {},
            ket: {
                qty: []
            },
            aktif: {},
            listsisa: {},
            checker: [],
            listbbm: {},
            uploadlist: [],
            hitung: {
                qty: []
            },
            uploood: {},
            daftarbbm: {},
            masukan: {},
            listqty: [],
            statusbbm: '',
            request: '',
            alastolak: '',
            input: {
                keteranganedit: "",
                alastolak: ""
            },
            listedit: {},
            qtybbm: 0,
            nomorbcm: '',
            tutup: true,
            upl: {
                qty: [],
                keterangan: []
            },
            bbmketerangan: '',
            uplistpo: {},
            uplistbbm: {},
            done: '',
            banding: '',
            ada: '',
            jmada: '',
            jmband: '',
            alastolak: ''
        }
    },
    created() {
        this.getPoAktif();
        this.getBbm();
        this.getlistbm();
        this.resetForm();
        this.default();
    },
    computed: {},
    methods: {
        getlistbm() {
            axios
                .get("/api/listbbm/" + this.$route.params.nomor)
                .then(res => {
                    this.checker = res.data.data;
                    for (let i = 0; i < this.checker.length; i++) {
                        axios
                            .get("/api/bcm/" + this.checker[i].bcm + "/" + this.checker[i].kode_barang)
                            .then(res => {
                                this.daftarbbm = res.data.data;
                                this.masukan = {
                                    qty: this.daftarbbm[0].qty
                                };
                                this
                                    .listqty
                                    .push(this.masukan);
                                this.hitung.qty[i] = this.daftarbbm[0].bongkar;
                                this
                                    .checker
                                    .push([{
                                        qty: this.hitung.qty[i]
                                    }])
                                this
                                    .checker
                                    .splice(this.checker.length - 1);
                                this.load = false;
                            });
                    }
                });

        },
        getBbm() {
            axios
                .get("/api/bbm/" + this.$route.params.nomor)
                .then(res => {
                    this.bbm = res.data.data;
                    this.statusbbm = this.bbm[0].status;
                    this.request = this.bbm[0].request;
                    this.input.keteranganedit = this.bbm[0].keteranganedit;
                    this.input.keteranganedit = this.bbm[0].keteranganedit;
                    this.bbmketerangan = this.bbm[0].keterangan;
                    this.nomorbcm = this.bbm[0].nomor_bcm;
                    this.alastolak = this.bbm[0].alastolak;
                    if (this.bbm[0].alastolak === null || this.bbm[0].alastolak === "" || this.bbm[0].alastolak === undefined) {
                        this.alastolak = null;
                    } else {
                        this.alastolak = this.this.bbm[0].alastolak;
                    }

                    if (this.ambiluser.purch === 1) {
                        this.tutup = true;
                    } else if (this.ambiluser.incoming === 1 && this.request === 'Accedit') {
                        this.tutup = false;
                    } else {
                        this.tutup = true;
                    }
                });
        },
        reqedit() {
            $("#modal-detailtolak").modal("hide");
            $("#modal-edit").modal("show");
        },
        getPoAktif() {
            axios
                .get("/api/poaktif/")
                .then(res => {
                    this.poaktif = res.data.data;
                });
        },
        now() {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month < 10 ?
                '0' :
                '') + month + "-" + (day < 10 ?
                '0' :
                '') + day;
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

            var output = d.getFullYear() + "-" + (month < 10 ?
                '0' :
                '') + month + "-" + (day < 10 ?
                '0' :
                '') + day;
            return output
        },
        bbm_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "BBM-" + d.getFullYear() + "-" + (month < 10 ?
                '0' :
                '') + month + "-";
            return output
        },
        showPo() {
            $("#modal-po").modal("show");
        },
        resetForm() {
            this.checker = [];
        },
        pilihPo(aktif) {
            this.checker = [];
            this.ket.supplier = aktif.supplier;
            this.ket.nomor_po = aktif.nomor_po;
            axios
                .get("/api/listrso/data/listpo/" + aktif.nomor_po)
                .then(res => {
                    this.listsisa = res.data.data;
                });
        },
        checklist() {
            $("#modal-po").modal("hide");
        },
        DateTime() {
            this.date = new Date();
            this.month = this
                .date
                .getMonth() + 1;
            this.year = this
                .date
                .getFullYear();
            this.hours = this
                .date
                .getHours();
            this.minute = this
                .date
                .getMinutes();
            this.seconds = this
                .date
                .getSeconds();
            if (this.month > 12) {
                this.month = 12;
            }
            this.day = this
                .date
                .getDate();
            this.dates = this.year + "-" + (this.month < 10 ?
                '0' :
                '') + this.month + "-" + this.day;
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ?
                '0' :
                '') + this.seconds;
            this.datetimes = this.dates + " " + this.times;
            return this.datetimes;
        },
        default () {
            axios
                .get("/api/bbm/" + this.$route.params.nomor)
                .then(res => {
                    this.bbm = res.data.data;
                    this.ket.nomor_po = this.bbm[0].nomor_po;
                    this.ket.supplier = this.bbm[0].supplier;
                });
        },
        kembali() {
            this
                .$router
                .push({
                    name: 'ingoods'
                });
        },
        print() {
            var x = window.open('/data/bbm/print/' + this.$route.params.nomor, '_blank');
            x.focus();
        },
        kirimedit() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons
                .fire({
                    title: 'Apakah anda yakin?',
                    text: "Ingin mengirim permintaan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, Yakin!',
                    cancelButtonText: 'Tidak!',
                    reverseButtons: true
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        if (this.input.keteranganedit === "") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Keterangan tidak boleh kosong!'
                            })
                        } else {
                            this.load = true;
                            axios
                                .put("/api/bbm/" + this.$route.params.nomor, {
                                    request: 'Edit',
                                    keteranganedit: this.input.keteranganedit,
                                    alastolak: ''
                                })
                                .then(res => {
                                    axios
                                        .post("/api/history", {
                                            nomor_dok: this.$route.params.nomor,
                                            nomor_ref: this.$route.params.nomor,
                                            id_user: this.ambiluser.id,
                                            notif: "Request perbaikan BBM",
                                            keterangan: "Request perbaikan BBM nomor : " + this.$route.params.nomor,
                                            jenis: "Bbm",
                                            tanggal: this.DateTime()
                                        })
                                        .then(res => {
                                            axios.post("/api/history", {
                                                nomor_dok: this.ket.nomor_po,
                                                nomor_ref: this.$route.params.nomor,
                                                id_user: this.ambiluser.id,
                                                notif: "Request perbaikan BBM",
                                                keterangan: "Request perbaikan BBM nomor : " + this.$route.params.nomor,
                                                jenis: "Po",
                                                tanggal: this.DateTime()
                                            })
                                        })
                                    this.load = false;
                                    $("#modal-edit").modal("hide");
                                    this.getPoAktif();
                                    this.getBbm();
                                    this.getlistbm();
                                    this.resetForm();
                                    this.default();
                                    swalWithBootstrapButtons.fire('Berhasil!', 'Mengirim permintaan.', 'success')
                                })
                        }
                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel) {
                        $("#modal-edit").modal("hide");
                        this.load = false;
                        swalWithBootstrapButtons.fire('Cancelled', 'Batal mengirim permintaan :)', 'error')
                    }
                })
        },
        bataledit() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons
                .fire({
                    title: 'Apakah anda yakin?',
                    text: "Ingin membatalkan pemintaan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, Yakin!',
                    cancelButtonText: 'Tidak!',
                    reverseButtons: true
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        this.load = true;
                        axios
                            .put("/api/bbm/" + this.$route.params.nomor, {
                                request: 'N',
                                keteranganedit: '',
                                alastolak: ''
                            })
                            .then(res => {
                                this.getPoAktif();
                                this.getBbm();
                                this.getlistbm();
                                this.resetForm();
                                this.default();
                                this.load = false;
                                axios
                                    .post("/api/history", {
                                        nomor_dok: this.$route.params.nomor,
                                        nomor_ref: this.$route.params.nomor,
                                        id_user: this.ambiluser.id,
                                        notif: "Request perbaikan BBM",
                                        keterangan: "Membatalkan request perbaikan BBM nomor : " + this.$route.params.nomor,
                                        jenis: "Bbm",
                                        tanggal: this.DateTime()
                                    })
                                    .then(res => {
                                        axios.post("/api/history", {
                                            nomor_dok: this.ket.nomor_po,
                                            nomor_ref: this.$route.params.nomor,
                                            id_user: this.ambiluser.id,
                                            notif: "Request perbaikan BBM",
                                            keterangan: "Membatalkan request perbaikan BBM nomor : " + this.$route.params.nomor,
                                            jenis: "Po",
                                            tanggal: this.DateTime()
                                        })
                                    })
                                swalWithBootstrapButtons.fire('Berhasil!', 'Permintaan berhasil di batalkan.', 'success')
                            })
                    } else if (
                        /* Read more about handling dismissals below */
                        result.dismiss === Swal.DismissReason.cancel) {
                        this.load = false;
                        swalWithBootstrapButtons.fire('Batal', 'Batal melakukan pembatalan :)', 'error')
                    }
                })
        },
        terimarequest() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menerima permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    if (this.request === 'Edit') {
                        axios.put("/api/po/" + this.ket.nomor_po, {
                                status: 'Acc'
                            })
                            .then(Res => {
                                axios.put("/api/bbm/" + this.$route.params.nomor, {
                                        request: 'Accedit',
                                        alastolak: '',
                                        keteranganedit: ''
                                    })
                                    .then(res => {
                                        this.qtybbm = 0;
                                        this.sisapo = 0;
                                        for (let i = 0; i < this.checker.length; i++) {
                                            axios
                                                .get("/api/listpo/data/" + this.ket.nomor_po + "/" + this.checker[i].kode_barang)
                                                .then(res => {
                                                    this.listedit = res.data.data;
                                                    this.qtybbm = (parseFloat(this.listedit[0].qty) - parseFloat(this.listedit[0].sisapo)) - parseFloat(this.checker[i].qty);
                                                    this.sisapo = parseFloat(this.listedit[0].qty) - parseFloat(this.qtybbm);
                                                    if (this.sisapo < 1) {
                                                        this.closepo = 'Y';
                                                    } else {
                                                        this.closepo = 'N';
                                                    }
                                                    axios.put("/api/listpo/" + this.listedit[0].id, {
                                                        bbm: this.qtybbm,
                                                        sisapo: this.sisapo,
                                                        closepo: this.closepo
                                                    })
                                                })
                                        }
                                        axios.post("/api/history", {
                                                nomor_dok: this.$route.params.nomor,
                                                nomor_ref: this.$route.params.nomor,
                                                id_user: this.ambiluser.id,
                                                notif: "Request perbaikan BBM",
                                                keterangan: "Request perbaikan nomor : " + this.$route.params.nomor + " Di terima",
                                                jenis: "Bbm",
                                                tanggal: this.DateTime()
                                            })
                                            .then(res => {
                                                axios.post("/api/history", {
                                                    nomor_dok: this.ket.nomor_po,
                                                    nomor_ref: this.$route.params.nomor,
                                                    id_user: this.ambiluser.id,
                                                    notif: "Request perbaikan BBM",
                                                    keterangan: "Request perbaikan nomor : " + this.$route.params.nomor + " Di terima",
                                                    jenis: "Po",
                                                    tanggal: this.DateTime()
                                                }).then(res => {
                                                    this.load = false;
                                                    swalWithBootstrapButtons.fire(
                                                        'Sukses!',
                                                        'Data berhasil di konfirmasi.',
                                                        'success'
                                                    )
                                                    this.kembali();
                                                })
                                            })
                                    })
                            })
                    } else if (this.request === 'Batal') {
                        axios.put("/api/bbm/" + this.$route.params.nomor, {
                            request: 'Acchapus',
                            alastolak: '',
                            keteranganedit: ''
                        }).then(res => {
                            axios.post("/api/history", {
                                nomor_dok: this.ket.nomor_po,
                                nomor_ref: this.$route.params.nomor,
                                id_user: this.ambiluser.id,
                                notif: "Request pembatalan BBM diterima",
                                keterangan: "Request pembatalan nomor : " + this.$route.params.nomor + " Di terima",
                                jenis: "Po",
                                tanggal: this.DateTime()
                            }).then(res => {
                                axios.post("/api/history", {
                                    nomor_dok: this.$route.params.nomor,
                                    nomor_ref: this.$route.params.nomor,
                                    id_user: this.ambiluser.id,
                                    notif: "Request pembatalan BBM diterima",
                                    keterangan: "Request pembatalan nomor : " + this.$route.params.nomor + " Di terima",
                                    jenis: "Bbm",
                                    tanggal: this.DateTime()
                                }).then(res => {
                                    this.load = false;
                                    swalWithBootstrapButtons.fire(
                                        'Sukses!',
                                        'Data berhasil di konfirmasi.',
                                        'success'
                                    )
                                    this.kembali();
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
                        'Batal menerima permintaan :)',
                        'error'
                    )
                }
            })
        },
        simpanbbm() {
            this.jmada = "";
            this.jmband = "";
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menyimpan BBM ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    for (let i = 0; i < this.checker.length; i++) {
                        if (this.upl.qty[i] === undefined || this.upl.qty[i] < 0 || this.upl.qty[i] === "") {
                            this.ada = "N"
                        } else {
                            this.ada = "Y"
                        }
                        this.jmada += this.ada;
                        this.jmband += "Y";
                    }
                    if (this.jmada === this.jmband) {
                        this.qtybbm = 0;
                        this.sisapo = 0;
                        for (let i = 0; i < this.checker.length; i++) {
                            if (this.upl.qty[i] === undefined || this.upl.qty[i] < 0 || this.upl.qty[i] === "") {
                                this.upl.qty[i] = 0;
                            }
                            axios.get("/api/listbbm/data/" + this.$route.params.nomor + "/" + this.checker[i].kode_barang)
                                .then(res => {
                                    this.uplistbbm = res.data.data;
                                    axios.put("/api/listbbm/" + this.uplistbbm[0].id, {
                                        qty: this.upl.qty[i],
                                        keterangan: this.upl.keterangan[i]
                                    }).then(res => {
                                        axios.get("/api/listpo/data/" + this.ket.nomor_po + "/" + this.checker[i].kode_barang)
                                            .then(res => {
                                                this.uplistpo = res.data.data;
                                                this.qtybbm = parseFloat(this.uplistpo[0].bbm) + parseFloat(this.upl.qty[i]);
                                                this.sisapo = parseFloat(this.uplistpo[0].qty) - parseFloat(this.qtybbm);
                                                if (this.sisapo < 1) {
                                                    this.closepo = "Y";
                                                } else {
                                                    this.closepo = "N";
                                                }

                                                this.done += this.closepo;
                                                this.banding += "Y";
                                                if (this.done === this.banding) {
                                                    axios.put("/api/po/" + this.ket.nomor_po, {
                                                        status: 'Selesai',
                                                        poselesai: 'Y'
                                                    })
                                                } else {
                                                    axios.put("/api/po/" + this.ket.nomor_po, {
                                                        status: 'Acc',
                                                        poselesai: 'N'
                                                    })
                                                }

                                                axios.put("/api/listpo/" + this.uplistpo[0].id, {
                                                    bbm: this.qtybbm,
                                                    sisapo: this.sisapo,
                                                    closepo: this.closepo
                                                })
                                            })
                                    })
                                }).then(res => {
                                    axios.put("/api/bbm/" + this.$route.params.nomor, {
                                        request: 'N',
                                        keterangan: this.bbmketerangan,
                                        keteranganedit: '',
                                        alastolak: '',
                                    })
                                })
                        }
                        axios.post("/api/history", {
                            nomor_dok: this.ket.nomor_po,
                            nomor_ref: this.$route.params.nomor,
                            id_user: this.ambiluser.id,
                            notif: "Request perbaikan BBM",
                            keterangan: "Mengupdate BBM nomor : " + this.$route.params.nomor,
                            jenis: "Po",
                            tanggal: this.DateTime()
                        }).then(res => {
                            axios.post("/api/history", {
                                nomor_dok: this.$route.params.nomor,
                                nomor_ref: this.$route.params.nomor,
                                id_user: this.ambiluser.id,
                                notif: "Request perbaikan BBM",
                                keterangan: "Mengupdate BBM nomor : " + this.$route.params.nomor,
                                jenis: "Bbm",
                                tanggal: this.DateTime()
                            }).then(res => {
                                this.load = false;
                                swalWithBootstrapButtons.fire(
                                    'Sukses!',
                                    'Berhasil mengirim form BBM.',
                                    'success'
                                )
                                this.kembali();
                            })
                        })
                    } else {
                        this.load = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Jumlah barang tidak boleh kosong!',
                        })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim form BBM :)',
                        'error'
                    )
                }
            })
        },
        cekinput() {
            for (let i = 0; i < this.checker.length; i++) {
                if (this.upl.qty[i] > this.hitung.qty[i]) {
                    this.upl.qty[i] = this.hitung.qty[i];
                } else if (this.upl.qty[i] < 0) {
                    this.upl.qty[i] = 0;
                }
            }
        },
        showtolak() {
            $("#modal-tolak").modal("show");
        },
        kirimtolak() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menolak permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.load = true;
                    if (this.input.alastolak === "") {
                        this.load = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Alasan tolak wajib diisi!',
                        })
                        $("#modal-tolak").modal("hide");
                    } else {
                        if (this.request === 'Batal') {
                            axios.put("/api/bbm/" + this.$route.params.nomor, {
                                request: 'Tolakhapus',
                                alastolak: this.input.alastolak,
                                keteranganedit: ''
                            }).then(res => {
                                axios.post("/api/history", {
                                    nomor_dok: this.nomorbcm,
                                    nomor_ref: this.$route.params.nomor,
                                    id_user: this.ambiluser.id,
                                    notif: "Request pembatalan di tolak",
                                    keterangan: "Request pembatalan nomor : " + this.$route.params.nomor + " Di tolak",
                                    jenis: "Bcm",
                                    tanggal: this.DateTime()
                                }).then(res => {
                                    axios.post("/api/history", {
                                        nomor_dok: this.$route.params.nomor,
                                        nomor_ref: this.$route.params.nomor,
                                        id_user: this.ambiluser.id,
                                        notif: "Request pembatalan di tolak",
                                        keterangan: "Request pembatalan nomor : " + this.$route.params.nomor + " Di tolak",
                                        jenis: "Bbm",
                                        tanggal: this.DateTime()
                                    }).then(res => {
                                        this.load = false;
                                        swalWithBootstrapButtons.fire(
                                            'Sukses!',
                                            'Berhasil mengirimkan penolakan.',
                                            'success'
                                        )
                                        $("#modal-tolak").modal("hide");
                                        this.kembali();
                                    })
                                })
                            })
                        } else if (this.request === 'Edit') {
                            axios.put("/api/bbm/" + this.$route.params.nomor, {
                                request: 'Tolakedit',
                                alastolak: this.input.alastolak,
                                keteranganedit: ''
                            }).then(res => {
                                axios.post("/api/history", {
                                    nomor_dok: this.nomorbcm,
                                    nomor_ref: this.$route.params.nomor,
                                    id_user: this.ambiluser.id,
                                    notif: "Request perbaikan di tolak",
                                    keterangan: "Request perbaikan di tolak",
                                    jenis: "Bcm",
                                    tanggal: this.DateTime()
                                }).then(res => {
                                    axios.post("/api/history", {
                                        nomor_dok: this.$route.params.nomor,
                                        nomor_ref: this.$route.params.nomor,
                                        id_user: this.ambiluser.id,
                                        notif: "Request perbaikan di tolak",
                                        keterangan: "Request perbaikan di tolak",
                                        jenis: "Bbm",
                                        tanggal: this.DateTime()
                                    }).then(res => {
                                        this.load = false;
                                        swalWithBootstrapButtons.fire(
                                            'Sukses!',
                                            'Berhasil mengirimkan penolakan.',
                                            'success'
                                        )
                                        $("#modal-tolak").modal("hide");
                                        this.kembali();
                                    })
                                })
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
                        'Batal menolak permintaan :)',
                        'error'
                    )
                }
            })
        },
        detailtolak() {
            $("#modal-detailtolak").modal("show");
        }
    }
}
</script>

<style></style>
