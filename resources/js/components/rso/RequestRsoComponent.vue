<template>
  <div class="container" @keyup.esc="resetForm()">
    <div class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor RSO:</label>
          <input v-model="rlist.nomor_rso" type="text" class="form-control col-12" disabled />
        </div>
        <div class="form-group">
          <label>Tanggal :</label>
          <input v-model="rlist.tanggal_rso" type="date" class="form-control col-12" disabled />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Customer</label>
          <select v-model="rlist.kode_customer" name="customer" class="col-12 form-control" disabled>
            <option v-for="custom in customers" :key="custom.kode" :value="custom.kode">{{ custom.nama }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>Sales</label>
          <select v-model="rlist.id_user" name="sales" class="col-12 form-control" disabled>
            <option :value="sl.id" v-for="(sl, index) in sales" :key="index">{{ sl.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>keterangan</label>
          <textarea v-model="rlist.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
        </div>
      </div>
    </div>
    <div id="dicoverflow" class="row mt-2 mx-auto">
      <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width: 100%">
        <thead>
          <tr v-for="u in form" :key="u.id">
            <th>No</th>
            <th>Item</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Status</th>
            <th v-if="u.status == 'Confirmed'">Estimasi Kedatangan</th>
            <th>Tersedia</th>
            <th>Tidak Tersedia</th>
            <th v-if="u.status == 'Sent'">Aksi</th>
          </tr>
        </thead>
        <tbody v-for="u in form" :key="u.id">
          <tr v-for="(list, index) in listrso" :key="list.nomor_rso">
            <td style="text-align: center">{{ index + 1 }}</td>
            <td>{{ list.nama_barang }}</td>
            <td>{{ list.qty }}</td>
            <td style="text-align: center">{{ list.satuan }}</td>
            <td style="text-align: center">
              <select v-model="list.status" name="change" class="form-control" disabled>
                <option value="Tersedia">Tersedia</option>
                <option value="Tersedia Sebagian">Tersedia Sebagian</option>
                <option value="Tidak Tersedia">Tidak Tersedia</option>
              </select>
            </td>
            <td v-if="u.status == 'Confirmed'">
              <div class="form-group">
                <input style="text-align: center" v-model="list.tgl_datang" type="text" class="form-control col-12 z1" disabled />
              </div>
            </td>
            <td>
              <div class="form-group">
                <input v-model="list.qty_tersedia" type="number" class="form-control col-12 z1" disabled />
              </div>
            </td>
            <td>
              <div class="form-group">
                <input v-model="list.qty_tdktersedia" type="number" class="form-control col-12 z1" disabled />
              </div>
            </td>
            <td>
              <button v-if="u.status == 'Sent' && list.booking == 'N'" @click="showModal(list)" class="btn btn-primary">Update</button>
            </td>
          </tr>
        </tbody>
      </table>
      <Circle5 id="load2" v-if="load"></Circle5>
    </div>
    <div v-for="up in form" :key="up.id">
      <button v-if="up.status == 'Sent'" @click="ConfirmRso(up)" class="btn btn-success mt-2">Konfirmasi</button>
    </div>
    <div v-for="list in listrso" :key="list.nomor_rso">
      <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div id="modal-width" class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Form Status</h5>
              <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Kode Barang</label>
                <input v-model="dic.kode" type="text" autocomplete="off" class="form-control" disabled />
              </div>
              <div class="form-group">
                <label>Nama Barang</label>
                <input v-model="dic.nama_barang" type="text" autocomplete="off" class="form-control" disabled />
              </div>
              <div class="form-group">
                <label>Jumlah Permintaan</label>
                <input v-model="dic.jumlahrso" type="number" autocomplete="off" class="form-control" disabled />
              </div>
              <div class="form-group">
                <label>Satuan</label>
                <input v-model="dic.satuan" type="text" autocomplete="off" class="form-control" disabled />
              </div>
              <div class="form-group">
                <label>Catatan</label>
                <textarea v-model="dic.catatan" name="catatan" class="form-control" disabled></textarea>
              </div>
              <div class="form-group">
                <label>Status Barang</label>
                <select @change="updateStatus(list)" v-model="update.status" name="status" class="form-control">
                  <option value="Tersedia">Tersedia</option>
                  <option value="Tersedia Sebagian">Tersedia Sebagian</option>
                  <option value="Tidak Tersedia">Tidak Tersedia</option>
                </select>
              </div>
              <div v-if="update.status == 'Tersedia Sebagian'" class="form-group">
                <label>Jumlah Tersedia</label>
                <input v-model="update.qty_tersedia" type="number" placeholder="Jumlah Barang Tersedia" autocomplete="off" class="form-control" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button v-if="selesaikah" type="button" @click="updateStatusklik(list)" class="btn btn-primary">Save changes</button>
            </div>
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
      form: {},
      customers: {},
      listrso: [],
      inputlrso: {
        status: "Confirmed",
      },
      inprso: {
        status: "Draft",
      },
      dic: {},
      status: "",
      update: {},
      urso: {},
      coba: 0,
      akses: "",
      up: {},
      load: true,
      selesaikah: false,
      sales: {},
      ceklist: {},
      aplus: 0,
      blus: "",
      aband: "",
      b: "",
    };
  },
  created() {
    this.getRso();
    this.tujuanConfirm();
  },
  mounted() {
    axios.get("/api/customer").then((res) => (this.customers = res.data.data));
  },
  methods: {
    getRso() {
      axios.get(`/api/rso/${this.$route.params.id}`).then((res) => {
        this.form = res.data.data;
        axios.get(`/api/listrso/${this.$route.params.id}`).then((res) => {
          this.listrso = res.data.data;
          axios.get("/api/user").then((res) => {
            this.sales = res.data.data;
            this.load = false;
          });
        });
      });
    },
    showModal() {
      $("#modal-form").modal("show");
    },
    showModal(list) {
      this.getRso();
      this.dic.kode = list.lkode_barang;
      this.dic.catatan = list.catatan;
      this.dic.id = list.id;
      this.dic.nama_barang = list.nama_barang;
      this.update.status = list.status;
      this.update.qty_tersedia = list.qty_tersedia;
      this.dic.jumlahrso = list.qty;
      this.dic.satuan = list.satuan;
      $("#modal-form").modal("show");
    },
    updateStatus(list) {
      this.selesaikah = false;
      axios.put(`/api/listrso/` + this.dic.id, this.update).then((response) => {
        this.getRso();
        if (this.update.status === "Tidak Tersedia") {
          this.update.qty_tersedia = "";
          this.update.so_tersedia = "Y";
          this.update.so_tdktersedia = "N";
          this.update.qty_tdktersedia = this.dic.jumlahrso;
          axios.put(`/api/listrso/` + this.dic.id, this.update);
          this.tujuanConfirm();
          this.selesaikah = true;
        } else if (this.update.status === "Tersedia") {
          this.getRso();
          this.update.so_tdktersedia = "Y";
          this.update.so_tersedia = "N";
          this.update.qty_tersedia = this.dic.jumlahrso;
          this.update.qty_tdktersedia = "";
          axios.put(`/api/listrso/` + this.dic.id, this.update).then((response) => {
            this.tujuanConfirm();
            this.selesaikah = true;
          });
        } else if (this.update.status === "Tersedia Sebagian") {
          this.getRso();
          this.update.so_tdktersedia = "N";
          this.update.so_tersedia = "N";
          axios.put(`/api/listrso/` + this.dic.id, this.update).then((response) => {
            this.tujuanConfirm();
            this.selesaikah = true;
          });
        }
      });
    },
    updateStatusklik(list) {
      if (this.update.status === "Tersedia Sebagian") {
        this.getRso();
        this.update.qty_tdktersedia = this.dic.jumlahrso - this.update.qty_tersedia;
        axios.put(`/api/listrso/` + this.dic.id, this.update).then((response) => {
          this.resetForm();
          this.tujuanConfirm();
          $("#modal-form").modal("hide");
        });
      } else if (this.update.status === "Tersedia") {
        this.resetForm();
        this.tujuanConfirm();
        $("#modal-form").modal("hide");
      } else if (this.update.status === "Tidak Tersedia") {
        this.resetForm();
        this.tujuanConfirm();
        $("#modal-form").modal("hide");
      }
    },
    resetForm() {
      this.getRso();
      this.dic.catatan = "";
      this.dic.id = "";
      this.dic.nama_barang = "";
      this.update.status = "";
      this.update.qty_tersedia = "";
      this.update.qty_tdktersedia = "";
      this.dic.jumlahrso = "";
      this.dic.satuan = "";
      this.coba = 0;
      this.dic.kode = "";
    },
    ConfirmRso(up) {
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
          text: "Anda ingin mengkonfirmasi RSO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, Yakin!",
          cancelButtonText: "Batalkan!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            axios.get("/api/listrso/" + up.nomor_rso).then((res) => {
              this.ceklist = res.data.data;
              this.b = "";
              this.bplus = "";
              this.aband = "";
              this.aplus = 0;
              for (let i = 0; i < this.ceklist.length; i++) {
                if (this.ceklist[i].qty_tersedia === "" || this.ceklist[i].qty_tersedia < 1 || this.ceklist[i].qty_tersedia === null || this.ceklist[i].qty_tersedia === undefined) {
                  this.ceklist[i].qty_tersedia = 0;
                }
                if (this.ceklist[i].qty_tdktersedia === "" || this.ceklist[i].qty_tdktersedia < 1 || this.ceklist[i].qty_tdktersedia === null || this.ceklist[i].qty_tdktersedia === undefined) {
                  this.ceklist[i].qty_tdktersedia = 0;
                }
                this.aplus = parseInt(this.ceklist[i].qty_tersedia) + parseInt(this.ceklist[i].qty_tdktersedia);
                if (this.aplus < 1) {
                  this.b = "N";
                } else {
                  this.b = "Y";
                }
                this.bplus += this.b;
                this.aband += "Y";
              }
              if (this.bplus === this.aband) {
                this.urso.kode_customer = up.kode_customer;
                this.urso.status = this.akses;
                this.urso.nomor_rso = up.nomor_rso;
                this.urso.id_user = up.id_user;
                this.urso.tanggal_rso = up.tanggal_rso;
                this.urso.dic = this.ambiluser.name;
                this.urso.tgl_dic = this.DateTime();
                axios.put(`/api/rso/` + this.urso.nomor_rso, this.urso).then((response) => {
                  this.up.booking = "Y";
                  axios.put(`/api/listrso/data/booking/` + this.urso.nomor_rso, this.up);
                  axios.post("/api/history", {
                    nomor_dok: this.$route.params.id,
                    id_user: this.ambiluser.id,
                    notif: "Nomor RSO " + this.$route.params.id + " di konfirmasi Inventory Control",
                    keterangan: "RSO di konfirmasi Inventory Control",
                    jenis: "RSO",
                    tanggal: this.DateTime(),
                  });
                  if (this.akses === "Purch") {
                    axios.post("/api/history", {
                      nomor_dok: this.$route.params.id,
                      id_user: this.ambiluser.id,
                      notif: "Menunggu konfirmasi Purchasing",
                      keterangan: "Menunggu konfirmasi Purchasing",
                      jenis: "RSO",
                      tanggal: this.DateTime(),
                    });
                  }
                  this.load = false;
                  this.$router.push({
                    name: "dic",
                  });
                });
                swalWithBootstrapButtons.fire("Berhasil!", "RSO berhasil di konfirmasi!.", "success");
              } else {
                this.load = false;
                Swal.fire({
                  icon: "error",
                  title: "Oops...",
                  text: "Periksa form anda kembali, pastikan semua item sudah di konfirmasi!",
                });
              }
            });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            this.load = false;
            swalWithBootstrapButtons.fire("Batal", "RSO batal di konfirmasi :)", "error");
          }
        });
    },
    tujuanConfirm() {
      axios.get(`/api/listrso/${this.$route.params.id}`).then((res) => {
        this.listrso = res.data.data;

        for (let index = 0; index < this.listrso.length; index++) {
          this.coba += this.listrso[index].qty_tdktersedia;
        }
        if (this.coba > 0) {
          this.akses = "Purch";
        }
        if (this.coba <= 0) {
          this.akses = "Confirmed";
        }
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
      this.dates = this.year + "-" + (this.month < 10 ? "0" : "") + this.month + "-" + this.day;
      this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? "0" : "") + this.seconds;
      this.datetimes = this.dates + " " + this.times;
      return this.datetimes;
    },
  },
};
</script>

<style>
#dicoverflow {
  width: 100%;
  max-height: 280px;
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
  z-index: 10;
}
</style>
