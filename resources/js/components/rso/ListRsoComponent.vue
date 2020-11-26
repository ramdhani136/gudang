<template>
  <div class="container">
    <Circle5 id="load3" v-if="load"></Circle5>
    <div v-if="akses">
      <div class="row row-cols-2">
        <div class="col-4">
          <div v-if="status === 'Draft' || status === 'Confirmed'" class="form-group">
            <label>Nomor RSO :</label>
            <input @input="cekinputrso()" v-model="upload.nomor_rso" type="text" maxlength="16" class="form-control col-12" :disabled="status !== 'Draft'" :class="{ 'is-valid': aktif }" />
          </div>
          <div v-if="status !== 'Draft' && status !== 'Confirmed'" class="form-group">
            <label>Nomor RSO :</label>
            <input @input="cekinputrso()" v-model="ket.nomor_rsofull" type="text" maxlength="16" class="form-control col-12" :disabled="status !== 'Draft'" :class="{ 'is-valid': aktif }" />
          </div>
          <div class="form-group">
            <label>Sales Area :</label>
            <select v-model="upload.kode_groupso" class="form-control" disabled>
              <option :value="gr.kode" v-for="(gr, index) in groupso" :key="index">
                {{ gr.area }}
              </option>
            </select>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>Customer</label>
            <input @click="showcustomer()" v-model="namacustomer" type="text" class="form-control" placeholder="Pilih Customer" :disabled="listpr.length > 0" />
          </div>
          <div class="form-group">
            <label>Tanggal :</label>
            <input v-model="upload.tanggal_rso" type="date" :min="now()" class="form-control col-12" :disabled="status !== 'Draft'" />
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>Sales</label>
            <select class="form-control" v-model="upload.id_user" disabled>
              <option v-for="(sl, index) in sales" :key="index" :value="sl.id">
                {{ sl.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>keterangan</label>
            <textarea v-model="upload.keterangan" name="keterangan" class="form-control col-12" :disabled="status !== 'Draft'"></textarea>
          </div>
        </div>
      </div>
      <button v-if="status === 'Draft'" @click="showmodal()" class="btn btn-orange float-left mt-4 ml-1">+ Tambah Item</button>
      <div id="total" class="mt-3 ml-auto mr-4">Total Invoice :&nbsp; {{ invoice | currency }}</div>
      <div id="rsoverflowso" class="row mt-2 mx-auto">
        <div class="row mt-1 mx-auto col-12">
          <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width: 100%">
            <thead>
              <tr>
                <th>No</th>
                <th style="width: 2%">Kode Barang</th>
                <th style="width: 20%">Nama Barang</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th style="width: 20%">Diskon Harga</th>
                <th style="width: 20%">Qty</th>
                <th v-if="status === 'Draft'">Catatan</th>
                <th v-if="status === 'So' || status === 'Confirmed'">Status</th>
                <th v-if="status === 'So' || status === 'Confirmed'">Tersedia</th>
                <th v-if="status === 'So' || status === 'Confirmed'">Tidak Tersedia</th>
                <th v-if="status === 'So' || status === 'Confirmed'">Estimasi Kedatangan</th>
                <th v-if="status === 'Draft'">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(lp, index) in listpr" :key="index">
                <td style="text-align: center">{{ index + 1 }}</td>
                <td>{{ lp.lkode_barang }}</td>
                <td>{{ lp.nama_barang }}</td>
                <td style="text-align: center">{{ lp.satuan }}</td>
                <td style="text-align: center">{{ lp.harga | currency }}</td>
                <td style="text-align: center">
                  <input style="text-align: center" :disabled="status !== 'Draft'" @input="hitunginvoice()" v-model="diskon[index]" type="number" class="form-control" />
                </td>
                <td style="text-align: center">
                  <input style="text-align: center" :disabled="status !== 'Draft'" @input="hitunginvoice()" v-model="hitung.qty[index]" type="number" class="form-control" />
                </td>
                <td v-if="status === 'Draft'" style="text-align: center">
                  <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
                </td>
                <td style="text-align: center" v-if="status === 'So' || status === 'Confirmed'">
                  {{ lp.status }}
                </td>
                <td style="text-align: center" v-if="status === 'So' || status === 'Confirmed'">
                  {{ lp.qty_tersedia }}
                </td>
                <td style="text-align: center" v-if="status === 'So' || status === 'Confirmed'">
                  {{ lp.qty_tdktersedia }}
                </td>
                <td style="text-align: center" v-if="status === 'So' || status === 'Confirmed'">
                  <div v-if="lp.acc_purch === 'Y'">{{ lp.tgl_datang }}</div>
                  <button @click="aksiDetail(lp)" v-if="lp.acc_purch === 'N'" style="text-align: center" class="btn btn-primary">Lihat</button>
                </td>
                <td v-if="status === 'Draft'">
                  <button @click="hapus(index)" style="text-align: center" class="btn btn-danger">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row mt-2">
        <button v-if="status === 'Draft' && ambiluser.sales === 1" class="btn btn-primary ml-3" @click="createDraft()">Simpan Draft</button>
        <button v-if="status === 'Draft' && ambiluser.sales === 1" @click="kirimrso()" class="btn-success btn ml-1" :disabled="bolehkirim">Kirim Permintaan</button>
        <button v-if="status === 'Confirmed' && btnedit === true && ambiluser.sales === 1" @click="reqedit()" class="btn-orange btn ml-3">Request Perbaikan</button>
        <router-link v-if="status === 'Confirmed' && ambiluser.sales === 1" to="/so/new" class="btn btn-success ml-1">+ Create SO</router-link>
        <button v-if="status === 'Sent'" @click="reqedit()" class="btn-orange btn ml-3">Request Edit</button>
        <button v-if="load === false && (status === 'Confirmed' || status === 'So') && ambiluser.sales === 1" @click="showprint()" class="btn-none btn ml-1">Print</button>
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
                <div class="input" @click="toggleVisible" v-text="custom ? custom.nama : ''"></div>
                <div class="placeholder" v-if="custom == null" v-text="ket.nama">Pilih Barang</div>
                <div class="popover" v-show="visible">
                  <input type="text" @keydown.up="up" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama barang .." />
                  <div class="optionbr" ref="optionList">
                    <ul>
                      <li v-for="(match, index) in matches" :key="match.kode" v-text="match.nama" :class="{ selected: selected == index }" @click="itemClicked(index)"></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>kode Barang</label>
                <input v-model="ket.kode" type="text" name="qty" autocomplete="off" class="form-control" disabled />
              </div>
              <div class="form-group">
                <label>Satuan</label>
                <input v-model="ket.satuan" type="text" class="form-control" disabled />
              </div>
              <div class="form-group">
                <label>Harga</label>
                <input v-model="ket.harga" type="text" class="form-control" disabled />
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
                <div class="input" @click="toggleVisible2" v-text="custom2 ? custom2.nama : ''"></div>
                <div class="placeholder" v-if="custom2 == null" v-text="ket2.nama">Pilih Customer</div>
                <div class="popover" v-show="visible2">
                  <input type="text" @keydown.up="up2" @keydown.down="down2" @keydown.enter="selectItem2" v-model="query2" placeholder="Masukan nama customer .." />
                  <div class="optionbr" ref="optionList">
                    <ul>
                      <li v-for="(match2, index) in matches2" :key="match2.kode" v-text="match2.nama" :class="{ selected: selected2 == index }" @click="itemClicked2(index)"></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Kode Customer</label>
                <input type="text" class="form-control" disabled v-model="ket2.kode" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" @click="savecustomer()" class="btn btn-primary">Save Change</button>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="modal fade" id="modal-detail" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                  <label>Nama Barang</label>
                  <input v-model="detail.nama_barang" type="text" class="form-control" disabled />
                </div>
                <div class="form-group">
                  <label>Jumlah</label>
                  <input v-model="detail.qty_tdktersedia" type="number" name="qty" autocomplete="off" class="form-control" disabled />
                </div>
                <div class="form-group">
                  <label>Satuan</label>
                  <input v-model="detail.satuan" type="text" class="form-control" disabled />
                </div>
                <div class="form-group">
                  <label>Alasan Penolakan</label>
                  <textarea v-model="detail.alastolak" name="catatan" class="form-control" disabled></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="modal fade" id="modal-print" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Cetak RSO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Status RSO</label>
                  <select class="form-control" v-model="pilihprint">
                    <option value="Tersedia" :disabled="distersedia">Tersedia</option>
                    <option value="Tidak Tersedia" :disabled="distt">Tidak Tersedia</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button @click="print()" type="button" class="btn btn-secondary" data-dismiss="modal">Print</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="akses === false && load === false" class="row akses">Anda tidak mempunyai akses untuk membuka halaman ini!</div>
  </div>
</template>

<script>
import { Circle5 } from "vue-loading-spinner";
export default {
  props: ["ambiluser"],
  components: {
    Circle5,
  },
  data() {
    return {
      pilihprint: "",
      load: true,
      upload: {},
      listpr: [],
      hitung: {
        qty: [],
        keterangan: [],
        barang: {},
      },
      visible: true,
      query: "",
      selected: 0,
      itemHeight: 39,
      custom: null,
      ket: {
        nama: "Pilih Barang",
      },
      /* Customer */
      visible2: true,
      query2: "",
      selected2: 0,
      itemHeight2: 39,
      custom2: null,
      ket2: {
        nama: "Pilih Customer",
      },
      /* End */
      barangs: {},
      sales: {},
      customer: {},
      namacustomer: null,
      kodecustomer: null,
      item: false,
      hargaSpecial: {},
      invoice: 0,
      subtotal: 0,
      inhitung: {
        qty: [],
        diskon: [],
      },
      cek: "",
      init: "",
      banding: "",
      rso: {},
      listrso: {},
      status: "",
      detail: {},
      history: {},
      dso: {},
      openso: false,
      diskon: [],
      groupso: {},
      aktif: false,
      adarso: {},
      bolehkirim: true,
      distersedia: false,
      distt: false,
      btnedit: true,
      akses: true,
    };
  },
  created() {
    this.getBarang();
    this.getrso();
    this.getakses();
  },
  computed: {
    matches() {
      if (this.query == "") {
        return [];
      }
      return this.barang.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()));
    },
    matches2() {
      if (this.query2 == "") {
        return [];
      }
      return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query2.toLowerCase()));
    },
    Filteredlist() {
      return this.listpr.filter((elem) => {
        return elem.lkode_barang.toLowerCase().includes(this.ket.kode.toLowerCase());
      });
    },
  },
  methods: {
    getBarang() {
      axios.get("/api/barang/").then((res) => {
        this.barang = res.data.data;
        axios.get("/api/user").then((res) => {
          this.sales = res.data.data;
          axios.get("/api/customer").then((res) => {
            this.customer = res.data.data;
            axios.get("/api/groupso/data/aktif").then((res) => {
              this.groupso = res.data.data;
              this.load = false;
              axios.get("/api/rso/tersedia/" + this.$route.params.id).then((res) => {
                if (res.data.data.length < 1) {
                  this.distersedia = true;
                } else {
                  this.distersedia = false;
                }
                axios.get("/api/rso/tdktersedia/" + this.$route.params.id).then((res) => {
                  if (res.data.data.length < 1) {
                    this.distt = true;
                  } else {
                    this.distt = false;
                  }
                });
              });
            });
          });
        });
      });
    },
    getrso() {
      axios.get("/api/rso/" + this.$route.params.id).then((res) => {
        this.rso = res.data.data;
        this.upload.nomor_rso = this.rso[0].nomor_rso.slice(0, 16);
        this.upload.tanggal_rso = this.rso[0].tanggal_rso;
        this.upload.id_user = this.rso[0].id_user;
        this.upload.kode_groupso = this.rso[0].kode_groupso;
        this.namacustomer = this.rso[0].customer;
        this.kodecustomer = this.rso[0].kode_customer;
        this.upload.keterangan = this.rso[0].keterangan;
        this.ket.nomor_rsofull = this.rso[0].nomor_rso;
        this.status = this.rso[0].status;
        this.getakses();
        axios.get("/api/listrso/" + this.$route.params.id).then((res) => {
          this.listpr = res.data.data;
          for (let i = 0; i < this.listpr.length; i++) {
            this.hitung.qty[i] = this.listpr[i].qty;
            this.diskon[i] = this.listpr[i].diskon;
            this.hitung.keterangan[i] = this.listpr[i].catatan;
            if (this.status === "Draft") {
              axios.get("/api/view/price/" + this.listpr[i].kode_customer + "/" + this.listpr[i].lkode_barang).then((res) => {
                this.hargaSpecial = res.data.data;
                if (this.hargaSpecial.length < 1) {
                  this.listpr[i].harga = 0;
                } else {
                  this.listpr[i].harga = this.hargaSpecial[0].harga;
                }
              });
            }
          }
          this.hitunginvoice();
        });
      });
    },
    getakses() {
      this.akses = true;
      if (this.ambiluser.sales === 1) {
        if (this.upload.kode_groupso === this.ambiluser.kode_groupso) {
          this.akses = true;
        } else {
          this.akses = false;
        }
      }
    },
    now() {
      var d = new Date();
      var month = d.getMonth() + 1;
      var day = d.getDate();

      var output = d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-" + (day < 10 ? "0" : "") + day;
      return output;
    },
    rso_nomor() {
      var d = new Date();
      var month = d.getMonth() + 1;

      var output = "RSO-" + d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-";
      return output;
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
      axios.get("/api/view/price/" + this.kodecustomer + "/" + this.custom.kode).then((res) => {
        this.hargaSpecial = res.data.data;
        if (this.hargaSpecial.length < 1) {
          this.ket.harga = 0;
        } else {
          this.ket.harga = this.hargaSpecial[0].harga;
          this.ket.id_custprice = this.hargaSpecial[0].id;
        }
        this.ket.satuan = this.custom.satuan;
        this.ket.nama = this.custom.nama;
        this.visible = false;
      });
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
      this.jenisharga = "N";
      $("#modal-form").modal("show");
    },
    tambahList() {
      if (this.listpr.length == 0) {
        if (this.ket.kode === undefined) {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Anda belum memilih tem !",
          });
        } else {
          this.barangs = {
            id_custprice: this.ket.id_custprice,
            harga: this.ket.harga,
            lkode_barang: this.ket.kode,
            nama_barang: this.ket.nama,
            satuan: this.ket.satuan,
          };
          this.listpr.push(this.barangs);
        }
      } else {
        if (this.Filteredlist.length > 1) {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Hanya dapat memilih 2 item yang sama, hapus terlebih dahulu satu item sebelumnya!",
          });
        } else {
          if (this.ket.kode === undefined) {
            Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Anda belum memilih tem !",
            });
          } else {
            if (this.listpr.length <= 5) {
              this.barangs = {
                id_custprice: this.ket.id_custprice,
                harga: this.ket.harga,
                lkode_barang: this.ket.kode,
                nama_barang: this.ket.nama,
                satuan: this.ket.satuan,
              };
              this.listpr.push(this.barangs);
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Maksimal input 6 item!",
              });
            }
          }
        }
      }
      (this.visible = true),
        (this.query = ""),
        (this.selected = 0),
        (this.custom = null),
        (this.ket = {
          nama: "Pilih Barang",
        }),
        this.cekkirim();
      $("#modal-form").modal("hide");
    },
    kirimrso() {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success ml-2",
          cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
      });

      swalWithBootstrapButtons
        .fire({
          title: "Apakah anda yakin?",
          text: "Kirim RSO ini?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, yakin",
          cancelButtonText: "Batalkan",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            this.getakses();
            if (this.akses === true) {
              if (this.listpr.length > 0) {
                this.init = "";
                this.cek = "";
                this.banding = "";
                for (let j = 0; j < this.listpr.length; j++) {
                  if (this.hitung.qty[j] === undefined || this.hitung.qty[j] === "") {
                    this.init = "N";
                  } else {
                    this.init = "Y";
                  }
                  this.cek += this.init;
                  this.banding += "Y";
                }
                if (this.cek === this.banding) {
                  this.upload.status = "Sent";
                  this.upload.tgl_sales = this.DateTime();
                  this.upload.nomor_rso = this.upload.nomor_rso + this.upload.kode_groupso;
                  axios
                    .put("/api/rso/" + this.$route.params.id, this.upload)
                    .then((res) => {
                      axios.get("/api/listrso/" + this.upload.nomor_rso).then((res) => {
                        this.listrso = res.data.data;
                        for (let o = 0; o < this.listrso.length; o++) {
                          axios.delete("/api/listrso/" + this.listrso[o].id);
                        }
                        for (let i = 0; i < this.listpr.length; i++) {
                          if (this.hitung.keterangan[i] === undefined) {
                            this.hitung.keterangan[i] = "";
                          }
                          this.uplist = {
                            nomor_rso: this.upload.nomor_rso,
                            tanggal_rso: this.upload.tanggal_rso,
                            kode_barang: this.listpr[i].lkode_barang,
                            harga: this.listpr[i].harga,
                            id_custprice: this.listpr[i].id_custprice,
                            qty: this.hitung.qty[i],
                            catatan: this.hitung.keterangan[i],
                            diskon: this.diskon[i],
                            idx: i,
                          };
                          axios.post("/api/listrso", this.uplist);
                        }
                      });
                      if (this.$route.params.id === this.upload.nomor_rso) {
                        axios.post("/api/history", {
                          nomor_dok: this.$route.params.id,
                          nomor_ref: this.$route.params.id,
                          id_user: this.ambiluser.id,
                          notif: "Anda mendapatkan permintaan RSO baru",
                          keterangan: "RSO di kirim ke Inventory Control",
                          jenis: "RSO",
                          tanggal: this.DateTime(),
                        });
                      } else {
                        axios.get("/api/history/" + this.$route.params.id).then((res) => {
                          this.history = res.data.data;
                          for (let i = 0; 1 < this.history.length; i++) {
                            axios.put("/api/history/" + this.history[i].id, {
                              nomor_dok: this.upload.nomor_rso,
                            });
                          }
                        });
                        axios.post("/api/history", {
                          nomor_dok: this.upload.nomor_rso,
                          id_user: this.ambiluser.id,
                          notif: "RSO " + this.$route.params.id + " di ganti dengan RSO " + this.upload.nomor_rs,
                          keterangan: "Mengubah nomor RSO lama : " + this.$route.params.id,
                          jenis: "RSO",
                          tanggal: this.DateTime(),
                          aktif: "N",
                        });
                        axios.post("/api/history", {
                          nomor_dok: this.upload.nomor_rso,
                          nomor_ref: this.$route.params.id,
                          id_user: this.ambiluser.id,
                          notif: "Anda mendapatkan permintaan RSO baru",
                          keterangan: "RSO di kirim ke Inventory Control",
                          jenis: "RSO",
                          tanggal: this.DateTime(),
                        });
                      }
                      this.$router.push({
                        name: "rso",
                      });
                      swalWithBootstrapButtons.fire("Save!", "Berhasil mengirimkan RSO.", "success");
                    })
                    .catch((error) => {
                      Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Cek kembali rincian rso anda!",
                      });
                    });
                } else {
                  Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Qty item tidak boleh kosong",
                  });
                }
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text: "Anda belum menginput item apapun!",
                });
              }
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Anda tidak mempunyai akses!",
              });
            }
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal mengirim RSO :)", "error");
          }
        });
    },
    reqedit() {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success ml-2",
          cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
      });

      swalWithBootstrapButtons
        .fire({
          title: "Apakah anda yakin",
          text: "Ingin melakukan perbaikan untuk RSO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.load = true;
            axios
              .put("/api/rso/" + this.$route.params.id, {
                status: "Sent",
                tgl_sales: "",
                dic: "",
                tgl_dic: "",
                purchasing: "",
                tgl_purchasing: "",
              })
              .then((res) => {
                axios
                  .post("/api/history", {
                    nomor_dok: this.$route.params.id,
                    nomor_ref: this.$route.params.id,
                    id_user: this.ambiluser.id,
                    notif: "RSO di edit",
                    keterangan: "Menarik RSO kembali (Edit)",
                    jenis: "RSO",
                    tanggal: this.DateTime(),
                  })
                  .then((res) => {
                    this.load = false;
                    swalWithBootstrapButtons.fire("Sukses!", "Silahkan edit RSO anda di list draft RSO", "success");
                    this.$router.push({
                      name: "rso",
                    });
                  });
              });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire("Cancelled", "Batal melakukan perbaikan :)", "error");
          }
        });
    },
    createDraft() {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success ml-2",
          cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
      });

      swalWithBootstrapButtons
        .fire({
          title: "Apakah anda yakin?",
          text: "Simpan RSO ini?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, yakin",
          cancelButtonText: "Batalkan",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            this.getakses();
            if (this.akses === true) {
              this.upload.status = "Draft";
              this.upload.kode_customer = this.kodecustomer;
              this.upload.nomor_rso = this.upload.nomor_rso + this.upload.kode_groupso;
              axios
                .put("/api/rso/" + this.$route.params.id, this.upload)
                .then((res) => {
                  axios.get("/api/listrso/" + this.upload.nomor_rso).then((res) => {
                    this.listrso = res.data.data;
                    for (let o = 0; o < this.listrso.length; o++) {
                      axios.delete("/api/listrso/" + this.listrso[o].id);
                    }
                    if (this.listpr.length > 0) {
                      for (let i = 0; i < this.listpr.length; i++) {
                        if (this.hitung.keterangan[i] === undefined) {
                          this.hitung.keterangan[i] = "";
                        }
                        this.uplist = {
                          nomor_rso: this.upload.nomor_rso,
                          tanggal_rso: this.upload.tanggal_rso,
                          kode_barang: this.listpr[i].lkode_barang,
                          harga: this.listpr[i].harga,
                          id_custprice: this.listpr[i].id_custprice,
                          qty: this.hitung.qty[i],
                          catatan: this.hitung.keterangan[i],
                          diskon: this.diskon[i],
                          idx: i,
                        };
                        axios.post("/api/listrso", this.uplist);
                      }
                    }
                  });
                  if (this.$route.params.id !== this.upload.nomor_rso) {
                    axios.get("/api/history/" + this.$route.params.id).then((res) => {
                      this.history = res.data.data;
                      for (let i = 0; 1 < this.history.length; i++) {
                        axios.put("/api/history/" + this.history[i].id, {
                          nomor_dok: this.upload.nomor_rso,
                        });
                      }
                    });
                    axios.post("/api/history", {
                      nomor_dok: this.upload.nomor_rso,
                      id_user: this.ambiluser.id,
                      notif: "RSO " + this.$route.params.id + " di ganti dengan RSO " + this.upload.nomor_rs,
                      keterangan: "Mengubah nomor RSO lama : " + this.$route.params.id,
                      jenis: "RSO",
                      tanggal: this.DateTime(),
                      aktif: "N",
                    });
                  }
                  this.$router.push({
                    name: "rso",
                  });
                  swalWithBootstrapButtons.fire("Save!", "Berhasil menyimpan RSO.", "success");
                })
                .catch((error) => {
                  Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Cek kembali rincian rso anda!",
                  });
                });
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Anda tidak mempunyai akses!",
              });
            }
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal menyimpan RSO :)", "error");
          }
        });
    },
    hapus(index) {
      this.listpr.splice(index, 1);
      this.hitung.qty.splice(index, 1);
      this.diskon.splice(index, 1);
      this.hitung.keterangan.splice(index, 1);
      this.hitunginvoice();
      this.cekkirim();
    },
    aksiDetail(lp) {
      $("#modal-detail").modal("show");
      this.detail = lp;
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
      this.dates = this.year + "-" + (this.month < 10 ? "0" : "") + this.month + "-" + this.day;
      this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? "0" : "") + this.seconds;
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
    pilihjenisharga() {
      /*  console.log(this.ket.harga); */
    },
    hitunginvoice() {
      this.subtotal = 0;
      this.invoice = 0;
      for (let i = 0; i < this.listpr.length; i++) {
        if (this.diskon[i] > this.listpr[i].harga) {
          this.diskon[i] = this.listpr[i].harga;
        }

        if (this.hitung.qty[i] === "") {
          this.inhitung.qty[i] = 0;
        } else if (this.hitung.qty[i] === undefined) {
          this.inhitung.qty[i] = 0;
        } else {
          this.inhitung.qty[i] = this.hitung.qty[i];
        }

        if (this.diskon[i] === "") {
          this.inhitung.diskon[i] = 0;
        } else if (this.diskon[i] === undefined) {
          this.inhitung.diskon[i] = 0;
        } else {
          this.inhitung.diskon[i] = this.diskon[i];
        }

        this.subtotal = (parseFloat(this.listpr[i].harga) - parseFloat(this.inhitung.diskon[i])) * parseFloat(this.inhitung.qty[i]);
        this.invoice += parseFloat(this.subtotal);
        this.cekkirim();
      }
    },
    cekinputrso() {
      axios.get("/api/rso/" + this.upload.nomor_rso + this.upload.kode_groupso).then((res) => {
        this.adarso = res.data.data;
        if (this.upload.nomor_rso.length === 16 && this.adarso.length === 0) {
          this.aktif = true;
        } else {
          this.aktif = false;
        }
      });
    },
    cekkirim() {
      this.aband = "";
      this.aplus = "";
      for (let i = 0; i < this.listpr.length; i++) {
        if (this.listpr[i].harga < 1 || this.hitung.qty[i] < 1 || this.hitung.qty[i] === "" || this.hitung.qty[i] === undefined) {
          this.a = "N";
        } else {
          this.a = "Y";
        }
        this.aplus += this.a;
        this.aband += "Y";
      }
      if (this.aplus === this.aband && this.listpr.length > 0) {
        this.bolehkirim = false;
      } else {
        this.bolehkirim = true;
      }
    },
    print() {
      if (this.pilihprint === "" || this.pilihprint === undefined) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Anda belum memilih status RSO!",
        });
      } else {
        if (this.pilihprint === "Tersedia") {
          var x = window.open("/data/rso/print/" + this.$route.params.id, "_blank");
          x.focus();
        } else {
          var x = window.open("/data/rsott/print/" + this.$route.params.id, "_blank");
          x.focus();
        }
      }
    },
    showprint() {
      $("#modal-print").modal("show");
    },
  },
};
</script>

