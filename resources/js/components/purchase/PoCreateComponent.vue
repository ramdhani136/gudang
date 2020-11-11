<template>
<div class="container" @keyup.esc="resetForm()">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor PO :</label>
                <input @input="cekinputrso()" v-model="lpo.nomor_po" type="text" class="form-control col-12" maxlength="15" :class="{ 'is-valid': nyala, 'is-invalid': !nyala }">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="lpo.tanggal_po" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Supplier</label>
                <input @click="pilihsupplier()" v-model="ket.nama" type="text" placeholder="Pilih Supplier" class="form-control">
            </div>
            <div class="form-group">
                <label>Purchasing</label>
                <select v-model="lpo.id_user" class="col-12 form-control" disabled>
                    <option v-for="(purch,index) in purchasing" :key="index" :value="purch.id">{{purch.name}}</option>
                </select>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Tanggal Datang :</label>
                <input v-model="lpo.tanggal_datang" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="lpo.keterangan" name="keterangan" class="form-control col-12" :disabled="lpo.status==='Request' || lpo.status==='Acc' || lpo.status==='Selesai'"></textarea>
            </div>
        </div>
    </div>
    <button style="font-size:1em;" @click="showmodal()" class=" float-left  ml-2 mt-4 label">Ambil Item</button>
    <div class="row">
        <div id="totalpo" class="mt-3 ml-auto mr-3">Total Invoice &nbsp; : &nbsp;{{totalPrice | currency}}</div>
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
                        <th>Jumlah PR</th>
                        <th>Qty PO</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Sub Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(list,index) in listfix" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{list.kode_barang}}</td>
                        <td>{{list.nama_barang}}</td>
                        <td style="text-align:center">{{list.qty}}</td>
                        <td style="text-align:center">
                            <input @input="hitunginv()" type="number" class="form-control" v-model="hitung.qty[index]">
                        </td>
                        <td style="text-align:center">{{list.satuan}}</td>
                        <td style="text-align:center">
                            <input @input="hitunginv()" v-model="hitung.harga[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">{{hitung.qty[index]*hitung.sub[index] | currency}}</td>
                        <td style="text-align:center">
                            <button @click="deletelist(index)" class="btn btn-danger">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <button @click="submitPo()" class="btn-success btn ml-4">
            Create PO
        </button>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode</label>
                        <input v-model="form.kode_barang" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Pilih Barang</label>
                        <select @change="getList(chooseItem)" v-model="chooseItem" class="form-control">
                            <option v-for="(prl,index) in pr" :key="index" :value="prl">{{prl.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Total Permintaan</label>
                        <input v-model="form.jumlah" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input v-model="ket.satuan" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="TambahItem()" class="btn btn-primary">Input Item</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Penolakan PO</h5>
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
                <div v-for="(lpo,index) in po" :key="index" class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="tolakPo(lpo)" class="btn btn-orange">Konfirmasi Tolak</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2" v-for="(lpo,index) in po" :key="index">
        <div v-if="lpo.status=='Tolak'" id="alastolak">
            <div>
                <b>{{lpo.alastolak}}</b>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-po" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">List Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>kode</label>
                        <input v-model="lpo.kode_supplier" type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Suplier</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="supply ? supply.nama:''"></div>
                        <div class="placeholder" v-if="supply==null">Pilih Supplier</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="atas" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama Supplier ..">
                            <div class="heightcust option" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.kode" v-text="match.nama" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label>PIC</label>
                        <input type="text" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Telepon/HP</label>
                        <input type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="resetForm()" type="button" class="btn btn-primary" data-dismiss="modal">Pilih</button>
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
            checked: [],
            pr: {},
            chooseItem: '',
            prlist: {},
            nyala: false,
            po: {},
            file: {},
            listpo: [],
            totalPrice: 0,
            pOpen: {},
            confirm: {
                status: 'Request'
            },
            up: {},
            visible: true,
            query: '',
            selected: 0,
            supply: null,
            itemHeight: 39,
            supplier: [],
            ket: {},
            upload: {},
            lpo: {
                tanggal_po: this.now(),
                nomor_po: this.po_nomor(),
                tanggal_datang: this.tglKirim(),
                id_user: this.ambiluser.id,
            },
            viewlist: [],
            urut: 0,
            ambilbarang: {},
            hitung: {
                qty: [],
                harga: [],
                sub: [],
                subqty: []
            },
            jumlah: 0,
            form: {
                jumlah: ''
            },
            listfix: [],
            ada: '',
            bandiing: '',
            purchasing: {},
            uplistpo: {},
            lengkap: '',
            subl: '',
            subban: '',
            listbagi: {},
            kasihso: 0,
            sisapembagi: 0,
            masihsisa: 0,
            sisapo: 0,
            openpo: 0,
            adapo: {}
        }
    },
    created() {
        this.getPr();
    },
    computed: {
        FilteredSatuan() {
            return this.pr.filter(elem => {
                return elem.kode_barang.toLowerCase().includes(this.chooseItem);
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
        getPr() {
            axios.get("/api/listso/data/group")
                .then(res => {
                    this.pr = res.data.data
                    axios.get("/api/supplier")
                        .then(res => {
                            this.supplier = res.data.data;
                            axios.get("/api/user")
                                .then(res => {
                                    this.purchasing = res.data.data
                                    this.load = false;
                                })
                        });
                });
        },
        getList(chooseItem) {
            this.load = true;
            axios.get("/api/listso/data/group/" + chooseItem.kode_barang)
                .then(res => {
                    this.prlist = res.data.data;
                    this.form.jumlah = 0;
                    for (let i = 0; i < this.prlist.length; i++) {
                        this.form.jumlah += parseInt(this.prlist[i].jumlah);
                    }
                    this.load = false;
                    this.form.kode_barang = this.prlist[0].kode;
                    this.form.nama_barang = this.prlist[0].nama_barang;
                    this.ket.satuan = this.prlist[0].satuan;
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
            if (this.po.tanggal_po < this.now()) {
                this.lpo.tanggal_po = this.now();
            }
        },
        tglKirim() {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate() + 2;

            var output = d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') + day;
            return output
        },
        po_nomor() {
            var d = new Date();
            var month = d.getMonth() + 1;

            var output = "PO-" + d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-";
            return output
        },
        showmodal() {
            this.errors = [];
            $("#modal-form").modal("show");
        },
        resetForm() {
            this.chooseItem = '';
            this.prlist = {};
            /* this.checked = []; */
            this.file = {};
            this.up.alastolak = "";
            this.form = {};
            this.ket.satuan = '';
        },
        TambahItem() {
            if (this.listfix.length === 0) {
                this.listfix.push({
                    kode_barang: this.form.kode_barang,
                    nama_barang: this.form.nama_barang,
                    qty: this.form.jumlah,
                    satuan: this.ket.satuan,
                })
            } else {
                this.ada = "";
                this.banding = "";
                for (let i = 0; i < this.listfix.length; i++) {
                    if (this.form.kode_barang === this.listfix[i].kode_barang) {
                        this.result = "Y";
                    } else {
                        this.result = "N";
                    }
                    this.ada += this.result;
                    this.banding += "N";
                }
                if (this.ada === this.banding) {
                    this.listfix.push({
                        kode_barang: this.form.kode_barang,
                        nama_barang: this.form.nama_barang,
                        qty: this.form.jumlah,
                        satuan: this.ket.satuan,
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menambahkan item...',
                        text: 'Item ini sudah di input!',
                    })
                }
            }

            $("#modal-form").modal("hide");
            this.resetForm();
        },
        deletelist(index) {
            /*    this.arrFiltered.splice(index, 1); */
            this.hitung.qty.splice(index, 1);
            this.hitung.harga.splice(index, 1);
            this.listfix.splice(index, 1);
            this.resetForm();
            this.hitunginv();

        },
        showDetail(list) {
            this.tujuan = list.kode_barang;
            this.npo = this.$route.params.nomor;
            axios.get("/api/listrso/data/groupopen/" + this.npo + "/" + this.tujuan)
                .then(res => {
                    this.pOpen = res.data.data;
                });
            $("#modal-pr").modal("show");
        },
        destroy(prl) {
            let keputusan = confirm("Yakin igin hapus ini?");
            if (keputusan === true) {
                this.file.nomor_po = "";
                this.file.open_po = "N";
                this.file.harga_supplier = 0;
                axios.put("/api/listrso/" + prl.id, this.file)
                    .then(res => {
                        this.getPr();
                        this.getListPo();
                        this.resetForm();
                        $("#modal-form").modal("hide");
                    });
            }
        },
        submitPo() {

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin membuat PO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.listfix.length < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Anda belum menginput data barang!',
                        })
                    } else {
                        this.hitunginv();
                        this.lengkap = "";
                        this.subl = '';
                        this.subban = ''
                        for (let i = 0; i < this.listfix.length; i++) {
                            if (this.hitung.sub[i] > 0 && this.hitung.subqty[i] > 0) {
                                this.lengkap = "Y";
                            } else {
                                this.lengkap = "N";
                            }
                            this.subl += this.lengkap;
                            this.subban += "Y";
                        }
                        if (this.subl === this.subban) {
                            /* input po */
                            axios.post("/api/po", this.lpo)
                                .then(res => {
                                    /* input listpo */
                                    for (let i = 0; i < this.listfix.length; i++) {
                                        this.uplistpo = {
                                            nomor_po: this.lpo.nomor_po,
                                            kode_barang: this.listfix[i].kode_barang,
                                            harga: this.hitung.harga[i],
                                            qty: this.hitung.qty[i],
                                            sisapo: this.hitung.qty[i]
                                        }
                                        axios.post("/api/listpo/", this.uplistpo)
                                    }
                                    this.kasihso = 0;
                                    this.sisapembagi = 0;
                                    this.masihsisa = 0;
                                    this.sisapo = 0;
                                    this.openpo = 0;
                                    for (let i = 0; i < this.listfix.length; i++) {
                                        axios.get("/api/listso/data/antrianpo/" + this.listfix[i].kode_barang)
                                            .then(res => {
                                                this.listbagi = res.data.data;
                                                this.kasihso = 0;
                                                this.sisapembagi = 0;
                                                this.masihsisa = 0;
                                                this.sisapo = 0;
                                                this.tutupso = '';
                                                this.bandingtutup = '';
                                                this.openpo = 0;
                                                for (let k = 0; k < this.listbagi.length; k++) {
                                                    /* ini sisa sonya */
                                                    this.sisapembagi = parseFloat(this.listbagi[k].qty) - parseFloat(this.listbagi[k].openpo);
                                                    /* end */
                                                    if (this.hitung.qty[i] < this.sisapembagi) {
                                                        this.kasihso = this.hitung.qty[i];
                                                        this.hitung.qty[i] = 0;
                                                    } else {
                                                        this.kasihso = this.sisapembagi;
                                                        this.hitung.qty[i] = parseFloat(this.hitung.qty[i]) - parseFloat(this.sisapembagi);
                                                    }

                                                    this.sisapo = parseFloat(this.listbagi[k].sisapo) - parseFloat(this.kasihso);
                                                    this.openpo = parseFloat(this.listbagi[k].openpo) + parseFloat(this.kasihso);

                                                    if (this.sisapo === 0) {
                                                        this.ubah = "Y";
                                                    } else {
                                                        this.ubah = "N";
                                                    }
                                                    axios.put("/api/listso/" + this.listbagi[k].id, {
                                                        sisapo: this.sisapo,
                                                        openpo: this.openpo,
                                                        statuspo: this.ubah,
                                                    })
                                                }
                                            })
                                    }
                                    /* end input listpo */
                                    axios.post("/api/history", {
                                        nomor_dok: this.lpo.nomor_po,
                                        id_user: this.ambiluser.id,
                                        notif: "Terdapat permintaan PO baru",
                                        keterangan: "Mengirim PO ke Purch Supervisor",
                                        jenis: "Po",
                                        tanggal: this.DateTime(),
                                    })
                                    swalWithBootstrapButtons.fire(
                                        'Sukses!',
                                        'PO berhasil di buat.',
                                        'success'
                                    )
                                    this.$router.push({
                                        name: 'po'
                                    })
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Cek kembali data PO anda!',
                                    })
                                })
                            /* End input PO */
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Qty & Harga barang tidak boleh kosong!',
                            })
                        }
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal membuat PO ini :)',
                        'error'
                    )
                }
            })
        },
        kembali() {
            this.$router.push({
                name: 'po'
            })
        },
        pilihsupplier() {
            $("#modal-po").modal("show");
        },
        toggleVisible() {
            this.visible = !this.visible;
        },
        itemClicked(index) {
            this.selected = index;
            this.selectItem();
        },
        selectItem() {
            this.supply = this.matches[this.selected];
            this.lpo.kode_supplier = this.supply.kode_supplier;
            this.ket.nama = this.supply.nama;
            this.ket.alamat = this.supply.alamat;
            this.ket.pic = this.supply.pic;
            this.ket.telepon = this.supply.telepon;
            this.ket.email = this.supply.email;
            this.visible = false;
        },
        atas() {
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
        hitunginv() {
            this.totalPrice = 0;
            this.sub = [];
            this.subqty = [];
            for (let i = 0; i < this.listfix.length; i++) {
                if (this.hitung.harga[i] === undefined || this.hitung.harga[i] === "") {
                    this.hitung.sub[i] = 0;
                } else if (this.hitung.harga[i] < 1) {
                    this.hitung.sub[i] = 0;
                    this.hitung.harga[i] = 0;
                } else {
                    this.hitung.sub[i] = this.hitung.harga[i]
                }

                if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                    this.hitung.subqty[i] = 0;
                } else if (this.hitung.qty[i] < 1) {
                    this.hitung.subqty[i] = 0;
                    this.hitung.qty[i] = 0;
                } else {
                    this.hitung.subqty[i] = this.hitung.qty[i]
                }

                if (this.hitung.qty[i] > this.listfix[i].qty) {
                    this.hitung.qty[i] = this.listfix[i].qty;
                }

                this.totalPrice += parseFloat(this.hitung.subqty[i]) * parseFloat(this.hitung.sub[i]);
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
        },
        cekinputrso() {
            axios.get("/api/po/" + this.lpo.nomor_po)
                .then(res => {
                    this.adapo = res.data.data;
                    if (this.lpo.nomor_po.length === 15 && this.adapo.length === 0) {
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
#totalpo {
    width: 30%;
    height: 40px;
    padding: 1%;
    border: solid 1px rgb(209, 209, 209);
    text-align: center;
    align-items: center;
    font-size: 1.1em;
    border-radius: 3px;
    color: #666;
    background-color: #fff;
    font-weight: 600;
    letter-spacing: 0.5px;
}

#overflowBody {
    max-height: 220px;
    font-size: 0.9em;
    overflow-y: scroll;
}

.heightcust {
    max-height: 320px;
}
</style>
