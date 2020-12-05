<template>
  <div class="container">
    <div class="row row-cols-2">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor Permintaan :</label>
          <input
            @input="cekpr()"
            v-model="upload.nomor_rso"
            type="text"
            maxlength="15"
            class="form-control col-12"
            :class="{ 'is-valid': aktif, 'is-invalid': !aktif }"
          />
        </div>
        <div class="form-group">
          <label>Tanggal :</label>
          <input
            v-model="upload.tanggal_rso"
            type="date"
            :min="now()"
            class="form-control col-12"
          />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Customer</label>
          <select
            v-model="upload.kode_customer"
            name="customer"
            class="col-12 form-control"
            disabled
          >
            <option value="default">Gudang Stok</option>
          </select>
        </div>
        <div class="form-group">
          <label>Diminta</label>
          <input
            type="text"
            class="form-control"
            value="Inventory Control"
            disabled
          />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>keterangan</label>
          <textarea
            v-model="upload.keterangan"
            name="keterangan"
            class="form-control col-12"
          ></textarea>
        </div>
      </div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
      <button @click="showmodal()" class="btn btn-orange mt-2">
        + Tambah Item
      </button>
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
              <th>Nama Barang</th>
              <th>Satuan</th>
              <th>Qty</th>
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
              <td style="text-align: center">
                <input
                  v-model="hitung.qty[index]"
                  type="number"
                  class="form-control"
                />
              </td>
              <td style="text-align: center">
                <textarea
                  v-model="hitung.keterangan[index]"
                  class="form-control"
                ></textarea>
              </td>
              <td>
                <button
                  @click="hapus(index)"
                  style="text-align: center"
                  class="btn btn-danger"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row mt-2">
      <button @click="kirimpr()" class="btn-success btn ml-3">
        Kirim Permintaan
      </button>
    </div>
    <div
      class="modal fade"
      id="modal-form"
      tabindex="-1"
      data-backdrop="static"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Form Barang</h5>
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
            <div class="form-group">
              <label>Nama Barang</label>
              <div class="autocomplete"></div>
              <div
                class="input"
                @click="toggleVisible"
                v-text="custom ? custom.nama : ''"
              ></div>
              <div class="placeholder" v-if="custom == null" v-text="ket.nama">
                Pilih Barang
              </div>
              <div class="popover" v-show="visible">
                <input
                  type="text"
                  @keydown.up="up"
                  @keydown.down="down"
                  @keydown.enter="selectItem"
                  v-model="query"
                  placeholder="Masukan nama barang .."
                />
                <div class="optionbr" ref="optionList">
                  <ul>
                    <li
                      v-for="(match, index) in matches"
                      :key="match.kode"
                      v-text="match.nama"
                      :class="{ selected: selected == index }"
                      @click="itemClicked(index)"
                    ></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>kode Barang</label>
              <input
                v-model="ket.kode"
                type="text"
                name="qty"
                autocomplete="off"
                class="form-control"
                disabled
              />
            </div>
            <div class="form-group">
              <label>Satuan</label>
              <input
                v-model="ket.satuan"
                type="text"
                class="form-control"
                disabled
              />
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
            <button @click="tambahList()" type="button" class="btn btn-primary">
              Input Item
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
      upload: {
        nomor_rso: this.pr_nomor(),
        tanggal_rso: this.now(),
        kode_customer: "default",
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
      barangs: {},
      aktif: false,
      rso: {},
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
      return this.barang.filter((item) =>
        item.nama.toLowerCase().includes(this.query.toLowerCase())
      );
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
        this.load = false;
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

      var output =
        "IR-" + d.getFullYear() + "-" + (month < 10 ? "0" : "") + month + "-";
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
      this.ket.satuan = this.custom.satuan;
      this.ket.nama = this.custom.nama;
      this.visible = false;
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
      $("#modal-form").modal("show");
    },
    tambahList() {
      if (this.listpr.length == 0) {
        this.barangs = {
          kode: this.ket.kode,
          nama: this.ket.nama,
          satuan: this.ket.satuan,
        };
        this.listpr.push(this.barangs);
      } else {
        if (this.Filteredlist.length > 0) {
        } else {
          this.barangs = {
            kode: this.ket.kode,
            nama: this.ket.nama,
            satuan: this.ket.satuan,
          };
          this.listpr.push(this.barangs);
        }
      }
      (this.visible = true),
        (this.query = ""),
        (this.selected = 0),
        (this.custom = null),
        (this.ket = {
          nama: "Pilih Barang",
        }),
        $("#modal-form").modal("hide");
    },
    kirimpr() {
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
          text: "Ingin mengririm PR ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            if (this.listpr.length > 0) {
              this.aplus = "";
              this.aband = "";
              for (let i = 0; i < this.listpr.length; i++) {
                if (
                  this.hitung.qty[i] === undefined ||
                  this.hitung.qty[i] === undefined < 1 ||
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
                this.upload.status = "Purch";
                this.upload.pr = "Y";
                this.upload.id_user = this.ambiluser.id;
                this.upload.kode_groupso = "GR";
                console.log(this.upload);
                axios.post("/api/rso", this.upload).then((res) => {
                  for (let i = 0; i < this.listpr.length; i++) {
                    this.uplist = {
                      nomor_rso: this.upload.nomor_rso,
                      tanggal_rso: this.upload.tanggal_rso,
                      kode_barang: this.listpr[i].kode,
                      harga: 0,
                      qty: this.hitung.qty[i],
                      qty_tdktersedia: this.hitung.qty[i],
                      status: "Tidak Tersedia",
                      catatan: this.hitung.keterangan[i],
                      pr: "Y",
                      dateaccso: this.DateTime(),
                      idx: i,
                    };
                    axios.post("/api/listrso", this.uplist).then((res) => {});
                  }
                  axios.post("/api/history", {
                    nomor_dok: this.upload.nomor_rso,
                    id_user: this.ambiluser.id,
                    notif: "Anda mendapatkan permintaan PR baru!",
                    keterangan: "Mengirim permintaan ke purchasing",
                    jenis: "RSO",
                    tanggal: this.DateTime(),
                  });
                  this.load = false;
                  swalWithBootstrapButtons.fire(
                    "Terkirim!",
                    "PR Berhasil di kirim.",
                    "success"
                  );
                  this.$router.push({
                    name: "Prcomponent",
                  });
                });
              } else {
                this.load = false;
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text:
                    "Periksa kembali list item barang, qty tidak boleh kosong!",
                });
              }
            } else {
              this.load = false;
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Anda belum menginput data apapun",
              });
            }
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire(
              "Batal",
              "Permintaan ini di batalkan :)",
              "error"
            );
          }
        });
    },
    hapus(index) {
      this.listpr.splice(index, 1);
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
    cekpr() {
      axios.get("/api/rso/" + this.upload.nomor_rso).then((res) => {
        this.rso = res.data.data;
        if (this.upload.nomor_rso.length === 15 && this.rso.length === 0) {
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
