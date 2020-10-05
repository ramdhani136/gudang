<template>
<div class="container">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Permintaan :</label>
                <input @input="cekinputrso()" v-model="upload.nomor_price" type="text" maxlength="15" class="form-control col-12" :class="{ 'is-valid': aktif}" :disabled="status!=='Draft' || ambiluser.sales===0">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="upload.tanggal" type="date" :min="now()" class="form-control col-12" disabled>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Customer</label>
                <input @click="showcustomer()" v-model="namacustomer" type="text" class="form-control" placeholder="Pilih Customer" :disabled="status!=='Draft' || listpr.length>0">
            </div>
            <div class="form-group">
                <label>Sales</label>
                <select class="form-control" v-model="upload.id_user" disabled>
                    <option v-for="(sl,index) in sales" :key="index" :value="sl.id">{{sl.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="upload.keterangan" name="keterangan" class="form-control col-12" :disabled="status!=='Draft' || ambiluser.sales===0"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <button v-if="status==='Draft' && ambiluser.sales===1" @click="showmodal()" class="btn btn-orange mt-4 ml-3" style="height:40px;">+ Tambah Item</button>
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
                        <th>Harga Terakhir</th>
                        <th>Request Harga</th>
                        <th v-if="status==='Draft' && ambiluser.sales===1">Aksi</th>
                        <th v-if="(ambiluser.sales===1 && status !=='Draft') || (ambiluser.susales===1 || ambiluser.kordisales===1)">Aksi</th>
                        <th v-if="(ambiluser.sales===1 && status !=='Draft') || (ambiluser.susales===1 || ambiluser.kordisales===1)">Alasan Tolak</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(lp,index) in listpr" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{lp.kode}}</td>
                        <td>{{lp.nama}}</td>
                        <td style="text-align:center">{{lp.satuan}}</td>
                        <td style="text-align:center">{{lp.harga | currency}}</td>
                        <td style="text-align:center">
                            <input type="number" class="form-control" v-model="hitung.harga[index]" :disabled="status!=='Draft' || ambiluser.sales===0">
                        </td>
                        <td v-if="status==='Draft' && ambiluser.sales===1" style="text-align:center">
                            <button @click="hapus(index)" style="text-align:center" class="btn btn-danger">Hapus</button>
                        </td>
                        <td v-if="(ambiluser.sales===1 && status !=='Draft') || (ambiluser.susales===1 || ambiluser.kordisales===1)" style="text-align:center">
                            <select @change="ubahaksi(index)" v-model="statusminta[index]" name="aksi" class="form-control" :disabled="tutup">
                                <option value="Aktif">Terima</option>
                                <option value="Di Tolak">Tolak</option>
                            </select>
                        </td>
                        <td v-if="(ambiluser.sales===1 && status !=='Draft') || (ambiluser.susales===1 || ambiluser.kordisales===1)">
                            <textarea v-model="alastolak[index]" name="alasan" class="form-control" :disabled="statusminta[index]!=='Di Tolak' || tutup"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <button v-if="status!=='Confirm' && ambiluser.sales===1" class="btn btn-none ml-3" @click="batalkan()">
            Batalkan
        </button>
        <button v-if="(status!=='Draft' && status!=='Confirm') &&  ambiluser.sales===1" class="btn btn-orange ml-1" @click="reqedit()">
            Request Edit
        </button>
        <button v-if="status==='Draft' && ambiluser.sales===1" class="btn btn-primary ml-1" @click="createDraft()">
            Simpan Draft
        </button>
        <button v-if="status==='Draft' && ambiluser.sales===1" @click="submit()" class="btn-success btn ml-1">
            Kirim Permintaan
        </button>
        <button v-if="status==='Kordinator' && ambiluser.kordisales===1" @click="konfirmharga()" class="btn-success btn ml-3">
            Kirim Konfirmasi
        </button>
        <button v-if="buttonkonfirm" @click="spvkonfirmasi()" class="btn-success btn ml-3">
            Konfirmasi
        </button>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="custom ? custom.nama:''"></div>
                        <div class="placeholder" v-if="custom==null" v-text="ket.nama">Pilih Barang</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="up" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama barang ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.kode" v-text="match.nama" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>kode Barang</label>
                        <input v-model="ket.kode" type="text" name="qty" autocomplete="off" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="ket.satuan" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Harga Trakhir</label>
                        <input v-model="ket.harga" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="tambahList()" type="button" class="btn btn-primary">Input Item</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-customer" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible2" v-text="custom2 ? custom2.nama:''"></div>
                        <div class="placeholder" v-if="custom2==null" v-text="ket2.nama">Pilih Customer</div>
                        <div class="popover" v-show="visible2">
                            <input type="text" @keydown.up="up2" @keydown.down="down2" @keydown.enter="selectItem2" v-model="query2" placeholder="Masukan nama customer ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match2,index) in matches2" :key="match2.kode" v-text="match2.nama" :class="{'selected':(selected2==index)}" @click="itemClicked2(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kode Customer</label>
                        <input type="text" class="form-control" disabled v-model="ket2.kode">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="savecustomer()" class="btn btn-primary">Save Change</button>
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
            upload: {
                nomor_price: this.price_nomor(),
                tanggal: this.now(),
                kode_groupso: this.ambiluser.kode_groupso,
                id_user: this.ambiluser.id,
            },
            listpr: [],
            visible: true,
            query: '',
            selected: 0,
            itemHeight: 39,
            custom: null,
            ket: {
                nama: "Pilih Barang"
            },
            /* Customer */
            visible2: true,
            query2: '',
            selected2: 0,
            itemHeight2: 39,
            custom2: null,
            ket2: {
                nama: "Pilih Customer"
            },
            /* End */
            barangs: {},
            sales: {},
            customer: {},
            namacustomer: null,
            kodecustomer: null,
            item: false,
            hs: false,
            hargaSpecial: {},
            cek: '',
            init: '',
            banding: '',
            groupso: {},
            aktif: false,
            adaprice: {},
            hitung: {
                harga: [],
                hargaakhir: []
            },
            uplist: {},
            formprice: {},
            status: '',
            listprice: {},
            hargaakhir: {},
            history: {},
            listcust: {},
            historyview: {},
            statusminta: [],
            alastolak: [],
            tutup: true,
            groupnya: null,
            buttonkonfirm: false,
        }
    },
    created() {
        this.getBarang();
    },
    computed: {
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.barang.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        },
        matches2() {
            if (this.query2 == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query2.toLowerCase()))
        },
        Filteredlist() {
            return this.listpr.filter(elem => {
                return elem.kode.toLowerCase().includes(this.ket.kode.toLowerCase());
            })
        }
    },
    methods: {
        getBarang() {
            axios.get("/api/formprice/" + this.$route.params.nomor)
                .then(res => {
                    this.formprice = res.data.data;
                    this.upload.nomor_price = this.formprice[0].nomor_price.slice(0, 15);
                    this.upload.keterangan = this.formprice[0].keterangan;
                    this.upload.tanggal = this.formprice[0].tanggal;
                    this.upload.id_user = this.formprice[0].id_user;
                    this.ket2.nama = this.formprice[0].customer;
                    this.ket2.kode = this.formprice[0].kode_customer;
                    this.namacustomer = this.formprice[0].customer;
                    this.status = this.formprice[0].status;
                    this.groupnya = this.formprice[0].kode_groupso;
                    axios.get("/api/view/data/" + this.$route.params.nomor)
                        .then(res => {
                            this.listprice = res.data.data;
                            for (let t = 0; t < this.listprice.length; t++) {
                                axios.get("/api/view/price/" + this.ket2.kode + "/" + this.listprice[t].kode_barang)
                                    .then(res => {
                                        this.hargaakhir = res.data.data;

                                        if (this.hargaakhir.length < 1) {
                                            this.hitung.hargaakhir[t] = 0;
                                        } else {
                                            this.hitung.hargaakhir[t] = this.hargaakhir[0].harga;
                                        }
                                        this.listpr.push({
                                            id: this.listprice[t].id,
                                            kode: this.listprice[t].kode_barang,
                                            nama: this.listprice[t].nama_barang,
                                            satuan: this.listprice[t].satuan,
                                            harga: this.hitung.hargaakhir[t],
                                            status: this.listprice[t].status,
                                            alastolak: this.listprice[t].alastolak,
                                        })
                                        this.hitung.harga[t] = this.listprice[t].harga;
                                        this.statusminta[t] = this.listpr[t].status;
                                        this.alastolak[t] = this.listpr[t].alastolak;
                                    });
                            }
                            axios.get("/api/barang/")
                                .then(res => {
                                    this.barang = res.data.data
                                    axios.get("/api/user")
                                        .then(res => {
                                            this.sales = res.data.data;
                                            axios.get("/api/customer")
                                                .then(res => {
                                                    this.customer = res.data.data;
                                                    axios.get("/api/groupso/data/aktif")
                                                        .then(res => {
                                                            this.groupso = res.data.data;
                                                            if (this.ambiluser.kordisales === 1 && this.status === "Kordinator") {
                                                                this.tutup = false;
                                                            } else if (this.ambiluser.susales === 1 && this.status === "Supervisor") {
                                                                this.tutup = false;
                                                            } else if (this.ambiluser.susales === 1 && this.groupnya === "GR" && this.status !== "Confirm") {
                                                                this.tutup = false;
                                                            } else {
                                                                this.tutup = true;
                                                            }

                                                            if (this.ambiluser.susales === 1 && this.groupnya === "GR" && this.status !== "Confirm") {
                                                                this.buttonkonfirm = true;
                                                            } else if (this.ambiluser.susales === 1 && this.status === "Supervisor") {
                                                                this.buttonkonfirm = true;
                                                            } else {
                                                                this.buttonkonfirm = false;
                                                            }
                                                            this.load = false;
                                                        });
                                                })
                                        })
                                })
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
        price_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "CP-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
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
            this.ket.kode = this.custom.kode;

            axios.get("/api/view/price/" + this.kodecustomer + "/" + this.custom.kode)
                .then(res => {
                    this.hargaSpecial = res.data.data;
                    if (this.hargaSpecial.length < 1) {
                        this.hs = false;
                        this.ket.harga = this.custom.harga;
                    } else {
                        this.hs = true;
                        this.visible = true;
                        this.visible = false;
                        if (this.hargaSpecial[0].harga === undefined || this.hargaSpecial[0].harga === '') {
                            this.ket.harga = 0;
                        } else {
                            this.ket.harga = this.hargaSpecial[0].harga;
                        }

                    }
                });
            this.ket.satuan = this.custom.satuan;
            this.ket.nama = this.custom.nama;
            this.ket.harga = this.custom.harga;
            this.hs = true;
            this.visible = false;
            this.ket.id_custprice = "";
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
        showmodal() {
            this.hs = false;
            this.jenisharga = 'N';
            $("#modal-form").modal("show");
        },
        tambahList() {
            if (this.listpr.length == 0) {
                if (this.ket.kode === undefined) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Anda belum memilih tem !',
                    })
                } else {
                    this.barangs = {
                        id_custprice: this.ket.id_custprice,
                        harga: this.ket.harga,
                        kode: this.ket.kode,
                        nama: this.ket.nama,
                        satuan: this.ket.satuan
                    };
                    this.listpr.push(this.barangs);
                }
            } else {
                if (this.Filteredlist.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Item sudah diinput sebelumnya, hapus terlebih dahulu untuk memperbarui item!',
                    })
                } else {
                    if (this.ket.kode === undefined) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum memilih tem !',
                        })
                    } else {
                        if (this.listpr.length <= 5) {
                            this.barangs = {
                                id_custprice: this.ket.id_custprice,
                                harga: this.ket.harga,
                                kode: this.ket.kode,
                                nama: this.ket.nama,
                                satuan: this.ket.satuan
                            };
                            this.listpr.push(this.barangs);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Maksimal input 6 item!',
                            })
                        }
                    }
                }
            }
            this.visible = true,
                this.query = '',
                this.selected = 0,
                this.custom = null,
                this.ket = {
                    nama: "Pilih Barang"
                },
                $("#modal-form").modal("hide");
        },
        submit() {
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
                text: "Ingin mengirim permintaan harga ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.listpr.length > 0) {
                        this.aplus = '';
                        this.aband = '';
                        for (let i = 0; i < this.listpr.length; i++) {
                            if (this.hitung.harga[i] === undefined || this.hitung.harga[i] < 1 || this.hitung.harga[i] === '') {
                                this.a = "N";
                            } else {
                                this.a = "Y";
                            }
                            this.aplus += this.a;
                            this.aband += "Y";
                        }
                        if (this.aplus === this.aband) {
                            if (this.upload.nomor_price === this.$route.params.nomor.slice(0, 15)) {
                                this.upload.nomor_price = this.upload.nomor_price + this.ambiluser.kode_groupso;
                                this.upload.status = "Kordinator";
                                axios.put("/api/formprice/" + this.$route.params.nomor, this.upload)
                                    .then(res => {
                                        axios.get("/api/view/data/" + this.$route.params.nomor)
                                            .then(res => {
                                                this.listcust = res.data.data;
                                                for (let y = 0; y < this.listcust.length; y++) {
                                                    axios.delete("/api/custprice/" + this.listcust[y].id)
                                                }
                                            }).then(res => {
                                                for (let i = 0; i < this.listpr.length; i++) {
                                                    this.uplist = {
                                                        nomor_price: this.upload.nomor_price,
                                                        id_user: this.ambiluser.id,
                                                        tanggal: this.DateTime(),
                                                        kode_customer: this.ket2.kode,
                                                        harga: this.hitung.harga[i],
                                                        kode_barang: this.listpr[i].kode,
                                                        keterangan: this.upload.keterangan,
                                                    }
                                                    axios.post("/api/custprice", this.uplist)
                                                };
                                                axios.post("/api/history", {
                                                    nomor_dok: this.upload.nomor_price,
                                                    id_user: this.ambiluser.id,
                                                    notif: "Anda mendapatkan permintaan  harga baru!",
                                                    keterangan: "Form permintan di kirim ke kordinator Sales",
                                                    jenis: "Cp",
                                                    tanggal: this.DateTime(),
                                                }).then(res => {
                                                    this.load = false;
                                                    swalWithBootstrapButtons.fire(
                                                        'Sukses!',
                                                        'Berhasil mengirim permintaan harga.',
                                                        'success'
                                                    )
                                                    this.$router.push({
                                                        name: 'formprice'
                                                    });
                                                });
                                            })
                                    }).catch(error => {
                                        this.load = false;
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Cek kembali rincian form anda!',
                                        })
                                    })
                            } else {
                                this.upload.nomor_price = this.upload.nomor_price + this.ambiluser.kode_groupso;
                                this.upload.status = "Kordinator";
                                axios.put("/api/formprice/" + this.$route.params.nomor, this.upload)
                                    .then(res => {
                                        axios.get("/api/view/data/" + this.upload.nomor_price)
                                            .then(res => {
                                                this.listcust = res.data.data;
                                                for (let y = 0; y < this.listcust.length; y++) {
                                                    axios.delete("/api/custprice/" + this.listcust[y].id)
                                                }
                                            }).then(res => {
                                                for (let i = 0; i < this.listpr.length; i++) {
                                                    this.uplist = {
                                                        nomor_price: this.upload.nomor_price,
                                                        id_user: this.ambiluser.id,
                                                        tanggal: this.DateTime(),
                                                        kode_customer: this.ket2.kode,
                                                        harga: this.hitung.harga[i],
                                                        kode_barang: this.listpr[i].kode,
                                                        keterangan: this.upload.keterangan
                                                    }
                                                    axios.post("/api/custprice", this.uplist)
                                                };
                                                axios.get("/api/history/" + this.$route.params.nomor)
                                                    .then(res => {
                                                        this.historyview = res.data.data;
                                                        for (let u = 0; u < this.historyview.length; u++) {
                                                            axios.put("/api/history/" + this.historyview[u].id, {
                                                                nomor_dok: this.upload.nomor_price
                                                            })
                                                        }
                                                    }).then(res => {
                                                        axios.post("/api/history", {
                                                            nomor_dok: this.upload.nomor_price,
                                                            id_user: this.ambiluser.id,
                                                            notif: "Anda mendapatkan permintaan  harga baru!",
                                                            keterangan: "Merubah nomor permintaan : " + this.$route.params.nomor,
                                                            jenis: "Cp",
                                                            tanggal: this.DateTime(),
                                                        }).then(res => {
                                                            axios.post("/api/history", {
                                                                nomor_dok: this.upload.nomor_price,
                                                                id_user: this.ambiluser.id,
                                                                notif: "Anda mendapatkan permintaan  harga baru!",
                                                                keterangan: "Form permintan di kirim ke kordinator Sales",
                                                                jenis: "Cp",
                                                                tanggal: this.DateTime(),
                                                            }).then(res => {
                                                                this.load = false;
                                                                swalWithBootstrapButtons.fire(
                                                                    'Sukses!',
                                                                    'Berhasil mengirim permintaan harga.',
                                                                    'success'
                                                                )
                                                                this.$router.push({
                                                                    name: 'formprice'
                                                                });
                                                            });
                                                        })
                                                    })
                                            })
                                    }).catch(error => {
                                        this.load = false;
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Cek kembali rincian form anda!',
                                        })
                                    })
                            }
                        } else {
                            this.load = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Periksa kembali harga item, tidak boleh kosong!',
                            })
                        }
                    } else {
                        this.load = false;
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
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal mengirim form permintaan harga :)',
                        'error'
                    )
                }
            })
        },
        createDraft() {
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
                text: "Ingin menyimpan form permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.listpr.length > 0) {
                        this.aplus = '';
                        this.aband = '';
                        for (let i = 0; i < this.listpr.length; i++) {
                            if (this.hitung.harga[i] === undefined || this.hitung.harga[i] < 1 || this.hitung.harga[i] === '') {
                                this.a = "N";
                            } else {
                                this.a = "Y";
                            }
                            this.aplus += this.a;
                            this.aband += "Y";
                        }
                        if (this.aplus === this.aband) {
                            if (this.upload.nomor_price === this.$route.params.nomor.slice(0, 15)) {
                                this.upload.nomor_price = this.upload.nomor_price + this.ambiluser.kode_groupso;
                                this.upload.status = "Draft";
                                axios.put("/api/formprice/" + this.$route.params.nomor, this.upload)
                                    .then(res => {
                                        axios.get("/api/view/data/" + this.$route.params.nomor)
                                            .then(res => {
                                                this.listcust = res.data.data;
                                                for (let y = 0; y < this.listcust.length; y++) {
                                                    axios.delete("/api/custprice/" + this.listcust[y].id)
                                                }
                                            }).then(res => {
                                                for (let i = 0; i < this.listpr.length; i++) {
                                                    this.uplist = {
                                                        nomor_price: this.upload.nomor_price,
                                                        id_user: this.ambiluser.id,
                                                        tanggal: this.DateTime(),
                                                        kode_customer: this.ket2.kode,
                                                        harga: this.hitung.harga[i],
                                                        kode_barang: this.listpr[i].kode,
                                                        keterangan: this.upload.keterangan
                                                    }
                                                    axios.post("/api/custprice", this.uplist)
                                                };
                                                this.load = false;
                                                swalWithBootstrapButtons.fire(
                                                    'Sukses!',
                                                    'Berhasil menyimpan permintan.',
                                                    'success'
                                                )
                                                this.$router.push({
                                                    name: 'formprice'
                                                });
                                            })
                                    }).catch(error => {
                                        this.load = false;
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Cek kembali rincian form anda!',
                                        })
                                    })
                            } else {
                                this.upload.nomor_price = this.upload.nomor_price + this.ambiluser.kode_groupso;
                                this.upload.status = "Draft";
                                axios.put("/api/formprice/" + this.$route.params.nomor, this.upload)
                                    .then(res => {
                                        axios.get("/api/view/data/" + this.upload.nomor_price)
                                            .then(res => {
                                                this.listcust = res.data.data;
                                                for (let y = 0; y < this.listcust.length; y++) {
                                                    axios.delete("/api/custprice/" + this.listcust[y].id)
                                                }
                                            }).then(res => {
                                                for (let i = 0; i < this.listpr.length; i++) {
                                                    this.uplist = {
                                                        nomor_price: this.upload.nomor_price,
                                                        id_user: this.ambiluser.id,
                                                        tanggal: this.DateTime(),
                                                        kode_customer: this.ket2.kode,
                                                        harga: this.hitung.harga[i],
                                                        kode_barang: this.listpr[i].kode,
                                                        keterangan: this.upload.keterangan
                                                    }
                                                    axios.post("/api/custprice", this.uplist)
                                                };
                                                axios.get("/api/history/" + this.$route.params.nomor)
                                                    .then(res => {
                                                        this.historyview = res.data.data;
                                                        for (let u = 0; u < this.historyview.length; u++) {
                                                            axios.put("/api/history/" + this.historyview[u].id, {
                                                                nomor_dok: this.upload.nomor_price
                                                            })
                                                        }
                                                    }).then(res => {
                                                        axios.post("/api/history", {
                                                            nomor_dok: this.upload.nomor_price,
                                                            id_user: this.ambiluser.id,
                                                            notif: "Anda mendapatkan permintaan  harga baru!",
                                                            keterangan: "Merubah nomor permintaan : " + this.$route.params.nomor,
                                                            jenis: "Cp",
                                                            tanggal: this.DateTime(),
                                                        }).then(res => {
                                                            this.load = false;
                                                            swalWithBootstrapButtons.fire(
                                                                'Sukses!',
                                                                'Berhasil menyimpan permintaan.',
                                                                'success'
                                                            )
                                                            this.$router.push({
                                                                name: 'formprice'
                                                            });
                                                        })
                                                    })
                                            })
                                    }).catch(error => {
                                        this.load = false;
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Cek kembali rincian form anda!',
                                        })
                                    })
                            }
                        } else {
                            this.load = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Periksa kembali harga item, tidak boleh kosong!',
                            })
                        }
                    } else {
                        this.load = false;
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
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menyimpan :)',
                        'error'
                    )
                }
            })
        },
        hapus(index) {
            this.listpr.splice(index, 1);
            this.hitung.harga.splice(index, 1);
            this.hitung.hargaakhir.splice(index, 1);
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
        showcustomer() {
            $("#modal-customer").modal("show");
        },
        /* pilih barang customer */
        toggleVisible2() {
            this.visible2 = !this.visible2;
        },
        itemClicked2(index) {
            this.selected2 = index;
            this.selectItem2();
        },
        selectItem2() {
            this.custom2 = this.matches2[this.selected2];
            this.ket2.kode = this.custom2.kode;
            this.ket2.nama = this.custom2.nama;
            this.item = true;
            this.visible2 = false;
        },
        up2() {
            if (this.selected2 == 0) {
                return;
            }
            this.selected2 -= 1;
            this.scrollToItem2();
        },
        down2() {
            if (this.selected2 >= this.matches2.length - 1) {
                return;
            }
            this.selected2 += 1;
            this.scrollToItem2();
        },
        scrollToItem2() {
            this.$refs.optionList.scrollTop = this.selected2 * this.itemHeight2;
        },
        /* End */
        savecustomer() {
            this.namacustomer = this.ket2.nama;
            this.kodecustomer = this.ket2.kode;
            this.upload.kode_customer = this.ket2.kode;
            $("#modal-customer").modal("hide");
        },
        cekinputrso() {
            axios.get("/api/formprice/" + this.upload.nomor_price + this.upload.kode_groupso)
                .then(res => {
                    this.adaprice = res.data.data;
                    if (this.upload.nomor_price.length === 15 && this.adaprice.length === 0) {
                        this.aktif = true;
                    } else {
                        this.aktif = false;
                    };
                })
        },
        batalkan() {
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
                text: "Ingin menghapus permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete("/api/formprice/" + this.$route.params.nomor)
                        .then(res => {
                            axios.get("/api/history/" + this.$route.params.nomor)
                                .then(res => {
                                    this.history = res.data.data;
                                    for (let k = 0; k < this.history.length; k++) {
                                        axios.delete("/api/history/" + this.history[k].id);
                                    }
                                });
                            this.load = false;
                            this.$router.push({
                                name: 'formprice'
                            });
                            swalWithBootstrapButtons.fire(
                                'Deleted!',
                                'Berhasil menghapus permintaan.',
                                'success'
                            )
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus permintaan :)',
                        'error'
                    )
                }
            })
        },
        reqedit() {
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
                text: "Ingin merubah data form ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put("/api/formprice/" + this.$route.params.nomor, {
                        status: 'Draft'
                    }).then(res => {
                        axios.post("/api/history", {
                            nomor_dok: this.$route.params.nomor,
                            id_user: this.ambiluser.id,
                            notif: "Permintaan ditarik!",
                            keterangan: "Permintaan di tarik kembali (Request edit)",
                            jenis: "Cp",
                            tanggal: this.DateTime(),
                        }).then(res => {
                            this.load = false;
                            swalWithBootstrapButtons.fire(
                                'Sukses!',
                                'Silahkan edit permintaan anda di list Draft.',
                                'success'
                            )
                            this.$router.push({
                                name: 'formprice'
                            });
                        })
                    })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan permintaan :)',
                        'error'
                    )
                }
            })
        },
        konfirmharga() {
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
                text: "Ingin mengkonfirmasi permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.lplus = "";
                    this.lband = "";
                    for (let i = 0; i < this.listpr.length; i++) {
                        if (this.statusminta[i] === undefined || this.statusminta[i] === '' || this.statusminta[i] === 'Non Aktif') {
                            this.l = "N";
                        } else {
                            this.l = "Y";
                        }
                        this.lplus += this.l;
                        this.lband += "Y";
                    }
                    if (this.lplus === this.lband) {
                        axios.put("/api/formprice/" + this.$route.params.nomor, {
                            status: 'Supervisor'
                        }).then(res => {
                            for (let i = 0; i < this.listpr.length; i++) {
                                axios.put("/api/custprice/" + this.listpr[i].id, {
                                    status: this.statusminta[i],
                                    alastolak: this.alastolak[i]
                                })
                            }
                            axios.post("/api/history", {
                                nomor_dok: this.$route.params.nomor,
                                id_user: this.ambiluser.id,
                                notif: "Permintaan di konfimasi!",
                                keterangan: "Di konfirmasi Kordinator Sales",
                                jenis: "Cp",
                                tanggal: this.DateTime(),
                            }).then(res => {
                                this.load = false;
                                swalWithBootstrapButtons.fire(
                                    'Berhasil!',
                                    'Permintaan telah di konfirmasi.',
                                    'success'
                                )
                                this.$router.push({
                                    name: 'formprice'
                                });
                            })
                        })
                    } else {
                        this.load = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Periksa kembali form anda, pastikan semua item sudah di konfirmasi!',
                        })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan konfirmasi permintaan :)',
                        'error'
                    )
                }
            })
        },
        spvkonfirmasi() {
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
                text: "Ingin mengkonfirmasi permintaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.lplus = "";
                    this.lband = "";
                    for (let i = 0; i < this.listpr.length; i++) {
                        if (this.statusminta[i] === undefined || this.statusminta[i] === '' || this.statusminta[i] === 'Non Aktif') {
                            this.l = "N";
                        } else {
                            this.l = "Y";
                        }
                        this.lplus += this.l;
                        this.lband += "Y";
                    }
                    if (this.lplus === this.lband) {
                        axios.put("/api/formprice/" + this.$route.params.nomor, {
                            status: 'Confirm',
                        }).then(res => {
                            for (let i = 0; i < this.listpr.length; i++) {
                                axios.put("/api/custprice/" + this.listpr[i].id, {
                                    status: this.statusminta[i],
                                    alastolak: this.alastolak[i],
                                    open: "Y",
                                })
                            }
                            axios.post("/api/history", {
                                nomor_dok: this.$route.params.nomor,
                                id_user: this.ambiluser.id,
                                notif: "Permintaan di konfimasi!",
                                keterangan: "Di konfirmasi Supervisor",
                                jenis: "Cp",
                                tanggal: this.DateTime(),
                            }).then(res => {
                                this.load = false;
                                swalWithBootstrapButtons.fire(
                                    'Berhasil!',
                                    'Permintaan telah di konfirmasi.',
                                    'success'
                                )
                                this.$router.push({
                                    name: 'formprice'
                                });
                            })
                        })
                    } else {
                        this.load = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Periksa kembali form anda, pastikan semua item sudah di konfirmasi!',
                        })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan konfirmasi permintaan :)',
                        'error'
                    )
                }
            })
        },
        ubahaksi(index) {
            if (this.statusminta[index] === "Aktif") {
                this.alastolak[index] = '';
            }
        }
    },
}
</script>

<style>
</style>
