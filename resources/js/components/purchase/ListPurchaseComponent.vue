<template>
<div class="container" @keyup.esc="resetForm()">
    <div class="row row-cols-2" v-for="rlist in form" :key="rlist.nomor_rso">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor Rso :</label>
                <input v-model="rlist.nomor_rso" type="text" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="rlist.tanggal_rso" type="date" class="form-control col-12" disabled>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Customer</label>
                <select v-model="rlist.kode_customer" name="customer" class="col-12 form-control" disabled>
                    <option v-for="custom in customers" :key="custom.kode" :value="custom.kode">{{custom.nama}}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Marketing</label>
                <select v-model="rlist.nip_sales" name="marketing" class="col-12 form-control" disabled>
                    <option :value="rlist.nip_sales">{{rlist.sales}}</option>
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
        <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Estimasi Kedatangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(list,index) in listrso" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td>{{list.lkode_barang}}</td>
                    <td>{{list.nama_barang}}</td>
                    <td style="text-align:center">{{list.qty_tdktersedia}}</td>
                    <td style="text-align:center">{{list.satuan}}</td>
                    <td>
                        <div style="text-align:center" class="form-group">
                            <input v-if="list.acc_purch=='Y'" style="text-align:center" v-model="list.tgl_datang" type="text" class="form-control" disabled>
                            <button v-if="list.acc_purch=='N'" class="btn btn-orange">Permintaan di tolak</button>
                        </div>
                    </td>
                    <td style="text-align:center">
                        <button @click="showModal(list)" class="btn btn-primary">Update</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load2" v-if="load"></Circle5>
    </div>
    <div v-for="rs in form" :key="rs.nomor_id">
        <button @click="ConfirmRso(rs)" class="btn btn-success mt-2">Konfirmasi</button>
    </div>
    <div v-for="(list,index) in listrso" :key="index">
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
                            <label>Nama Barang</label>
                            <input v-model="dic.nama_barang" type="text" autocomplete="off" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Permintaan</label>
                            <input v-model="dic.jumlahrso" type="number" autocomplete="off" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <input v-model="dic.satuan" type="text" autocomplete="off" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea v-model="dic.catatan" name="catatan" class="form-control" disabled></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select v-model="update.acc_purch" @change="updateAcc(list)" class="form-control">
                                <option value="Y">Terima Permintaan</option>
                                <option value="N">Tolak Permintaan</option>
                            </select>
                        </div>
                        <div v-if="update.acc_purch=='Y'" class="form-group">
                            <label>Tanggal Estimasi</label>
                            <input v-model="update.tanggal_datang" @change="validate()" :min="now()" type="date" class="form-control">
                        </div>
                        <div v-if="update.acc_purch=='N'" class="form-group">
                            <label>Alasan Penolakan</label>
                            <textarea v-model="update.alastolak" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="resetForm()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" @click="updateStatusklik()" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import {
    Circle5
} from 'vue-loading-spinner'
export default {
    props: ['ambiluser'],
    components: {
        Circle5
    },
    data() {
        return {
            form: {},
            customers: {},
            listrso: {},
            dic: {},
            konfirm: {},
            update: {},
            urso: {},
            aksi: {},
            load: true,
            cso: {},
            listrso: {},
            elist: {},
            banding: {},
            params: '',
            upso: '',
            value: '',
            uplist: {},
        }
    },
    created() {
        this.getRso();
        this.getlistRso();
    },
    mounted() {
        axios.get("/api/customer")
            .then(res => this.customers = res.data.data)
    },
    methods: {
        getRso() {
            axios.get(`/api/rso/${this.$route.params.id}`)
                .then(res => this.form = res.data.data)
        },
        getlistRso() {
            axios.get(`/api/listrso/data/purch/${this.$route.params.id}`)
                .then(res => {
                    this.listrso = res.data.data
                    this.load = false;
                    this.banding = this.listrso;
                });
        },
        showModal(list) {
            this.getlistRso()
            this.dic.catatan = list.catatan
            this.dic.id = list.id
            this.dic.nama_barang = list.nama_barang
            this.dic.jumlahrso = list.qty_tdktersedia
            this.dic.satuan = list.satuan
            this.dic.tanggal_datang = list.tgl_datang
            this.update.nomor_rso = list.nomor_rso
            this.update.tanggal_rso = list.tanggal_rso
            this.update.kode_barang = list.kode_barang
            this.update.qty = list.qty
            this.update.qty_tersedia = list.qty_tersedia
            this.update.qty_tdktersedia = list.qty_tdktersedia
            this.update.status = list.status
            this.update.acc_purch = list.acc_purch
            this.update.alastolak = list.alastolak
            this.update.tanggal_datang = list.tgl_datang
            $("#modal-form").modal("show");
        },
        updateStatusklik() {
            axios.put(`/api/listrso/` + this.dic.id, this.update)
                .then((response) => {
                    this.resetForm()
                    $("#modal-form").modal("hide");
                })
        },
        ConfirmRso(rs) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin mengkonfirmasi RSO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Yakin!',
                cancelButtonText: 'Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.ketban = "";
                    this.nilai = "";
                    for (let k = 0; k < this.banding.length; k++) {
                        if (this.banding[k].acc_purch === "Y" && this.banding[k].tgl_datang === null) {
                            this.params = 'N';
                        } else if (this.banding[k].acc_purch === "N" && this.banding[k].alastolak === null) {
                            this.params = 'N';
                        } else {
                            this.params = 'Y';
                        }
                        this.ketban += this.params;
                        this.nilai += "Y";
                    }
                    if (this.ketban === this.nilai) {
                        if (rs.pr === "N") {
                            this.konfirm.status = 'Confirmed';
                            this.konfirm.tanggal_rso = rs.tanggal_rso;
                            this.konfirm.nip_sales = rs.nip_sales;
                            this.konfirm.kode_customer = rs.kode_customer;
                            this.konfirm.keterangan = rs.keterangan;
                            axios.put(`/api/rso/` + rs.nomor_rso, this.konfirm)
                                .then((response) => {
                                    axios.post("/api/history", {
                                        nomor_dok: this.$route.params.id,
                                        id_user: this.ambiluser.id,
                                        notif: "RSO nomor : " + this.$route.params.id + " di konfirmasi Purchasing",
                                        keterangan: "RSO di konfirmasi Purchasing",
                                        jenis: "RSO",
                                        tanggal: this.DateTime(),
                                    })
                                    swalWithBootstrapButtons.fire(
                                        'Berhasil!',
                                        'RSO berhasil di konfirmasi.',
                                        'success'
                                    )
                                    this.$router.push({
                                        name: 'purchase'
                                    });
                                })
                        } else {
                            this.upso = '';
                            this.value = '';
                            for (let n = 0; n < this.banding.length; n++) {
                                this.upso += this.banding[n].acc_purch;
                                this.value += 'Y';
                            }
                            if (this.upso === this.value) {
                                this.konfirm.status = 'So';
                                this.konfirm.tanggal_rso = rs.tanggal_rso;
                                this.konfirm.nip_sales = rs.nip_sales;
                                this.konfirm.kode_customer = rs.kode_customer;
                                this.konfirm.keterangan = rs.keterangan;
                                axios.put(`/api/rso/` + rs.nomor_rso, this.konfirm)
                                    .then((response) => {
                                        axios.get("/api/listrso/" + rs.nomor_rso)
                                            .then(res => {
                                                axios.post("/api/so", {
                                                        nomor_so: rs.nomor_rso,
                                                        tanggal_so: rs.tanggal_rso,
                                                        nomor_rso: rs.nomor_rso,
                                                        keterangan: rs.keterangan,
                                                        status: "Acc",
                                                        distribusi: "Ambil",
                                                        statusso: "tidaktersedia",
                                                        pr: "Y",
                                                        lokasi: "PT. Ekatunggal Tunas Mandiri",
                                                        alamat: "Jl. Pahlawan No.29A, RT.003/005 Citeureup, Kab. Bogor",
                                                        tanggal_kirim: rs.tanggal_rso,
                                                        statusso: 'tidaktersedia'
                                                    })
                                                    .then(res => {
                                                        for (let s = 0; s < this.listrso.length; s++) {
                                                            this.uplist = {
                                                                nomor_so: rs.nomor_rso,
                                                                kode_barang: this.listrso[s].lkode_barang,
                                                                harga: this.listrso[s].harga,
                                                                id_custprice: this.listrso[s].id_custprice,
                                                                qty: this.listrso[s].qty,
                                                                catatan: this.listrso[s].catatan,
                                                                qtyrso: this.listrso[s].qty,
                                                                openso: "Y",
                                                                statusso: "Tidak Tersedia",
                                                                qtyrso: this.listrso[s].qty,
                                                                pr: "Y",
                                                                accdate: this.DateTime(),
                                                                sisapo: this.listrso[s].qty,
                                                            }
                                                            axios.post("/api/listso", this.uplist)
                                                        }
                                                    })
                                            })
                                        this.$router.push({
                                            name: 'purchase'
                                        });
                                    })
                            } else {
                                this.konfirm.status = 'Confirmed';
                                this.konfirm.tanggal_rso = rs.tanggal_rso;
                                this.konfirm.nip_sales = rs.nip_sales;
                                this.konfirm.kode_customer = rs.kode_customer;
                                this.konfirm.keterangan = rs.keterangan;
                                axios.put(`/api/rso/` + rs.nomor_rso, this.konfirm)
                                    .then((response) => {
                                        this.$router.push({
                                            name: 'purchase'
                                        });
                                    })
                            }
                            swalWithBootstrapButtons.fire(
                                'Berhasil!',
                                'RSO berhasil di konfirmasi.',
                                'success'
                            )
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Pastikan data sudah di input semua!',
                        })
                    }
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Batal',
                        'RSO batal di konfirmasi :)',
                        'error'
                    )
                }
            })
        },
        resetForm() {
            this.getlistRso()
            this.dic.catatan = ""
            this.dic.id = ""
            this.dic.nama_barang = ""
            this.dic.jumlahrso = ""
            this.dic.satuan = ""
            this.dic.tanggal_datang = ""
        },
        now() {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') + day;
            return output
        },
        validate() {
            if (this.dic.tanggal_datang < this.now()) {
                this.dic.tanggal_datang = this.now();
            }
        },
        updateAcc(list) {
            axios.put(`/api/listrso/` + this.dic.id, this.update)
                .then((response) => {
                    this.getlistRso()
                    if (list.acc_purch == "N") {
                        this.update.alastolak = "";
                        this.update.so_tdktersedia = "N";
                        axios.put(`/api/listrso/` + this.dic.id, this.update)
                            .then((response) => {
                                this.list.acc_purch = "Y";
                                this.getlistRso()
                            })
                    } else if (list.acc_purch == "Y") {
                        this.update.tanggal_datang = "";
                        this.update.so_tdktersedia = "Y";
                        axios.put(`/api/listrso/` + this.dic.id, this.update)
                            .then((response) => {
                                this.list.acc_purch = "N";
                                this.getlistRso()
                            })
                    }
                })

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
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + this.day;
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
            this.datetimes = this.dates + " " + this.times;
            return this.datetimes;
        }

    },
}
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
    box-shadow: inset 0 0 0 #dee2e6,
        inset 0 -1px 0 #dee2e6;
    z-index: 10;
}
</style>
