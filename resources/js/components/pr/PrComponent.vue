<template>
  <div class="container">
    <div class="form-group ml-n4 col-1 my-3 float-right">
      <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
      <input
        v-model="filter.nomor"
        type="text"
        class="form-control"
        placeholder="Nomor PR"
      />
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
      <select
        @change="cekstatus()"
        name="status"
        v-model="filter.status"
        class="form-control"
      >
        <option v-if="ambiluser.inventory === 1" value="Draft">Draft</option>
        <option value="Sent">{{ menu }}</option>
        <option value="Open">Open</option>
        <option value="Tolak">Di Tolak</option>
        <option value="Reqedit">Request Perbaikan</option>
        <option value="Selesai">Selesai</option>
      </select>
    </div>
    <div class="row">
      <router-link to="/data/pr/create" class="btn btn-success my-3"
        >+ Create PR</router-link
      >
    </div>
    <div id="overflow" class="border-top">
      <table
        v-if="filter.status !== 'Reqedit'"
        id="thead"
        class="table table-striped table-bordered"
        style="width: 100%"
      >
        <thead>
          <tr>
            <th>No</th>
            <th>Nomor PR</th>
            <th>Tanggal</th>
            <th>Permintaan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(lp, index) in FilteredPr" :key="index">
            <td style="text-align: center">{{ index + 1 }}</td>
            <td style="text-align: center">
              <router-link
                :to="{
                  name: 'viewpr',
                  params: { nomor: lp.nomor_pr },
                }"
                class="btn btn-none"
              >
                {{ lp.nomor_pr }}
              </router-link>
            </td>
            <td style="text-align: center">{{ lp.tanggal }}</td>
            <td style="text-align: center">{{ lp.user }}</td>
            <td style="text-align: center">
              <button @click="showhistory(lp)" class="btn btn-primary">
                Lihat Rincian
              </button>
              <button
                v-if="ambiluser.inventory === 1"
                @click="batalkan(lp)"
                class="btn btn-danger"
              >
                Batalkan
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <Circle5 id="load" v-if="load"></Circle5>
    </div>

    <!-- table edit  -->
    <div style="margin-top: -700px" id="overflow" class="border-top">
      <table
        v-if="filter.status === 'Reqedit'"
        id="thead"
        class="table table-striped table-bordered"
        style="width: 100%"
      >
        <thead>
          <tr>
            <th>No</th>
            <th>Nomor PR</th>
            <th>Tanggal</th>
            <th>Permintaan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(lp, index) in listedit" :key="index">
            <td style="text-align: center">{{ index + 1 }}</td>
            <td style="text-align: center">
              <router-link
                :to="{
                  name: 'viewpr',
                  params: { nomor: lp.nomor_pr },
                }"
                class="btn btn-none"
              >
                {{ lp.nomor_pr }}
              </router-link>
            </td>
            <td style="text-align: center">{{ lp.tanggal }}</td>
            <td style="text-align: center">{{ lp.user }}</td>
            <td style="text-align: center">
              <button @click="showhistory(lp)" class="btn btn-primary">
                Lihat Rincian
              </button>
              <button
                v-if="ambiluser.inventory === 1"
                @click="batalkan(lp)"
                class="btn btn-danger"
              >
                Batalkan
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- end table edit -->
    <div
      class="modal fade"
      id="modal-history"
      tabindex="-1"
      data-backdrop="static"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Rincian History PR</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table
              id="thead"
              class="table table-striped table-bordered"
              style="width: 100%"
            >
              <thead>
                <tr>
                  <th>tanggal</th>
                  <th>Nomor PR</th>
                  <th>keterangan</th>
                  <th>Oleh</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(hs, index) in historyview" :key="index">
                  <td>{{ hs.tanggal }}</td>
                  <td>{{ hs.nomor_dok }}</td>
                  <td>{{ hs.keterangan }}</td>
                  <td>{{ hs.user }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="modal-filter"
      tabindex="-1"
      data-backdrop="static"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div id="modal-width" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Mulai Tanggal</label>
              <input v-model="filter.mulaitanggal" type="date" class="form-control" />
            </div>
            <div class="form-group">
              <label>Sampai Tanggal</label>
              <input
                v-model="filter.sampaitanggal"
                type="date"
                class="form-control"
                :min="filter.mulaitanggal"
              />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">
              Save Change
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
      pr: [],
      historyview: {},
      statusnya: "",
      listso: {},
      openpo: 0,
      history: {},
      filter: {
        mulaitanggal: this.FirstDate(),
        sampaitanggal: this.today(),
        status: "Sent",
        nomor: "",
      },
      history: {},
      listpr: {},
      listhapus: {},
      menu: "Sent",
      edit: "N",
      listedit: {},
    };
  },
  created() {
    this.getPr();
  },
  computed: {
    FilteredPr() {
      var vm = this,
        lists = vm.pr;
      return _.filter(lists, function (query) {
        var tanggal =
            query.tanggal >= vm.filter.mulaitanggal &&
            query.tanggal <= vm.filter.sampaitanggal,
          nomorpr = vm.filter.nomor
            ? query.nomor_pr.toLowerCase().includes(vm.filter.nomor.toLowerCase())
            : true,
          status = vm.filter.status ? query.status == vm.filter.status : true;
        return tanggal && nomorpr && status;
      });
    },
  },
  methods: {
    getPr() {
      axios.get("/api/pr").then((res) => {
        if (this.ambiluser.inventory === 1) {
          this.menu = "Sent";
        } else {
          this.menu = "Request PR";
        }
        this.pr = res.data.data;
        this.load = false;
      });
    },
    getedit() {
      axios.get("/api/pr/data/listedit").then((res) => {
        this.listedit = res.data.data;
      });
    },
    showhistory(lp) {
      $("#modal-history").modal("show");
      axios.get("/api/history/data/" + lp.nomor_pr + "/Pr").then((res) => {
        this.historyview = res.data.data;
      });
    },
    batalkan(lp) {
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
          text: "Ingin menghapus PR ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Yakin",
          cancelButtonText: "Tidak!",
          reverseButtons: true,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.load = true;
            axios.get("/api/listpr/" + lp.nomor_pr).then((res) => {
              this.listpr = res.data.data;
              this.sisapo = 0;
              this.openpo = 0;
              this.statuspo = "";
              for (let i = 0; i < this.listpr.length; i++) {
                axios
                  .get("/api/listso/data/kembalikanpo/" + this.listpr[i].kode_barang)
                  .then((res) => {
                    this.listhapus = res.data.data;
                    for (let k = 0; k < this.listhapus.length; k++) {
                      if (this.listpr[i].qty >= this.listhapus[k].openpo) {
                        this.sisapo = this.listhapus[k].openpo + this.listhapus[k].sisapo;
                        this.openpo = 0;
                        this.statuspo = "N";
                        this.listpr[i].qty =
                          parseInt(this.listpr[i].qty) -
                          parseInt(this.listhapus[k].openpo);
                      } else {
                        this.openpo =
                          parseInt(this.listhapus[k].openpo) -
                          parseInt(this.listpr[i].qty);
                        if (this.listpr[i].qty > this.listhapus[k].qty) {
                          this.sisapo =
                            parseInt(this.listhapus[k].sisapo) +
                            parseInt(this.listhapus[k].open);
                        } else {
                          this.sisapo =
                            parseInt(this.listhapus[k].sisapo) +
                            parseInt(this.listpr[i].qty);
                        }
                        if (this.sisapo < 1) {
                          this.statuspo = "Y";
                        } else {
                          this.statuspo = "N";
                        }
                        this.listpr[i].qty =
                          parseInt(this.listpr[i].qty) -
                          parseInt(this.listhapus[k].openpo);
                        if (this.listpr[i].qty < 1) {
                          this.listpr[i].qty = 0;
                        }
                      }
                      axios.put("/api/listso/" + this.listhapus[k].id, {
                        sisapo: this.sisapo,
                        openpo: this.openpo,
                        statuspo: this.statuspo,
                      });
                    }
                  });
              }
              axios.delete("/api/pr/" + lp.nomor_pr).then((res) => {
                axios.get("/api/history/" + lp.nomor_pr).then((res) => {
                  this.history = res.data.data;
                  for (let o = 0; 0 < this.history.length; o++) {
                    axios.delete("/api/history/" + this.history[o].id);
                  }
                });
              });
              this.getPr();
              this.load = false;
              swalWithBootstrapButtons.fire(
                "Deleted!",
                "PR berhasil di hapus.",
                "success"
              );
            });
          } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire("Cancelled", "Batal menghapus PR :)", "error");
          }
        });
    },
    showfilter() {
      $("#modal-filter").modal("show");
    },
    FirstDate() {
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
        this.year + "-" + (this.month < 10 ? "0" : "") + this.month + "-" + "01";
      this.times =
        this.hours +
        ":" +
        this.minute +
        ":" +
        (this.seconds < 10 ? "0" : "") +
        this.seconds;
      this.datetimes = this.dates;
      return this.datetimes;
    },
    today() {
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
      this.datetimes = this.dates;
      return this.datetimes;
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
    cekstatus() {
      this.getedit();
    },
  },
};
</script>

<style></style>