<style>
#total {
  width: 30%;
  height: 50px;
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

#load2 {
  position: relative;
  margin: 0 auto;
  margin-top: 60px;
}

#rsoverflow {
  width: 100%;
  max-height: 240px;
  overflow-y: scroll;
  border-top: solid 1px #dee2e6;
}

#rsthead thead tr th {
  text-align: center;
  border-bottom: none;
  position: sticky;
  top: 0;
  background-color: #fff;
  top: -1px;
  border-collapse: collapse;
  box-shadow: inset 0 0 0 #dee2e6, inset 0 -1px 0 #dee2e6;
}

.btn-orange {
  background-color: lightsalmon;
  border: solid 1px rgb(247, 141, 99);
  color: white;
}

.btn-orange:hover {
  background-color: rgb(253, 143, 100);
  border: solid 1px rgb(243, 127, 81);
  color: white;
}

.optionbr {
  max-height: 350px;
  overflow-y: scroll;
  margin-top: 5px;
}

.optionbr ul {
  list-style-type: none;
  text-align: left;
  padding-left: 0;
}

.optionbr ul li {
  border-bottom: 1px solid lightgrey;
  padding: 10px;
  cursor: pointer;
  background-color: #f1f1f1;
}

.optionbr ul li.selected {
  background-color: #58bd4c;
  color: #fff;
}

.popovercs {
  max-width: 92%;
  min-height: 50px;
  border: 1px solid lightgray;
  position: absolute;
  z-index: 800;
  top: 70px;
  left: 4%;
  right: 0;
  background-color: #fff;
  border-radius: 3px;
  text-align: center;
}

.popovercs input {
  width: 95%;
  margin-top: 5px;
  height: 40px;
  font-size: 14px;
  border-radius: 3px;
  border: solid 1px lightgray;
  padding-left: 8px;
}

.akses {
  margin-left: 30%;
  color: #666;
}
</style>
