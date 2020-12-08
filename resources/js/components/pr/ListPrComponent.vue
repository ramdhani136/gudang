<template>
  <div class="container" @keyup.esc="resetForm()">
    <div class="row row-cols-2">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor PR :</label>
          <input
            :disabled="statuspr !== 'Draft'"
            @input="cekinputPr()"
            v-model="lpo.nomor_pr"
            type="text"
            class="form-control col-12"
            maxlength="15"
            :class="{ 'is-valid': nyala }"
          />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>User</label>
          <select v-model="lpo.id_user" class="col-12 form-control" disabled>
            <option v-for="(usr, index) in user" :key="index" :value="usr.id">
              {{ usr.name }}
            </option>
          </select>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Tanggal :</label>
          <input
            :disabled="statuspr !== 'Draft'"
            v-model="lpo.tanggal"
            type="date"
            class="form-control col-12"
          />
        </div>
      </div>
    </div>
    <button
      v-if="statuspr === 'Draft'"
      style="font-size: 1em"
      @click="showmodal()"
      class="float-left ml-2 mt-4 label mb-1"
    >
      Ambil Item
    </button>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
      <div class="row mt-1 mx-auto col-12">
        <Circle5 id="load3" v-if="load"></Circle5>
        <table
          id="rsthead"
          class="table mt-2 table-striped table-bordered"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th>No</th>
              <th>Kode</th>
              <th>Nama Barang</th>
              <th v-if="statuspr === 'Draft'">Total Permintaan</th>
              <th>Qty PR</th>
              <th>Satuan</th>
              <th>Catatan</th>
              <th v-if="statuspr === 'Draft'">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(list, index) in listfix" :key="index">
              <td style="text-align: center">{{ index + 1 }}</td>
              <td>{{ list.kode_barang }}</td>
              <td>{{ list.nama_barang }}</td>
              <td v-if="statuspr === 'Draft'" style="text-align: center">
                {{ list.qty }}
              </td>
              <td style="text-align: center">
                <input
                  :disabled="statuspr !== 'Draft'"
                  @input="cekqty()"
                  style="width: 130px"
                  type="number"
                  class="form-control"
                  v-model="hitung.qty[index]"
                />
              </td>
              <td style="text-align: center">{{ list.satuan }}</td>
              <td style="text-align: center">
                <textarea
                  :disabled="statuspr !== 'Draft'"
                  v-model="hitung.keterangan[index]"
                  style="width: 200px"
                  class="form-control"
                ></textarea>
              </td>
              <td v-if="statuspr === 'Draft'" style="text-align: center">
                <button @click="deletelist(index)" class="btn btn-danger">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-12 mt-2">
      <div class="form-group">
        <label>Keterangan</label>
        <textarea
          :disabled="statuspr !== 'Draft'"
          v-model="lpo.keterangan"
          class="form-control"
        ></textarea>
        <div v-if="edit === 'Y' && ambiluser.purch === 1" class="notif">{{ alasan }}</div>
      </div>
    </div>
    <div class="row mt-2">
      <button
        v-if="statuspr === 'Draft'"
        @click="submitPr()"
        class="btn-success btn ml-4"
      >
        Kirim PR
      </button>
      <button
        v-if="statuspr !== 'Draft' && ambiluser.inventory === 1 && edit === 'N'"
        @click="reqedit()"
        class="btn-orange btn ml-4"
      >
        Request Edit
      </button>
      <button
        v-if="statuspr === 'Sent' && ambiluser.purch === 1 && edit === 'N'"
        @click="showtolak()"
        class="btn-danger btn ml-4"
      >
        Tolak
      </button>
      <button
        v-if="statuspr === 'Sent' && ambiluser.purch === 1 && edit === 'N'"
        @click="terimapr()"
        class="btn-success btn ml-2"
      >
        Terima PR
      </button>
      <button
        v-if="ambiluser.purch === 1 && edit === 'Y'"
        @click="tolakedit()"
        class="btn-danger btn ml-4"
      >
        Tolak Perbaikan
      </button>
      <button
        v-if="ambiluser.purch === 1 && edit === 'Y'"
        @click="terimaedit()"
        class="btn-success btn ml-1"
      >
        Izinkan Perbaikan
      </button>
      <button
        v-if="statuspr !== 'Draft' && ambiluser.inventory === 1 && edit === 'Y'"
        @click="bataledit()"
        class="btn-primary btn ml-4"
      >
        Batalkan Perbaikan
      </button>
    </div>
    <div
      class="modal fade"
      id="modal-form"
      tabindex="-1"
      data-backdrop="static"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
            <button
              @click="resetForm()"
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Kode</label>
              <input
                v-model="form.kode_barang"
                type="text"
                class="form-control"
                disabled
              />
            </div>
            <div class="form-group">
              <label>Pilih Barang</label>
              <select
                @change="getList(chooseItem)"
                v-model="chooseItem"
                class="form-control"
              >
                <option v-for="(prl, index) in pr" :key="index" :value="prl">
                  {{ prl.nama }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Total Permintaan</label>
              <input v-model="form.jumlah" type="text" class="form-control" disabled />
            </div>
            <div class="form-group">
              <label>Satuan</label>
              <input v-model="ket.satuan" type="text" class="form-control" disabled />
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="resetForm()"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button
              v-if="form.jumlah > 0"
              type="button"
              @click="TambahItem()"
              class="btn btn-primary"
            >
              Input Item
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="modal-tolak"
      tabindex="-1"
      data-backdrop="static"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
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
          <div v-for="(lpo, index) in po" :key="index" class="modal-footer">
            <button
              type="button"
              @click="resetForm()"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button type="button" @click="tolakpr(lpo)" class="btn btn-orange">
              Konfirmasi Tolak
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-2" v-for="(lpo, index) in po" :key="index">
      <div v-if="lpo.status == 'Tolak'" id="alastolak">
        <div>
          <b>{{ lpo.alastolak }}</b>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="modal-request"
      tabindex="-1"
      data-backdrop="static"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Form Request Perbaikan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Alasan permintaan</label>
              <textarea
                :disabled="edit === 'Y'"
                v-model="alasan"
                class="form-control"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button
              v-if="edit === 'N'"
              @click="kirimrequest()"
              type="button"
              class="btn btn-success"
              data-dismiss="modal"
            >
              Kirim Permintaan
            </button>
            <button
              v-if="edit === 'Y'"
              @click="kirimbatalrequest()"
              type="button"
              class="btn btn-success"
              data-dismiss="modal"
            >
              Batalkan permintaan
            </button>
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
      pr: {},
      chooseItem: "",
      prlist: {},
      nyala: false,
      pr: {},
      listpr: [],
      prOpen: {},
      confirm: {
        status: "Request",
      },
      up: {},
      ket: {},
      upload: {},
      lpo: {
        tanggal: this.now(),
        nomor_pr: this.pr_nomor(),
        id_user: this.ambiluser.id,
        keterangan: "",
      },
      hitung: {
        qty: [],
        subqty: [],
        keterangan: [],
      },
      form: {
        jumlah: "",
      },
      listfix: [],
      ada: "",
      adapr: {},
      siap: "",
      banding: "",
      siapband: "",
      user: {},
      bagilistso: {},
      listbagi: {},
      kasihso: 0,
      sisapembagi: 0,
      masihsisa: 0,
      sisapo: 0,
      openpo: 0,
      prini: {},
      statuspr: "Reqedit",
      alasan: "",
      edit: "N",
      statusupdate: "",
    };
  },
  created() {
    this.getPr();
  },
  computed: {
    FilteredSatuan() {
      return this.pr.filter((elem) => {
        return elem.kode_barang.toLowerCase().includes(this.chooseItem);
      });
    },
    matches() {
      if (this.query == "") {
        return [];
      }
      return this.supplier.filter((item) =>
        item.nama.toLowerCase().includes(this.query.toLowerCase())
      );
    },
  },
  methods: {
    getPr() {
      axios.get("/api/listso/data/group").then((res) => {
        this.pr = res.data.data;
        axios.get("/api/user").then((res) => {
          this.user = res.data.data;
          axios.get("/api/pr/" + this.$route.params.nomor).then((res) => {
            this.prini = res.data.data;
            this.lpo.tanggal = this.prini[0].tanggal;
            this.lpo.nomor_pr = this.prini[0].nomor_pr;
            this.lpo.id_user = this.prini[0].id_user;
            this.lpo.keterangan = this.prini[0].keterangan;
            this.edit = this.prini[0].reqedit;
            this.statuspr = this.prini[0].status;
            this.alasan = this.prini[0].alasan;
            axios.get("/api/listpr/" + this.lpo.nomor_pr).then((res) => {
              this.listfix = res.data.data;
              console.log(this.listfix);
              for (let u = 0; u < this.listfix.length; u++) {
                this.hitung.qty[u] = this.listfix[u].qty;
                this.hitung.keterangan[u] = this.listfix[u].keterangan;
              }
            });
            this.load = false;
          });
        });
      });
    },
    getList(chooseItem) {
      this.load = true;
      axios.get("/api/listso/data/group/" + chooseItem.kode_barang).then((res) => {
        this.prlist = res.data.data;
        this.form.jumlah = 0;
        for (let i = 0; i < this.prlist.length; i++) {
          this.form.jumlah += parseInt(this.prlist[i].jumlah);
        }
        this.load = false;
        this.form.kode_barang = this.prlist[0].kode;
        this.form.nama_barang = this.prlist[0].nama_barang;
        this.ket.satuan = this.prlist[0].satuan;
      });
    },
    now() {
      var d = new Date();
      var month = d.getMonth() + 1;
      var day = d.getDate();

      var output =
        d.getFullYear() +
        "-" +
        (month < 10 ? "0" : "") +
        month +
        "-" +
        (day < 10 ? "0" : "") +
        day;
      return output;
    },
    pr_nomor() {
      var d = new Date();
      var month = d.getMonth() + 1;

      var output = "PR-" + d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-";
      return output;
    },
    showmodal() {
      this.errors = [];
      $("#modal-form").modal("show");
    },
    resetForm() {
      this.chooseItem = "";
      this.prlist = {};
      this.up.alastolak = "";
      this.form = {};
      this.ket.satuan = "";
    },
    TambahItem() {
      if (this.listfix.length === 0) {
        this.listfix.push({
          kode_barang: this.form.kode_barang,
          nama_barang: this.form.nama_barang,
          qty: this.form.jumlah,
          satuan: this.ket.satuan,
        });
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
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal menambahkan item...",
            text: "Item ini sudah di input!",
          });
        }
      }

      $("#modal-form").modal("hide");
      this.resetForm();
    },
    deletelist(index) {
      this.hitung.qty.splice(index, 1);
      this.hitung.keterangan.splice(index, 1);
      this.listfix.splice(index, 1);
      this.resetForm();
    },
    submitPr() {
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
          text: "Ingin mengirim PR ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.load = true;
            if (this.listfix.length > 0) {
              this.siap = "";
              this.siapband = "";
              for (let i = 0; i < this.listfix.length; i++) {
                console.log(this.hitung.qty[i]);
                if (
                  this.hitung.qty[i] < 1 ||
                  this.hitung.qty[i] === "" ||
                  this.hitung.qty[i] === undefined
                ) {
                  this.a = "N";
                } else {
                  this.a = "Y";
                }
                this.siap += this.a;
                this.siapband += "Y";
              }
              if (this.siap === this.siapband) {
                this.lpo.tgl_inventory = this.DateTime();
                axios.post("/api/pr", this.lpo).then((res) => {
                  for (let i = 0; i < this.listfix.length; i++) {
                    axios.post("/api/listpr", {
                      nomor_pr: this.lpo.nomor_pr,
                      kode_barang: this.listfix[i].kode_barang,
                      qty: this.hitung.qty[i],
                      keterangan: this.hitung.keterangan[i],
                    });
                  }
                  this.kasihso = 0;
                  this.sisapembagi = 0;
                  this.masihsisa = 0;
                  this.sisapo = 0;
                  this.openpo = 0;
                  for (let i = 0; i < this.listfix.length; i++) {
                    axios
                      .get("/api/listso/data/antrianpo/" + this.listfix[i].kode_barang)
                      .then((res) => {
                        this.listbagi = res.data.data;
                        this.kasihso = 0;
                        this.sisapembagi = 0;
                        this.masihsisa = 0;
                        this.sisapo = 0;
                        this.tutupso = "";
                        this.bandingtutup = "";
                        this.openpo = 0;
                        for (let k = 0; k < this.listbagi.length; k++) {
                          /* ini sisa sonya */
                          this.sisapembagi =
                            parseFloat(this.listbagi[k].qty) -
                            parseFloat(this.listbagi[k].openpo);
                          /* end */
                          if (this.hitung.qty[i] < this.sisapembagi) {
                            this.kasihso = this.hitung.qty[i];
                            this.hitung.qty[i] = 0;
                          } else {
                            this.kasihso = this.sisapembagi;
                            this.hitung.qty[i] =
                              parseFloat(this.hitung.qty[i]) -
                              parseFloat(this.sisapembagi);
                          }

                          this.sisapo =
                            parseFloat(this.listbagi[k].sisapo) -
                            parseFloat(this.kasihso);
                          this.openpo =
                            parseFloat(this.listbagi[k].openpo) +
                            parseFloat(this.kasihso);

                          if (this.sisapo === 0) {
                            this.ubah = "Y";
                          } else {
                            this.ubah = "N";
                          }
                          axios.put("/api/listso/" + this.listbagi[k].id, {
                            sisapo: this.sisapo,
                            openpo: this.openpo,
                            statuspo: this.ubah,
                          });
                        }
                      });
                  }
                  axios
                    .post("/api/history", {
                      nomor_dok: this.lpo.nomor_pr,
                      id_user: this.ambiluser.id,
                      notif: "Terdapat permintaan PR baru",
                      keterangan: "Mengirim PR ke Purch Purchasing",
                      jenis: "Pr",
                      tanggal: this.DateTime(),
                    })
                    .then((res) => {
                      this.load = false;
                      swalWithBootstrapButtons.fire(
                        "Success!",
                        "Form PR berhasil dikirim.",
                        "success"
                      );
                      this.kembali();
                    });
                });
              } else {
                this.load = false;
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text: "Periksa item anda kembali, qty harus diisi!",
                });
              }
            } else {
              this.load = false;
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Anda belum menginput barang apapun!",
              });
            }
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire("Cancelled", "Batal mengirim PR :)", "error");
          }
        });
    },
    kembali() {
      this.$router.push({
        name: "datapr",
      });
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
      this.dates =
        this.year + "-" + (this.month < 10 ? "0" : "") + this.month + "-" + this.day;
      this.times =
        this.hours +
        ":" +
        this.minute +
        ":" +
        (this.seconds < 10 ? "0" : "") +
        this.seconds;
      this.datetimes = this.dates + " " + this.times;
      return this.datetimes;
    },
    cekinputPr() {
      axios.get("/api/pr/" + this.lpo.nomor_pr).then((res) => {
        this.adapr = res.data.data;
        if (this.lpo.nomor_pr.length === 15 && this.adapr.length === 0) {
          this.nyala = true;
        } else {
          this.nyala = false;
        }
      });
    },
    cekqty() {
      for (let i = 0; i < this.listfix.length; i++) {
        if (this.hitung.qty[i] > this.listfix[i].qty || this.hitung.qty[i] < 0) {
          this.hitung.qty[i] = this.listfix[i].qty;
        }
      }
    },
    reqedit() {
      $("#modal-request").modal("show");
    },
    kirimrequest() {
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
          text: "Ingin mengirim permintaan ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.load = true;
            if (this.alasan === undefined || this.alasan === "") {
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Wajib mengisi alasan permintaan!",
              });
              this.load = false;
            } else {
              if (this.statuspr === "Sent") {
                this.statusupdate = "Draft";
                this.edit = "N";
                this.alasan = "";
              } else {
                this.statusupdate = "Open";
                this.edit = "Y";
              }
              axios
                .put("/api/pr/" + this.$route.params.nomor, {
                  reqedit: this.edit,
                  alasan: this.alasan,
                  status: this.statusupdate,
                })
                .then((res) => {
                  axios
                    .post("/api/history", {
                      nomor_dok: this.lpo.nomor_pr,
                      id_user: this.ambiluser.id,
                      notif: "request perbaikan",
                      keterangan: "Mengirim request perbaikan",
                      jenis: "Pr",
                      tanggal: this.DateTime(),
                    })
                    .then((res) => {
                      this.getPr();
                      this.load = false;
                      swalWithBootstrapButtons.fire(
                        "Success!",
                        "Permintaan perbaikan berhasil di kirim.",
                        "success"
                      );
                    });
                });
            }
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire(
              "Cancelled",
              "Batal mengirimkan permintaan :)",
              "error"
            );
          }
        });
    },
    bataledit() {
      $("#modal-request").modal("show");
    },
    kirimbatalrequest() {
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
          text: "Ingin membatalkan permintaan perbaikan PR ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.load = true;
            if (this.statuspr === "Open") {
              this.statusupdate = "Open";
            } else {
              this.statusupdate = "Sent";
            }
            axios
              .put("/api/pr/" + this.$route.params.nomor, {
                reqedit: "N",
                alasan: "",
                status: this.statusupdate,
              })
              .then((res) => {
                axios
                  .post("/api/history", {
                    nomor_dok: this.lpo.nomor_pr,
                    id_user: this.ambiluser.id,
                    notif: "batal request perbaikan",
                    keterangan: "Batal request perbaikan",
                    jenis: "Pr",
                    tanggal: this.DateTime(),
                  })
                  .then((res) => {
                    this.getPr();
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                      "Sukses!",
                      "Permintaan perbaikan berhasil di batalkan.",
                      "success"
                    );
                  });
              });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire(
              "Cancelled",
              "Batal membatalkan permintaan perbaikan :)",
              "error"
            );
          }
        });
    },
    terimaedit() {
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
          text: "Ingin menerima permintaan perbaikan PR ini!",
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
              .put("/api/pr/" + this.$route.params.nomor, {
                status: "Draft",
                alasan: "",
                reqedit: "N",
              })
              .then((res) => {
                axios
                  .post("/api/history", {
                    nomor_dok: this.lpo.nomor_pr,
                    id_user: this.ambiluser.id,
                    notif: "Perbaikan di terima",
                    keterangan: "Request perbaikan di terima",
                    jenis: "Pr",
                    tanggal: this.DateTime(),
                  })
                  .then((res) => {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                      "Sukses!",
                      "Permintaan berhasil di terima.",
                      "success"
                    );
                    this.kembali();
                  });
              });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire(
              "Cancelled",
              "Batal menerima permintaan :)",
              "error"
            );
          }
        });
    },
    terimapr() {
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
          text: "Ingin menerima PR ini!",
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
              .put("/api/pr/" + this.$route.params.nomor, {
                status: "Open",
                purchase: this.ambiluser.name,
                tgl_purchase: this.DateTime(),
              })
              .then((res) => {
                axios
                  .post("/api/history", {
                    nomor_dok: this.lpo.nomor_pr,
                    id_user: this.ambiluser.id,
                    notif: "Pr di terima",
                    keterangan: "PR di terima",
                    jenis: "Pr",
                    tanggal: this.DateTime(),
                  })
                  .then((res) => {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                      "Sukses!",
                      "Berhasil menerima PR.",
                      "success"
                    );
                    this.kembali();
                  });
              });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire("Cancelled", "Batal menerima PR :)", "error");
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
</style>

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

.notif {
  width: 98%;
  padding: 1%;
  height: auto;
  border: solid 1px rgba(156, 156, 8, 0.459);
  background-color: rgba(255, 255, 115, 0.459);
  color: rgb(168, 168, 6);
  text-align: center;
  margin-top: 20px;
}
</style>
