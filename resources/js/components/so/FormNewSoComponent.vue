<template>
  <div class="container">
    <div class="row row-cols-2">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor SO :</label>
          <input @input="ceknomorso()" v-model="up.nomor_so" type="text" maxlength="15" class="form-control col-12" :class="{ 'is-valid': nyala, 'is-invalid': !nyala }" />
        </div>
        <div class="form-group">
          <label>Tanggal :</label>
          <input v-model="up.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12" />
        </div>
        <div class="form-group">
          <label>Tanggal Kirim :</label>
          <input v-model="up.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Customer</label>
          <select v-model="ket.customer" name="customer" class="col-12 form-control" disabled>
            <option :value="ket.customer">{{ ket.customer }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nomor RSO</label>
          <input v-model="up.nomor_rso" @click="showPo()" type="text" class="form-control" placeholder="Pilih RSO" />
        </div>
        <div class="form-group">
          <label>keterangan</label>
          <textarea v-model="up.keterangan" name="keterangan" class="form-control col-12"></textarea>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Distribusi :</label>
          <select class="form-control" v-model="up.distribusi" @change="aksidistribusi()" :disabled="disabled == 1">
            <option value="default">- Masukan pilihan anda -</option>
            <option value="kirim">Di Kirim</option>
            <option value="ambil">Ambil Sendiri</option>
          </select>
        </div>
        <div class="form-group">
          <label>Lokasi</label>
          <input @click="clikdistribusi()" v-model="up.lokasi" name="alamat" class="form-control col-12" disabled />
        </div>
        <div class="form-group">
          <label>Alamat</label>
          <textarea v-model="up.alamat" name="alamat" class="form-control col-12" disabled></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{ invoice | currency }}</div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
      <div class="row mt-1 mx-auto col-12">
        <Circle5 id="load3" v-if="load"></Circle5>
        <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width: 100%">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Barang</th>
              <th>RSO</th>
              <th>Qty</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Diskon</th>
              <th>Status</th>
              <th>Sub Total</th>
              <th v-if="tampil">Estimasi</th>
              <th>Kubikasi (M3)</th>
              <th>Tonase (KG)</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ch, index) in checkrso" :key="index">
              <td style="text-align: center">{{ index + 1 }}</td>
              <td>{{ ch.nama_barang }}</td>
              <td style="text-align: center">{{ ket.coba[index] }}</td>
              <td style="text-align: center">
                <input style="width: 120px" @input="validasiqty" v-model="hitung.qty[index]" type="number" class="form-control" />
              </td>
              <td style="text-align: center">{{ ch.satuan }}</td>
              <td style="text-align: center">{{ ch.harga | currency }}</td>
              <td style="text-align: center">{{ ch.diskon | currency }}</td>
              <td style="text-align: center">{{ lstatus }}</td>
              <td>
                {{ ((parseFloat(ch.harga) - parseFloat(ch.diskon)) * parseFloat(hitung.qty[index])) | currency }}
              </td>
              <td v-if="tampil" style="text-align: center">
                {{ ch.tgl_datang }}
              </td>
              <td style="text-align: center">{{ parseFloat(hitung.qty[index]) * parseFloat(ch.kubikasi) }}</td>
              <td style="text-align: center">{{ parseFloat(hitung.qty[index]) * parseFloat(ch.tonase) }}</td>
              <td style="text-align: center">
                <button @click="hapuslistSo(index)" class="btn btn-danger">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row mt-2">
      <button @click="submitSo()" class="btn-success btn ml-1">Kirim SO</button>
      <div class="tonkg">
        <b>Kubikasi : {{ kubikasi }} M3 |</b>
        <b>Tonase : {{ tonase }} KG</b>
      </div>
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
              <label>Pilih RSO</label>
              <select @change="pilihRso(aktif)" v-model="aktif" class="form-control">
                <option :value="aktif" v-for="(aktif, index) in rso" :key="index">
                  {{ aktif.nomor_rso }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Customer</label>
              <input v-model="ket.customer" type="text" class="form-control" disabled />
            </div>
            <div class="form-group">
              <label>Status</label>
              <select @change="pilihStatus()" v-model="status" class="form-control">
                <option value="Tersedia">Tersedia</option>
                <option value="TidakTersedia">Tidak Tersedia</option>
              </select>
            </div>
            <!-- coba -->
            <div class="form-group">
              <label>Rincian Barang</label>
            </div>
            <table id="rsthead" class="table table-striped table-bordered mt-n2" style="width: 100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(ls, index) in listso" :key="index">
                  <td style="text-align: center">{{ index + 1 }}</td>
                  <td>{{ ls.nama_barang }}</td>
                  <td style="text-align: center">{{ ket.qty[index] }}</td>
                  <td style="text-align: center">{{ ls.satuan }}</td>
                  <td style="text-align: center">
                    <input :value="ls" v-model="checkrso" type="checkbox" />
                  </td>
                </tr>
              </tbody>
            </table>
            <!-- end -->

            <div id="overflowBody"></div>
          </div>
          <div class="modal-footer">
            <button @click="resetForm()" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" @click="inputlist()" class="btn btn-primary">Save changes</button>
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
              <table id="thead" class="table table-striped table-bordered" style="width: 100%">
                <thead>
                  <tr>
                    <th style="text-align: center">No</th>
                    <th style="text-align: center">Nomor SO</th>
                    <th>Customer</th>
                    <th style="text-align: center">Jumlah</th>
                    <th style="text-align: center">Satuan</th>
                    <th style="text-align: center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
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
            <div class="form-group" v-if="lokasi === 'ekspedisi'">
              <label>Lokasi</label>
              <div class="autocomplete"></div>
              <div class="input" @click="toggleVisible" v-text="eks ? eks.nama : ''"></div>
              <div class="placeholder" v-if="eks == null">Pilih Ekspedisi</div>
              <div class="popover" v-show="visible">
                <input type="text" @keydown.up="up" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama ekspedisi.." />
                <div class="optionbr" ref="optionList">
                  <ul>
                    <li v-for="(match, index) in matches" :key="match.id" v-text="match.nama" :class="{ selected: selected == index }" @click="itemClicked(index)"></li>
                  </ul>
                </div>
              </div>
            </div>

            <div v-if="lokasi === 'ekspedisi'" class="form-group mt-3">
              <label>Alamat</label>
              <textarea v-model="eksal" class="form-control" disabled></textarea>
            </div>
            <div v-if="lokasi === 'default'" class="form-group">
              <label>Alamat</label>
              <textarea v-model="defaultal" class="form-control" disabled></textarea>
            </div>
            <div v-if="lokasi === 'lainnya'" class="form-group">
              <label>Tempat/Gedung</label>
              <input type="text" class="form-control" v-model="lainlok" />
            </div>
            <div v-if="lokasi === 'lainnya'" class="form-group">
              <label>Alamat</label>
              <textarea v-model="lainal" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" @click="selectLokasi()" class="btn btn-primary" data-dismiss="modal">Save Change</button>
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
      up: {
        nomor_so: this.so_nomor(),
        tanggal_so: this.now(),
        tanggal_kirim: this.tglKirim(),
      },
      ket: {
        qty: [],
        coba: [],
      },
      aktif: {},
      listsisa: {},
      rso: {},
      status: "Tersedia",
      listso: {},
      upload: {},
      listrso: {},
      gagal: false,
      distribusi: "default",
      lokasi: "default",
      dataekspedisi: {},
      visible: false,
      visiblecust: false,
      query: "",
      selected: 0,
      eks: null,
      itemHeight: 39,
      disabled: 1,
      checkrso: [],
      hitung: {
        qty: [],
      },
      ada: 0,
      sub: 0,
      invoice: 0,
      ok: "",
      okin: "",
      banding: "",
      uprso: {},
      done: "",
      pembanding: "",
      lisallrso: {},
      statpo: "Y",
      statusso: "",
      statusnya: "",
      nyala: false,
      adaso: {},
      tampil: null,
      defaultal: "",
      kubikasi: 0,
      tonase: 0,
    };
  },
  created() {
    this.getRso();
    this.resetForm();
    this.timer();
  },
  computed: {
    matches() {
      if (this.query == "") {
        return [];
      }
      return this.dataekspedisi.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()));
    },
  },
  methods: {
    getRso() {
      axios.get("/api/rso/data/confirm/" + this.ambiluser.kode_groupso).then((res) => {
        this.rso = res.data.data;
        axios.get("/api/ekspedisi").then((res) => {
          this.dataekspedisi = res.data.data;
          this.load = false;
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
    validate() {
      if (this.so.tanggal_so < this.now()) {
        this.so.tanggal_so = this.now();
      }
    },
    tglKirim() {
      var d = new Date();
      var month = d.getMonth() + 1;
      var day = d.getDate() + 2;

      var output = d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-" + (day < 10 ? "0" : "") + day;
      return output;
    },
    so_nomor() {
      var d = new Date();
      var month = d.getMonth() + 1;

      var output = "SO-" + d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-";
      return output;
    },
    showPo() {
      $("#modal-po").modal("show");
    },
    resetForm() {
      this.checkrso = [];
      this.hitung = {
        qty: [],
      };
    },
    pilihRso(aktif) {
      this.hitung = {
        qty: [],
      };
      this.checkrso = [];
      this.ket.customer = aktif.customer;
      this.up.nomor_rso = aktif.nomor_rso;
      this.defaultlok = aktif.customer;
      this.defaultal = aktif.alamat;
      axios.get("/api/listrso/data/listpo/" + aktif.nomor_po).then((res) => {
        this.listsisa = res.data.data;
        this.pilihStatus();
      });
    },
    checklist() {
      $("#modal-po").modal("hide");
    },
    pilihStatus() {
      this.checkrso = [];
      this.hitung = {
        qty: [],
      };
      if (this.status === "Tersedia") {
        axios.get("/api/listrso/data/dic/" + this.up.nomor_rso).then((res) => {
          this.listso = res.data.data;
          this.total = 0;
          for (let i = 0; i < this.listso.length; i++) {
            this.ket.qty[i] = this.listso[i].qty_tersedia;
            this.lstatus = "Tersedia";
            this.tampil = false;
            this.statuspo = true;
            this.disabled = 0;
          }
        });
      } else if (this.status === "TidakTersedia") {
        axios.get("/api/listrso/data/acc/" + this.up.nomor_rso).then((res) => {
          this.listso = res.data.data;
          this.total = 0;
          for (let i = 0; i < this.listso.length; i++) {
            this.ket.qty[i] = this.listso[i].qty_tdktersedia;
            this.lstatus = "Tidak Tersedia";
            this.tampil = true;
            this.statuspo = false;
            this.disabled = 0;
          }
        });
      }
    },
    inputlist() {
      for (let i = 0; i < this.checkrso.length; i++) {
        if (this.status === "Tersedia") {
          this.ket.coba[i] = this.checkrso[i].qty_tersedia;
        } else {
          this.ket.coba[i] = this.checkrso[i].qty_tdktersedia;
        }
        this.ket.coba.push({
          ds: 0,
        });
      }
      $("#modal-po").modal("hide");
    },
    submitSo() {
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
          text: "Ingin mengirim SO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.okin = "";
            this.banding = "";
            this.ok = "";
            for (let i = 0; i < this.checkrso.length; i++) {
              if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                this.ok = "N";
              } else {
                this.ok = "Y";
              }
              this.okin += this.ok;
              this.banding += "Y";
            }
            if (this.okin === this.banding) {
              if (this.status === "Tersedia") {
                this.up.statusso = "tersedia";
              } else {
                this.up.statusso = "tidaktersedia";
              }
              this.up.status = "Kordinator";
              this.up.id_user = this.ambiluser.id;
              this.up.nomor_so = this.up.nomor_so + this.ambiluser.kode_groupso;
              this.up.kode_groupso = this.ambiluser.kode_groupso;
              this.up.tgl_sales = this.DateTime();
              axios
                .post("/api/so", this.up)
                .then((res) => {
                  this.ada = 0;
                  this.upload = {};
                  for (let i = 0; i < this.checkrso.length; i++) {
                    if (this.status === "Tersedia") {
                      this.ada = this.hitung.qty[i];
                      this.statpo = "Y";
                      this.statusso = "Tersedia";
                      this.sisapo = 0;
                    } else {
                      this.ada = 0;
                      this.statpo = "N";
                      this.statusso = "Tidak Tersedia";
                      this.sisapo = this.hitung.qty[i];
                    }
                    this.upload = {
                      qtyrso: this.ket.coba[i],
                      nomor_so: this.up.nomor_so,
                      kode_barang: this.checkrso[i].lkode_barang,
                      harga: this.checkrso[i].harga,
                      tanggal_datang: this.checkrso[i].tgl_datang,
                      id_custprice: this.checkrso[i].id_custprice,
                      qty: this.hitung.qty[i],
                      tersedia: this.ada,
                      statuspo: this.statpo,
                      statusso: this.statusso,
                      sisapo: this.sisapo,
                      diskon: this.checkrso[i].diskon,
                      idx: this.checkrso[i].idx,
                    };
                    axios.post("/api/listso", this.upload).then((res) => {
                      this.uprso = {};
                      for (let n = 0; n < this.listso.length; n++) {
                        if (this.status === "Tersedia") {
                          this.uprso.so_tersedia = "Y";
                        } else {
                          this.uprso.so_tdktersedia = "Y";
                        }
                        axios.put("/api/listrso/" + this.listso[n].id, this.uprso).then((res) => {
                          axios.get("/api/listrso/" + this.up.nomor_rso).then((res) => {
                            this.lisallrso = res.data.data;
                            this.done = "";
                            this.pembanding = "";
                            for (let h = 0; h < this.lisallrso.length; h++) {
                              this.done += this.lisallrso[h].so_tersedia + this.lisallrso[h].so_tdktersedia;
                              this.pembanding += "YY";
                            }
                            if (this.done === this.pembanding) {
                              axios.put("/api/rso/" + this.up.nomor_rso, {
                                status: "So",
                              });
                            }
                          });
                        });
                      }
                    });
                  }
                  axios.post("/api/history", {
                    nomor_dok: this.up.nomor_so,
                    nomor_ref: this.up.nomor_rso,
                    id_user: this.ambiluser.id,
                    notif: "Anda mendapatkan permintaan SO baru",
                    keterangan: "SO di kirim ke Sales Kordinator",
                    jenis: "So",
                    tanggal: this.DateTime(),
                  });
                  if (this.status === "Tersedia") {
                    this.statusnya = "Tersedia";
                  } else {
                    this.statusnya = "Tidak Tersedia";
                  }
                  axios
                    .post("/api/history", {
                      nomor_dok: this.up.nomor_rso,
                      nomor_ref: this.up.nomor_so,
                      id_user: this.ambiluser.id,
                      notif: "Anda mendapatkan permintaan SO baru",
                      keterangan: "Membuat SO " + this.statusnya + " nomor : " + this.up.nomor_so,
                      jenis: "RSO",
                      tanggal: this.DateTime(),
                      aktif: "N",
                    })
                    .then((res) => {
                      this.$router.push({
                        name: "so",
                      });
                    });
                  swalWithBootstrapButtons.fire("Sukses!", "SO berhasil di kirim.", "success");
                })
                .catch((error) => {
                  Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Cek kembali rincian SO anda!",
                  });
                });
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Qty item tidak boleh kosong",
              });
            }
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal mengirim SO :)", "error");
          }
        });
    },
    validasiqty() {
      this.invoice = 0;
      this.kubikasi = 0;
      this.tonase = 0;
      for (let i = 0; i < this.checkrso.length; i++) {
        if (this.hitung.qty[i] > this.ket.coba[i]) {
          this.hitung.qty[i] = this.ket.coba[i];
        }

        if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
          this.sub = 0;
        } else {
          this.sub = this.hitung.qty[i];
        }

        if (this.checkrso[i].diskon === null) {
          this.checkrso[i].diskon = 0;
        }

        this.invoice += parseFloat(this.sub) * (parseFloat(this.checkrso[i].harga) - parseFloat(this.checkrso[i].diskon));
        this.kubikasi += parseFloat(this.hitung.qty[i]) * parseFloat(this.checkrso[i].kubikasi);
        this.tonase += parseFloat(this.hitung.qty[i]) * parseFloat(this.checkrso[i].tonase);
      }
    },
    draftSo() {
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
          text: "Barang yang tidak di input akan di unbooking",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.okin = "";
            this.banding = "";
            this.ok = "";
            for (let i = 0; i < this.checkrso.length; i++) {
              if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
                this.ok = "N";
              } else {
                this.ok = "Y";
              }
              this.okin += this.ok;
              this.banding += "Y";
            }
            if (this.okin === this.banding) {
              if (this.status === "Tersedia") {
                this.up.statusso = "tersedia";
              } else {
                this.up.statusso = "tidaktersedia";
              }
              this.up.status = "Draft";
              this.up.id_user = this.ambiluser.id;
              this.up.kode_groupso = this.ambiluser.kode_groupso;
              this.up.nomor_so = this.up.nomor_so + this.ambiluser.kode_groupso;
              axios
                .post("/api/so", this.up)
                .then((res) => {
                  this.ada = 0;
                  this.upload = {};
                  for (let i = 0; i < this.checkrso.length; i++) {
                    if (this.status === "Tersedia") {
                      this.ada = this.hitung.qty[i];
                      this.statpo = "Y";
                      this.statusso = "Tersedia";
                      this.sisapo = 0;
                    } else {
                      this.ada = 0;
                      this.statpo = "N";
                      this.statusso = "Tidak Tersedia";
                      this.sisapo = this.hitung.qty[i];
                    }
                    this.upload = {
                      qtyrso: this.ket.coba[i],
                      nomor_so: this.up.nomor_so,
                      kode_barang: this.checkrso[i].lkode_barang,
                      harga: this.checkrso[i].harga,
                      id_custprice: this.checkrso[i].id_custprice,
                      qty: this.hitung.qty[i],
                      tersedia: this.ada,
                      statuspo: this.statpo,
                      statusso: this.statusso,
                      sisapo: this.sisapo,
                      diskon: this.checkrso[i].diskon,
                      idx: this.checkrso[i].idx,
                    };
                    axios.post("/api/listso", this.upload).then((res) => {
                      this.uprso = {};
                      for (let n = 0; n < this.listso.length; n++) {
                        if (this.status === "Tersedia") {
                          this.uprso.so_tersedia = "Y";
                        } else {
                          this.uprso.so_tdktersedia = "Y";
                        }
                        axios.put("/api/listrso/" + this.listso[n].id, this.uprso).then((res) => {
                          axios.get("/api/listrso/" + this.up.nomor_rso).then((res) => {
                            this.lisallrso = res.data.data;
                            this.done = "";
                            this.pembanding = "";
                            for (let h = 0; h < this.lisallrso.length; h++) {
                              this.done += this.lisallrso[h].so_tersedia + this.lisallrso[h].so_tdktersedia;
                              this.pembanding += "YY";
                            }
                            if (this.done === this.pembanding) {
                              axios
                                .put("/api/rso/" + this.up.nomor_rso, {
                                  status: "So",
                                })
                                .then((res) => {
                                  this.$router.push({
                                    name: "so",
                                  });
                                });
                            } else {
                              this.$router.push({
                                name: "so",
                              });
                            }
                          });
                        });
                      }
                    });
                  }
                  swalWithBootstrapButtons.fire("Sukses!", "Draft SO berhasil di Di simpan.", "success");
                })
                .catch((error) => {
                  Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Cek kembali rincian SO anda!",
                  });
                });
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Qty item tidak boleh kosong",
              });
            }
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal mengirim SO :)", "error");
          }
        });
    },
    hapuslistSo(index) {
      this.checkrso.splice(index, 1);
      this.ket.coba.splice(index, 1);
      this.hitung.qty.splice(index, 1);
      this.validasiqty();
    },
    timer() {
      setInterval(() => {
        this.gagal = false;
      }, 5000);
    },
    aksidistribusi() {
      if (this.up.distribusi === "kirim") {
        this.lokasi = "default";
        if (this.lokasi === "default") {
        }
        $("#modal-lokasi").modal("show");
      } else if (this.up.distribusi === "ambil") {
        this.up.lokasi = "PT. Ekatunggal Tunas Mandiri";
        this.up.alamat = "Jl. Pahlawan No.29A, RT.003/005 Citeureup, Kab. Bogor, Jawa Barat";
      } else if (this.up.distribusi === "default") {
        this.up.alamat = "";
        this.up.lokasi = "";
      }
    },
    selectLokasi() {
      this.up.distribusi = "default";
      this.up.distribusi = "kirim";
      if (this.lokasi === "ekspedisi") {
        this.lok = this.ekslok;
        this.al = this.eksal;
      } else if (this.lokasi === "lainnya") {
        this.lok = this.lainlok;
        this.al = this.lainal;
      } else if (this.lokasi === "default") {
        this.lok = this.defaultlok;
        this.al = this.defaultal;
      }
      this.up.lokasi = this.lok;
      this.up.alamat = this.al;
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
      this.up.id_ekspedisi = this.eks.id;
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
    ceknomorso() {
      axios.get("/api/so/" + this.up.nomor_so + this.ambiluser.kode_groupso).then((res) => {
        this.adaso = res.data.data;
        if (this.up.nomor_so.length === 15 && this.adaso.length === 0) {
          this.nyala = true;
        } else {
          this.nyala = false;
        }
      });
    },
  },
};
</script>

