<template>
  <div class="container">
    <div class="row row-cols-2">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor BBK :</label>
          <input
            v-model="up.bbk"
            type="text"
            class="form-control col-12"
            disabled
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
            disabled
          />
        </div>
        <div class="form-group">
          <label>Nomor BCK</label>
          <input
            v-model="up.nomor_bck"
            @click="showSo()"
            type="text"
            class="form-control"
            placeholder="Pilih Sales Order"
            disabled
          />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Nomor SO</label>
          <input
            v-model="ket.nomor_so"
            type="text"
            class="form-control"
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
          <label>Distribusi</label>
          <input
            v-model="ket.distribusi"
            type="text"
            class="form-control"
            disabled
          />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Lokasi</label>
          <input
            v-model="ket.lokasi"
            type="text"
            class="form-control"
            disabled
          />
        </div>
        <div class="form-group">
          <label>Alamat</label>
          <textarea
            v-model="ket.alamat"
            name="keterangan"
            class="form-control col-12"
            disabled
          ></textarea>
        </div>
        <div class="form-group">
          <label>keterangan</label>
          <textarea
            v-model="up.keterangan"
            name="keterangan"
            class="form-control col-12"
            disabled
          ></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div id="total" class="mt-3 ml-auto mr-3">
        Total Invoice :&nbsp; {{ total | currency }}
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
              <th>Kode Barang</th>
              <th style="width: 25%">Nama Barang</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Diskon</th>
              <th>Qty BCK</th>
              <th>Qty termuat</th>
              <th>Keterangan</th>
              <th>Kubikasi</th>
              <th>Tonase</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(list, index) in listbbk" :key="index">
              <td style="text-align: center">{{ index + 1 }}</td>
              <td>{{ list.kode_barang }}</td>
              <td>{{ list.nama_barang }}</td>
              <td style="text-align: center">{{ list.satuan }}</td>
              <td style="text-align: center">{{ list.harga | currency }}</td>
              <td style="text-align: center">{{ list.diskon | currency }}</td>
              <td style="text-align: center">{{ hitung.qtynya[index] }}</td>
              <td style="text-align: center">
                <input
                  style="width: 120px"
                  @input="validqty(index)"
                  v-model="inputin.qty[index]"
                  type="number"
                  class="form-control"
                  disabled
                />
              </td>
              <td style="text-align: center">
                <textarea
                  style="width: 200px"
                  v-model="hitung.keterangan[index]"
                  class="form-control"
                  disabled
                ></textarea>
              </td>
              <td>
                {{ parseFloat(inputin.qty[index]) * parseFloat(list.kubikasi) }}
              </td>
              <td>
                {{ parseFloat(inputin.qty[index]) * parseFloat(list.tonase) }}
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
      <!--  <button @click="submitBck()" class="btn-success btn ml-2" >
                    Simpan  
                </button> -->
      <button @click="batal()" class="btn-primary btn ml-4">Kembali</button>
      <button @click="print()" class="btn-none btn ml-1">Print</button>
      <div class="tonkg">
        <b>Kubikasi : {{ kubikasi }} M3 |</b>
        <b>Tonase : {{ tonase }} KG</b>
      </div>
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
                @change="pilihBck(aktif)"
                v-model="aktif"
                class="form-control"
              >
                <option
                  :value="aktif"
                  v-for="(aktif, index) in bckaktif"
                  :key="index"
                >
                  {{ aktif.bck }}
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
                    <th style="text-align: center">jumlah BCK</th>
                    <th style="text-align: center">Pilih</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(ls, index) in listbck" :key="index">
                    <td style="text-align: center">{{ index + 1 }}</td>
                    <td>{{ ls.kode_barang }}</td>
                    <td>{{ ls.nama_barang }}</td>
                    <td style="text-align: center">{{ ls.satuan }}</td>
                    <td style="text-align: center">{{ ls.qty }}</td>
                    <td style="text-align: center">
                      <input
                        @change="pilihlistchecker()"
                        v-model="checker"
                        type="checkbox"
                        :value="ls"
                        disabled
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
import { Circle5 } from "vue-loading-spinner";
export default {
  components: {
    Circle5,
  },
  data() {
    return {
      load: false,
      up: {},
      ket: {
        sisaso: [],
        sisasopilih: [],
        tersedia: [],
      },
      aktif: {},
      listsisa: {},
      checker: [],
      listbcm: {},
      uploadlist: [],
      hitung: {
        qty: [],
        keterangan: [],
        jumlah: [],
        qtynya: [],
      },
      inputin: {
        qty: [],
      },
      uplistsisa: {
        qty_masuk: 0,
      },
      bckaktif: {},
      listso: {},
      gagal: false,
      uplist: {},
      qty: 0,
      listrso: {},
      nilai: null,
      harga: 0,
      subtotal: 0,
      total: 0,
      listbck: {},
      uplistrso: {},
      qtyupdate: 0,
      upso: {},
      listbbk: {},
      bbk: {},
      viewqtybck: {},
      kubikasi: 0,
      tonase: 0,
    };
  },
  created() {
    this.getBckAktif();
    this.resetForm();
    this.timer();
    this.getBbk();
  },
  computed: {},
  methods: {
    getBbk() {
      axios.get("/api/bbk/" + this.$route.params.bbk).then((res) => {
        this.bbk = res.data.data;
        this.up = {
          bbk: this.bbk[0].bbk,
          tanggal: this.bbk[0].tanggal,
          nomor_bck: this.bbk[0].nomor_bck,
        };
        this.ket = {
          nomor_so: this.bbk[0].nomor_so,
          customer: this.bbk[0].customer,
          distribusi: this.bbk[0].distribusi,
          alamat: this.bbk[0].alamat,
          lokasi: this.bbk[0].lokasi,
          keterangan: this.bbk[0].keterangan,
        };
        axios.get("/api/listbbk/" + this.bbk[0].bbk).then((res) => {
          this.listbbk = res.data.data;
          for (let k = 0; k < this.listbbk.length; k++) {
            this.total +=
              parseFloat(this.listbbk[k].qty) *
              (parseFloat(this.listbbk[k].harga) -
                parseFloat(this.listbbk[k].diskon));
            this.kubikasi +=
              parseFloat(this.listbbk[k].qty) *
              parseFloat(this.listbbk[k].kubikasi);
            this.tonase +=
              parseFloat(this.listbbk[k].qty) *
              parseFloat(this.listbbk[k].tonase);
            this.inputin.qty[k] = this.listbbk[k].qty;
            axios
              .get(
                "/api/listbck/view/" +
                  this.up.nomor_bck +
                  "/" +
                  this.listbbk[k].kode_barang +
                  "/" +
                  this.listbbk[k].idx
              )
              .then((res) => {
                this.viewqtybck = res.data.data;
                this.hitung.qtynya.push(900);
                this.hitung.qtynya.splice(this.listbbk.length, 1);
                this.hitung.qtynya[k] = this.viewqtybck[0].qty;
                this.hitung.keterangan[k] = this.listbbk[k].keterangan;
              });
          }
          this.load = false;
        });
      });
    },
    getBckAktif() {
      axios.get("/api/bck/data/aktif").then((res) => {
        this.bckaktif = res.data.data;
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
    bbk_nomor() {
      var d = new Date();
      var month = d.getMonth() + 1;

      var output =
        "BBK-" + d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-";
      return output;
    },
    showSo() {
      $("#modal-po").modal("show");
    },
    resetForm() {
      this.checker = [];
    },
    pilihBck(aktif) {
      this.hitung.qty = [];
      this.checker = [];
      this.ket.customer = aktif.customer;
      this.ket.nomor_so = aktif.nomor_so;
      this.ket.lokasi = aktif.lokasi;
      this.ket.alamat = aktif.alamat;
      this.ket.distribusi = aktif.distribusi;
      this.up.nomor_bck = aktif.bck;

      axios.get("/api/listbck/data/aktif/" + this.up.nomor_bck).then((res) => {
        this.listbck = res.data.data;
        for (let i in this.listbck) {
          this.checker.push(this.listbck[i]);
        }
      });
    },
    checklist() {
      $("#modal-po").modal("hide");
    },
    submitBck() {
      let yakin = confirm("Yakin ingin membuat Bukti Checker ini?");
      if (yakin === true) {
        if (this.checker.length > 0) {
          axios.post("/api/bbk", this.up).then((res) => {
            for (let i = 0; i < this.checker.length; i++) {
              this.nomor_bbk = this.up.bbk;
              this.kode_barang = this.checker[i].kode_barang;
              this.qty = 0;
              this.qty = parseFloat(this.hitung.qty[i]);
              this.harga = this.checker[i].harga;
              this.qty_bck = this.checker[i].qty;
              this.keterangan = this.hitung.keterangan[i];
              this.uplist = {
                nomor_bbk: this.nomor_bbk,
                kode_barang: this.kode_barang,
                qty: this.qty,
                qty_bck: this.qty_bck,
                keterangan: this.keterangan,
                harga: this.harga,
              };
              axios.post("/api/listbbk", this.uplist).then((res) => {
                axios
                  .put("/api/bck/" + this.up.nomor_bck, {
                    status: "close",
                  })
                  .then((res) => {
                    axios
                      .get(
                        "/api/listrso/data/" +
                          this.checker[i].nomor_rso +
                          "/" +
                          this.checker[i].kode_barang
                      )
                      .then((res) => {
                        this.listrso = res.data.data;
                        for (let o = 0; o < this.listrso.length; o++) {
                          if (
                            parseFloat(this.hitung.qty[i]) < this.checker[i].qty
                          ) {
                            if (this.checker[i].statusso === "tersedia") {
                              this.qtyupdate =
                                parseFloat(this.listrso[o].keluar_tersedia) -
                                parseFloat(this.checker[i].qty) +
                                parseFloat(this.hitung.qty[i]);
                              this.uplistrso = {
                                out_yes: this.qtyupdate,
                                sotersedia_close: "N",
                              };
                              axios
                                .put(
                                  "/api/listrso/" + this.listrso[o].id,
                                  this.uplistrso
                                )
                                .then((res) => {
                                  this.upso = {
                                    status: "Acc",
                                  };
                                  axios
                                    .put(
                                      "/api/so/" + this.listbck[i].nomor_so,
                                      this.upso
                                    )
                                    .then((res) => {
                                      this.$router.push({
                                        name: "distribusibbk",
                                      });
                                    });
                                });
                            } else {
                              this.qtyupdate =
                                parseFloat(this.listrso[o].keluar_tdktersedia) -
                                parseFloat(this.checker[i].qty) +
                                parseFloat(this.hitung.qty[i]);
                              this.uplistrso = {
                                out_no: this.qtyupdate,
                                sotdk_close: "N",
                              };
                              axios
                                .put(
                                  "/api/listrso/" + this.listrso[o].id,
                                  this.uplistrso
                                )
                                .then((res) => {
                                  this.upso = {
                                    status: "Acc",
                                  };
                                  axios
                                    .put(
                                      "/api/so/" + this.listbck[i].nomor_so,
                                      this.upso
                                    )
                                    .then((res) => {
                                      this.$router.push({
                                        name: "distribusibbk",
                                      });
                                    });
                                });
                            }
                          }
                          this.$router.push({
                            name: "distribusibbk",
                          });
                        }
                      });
                  });
              });
            }
          });
        } else {
          this.gagal = true;
        }
      }
    },
    validqty(index) {
      if (
        parseFloat(this.hitung.qty[index]) > parseFloat(this.checker[index].qty)
      ) {
        this.hitung.qty[index] = this.checker[index].qty;
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
    batal() {
      this.$router.push({
        name: "distribusibbk",
      });
    },
    timer() {
      setInterval(() => {
        this.gagal = false;
      }, 5000);
    },
    print() {
      var x = window.open(
        "/data/bbk/print/" + this.$route.params.bbk,
        "_blank"
      );
      x.focus();
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
