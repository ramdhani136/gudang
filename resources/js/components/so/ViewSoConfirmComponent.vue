<template>
  <div class="container">
    <div class="row row-cols-2" v-for="vso in so" :key="vso.nomor_so">
      <div class="col-4">
        <div class="form-group">
          <label>Nomor SO :</label>
          <input v-model="vso.nomor_so" type="text" class="form-control col-12" disabled />
        </div>
        <div class="form-group">
          <label>Tanggal :</label>
          <input v-model="vso.tanggal_so" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled />
        </div>
        <div class="form-group">
          <label>Tanggal Kirim :</label>
          <input v-model="vso.tanggal_kirim" type="date" @change="validate()" :min="now()" class="form-control col-12" disabled />
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Customer</label>
          <select v-model="vso.kode_customer" name="customer" class="col-12 form-control" disabled>
            <option :value="vso.kode_customer">{{ vso.customer }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>Marketing</label>
          <select v-model="vso.nip_sales" name="marketing" class="col-12 form-control" disabled>
            <option :value="vso.nip_sales">{{ vso.sales }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>keterangan</label>
          <textarea v-model="vso.keterangan" name="keterangan" class="form-control col-12" disabled></textarea>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <label>Distribusi :</label>
          <select class="form-control" v-model="vso.distribusi" @click="ifkirim()" @change="aksidistribusi()" disabled>
            <option value="default">- Masukan pilihan anda -</option>
            <option value="kirim">Di Kirim</option>
            <option value="ambil">Ambil Sendiri</option>
          </select>
        </div>
        <div class="form-group">
          <label>Lokasi</label>
          <input @click="clikdistribusi()" v-model="vso.lokasi" name="alamat" class="form-control col-12" disabled />
        </div>
        <div class="form-group">
          <label>Alamat</label>
          <textarea v-model="vso.alamat" name="alamat" class="form-control col-12" disabled></textarea>
        </div>
      </div>
    </div>
    <div id="total" class="mt-3 ml-auto mr-3">Total Invoice :&nbsp; {{ ket.total | currency }}</div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
      <div class="row mt-1 mx-auto col-12">
        <Circle5 id="load3" v-if="load"></Circle5>
        <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width: 100%">
          <thead>
            <tr></tr>
            <tr>
              <th>No</th>
              <th>Nama Barang</th>
              <th>Qty</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Diskon</th>
              <th>Sub Total</th>
              <th style="width: 10%">Status</th>
              <th v-if="lstatus === 'Tidak Tersedia'">Estimasi</th>
              <th>Sudah Kirim</th>
              <th>Sisa SO</th>
              <th>Kubikasi</th>
              <th>Tonase</th>
            </tr>
          </thead>
          <tbody>
            <tr></tr>
            <tr v-for="(ch, index) in listso" :key="index">
              <td style="text-align: center">{{ index + 1 }}</td>
              <td>{{ ch.nama_barang }}</td>
              <td style="text-align: center">{{ ch.qty }}</td>
              <td style="text-align: center">{{ ch.satuan }}</td>
              <td style="text-align: center">{{ ch.harga | currency }}</td>
              <td style="text-align: center">{{ ch.diskon | currency }}</td>
              <td>{{ ((parseFloat(ch.harga) - parseFloat(ch.diskon)) * parseFloat(ch.qty)) | currency }}</td>
              <td style="text-align: center">{{ lstatus }}</td>
              <td v-if="lstatus === 'Tidak Tersedia'" style="text-align: center">
                {{ ch.tgl_datang }}
              </td>
              <td>{{ ch.bbk }}</td>
              <td>{{ ch.qty - ch.bbk }}</td>
              <td>{{ parseFloat(ch.qty) * parseFloat(ch.kubikasi) }}</td>
              <td>{{ parseFloat(ch.qty) * parseFloat(ch.tonase) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row mt-2" v-for="(lso, index) in so" :key="index">
      <button v-if="lso.status == 'Sent' && ambiluser.susales === 1" @click="confirmSO(lso)" class="btn-orange btn ml-3">Terima SO</button>
      <button @click="showModal()" v-if="lso.status == 'Sent' && ambiluser.susales === 1" class="btn-danger btn ml-1">Tolak SO</button>

      <button v-if="lso.status == 'Kordinator' && ambiluser.kordisales === 1" @click="kordinconfirm(lso)" class="btn-orange btn ml-3">Terima SO</button>

      <button v-if="lso.status == 'Kordinator' && ambiluser.kordisales === 1" @click="showModal()" class="btn-danger btn ml-1">Tolak SO</button>

      <button v-if="lso.status == 'Kordinator' && ambiluser.susales === 1 && ambiluser.kode_groupso === 'GR' && groupso === 'GR'" @click="kordinconfirm(lso)" class="btn-orange btn ml-3">Terima S</button>

      <button v-if="lso.status == 'Kordinator' && ambiluser.susales === 1 && ambiluser.kode_groupso === 'GR' && groupso === 'GR'" @click="showModal()" class="btn-danger btn ml-1">Tolak SO</button>
       <div class="tonkg">
          <b>Kubikasi : {{ kubikasi }} M3 |</b>
          <b>Tonase : {{ tonase }} KG</b>
        </div>
    </div>
    <div v-if="vso.status == 'Tolak'" v-for="vso in so" :key="vso.nomor_so" id="alastolak">
      <div v-for="(lso, index) in so" :key="index">
        <b>{{ lso.alastolak }}</b>
      </div>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Form Penolakan SO</h5>
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
          <div v-for="(lso, index) in so" :key="index" class="modal-footer">
            <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button v-if="ambiluser.susales === 1" type="button" @click="tolakSo(lso)" class="btn btn-primary">Konfirmasi Tolak</button>
            <button v-if="ambiluser.kordisales === 1" type="button" @click="tolakkordinatorSo(lso)" class="btn btn-primary">Konfirmasi Tolak</button>
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
      so: {},
      vso: {},
      listso: {},
      tujuan: "",
      up: {},
      load: true,
      statusSo: {
        so_open: "Y",
      },
      ket: {
        qtypesan: 0,
        total: 0,
      },
      uplist: [],
      aksiup: {},
      sk: {
        qty: [],
      },
      lstatus: "",
      groupso: "",
      kubikasi: 0,
      tonase: 0,
    };
  },
  created() {
    this.getSo();
  },
  computed: {},
  methods: {
    showModal() {
      $("#modal-form").modal("show");
    },
    getSo() {
      axios.get(`/api/so/${this.$route.params.id}`).then((res) => {
        this.so = res.data.data;
        if (this.so[0].statusso === "tersedia") {
          this.lstatus = "Tersedia";
        } else {
          this.lstatus = "Tidak Tersedia";
        }
        this.groupso = this.so[0].kode_groupso;
        axios.get("/api/listso/" + this.$route.params.id).then((res) => {
          this.listso = res.data.data;
          this.ket.tolak = 0;
          for (let i = 0; i < this.listso.length; i++) {
            this.ket.total += (parseFloat(this.listso[i].harga) - parseFloat(this.listso[i].diskon)) * parseFloat(this.listso[i].qty);
            this.kubikasi += parseFloat(this.listso[i].kubikasi) * parseFloat(this.listso[i].qty);
            this.tonase += parseFloat(this.listso[i].tonase) * parseFloat(this.listso[i].qty);
          }
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
    validateKirim() {
      if (this.so.tanggal_kirim < this.tglKirim()) {
        this.so.tanggal_kirim = this.tglKirim();
      }
    },
    confirmSO(lso) {
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
          text: "Ingin menerima SO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            this.up.nomor_so = lso.nomor_so;
            this.up.tanggal_so = lso.tanggal_so;
            this.up.tanggal_kirim = lso.tanggal_kirim;
            this.up.nomor_rso = lso.nomor_rso;
            this.up.keterangan = lso.keterangan;
            this.up.status = "Dic";
            this.up.tgl_spv = this.DateTime();
            this.up.spv = this.ambiluser.name;
            axios.put("/api/so/" + this.up.nomor_so, this.up).then((response) => {
              axios.post("/api/history", {
                nomor_dok: this.$route.params.id,
                id_user: this.ambiluser.id,
                notif: "So nomor : " + this.$route.params.id + " DI konfirmasi SPV",
                keterangan: "SO di terima Sales Supervisor",
                jenis: "So",
                tanggal: this.DateTime(),
              });
              axios.post("/api/history", {
                nomor_dok: this.$route.params.id,
                id_user: this.ambiluser.id,
                notif: "So nomor : " + this.$route.params.id + " DI konfirmasi SPV",
                keterangan: "Menunggu konfirmasi DIC",
                jenis: "So",
                tanggal: this.DateTime(),
              });
              this.$router.push({
                name: "soconfirm",
              });
              swalWithBootstrapButtons.fire("Selesai!", "SO Berhasil di terima !", "success");
            });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Batal", "SO Batal diterima :)", "error");
          }
        });
    },
    kordinconfirm(lso) {
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
          text: "Ingin menerima SO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            this.up.nomor_so = lso.nomor_so;
            this.up.tanggal_so = lso.tanggal_so;
            this.up.tanggal_kirim = lso.tanggal_kirim;
            this.up.nomor_rso = lso.nomor_rso;
            this.up.keterangan = lso.keterangan;
            this.up.status = "Sent";
            this.up.tgl_kordinator = this.DateTime();
            this.up.kordinator = this.ambiluser.name;
            axios.put("/api/so/" + this.up.nomor_so, this.up).then((response) => {
              axios.post("/api/history", {
                nomor_dok: this.$route.params.id,
                id_user: this.ambiluser.id,
                notif: "So nomor : " + this.$route.params.id + " DI konfirmasi Kordinator Sales",
                keterangan: "SO di terima Sales Kordinator",
                jenis: "So",
                tanggal: this.DateTime(),
              });
              axios.post("/api/history", {
                nomor_dok: this.$route.params.id,
                id_user: this.ambiluser.id,
                notif: "So nomor : " + this.$route.params.id + " DI konfirmasi kordinator",
                keterangan: "Menunggu konfirmasi Sales SPV",
                jenis: "So",
                tanggal: this.DateTime(),
              });
              this.$router.push({
                name: "soconfirm",
              });
              swalWithBootstrapButtons.fire("Selesai!", "SO Berhasil di terima !", "success");
            });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Batal", "SO Batal diterima :)", "error");
          }
        });
    },
    tolakSo(lso) {
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
          text: "Ingin menolak SO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            this.up.nomor_so = lso.nomor_so;
            this.up.tanggal_so = lso.tanggal_so;
            this.up.tanggal_kirim = lso.tanggal_kirim;
            this.up.nomor_rso = lso.nomor_rso;
            this.up.keterangan = lso.keterangan;
            this.up.status = "Tolak";
            axios.put("/api/so/" + this.up.nomor_so, this.up).then((response) => {
              $("#modal-form").modal("hide");
              axios.post("/api/history", {
                nomor_dok: this.$route.params.id,
                id_user: this.ambiluser.id,
                notif: "So nomor : " + this.$route.params.id + " Di tolak SPV",
                keterangan: "SO di tolak Sales Supervisor",
                jenis: "So",
                tanggal: this.DateTime(),
              });
              swalWithBootstrapButtons.fire("Sukses!", "SO berhasil di tolak !", "success");
              this.$router.push({
                name: "soconfirm",
              });
            });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal melakukan penolakan :)", "error");
          }
        });
    },
    tolakkordinatorSo(lso) {
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
          text: "Ingin menolak SO ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin!",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.value) {
            this.up.nomor_so = lso.nomor_so;
            this.up.tanggal_so = lso.tanggal_so;
            this.up.tanggal_kirim = lso.tanggal_kirim;
            this.up.nomor_rso = lso.nomor_rso;
            this.up.keterangan = lso.keterangan;
            this.up.status = "Tolak";
            axios.put("/api/so/" + this.up.nomor_so, this.up).then((response) => {
              $("#modal-form").modal("hide");
              axios.post("/api/history", {
                nomor_dok: this.$route.params.id,
                id_user: this.ambiluser.id,
                notif: "So nomor : " + this.$route.params.id + " Di tolak Kordinator Sales",
                keterangan: "SO di tolak Sales Kordinator",
                jenis: "So",
                tanggal: this.DateTime(),
              });
              swalWithBootstrapButtons.fire("Sukses!", "SO berhasil di tolak !", "success");
              this.$router.push({
                name: "soconfirm",
              });
            });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal melakukan penolakan :)", "error");
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
      console.log(this.datetimes);
      return this.datetimes;
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
</style>>
  
</style>

<style>
#alastolak {
  width: 100%;
  padding: 1%;
  border: solid 1px rgb(253, 98, 98);
  text-align: center;
  align-content: center;
  margin-top: 5px;
  background-color: rgb(252, 156, 156);
  border-radius: 3px;
  font-size: 1.1em;
  color: rgb(255, 68, 68);
}

#rsoverflowso {
  width: 100%;
  max-height: 275px;
  overflow-y: scroll;
  border-top: solid 1px #dee2e6;
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