<style scoped>
.dtHorizontalVerticalExampleWrapper {
  max-width: 600px;
  margin: 0 auto;
}
#dtHorizontalVerticalExample th,
td {
  white-space: nowrap;
}
table.dataTable thead .sorting:after,
table.dataTable thead .sorting:before,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_asc:before,
table.dataTable thead .sorting_asc_disabled:after,
table.dataTable thead .sorting_asc_disabled:before,
table.dataTable thead .sorting_desc:after,
table.dataTable thead .sorting_desc:before,
table.dataTable thead .sorting_desc_disabled:after,
table.dataTable thead .sorting_desc_disabled:before {
  bottom: 0.5em;
}

.tonkg {
  width: auto;
  height: auto;
  position: absolute;
  right: 5%;
}

.tonkg b {
  color: rgb(177, 176, 176);
}
</style>

<style>
#rsoverflowso {
  width: 100%;
  max-height: 275px;
  overflow-y: scroll;
  border-top: solid 1px #dee2e6;
}

#load3 {
  position: relative;
  margin: 0 auto;
  margin-bottom: 10px;
}

.label {
  border: solid 1px rgb(42, 199, 42);
  background-color: rgb(104, 226, 104);
  font-size: 16px;
  border-radius: 3px;
  padding: 5px;
  padding-left: 1%;
  padding-right: 1%;
  color: #fff;
}

.labelt {
  border: solid 1px rgb(40, 131, 206);
  background-color: rgb(75, 161, 231);
  font-size: 16px;
  border-radius: 3px;
  padding: 5px;
  padding-left: 1%;
  padding-right: 1%;
  color: #fff;
}

.error-template {
  padding: 40px 15px;
  text-align: center;
}

.error-actions {
  margin-top: 15px;
  margin-bottom: 15px;
}

.error-actions .btn {
  margin-right: 10px;
}
</style>
