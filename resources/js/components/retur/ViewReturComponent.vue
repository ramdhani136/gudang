<template>
  <div class="container">
    <div class="row row-cols-2">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor Retur :</label>
          <input
            @input="ceknomor()"
            v-model="up.nomor_retur"
            type="text"
            class="form-control col-12"
            maxlength="15"
            :class="{ 'is-valid': bisakirim }"
            :disabled="statusretur !== 'Draft' || ambiluser.sales === 0"
          />
        </div>
        <div class="form-group">
          <label>Tanggal :</label>
          <input
            v-model="up.tanggal"
            type="date"
            @change="validate()"
            :min="now()"
            class="form-control col-12"
            :disabled="statusretur !== 'Draft' || ambiluser.sales === 0"
          />
        </div>
        <div class="form-group">
          <label>Tanggal So :</label>
          <input
            v-model="ket.tanggal_kirim"
            type="date"
            @change="validate()"
            :min="now()"
            class="form-control col-12"
            disabled
          />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Nomor SO</label>
          <input
            v-model="up.nomor_so"
            @click="showSo()"
            type="text"
            class="form-control"
            placeholder="Pilih Sales Order"
            disabled
          />
        </div>
        <div class="form-group">
          <label>Customer</label>
          <select
            v-model="ket.customer"
            name="customer"
            class="col-12 form-control"
            disabled
          >
            <option :value="ket.customer">{{ ket.customer }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>keterangan</label>
          <textarea
            v-model="up.keterangan"
            name="keterangan"
            class="form-control col-12"
            :disabled="statusretur !== 'Draft' || ambiluser.sales === 0"
          ></textarea>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Distribusi :</label>
          <input
            v-model="ket.distribusi"
            type="text"
            class="form-control col-12"
            disabled
          />
        </div>
        <div class="form-group">
          <label>Lokasi</label>
          <input
            v-model="ket.lokasi"
            type="text"
            class="form-control col-12"
            disabled
          />
        </div>
        <div class="form-group">
          <label>Alamat</label>
          <textarea
            class="form-control"
            v-model="ket.alamat"
            disabled
          ></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div v-if="statusretur !== 'Acc'" id="total" class="mt-3 ml-auto mr-3">
        Total Invoice :&nbsp; {{ total | currency }}
      </div>
      <div
        v-if="statusretur === 'Acc' || statusretur === 'Selesai'"
        id="total"
        class="mt-3 ml-auto mr-3"
      >
        Total Invoice :&nbsp; {{ totalreal | currency }}
      </div>
    </div>
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
              <th>Satuan</th>
              <th>Harga</th>
              <th>Diskon</th>
              <th>SO Terkirim</th>
              <th>Jumlah Retur</th>
              <th v-if="statusretur === 'Acc' && ambiluser.incoming === 1">
                Jumlah Real
              </th>
              <th>Keterangan</th>
              <th v-if="statusretur === 'Draft' && ambiluser.sales === 1">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(listbcm, index) in checker" :key="index">
              <td style="text-align: center">{{ index + 1 }}</td>
              <td>{{ listbcm.kode_barang }}</td>
              <td>{{ listbcm.nama_barang }}</td>
              <td style="text-align: center">{{ listbcm.satuan }}</td>
              <td style="text-align: center">{{ listbcm.harga | currency }}</td>
              <td style="text-align: center">
                {{ listbcm.diskon | currency }}
              </td>
              <td style="text-align: center">{{ listbcm.bbk }}</td>
              <td style="text-align: center">
                <input
                  @input="validqty(index)"
                  v-model="hitung.qty[index]"
                  type="number"
                  class="form-control"
                  :disabled="statusretur !== 'Draft' || ambiluser.sales === 0"
                />
              </td>
              <td
                v-if="statusretur === 'Acc' && ambiluser.incoming === 1"
                style="text-align: center"
              >
                <input
                  @input="validreal(index)"
                  v-model="hitung.real[index]"
                  type="number"
                  class="form-control"
                />
              </td>
              <td style="text-align: center">
                <textarea
                  v-model="hitung.keterangan[index]"
                  class="form-control"
                  :disabled="statusretur !== 'Draft' || ambiluser.sales === 0"
                ></textarea>
              </td>
              <td
                v-if="statusretur === 'Draft' && ambiluser.sales === 1"
                style="text-align: center"
              >
                <button @click="hapus(index)" class="btn btn-danger">
                  Hapus
                </button>
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
      <button @click="batal()" class="btn-primary btn ml-4">Kembali</button>
      <button
        v-if="statusretur === 'Acc' && ambiluser.incoming === 1"
        @click="print()"
        class="btn-none btn ml-1"
      >
        Print
      </button>
      <button
        v-if="statusretur === 'Acc' && ambiluser.incoming === 1"
        @click="kirimbbm()"
        class="btn-success btn ml-1"
      >
        Kirim BBM Retur
      </button>
      <button
        v-if="tombolaktif === true"
        @click="terima()"
        class="btn-success btn ml-1"
      >
        Terima Retur
      </button>
      <button
        v-if="tombolaktif === true"
        @click="tolak()"
        class="btn-danger btn ml-1"
      >
        Tolak
      </button>
    </div>
    <div
      class="modal fade"
      id="modal-po"
      tabindex="-1"
      data-backdrop="static"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Form SO</h5>
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
              <label>Pilih SO</label>
              <select
                @change="pilihSo(aktif)"
                v-model="aktif"
                class="form-control"
              >
                <option
                  :value="aktif"
                  v-for="(aktif, index) in soaktif"
                  :key="index"
                >
                  {{ aktif.nomor_so }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Customer</label>
              <input
                v-model="ket.customer"
                type="text"
                class="form-control"
                disabled
              />
            </div>
            <div id="overflowBody">
              <table
                class="table mt-2 table-striped table-bordered"
                style="width: 100%"
              >
                <thead id="rsthead">
                  <tr>
                    <th style="text-align: center">No</th>
                    <th style="text-align: center">Kode</th>
                    <th style="text-align: center">Item</th>
                    <th style="text-align: center">Satuan</th>
                    <th style="text-align: center">Sudah Kirim</th>
                    <th style="text-align: center">Pilih</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(ls, index) in listso" :key="index">
                    <td style="text-align: center">{{ index + 1 }}</td>
                    <td>{{ ls.kode_barang }}</td>
                    <td>{{ ls.nama_barang }}</td>
                    <td style="text-align: center">{{ ls.satuan }}</td>
                    <td style="text-align: center">{{ ls.bbk }}</td>
                    <td style="text-align: center">
                      <input
                        @change="pilihlistchecker()"
                        v-model="checker"
                        type="checkbox"
                        :value="ls"
                        :disabled="ket.ltersedia[index] === 0"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button
              @click="resetForm()"
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button type="button" @click="checklist()" class="btn btn-primary">
              Save changes
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="modal-pr"
      tabindex="-1"
      data-backdrop="static"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              Rincian Permintaan
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="scrollList">
              <table
                id="thead"
                class="table table-striped table-bordered"
                style="width: 100%"
              >
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
                      <button
                        type="button"
                        class="btn btn-danger"
                        data-dismiss="modal"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
          </div>
          <div class="error-actions">
            <router-link to="/so" class="btn btn-primary btn-lg">
              Lihat Data SO
            </router-link>
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
      up: {},
      ket: {
        sisaso: [],
        sisasopilih: [],
        tersedia: [],
        ltersedia: [],
      },
      aktif: {},
      checker: [],
      hitung: {
        qty: [],
        keterangan: [],
        jumlah: [],
        real: [],
        jumlahreal: [],
      },
      soaktif: [],
      listso: {},
      uplist: {},
      qty: 0,
      listsokurang: {},
      nilai: null,
      harga: 0,
      subtotal: 0,
      total: 0,
      bisakirim: false,
      retur: {},
      listaktif: {},
      sopilih: {},
      adagak: 0,
      listretur: {},
      statusretur: "",
      listsoview: [],
      terkirim: 0,
      listreturall: {},
      jumlahlist: 0,
      tombolaktif: false,
      totalreal: 0,
      listsonya: {},
    };
  },
  created() {
    this.getSoAktif();
    this.resetForm();
    this.timer();
  },
  computed: {},
  methods: {
    getSoAktif() {
      axios
        .get("/api/retur")
        .then((res) => {
          this.retur = res.data.data;
          this.up.nomor_retur = this.retur[0].nomor_retur.slice(0, 15);
          this.up.tanggal = this.retur[0].tanggal;
          this.ket.tanggal_kirim = this.retur[0].tanggal_so;
          this.up.nomor_so = this.retur[0].nomor_so;
          this.ket.customer = this.retur[0].customer;
          this.up.keterangan = this.retur[0].keterangan;
          this.ket.distribusi = this.retur[0].distribusi;
          this.ket.alamat = this.retur[0].alamat;
          this.ket.lokasi = this.retur[0].lokasi;
          this.statusretur = this.retur[0].status;
          if (this.ambiluser.kordisales === 1) {
            if (this.statusretur === "Kordinator") {
              this.tombolaktif = true;
            } else {
              this.tombolaktif = false;
            }
          } else if (this.ambiluser.susales === 1) {
            if (this.statusretur === "Supervisor") {
              this.tombolaktif = true;
            } else {
              this.tombolaktif = false;
            }
          } else if (this.ambiluser.incoming === 1) {
            if (this.statusretur === "Dic") {
              this.tombolaktif = true;
            } else {
              this.tombolaktif = false;
            }
          } else {
            this.tombolaktif = false;
          }
        })
        .then((res) => {
          axios
            .get("/api/listretur/" + this.$route.params.nomor)
            .then((res) => {
              this.listretur = res.data.data;
              for (let i = 0; i < this.listretur.length; i++) {
                axios
                  .get(
                    "/api/listso/data/" +
                      this.up.nomor_so +
                      "/" +
                      this.listretur[i].kode_barang
                  )
                  .then((res) => {
                    this.listsoview.push({
                      id: res.data.data[0].id,
                      bbk: res.data.data[0].bbk,
                      kode_barang: res.data.data[0].kode_barang,
                      harga: res.data.data[0].harga,
                      diskon: res.data.data[0].diskon,
                    });
                    axios
                      .get(
                        "/api/listretur/bbk/" +
                          this.up.nomor_so +
                          "/" +
                          this.listsoview[i].kode_barang
                      )
                      .then((res) => {
                        this.listreturall = res.data.data;
                        this.jumlahlist = 0;
                        for (let j = 0; j < this.listreturall.length; j++) {
                          this.jumlahlist += parseFloat(
                            this.listreturall[j].qty
                          );
                        }
                        this.terkirim =
                          parseFloat(this.listsoview[i].bbk) -
                          parseFloat(this.jumlahlist) +
                          parseFloat(this.listretur[i].qty);
                        this.checker.push({
                          id: this.listretur[i].id,
                          kode_barang: this.listretur[i].kode_barang,
                          nama_barang: this.listretur[i].nama_barang,
                          satuan: this.listretur[i].satuan,
                          harga: this.listsoview[i].harga,
                          diskon: this.listsoview[i].diskon,
                          bbk: this.terkirim,
                          qty: this.listretur[i].qty,
                          id_so: this.listsoview[i].id,
                        });
                        this.hitung.qty[i] = this.listretur[i].qty;
                        this.hitung.real[i] = this.listretur[i].real;
                        this.hitung.keterangan[i] = this.listretur[i].catatan;
                        this.validqty(i);
                        this.validreal(i);
                        this.load = false;
                      });
                  });
              }
            })
            .then((res) => {
              axios
                .get("/api/groupso/retur/" + this.ambiluser.kode_groupso)
                .then((res) => {
                  this.sopilih = res.data.data;
                  for (let i = 0; i < this.sopilih.length; i++) {
                    axios
                      .get("/api/listso/" + this.sopilih[i].nomor_so)
                      .then((res) => {
                        this.listaktif = res.data.data;
                        this.adagak = 0;
                        for (let o = 0; o < this.listaktif.length; o++) {
                          this.adagak += parseFloat(this.listaktif[o].bbk);
                        }
                        if (this.adagak > 0) {
                          this.soaktif.push(this.sopilih[i]);
                        }
                      });
                  }
                  this.load = false;
                });
            });
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
    validate() {
      if (this.so.tanggal_so < this.now()) {
        this.so.tanggal_so = this.now();
      }
    },
    tglKirim() {
      var d = new Date();
      var month = d.getMonth() + 1;
      var day = d.getDate() + 2;

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
    retur_nomor() {
      var d = new Date();
      var month = d.getMonth() + 1;

      var output =
        "RT-" + d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-";
      return output;
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
      axios.get("/api/listso/data/lretur/" + this.up.nomor_so).then((res) => {
        this.listso = res.data.data;
      });
    },
    checklist() {
      $("#modal-po").modal("hide");
    },
    pilihlistchecker() {
      for (let i = 0; i < this.checker.length; i++) {
        if (this.aktif.statusso === "tersedia") {
          this.ket.sisasopilih[i] =
            parseFloat(this.checker[i].qty_tersedia) -
            parseFloat(this.checker[i].keluar_tersedia);
          this.ket.tersedia[i] =
            parseFloat(this.checker[i].qty_tersedia) -
            parseFloat(this.checker[i].keluar_tersedia);
        } else {
          this.ket.sisasopilih[i] =
            parseFloat(this.checker[i].qty_tdktersedia) -
            parseFloat(this.checker[i].keluar_tdktersedia);
          this.ket.tersedia[i] =
            parseFloat(this.checker[i].qty_masuk) -
            parseFloat(this.checker[i].keluar_tdktersedia);
        }
      }
    },
    submitretur() {
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
          text: "Ingin mengirim permintaan retur ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            if (this.checker.length > 0) {
              this.aplus = "";
              this.aband = "";
              for (let i = 0; i < this.checker.length; i++) {
                if (
                  this.hitung.qty[i] === undefined ||
                  this.hitung.qty[i] < 1 ||
                  this.hitung.qty[i] === ""
                ) {
                  this.a = "N";
                } else {
                  this.a = "Y";
                }
                this.aplus += this.a;
                this.aband += "Y";
              }
              if (this.aplus === this.aband) {
                this.up.nomor_retur =
                  this.up.nomor_retur + this.ambiluser.kode_groupso;
                this.up.kode_groupso = this.ambiluser.kode_groupso;
                axios
                  .post("/api/retur", this.up)
                  .then((res) => {
                    for (let i = 0; i < this.checker.length; i++) {
                      this.uplist = {
                        nomor_retur: this.up.nomor_retur,
                        kode_barang: this.checker[i].kode_barang,
                        qty: this.hitung.qty[i],
                        catatan: this.hitung.keterangan[i],
                      };
                      axios.post("/api/listretur", this.uplist);
                      // axios.get("/api/listso/data/" + this.up.nomor_so + "/" + this.checker[i].kode_barang)
                      //     .then(res => {
                      //         this.listsokurang = res.data.data;
                      //         axios.put("/api/listso/" + this.listsokurang[0].id, {
                      //             bbk: parseInt(this.listsokurang[0].bbk) - parseInt(this.hitung.qty[i]),
                      //             closeso: 'N',
                      //             bck: ((parseInt(this.listsokurang[0].bck)) + (parseInt(this.listsokurang[0].bbk) - parseInt(this.listsokurang[0].bck))) - parseInt(this.hitung.qty[i]),
                      //         })
                      //     })
                    }
                    // axios.put("/api/so/" + this.up.nomor_so, {
                    //     status: 'Acc',
                    //     closebck: 'N'
                    // }).then(res => {
                    axios
                      .post("/api/history", {
                        nomor_dok: this.up.nomor_retur,
                        id_user: this.ambiluser.id,
                        notif: "Anda mendapatkan permintaan Retur baru!",
                        keterangan: "Mengirim permintaan ke Kordinator Sales",
                        jenis: "Rt",
                        tanggal: this.DateTime(),
                      })
                      .then((res) => {
                        this.load = false;
                        swalWithBootstrapButtons.fire(
                          "Sukses!",
                          "Permintaan retur berhasil di kirim.",
                          "success"
                        );
                        this.$router.push({
                          name: "returcomponent",
                        });
                      });
                    // })
                  })
                  .catch((error) => {
                    this.load = false;
                    Swal.fire({
                      icon: "error",
                      title: "Oops...",
                      text: "Cek kembali form retur anda!",
                    });
                  });
              } else {
                this.load = false;
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text: "Periksa kembali qty retur, jumlah tidak boleh kosong!",
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
            swalWithBootstrapButtons.fire(
              "Cancelled",
              "Batal melakukan permintaan ini :)",
              "error"
            );
          }
        });
    },
    validqty(index) {
      if (
        parseFloat(this.hitung.qty[index]) >
          parseFloat(this.checker[index].bbk) ||
        this.hitung.qty[index] < 0
      ) {
        this.hitung.qty[index] = this.checker[index].bbk;
      }
      this.total = 0;
      for (let i = 0; i < this.checker.length; i++) {
        if (this.hitung.qty[i] === undefined || this.hitung.qty[i] === "") {
          this.hitung.jumlah[i] = 0;
        } else {
          this.hitung.jumlah[i] = this.hitung.qty[i];
        }
        this.subtotal =
          (parseFloat(this.checker[i].harga) -
            parseFloat(this.checker[i].diskon)) *
          parseFloat(this.hitung.jumlah[i]);
        this.total += parseFloat(this.subtotal);
      }
    },
    validreal(index) {
      if (this.hitung.real[index] < 0) {
        this.hitung.real[index] = 0;
      } else if (this.hitung.real[index] > this.hitung.qty[index]) {
        this.hitung.real[index] = this.hitung.qty[index];
      }
      this.totalreal = 0;
      for (let i = 0; i < this.checker.length; i++) {
        if (this.hitung.real[i] === undefined || this.hitung.real[i] === "") {
          this.hitung.jumlahreal[i] = 0;
        } else {
          this.hitung.jumlahreal[i] = this.hitung.real[i];
        }
        this.totalreal +=
          (parseFloat(this.checker[i].harga) -
            parseFloat(this.checker[i].diskon)) *
          parseFloat(this.hitung.jumlahreal[i]);
      }
    },
    batal() {
      this.$router.push({
        name: "returcomponent",
      });
    },
    timer() {
      setInterval(() => {
        this.gagal = false;
      }, 5000);
    },
    ceknomor() {
      axios
        .get("/api/retur/" + this.up.nomor_retur + this.ambiluser.kode_groupso)
        .then((res) => {
          this.retur = res.data.data;
          if (this.up.nomor_retur.length === 15 && this.retur.length < 1) {
            this.bisakirim = true;
          } else {
            this.bisakirim = false;
          }
        });
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
        this.subtotal =
          (parseFloat(this.checker[i].harga) -
            parseFloat(this.checker[i].diskon)) *
          parseFloat(this.hitung.jumlah[i]);
        this.total += parseFloat(this.subtotal);
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
      this.dates =
        this.year +
        "-" +
        (this.month < 10 ? "0" : "") +
        this.month +
        "-" +
        this.day;
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
          title: "Apakah anda yakin?",
          text: "Ingin melakukan perbaikan data retur ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            axios
              .put("/api/retur/" + this.$route.params.nomor, {
                status: "Draft",
              })
              .then((res) => {
                swalWithBootstrapButtons.fire(
                  "Sukses!",
                  "Silahkan edit data anda di list draft retur.",
                  "success"
                );
                this.$router.push({
                  name: "returcomponent",
                });
              });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              "Cancelled",
              "Batal melakukan permintaan :)",
              "error"
            );
          }
        });
    },
    terima() {
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
          text: "Ingin menerima permintaan ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.load = true;
            if (this.statusretur === "Kordinator") {
              axios
                .put("/api/retur/" + this.$route.params.nomor, {
                  status: "Supervisor",
                })
                .then((res) => {
                  axios
                    .post("/api/history", {
                      nomor_dok: this.up.nomor_so,
                      id_user: this.ambiluser.id,
                      notif: "Permintaan retur diterima!",
                      keterangan:
                        "Permintaan retur nomor " +
                        this.$route.params.nomor +
                        " di terima kordinator sales",
                      jenis: "So",
                      tanggal: this.DateTime(),
                    })
                    .then((res) => {
                      axios
                        .post("/api/history", {
                          nomor_dok: this.$route.params.nomor,
                          id_user: this.ambiluser.id,
                          notif: "Permintaan retur diterima!",
                          keterangan: "Di terima oleh kordinator Sales",
                          jenis: "Rt",
                          tanggal: this.DateTime(),
                        })
                        .then((res) => {
                          axios
                            .post("/api/history", {
                              nomor_dok: this.$route.params.nomor,
                              id_user: this.ambiluser.id,
                              notif: "Menunggu konfirmasi Supervisor!",
                              keterangan: "Menunggu konfirmasi Sales SPV",
                              jenis: "Rt",
                              tanggal: this.DateTime(),
                            })
                            .then((res) => {
                              this.load = false;
                              swalWithBootstrapButtons.fire(
                                "Success!",
                                "Berhasil menerima permintaan.",
                                "success"
                              );
                              this.batal();
                            });
                        });
                    });
                });
            } else if (this.statusretur === "Supervisor") {
              axios
                .put("/api/retur/" + this.$route.params.nomor, {
                  status: "Dic",
                })
                .then((res) => {
                  axios
                    .post("/api/history", {
                      nomor_dok: this.up.nomor_so,
                      id_user: this.ambiluser.id,
                      notif: "Permintaan retur diterima!",
                      keterangan:
                        "Permintaan retur nomor " +
                        this.$route.params.nomor +
                        " di terima kordinator SPV Sales",
                      jenis: "So",
                      tanggal: this.DateTime(),
                    })
                    .then((res) => {
                      axios
                        .post("/api/history", {
                          nomor_dok: this.$route.params.nomor,
                          id_user: this.ambiluser.id,
                          notif: "Permintaan retur diterima!",
                          keterangan: "Diterima Sales SPV",
                          jenis: "Rt",
                          tanggal: this.DateTime(),
                        })
                        .then((res) => {
                          axios
                            .post("/api/history", {
                              nomor_dok: this.$route.params.nomor,
                              id_user: this.ambiluser.id,
                              notif: "Menunggu konfirmasi Dic!",
                              keterangan:
                                "Menunggu konfirmasi DIC barang masuk",
                              jenis: "Rt",
                              tanggal: this.DateTime(),
                            })
                            .then((res) => {
                              this.load = false;
                              swalWithBootstrapButtons.fire(
                                "Success!",
                                "Berhasil menerima permintaan.",
                                "success"
                              );
                              this.batal();
                            });
                        });
                    });
                });
            } else if (this.statusretur === "Dic") {
              axios
                .put("/api/retur/" + this.$route.params.nomor, {
                  status: "Acc",
                })
                .then((res) => {
                  axios
                    .post("/api/history", {
                      nomor_dok: this.up.nomor_so,
                      id_user: this.ambiluser.id,
                      notif: "Permintaan retur diterima!",
                      keterangan:
                        "Permintaan retur nomor " +
                        this.$route.params.nomor +
                        " di terima oleh DIC barang masuk",
                      jenis: "So",
                      tanggal: this.DateTime(),
                    })
                    .then((res) => {
                      axios
                        .post("/api/history", {
                          nomor_dok: this.$route.params.nomor,
                          id_user: this.ambiluser.id,
                          notif: "Permintaan retur diterima!",
                          keterangan: "Diterima DIC barang masuk",
                          jenis: "Rt",
                          tanggal: this.DateTime(),
                        })
                        .then((res) => {
                          axios
                            .post("/api/history", {
                              nomor_dok: this.$route.params.nomor,
                              id_user: this.ambiluser.id,
                              notif:
                                "BCK Retur di kirimkaan ke Gudang bongkar muat!",
                              keterangan:
                                "Mengirim BCM retur ke gudang bongkar/muat",
                              jenis: "Rt",
                              tanggal: this.DateTime(),
                            })
                            .then((res) => {
                              this.load = false;
                              swalWithBootstrapButtons.fire(
                                "Success!",
                                "Berhasil menerima permintaan.",
                                "success"
                              );
                              this.batal();
                            });
                        });
                    });
                });
            }
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              "Cancelled",
              "Batal menerima permintaan :)",
              "error"
            );
          }
        });
    },
    kirimbbm() {
      // axios
      //   .put("/api/retur/" + this.$route.params.nomor, {
      //     status: "Selesai",
      //   })
      //   .then((res) => {
      for (let i = 0; i < this.checker.length; i++) {
        // axios.put("/api/listretur/" + this.checker[i].id, {
        //   real: this.hitung.real[i],
        // });
        axios
          .get("/api/listso/" + this.checker[i].id_so + "/edit")
          .then((res) => {
            this.listsonya = res.data.data;
            console.log(this.listsonya[0].bbk);
            console.log(this.listsonya[0].bck);
            console.log(this.listsonya[0].closeso);
            console.log(this.listsonya[0].qtyretur);
            console.log(this.listsonya[0].qty);
          });
      }
      // });
    },
  },
};
</script>

<style></style>
