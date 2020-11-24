<template>
  <div class="container">
    <div class="row row-cols-2">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor RSO :</label>
          <input @input="cekinputrso()" v-model="upload.nomor_rso" type="text" maxlength="16" class="form-control col-12" :class="{ 'is-valid': aktif, 'is-invalid': !aktif }" />
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
          <label>Tanggal :</label>
          <input v-model="upload.tanggal_rso" type="date" :min="now()" class="form-control col-12" />
        </div>
        <div class="form-group">
          <label>Customer</label>
          <input @click="showcustomer()" v-model="namacustomer" type="text" class="form-control" placeholder="Pilih Customer" :disabled="listpr.length > 0" />
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
          <textarea v-model="upload.keterangan" name="keterangan" class="form-control col-12"></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <button v-if="item" @click="showmodal()" class="btn btn-orange mt-4 ml-3" style="height: 40px">+ Tambah Item</button>
      <div id="total" class="mt-3 ml-auto mr-4">Total Invoice :&nbsp; {{ invoice | currency }}</div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
      <div class="row mt-1 mx-auto col-12">
        <Circle5 id="load3" v-if="load"></Circle5>
        <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width: 100%">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Barang</th>
              <th>Nama Barang</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Diskon Harga</th>
              <th>Qty</th>
              <th>Sub Total</th>
              <th>Catatan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(lp, index) in listpr" :key="index">
              <td style="text-align: center">{{ index + 1 }}</td>
              <td>{{ lp.kode }}</td>
              <td>{{ lp.nama }}</td>
              <td style="text-align: center">{{ lp.satuan }}</td>
              <td style="text-align: center">{{ lp.harga | currency }}</td>
              <td style="text-align: center">
                <input @input="hitunginvoice()" v-model="diskon[index]" type="number" class="form-control" />
              </td>
              <td style="text-align: center">
                <input @input="hitunginvoice()" v-model="hitung.qty[index]" type="number" class="form-control" />
              </td>
              <td style="text-align: center">
                {{ ((lp.harga - diskon[index]) * hitung.qty[index]) | currency }}
              </td>
              <td style="text-align: center">
                <textarea v-model="hitung.keterangan[index]" class="form-control"></textarea>
              </td>
              <td>
                <button @click="hapus(index)" style="text-align: center" class="btn btn-danger">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row mt-2">
      <button class="btn btn-primary ml-3" @click="createDraft()">Simpan Draft</button>
      <button @click="kirimrso()" class="btn-success btn ml-1" :disabled="bolehkirim">Kirim Permintaan</button>
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
      load: true,
      upload: {
        nomor_rso: this.rso_nomor(),
        tanggal_rso: this.now(),
        kode_groupso: this.ambiluser.kode_groupso,
        id_user: this.ambiluser.id,
      },
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
      hs: false,
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
      diskon: [],
      groupso: {},
      aktif: false,
      adarso: {},
      bolehkirim: true,
    };
  },
  created() {
    this.getBarang();
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
        return elem.kode.toLowerCase().includes(this.ket.kode.toLowerCase());
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
            });
          });
        });
      });
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
            text: "Anda belum memilih item !",
          });
        } else {
          this.barangs = {
            id_custprice: this.ket.id_custprice,
            harga: this.ket.harga,
            kode: this.ket.kode,
            nama: this.ket.nama,
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
                kode: this.ket.kode,
                nama: this.ket.nama,
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
      this.visible = true;
      this.query = "";
      this.selected = 0;
      this.custom = null;
      this.ket = {
        nama: "Pilih Barang",
      };
      this.aplus = "";
      this.aband = "";
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
                this.upload.nomor_rso = this.upload.nomor_rso + this.upload.kode_groupso;
                axios
                  .post("/api/rso", this.upload)
                  .then((res) => {
                    for (let i = 0; i < this.listpr.length; i++) {
                      if (this.hitung.keterangan[i] === undefined) {
                        this.hitung.keterangan[i] = "";
                      }
                      this.uplist = {
                        nomor_rso: this.upload.nomor_rso,
                        tanggal_rso: this.upload.tanggal_rso,
                        kode_barang: this.listpr[i].kode,
                        harga: this.listpr[i].harga,
                        id_custprice: this.listpr[i].id_custprice,
                        qty: this.hitung.qty[i],
                        qty: this.hitung.qty[i],
                        catatan: this.hitung.keterangan[i],
                        diskon: this.inhitung.diskon[i],
                        idx: i,
                      };
                      axios.post("/api/listrso", this.uplist);
                    }
                    axios.post("/api/history", {
                      nomor_dok: this.upload.nomor_rso,
                      id_user: this.ambiluser.id,
                      notif: "Anda mendapatkan permintaan RSO baru!",
                      keterangan: "RSO di kirim ke Inventory Control",
                      jenis: "RSO",
                      tanggal: this.DateTime(),
                    });
                    swalWithBootstrapButtons.fire("Save!", "Berhasil mengirimkan RSO.", "success");
                    this.$router.push({
                      name: "rso",
                    });
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
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal mengirim RSO :)", "error");
          }
        });
    },
    createDraft() {
      this.load = true;
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
            this.upload.status = "Draft";
            this.upload.nomor_rso = this.upload.nomor_rso + this.upload.kode_groupso;
            axios
              .post("/api/rso", this.upload)
              .then((res) => {
                if (this.listpr.length > 0) {
                  for (let i = 0; i < this.listpr.length; i++) {
                    if (this.hitung.keterangan[i] === undefined) {
                      this.hitung.keterangan[i] = "";
                    }
                    this.uplist = {
                      nomor_rso: this.upload.nomor_rso,
                      tanggal_rso: this.upload.tanggal_rso,
                      kode_barang: this.listpr[i].kode,
                      harga: this.listpr[i].harga,
                      id_custprice: this.listpr[i].id_custprice,
                      qty: this.hitung.qty[i],
                      qty: this.hitung.qty[i],
                      catatan: this.hitung.keterangan[i],
                      diskon: this.inhitung.diskon[i],
                      idx: i,
                    };
                    axios.post("/api/listrso", this.uplist);
                  }
                  this.load = false;
                  swalWithBootstrapButtons.fire("Save!", "Berhasil menyimpan draft RSO.", "success");
                  this.$router.push({
                    name: "rso",
                  });
                } else {
                  this.load = false;
                  swalWithBootstrapButtons.fire("Save!", "Berhasil menyimpan draft RSO.", "success");
                  this.$router.push({
                    name: "rso",
                  });
                }
              })
              .catch((error) => {
                this.load = false;
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text: "Cek kembali rincian rso anda!",
                });
              });
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            this.load = false;
            swalWithBootstrapButtons.fire("Cancelled", "Batal menyimpan Draft :)", "error");
          }
        });
    },
    hapus(index) {
      this.listpr.splice(index, 1);
      this.hitung.qty.splice(index, 1);
      this.diskon.splice(index, 1);
      this.hitung.keterangan.splice(index, 1);
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
      console.log(this.ket.harga);
    },
    hitunginvoice() {
      this.subtotal = 0;
      this.invoice = 0;
      this.aplus = "";
      this.aband = "";
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
  },
};
</script>

<style></style>
