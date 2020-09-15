<template>
<div class="container" @keyup.esc="resetForm()">
    <div class="row row-cols-2">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor PO :</label>
                <input v-model="lpo.nomor_po" type="text" class="form-control col-12">
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="lpo.tanggal_po" type="date" @change="validate()" :min="now()" class="form-control col-12">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Supplier</label>
                <select @click="pilihsupplier()" v-model="ket.kode_supplier" name="customer" class="col-12 form-control" :disabled="lpo.status==='Request' || lpo.status==='Acc' || lpo.status==='Selesai'">
                    <option :value="ket.kode_supplier">{{ket.nama}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Purchasing</label>
                <select v-model="lpo.nip_purchasing" class="col-12 form-control">
                    <option :value="0">Dida</option>
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
    <div id="rsoverflowso" class="row mt-2 mx-auto">
        <button @click="showmodal()" class="row float-left  ml-3 mt-4 label">Ambil Permintaan</button>
        <div id="totalpo" class="mt-3 ml-auto mr-3">Total Invoice &nbsp; : &nbsp;{{totalPrice | currency}}</div>
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
                        <th>Sub Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(list,index) in arrFiltered" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{list.kode}}</td>
                        <td>{{list.nama_barang}}</td>
                        <td style="text-align:center">{{hitung.qty[index]}}</td>
                        <td style="text-align:center">{{list.satuan}}</td>
                        <td style="text-align:center">
                            <input @input="hitunginv()" v-model="hitung.harga[index]" type="number" class="form-control">
                        </td>
                        <td style="text-align:center">{{hitung.qty[index]*hitung.harga[index] | currency}}</td>
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
            Kirim PO
        </button>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Customer</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Barang</label>
                        <select @change="getList()" v-model="chooseItem" class="form-control">
                            <option v-for="(prl,index) in pr" :key="index" :value="prl.kode_barang">{{prl.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group" v-if="aktif">
                        <label>Satuan</label>
                        <input v-for="(fill,index) in FilteredSatuan" :key="index" v-model="fill.satuan" type="text" class="form-control" disabled>
                    </div>
                    <div id="overflowBody">
                        <table class="table mt-2 table-striped table-bordered" style="width:100%">
                            <thead id="rsthead">
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Nomor SO</th>
                                    <th style="text-align:center">Customer</th>
                                    <th style="text-align:center">Jumlah</th>
                                    <th style="text-align:center">Tandai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(choosepr,index) in prlist" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td style="text-align:center">{{choosepr.nomor_so}}</td>
                                    <td>{{choosepr.customer}}</td>
                                    <td style="text-align:center">{{choosepr.jumlah}}</td>
                                    <td style="text-align:center">
                                        <input v-model="checked" type="checkbox" :value="choosepr">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="TambahItem()" class="btn btn-primary">Save changes</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Form Purchase</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Suplier</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="supply ? supply.nama:''"></div>
                        <div class="placeholder" v-if="supply==null">Pilih Supplier</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="atas" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama Supplier ..">
                            <div class="option" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.kode" v-text="match.nama" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="resetForm()" type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
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
    components: {
        Circle5
    },
    data() {
        return {
            load: false,
            checked: [],
            pr: {},
            chooseItem: '',
            prlist: {},
            aktif: false,
            po: {},
            file: {},
            listpo: [],
            totalPrice: 0,
            pOpen: {},
            confirm: {
                status: 'Request'
            },
            up: {},
            visible: false,
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
            },
            viewlist: [],
            urut: 0,
            ambilbarang: {},
            hitung: {
                qty: [],
                harga: [],
                sub: []
            },
            jumlah: 0
        }
    },
    created() {
        this.getPr();
        /*  this.getListPo(); */
        this.getSupplier();
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
        /*    getListPo() {
               axios.get("/api/listso/data/listpo/" + this.$route.params.nomor)
                   .then(res => {
                       this.listpo = res.data.data;
                       this.totalPrice = 0;
                       for (let i = 0; i < this.listpo.length; i++) {
                           this.subTotal = parseInt(this.listpo[i].subtotal);
                           this.totalPrice += this.subTotal;
                       }
                       this.load = false;
                   });
           }, */
        getPr() {
            axios.get("/api/listso/data/group")
                .then(res => {
                    this.pr = res.data.data
                });
        },
        getList() {
            axios.get("/api/listso/data/group/" + this.chooseItem)
                .then(res => {
                    this.prlist = res.data.data;
                    this.aktif = true;
                })
        },
        getSupplier() {
            axios.get("/api/supplier")
                .then(res => {
                    this.supplier = res.data.data;
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
            this.aktif = false;
            this.chooseItem = '';
            this.prlist = {};
            /* this.checked = []; */
            this.file = {};
            this.up.alastolak = "";
        },
        TambahItem() {
            var uniq = {}
            this.arrFiltered = this.checked.filter(obj => !uniq[obj.kode] && (uniq[obj.kode] = true));
            for (let i = 0; i < this.arrFiltered.length; i++) {
                var unik = {};
                this.barangFiltered = this.checked.filter(obj => obj.kode.includes(this.arrFiltered[i].kode));
                this.jumlah = 0;
                for (let u = 0; u < this.barangFiltered.length; u++) {
                    this.jumlah += parseInt(this.barangFiltered[u].jumlah);
                }
                this.hitung.qty[i] = this.jumlah;
                this.hitung.harga[i] = "";
                this.hitunginv();
            }

            $("#modal-form").modal("hide");
            this.resetForm();
        },
        deletelist(index) {
            this.arrFiltered.splice(index, 1);
            this.hitung.qty.splice(index, 1);
            this.hitung.harga.splice(index, 1);
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
            this.lpo.kode_customer = this.ket.kode_supplier;
            console.log(this.lpo);
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
            this.ket.kode_supplier = this.supply.kode_supplier;
            this.ket.nama = this.supply.nama;
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
            for (let i = 0; i < this.arrFiltered.length; i++) {
                if (this.hitung.harga[i] === undefined || this.hitung.harga[i] === "") {
                    this.hitung.sub[i] = 0;
                } else {
                    this.hitung.sub[i] = this.hitung.harga[i]
                }

                this.totalPrice += parseInt(this.hitung.qty[i]) * parseInt(this.hitung.sub[i]);
            }
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
</style>
