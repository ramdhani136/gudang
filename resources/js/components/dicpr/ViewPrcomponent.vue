<template>
<div class="container">
    <div class="row row-cols-2" v-for="(lr,index) in rso" :key="index">
        <div class="col-4">
            <div class="form-group">
                <label>Nomor PR :</label>
                <input v-model="lr.nomor_rso" type="text" class="form-control col-12" disabled>
            </div>
            <div class="form-group">
                <label>Tanggal :</label>
                <input v-model="lr.tanggal_rso" type="date" class="form-control col-12" disabled>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>Customer</label>
                <select v-model="lr.kode_customer" name="customer" class="col-12 form-control" disabled>
                    <option value="default">Gudang Stok</option>
                </select>
            </div>
            <div class="form-group">
                <label>Diminta</label>
                <input type="text" class="form-control" value="Inventory Control" disabled>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label>keterangan</label>
                <textarea v-model="rsoketerangan" name="keterangan" class="form-control col-12" :disabled="disabled===1"></textarea>
            </div>
        </div>
    </div>
    <div id="rsoverflowso" class="row mt-2 mx-auto">
        <div class="row mt-1 mx-auto col-12">
            <Circle5 id="load3" v-if="load"></Circle5>
            <table id="rsthead" class="table mt-2 table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Qty</th>
                        <th v-if="status==='Confirmed'">Status</th>
                    </tr>
                </thead>
                <!-- Status Menunggu -->
                <tbody v-if="status==='Purch'">
                    <tr v-for="(lp,index) in listpr" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{lp.lkode_barang}}</td>
                        <td>{{lp.nama_barang}}</td>
                        <td style="text-align:center">{{lp.satuan}}</td>
                        <td style="text-align:center">
                            <input v-model="hitung.qty[index]" type="number" class="form-control" :disabled="disabled===1">
                        </td>
                    </tr>
                </tbody>
                <!-- Status Konfirmasi -->
                <tbody v-if="status==='Confirmed'">
                    <tr v-for="(lp,index) in listpr" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{lp.lkode_barang}}</td>
                        <td>{{lp.nama_barang}}</td>
                        <td style="text-align:center">{{lp.satuan}}</td>
                        <td style="text-align:center">
                            <input v-model="hitung.qty[index]" type="number" class="form-control" :disabled="disabled===1">
                        </td>
                        <td>
                            <button v-if="lp.acc_purch==='Y'" class="btn btn-primary">Di terima</button>
                            <button v-if="lp.acc_purch==='N'" @click="lihattolak(lp)" class="btn btn-none">Di tolak</button>
                        </td>
                    </tr>
                </tbody>

                <!-- Status create SO -->
                <tbody v-if="status==='So'">
                    <tr v-for="(lp,index) in listprso" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td>{{lp.lkode_barang}}</td>
                        <td>{{lp.nama_barang}}</td>
                        <td style="text-align:center">{{lp.satuan}}</td>
                        <td style="text-align:center">
                            <input v-model="hitung.qty[index]" type="number" class="form-control" :disabled="disabled===1">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-2">
        <button @click="aktiftombol=!aktiftombol" v-if="status==='Confirmed'" class="btn-orange btn ml-3">
            {{namatombol}} {{aktiftombol? edit():close()}}
        </button>
        <button @click="requestulang()" v-if="status==='Confirmed' && namatombol==='Close'" class="btn-primary btn ml-2">
            Request Ulang
        </button>
        <button @click="hapuspr()" v-if="status==='Confirmed' || status==='Purch'" class=" btn-danger btn ml-2">
            Batalkan PR
        </button>
        <button @click="createso()" v-if="status==='Confirmed' && namatombol==='Edit'" class="btn-success btn ml-2">
            Kirim PR
        </button>
        <button @click="kembali()" v-if="status==='So'" class="btn-primary btn ml-3">
            Kembali
        </button>
    </div>
    <div class="modal fade" id="modal-detail" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alasan Penolakan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea v-model="alastolak" name="catatan" class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            load: true,
            rso: {},
            listpr: [],
            hitung: {
                keterangan: [],
                qty: [],
            },
            disabled: 1,
            tombol: false,
            alastolak: '',
            status: '',
            aktiftombol: true,
            namatombol: 'Edit',
            kirimlagi: {},
            rsoketerangan: '',
            cso: {},
            listprso: {},
            uplist: {}
        }
    },
    created() {
        this.getRso();
    },
    computed: {

    },
    methods: {
        getRso() {
            axios.get("/api/rso/data/pr/" + this.$route.params.nomor)
                .then(res => {
                    this.rso = res.data.data;
                    this.status = this.rso[0].status;
                    this.rsoketerangan = this.rso[0].keterangan;
                    axios.get("/api/listrso/" + this.$route.params.nomor)
                        .then(res => {
                            this.listpr = res.data.data;
                            for (let i = 0; i < this.listpr.length; i++) {
                                this.hitung.qty[i] = this.listpr[i].qty;
                                this.hitung.keterangan[i] = this.listpr[i].catatan;
                            }
                            this.load = false;
                        })
                    axios.get("/api/listrso/data/pr/" + this.$route.params.nomor)
                        .then(res => {
                            this.listprso = res.data.data;
                        });
                });
        },
        lihattolak(lp) {
            this.alastolak = lp.alastolak;
            $("#modal-detail").modal("show");
        },
        kembali() {
            this.$router.push({
                name: 'Prcomponent'
            });
        },
        edit() {
            this.namatombol = 'Edit';
            this.disabled = 1;
            for (let i = 0; i < this.listpr.length; i++) {
                this.hitung.qty[i] = this.listpr[i].qty;
            }

        },
        close() {
            this.namatombol = 'Close';
            this.disabled = 0;
        },
        requestulang() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin meminta konfirmasi ulang PR ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put("/api/rso/" + this.$route.params.nomor, {
                            status: 'Purch',
                            keterangan: this.rsoketerangan
                        })
                        .then(res => {
                            for (let i = 0; i < this.listpr.length; i++) {
                                this.kirimlagi = {
                                    qty: this.hitung.qty[i],
                                    qty_tdktersedia: this.hitung.qty[i]
                                };
                                axios.put("/api/listrso/" + this.listpr[i].id, this.kirimlagi)
                            }
                            swalWithBootstrapButtons.fire(
                                'Terkirim!',
                                'Silahkan tunggu konfirmasi purchasing.',
                                'success'
                            )
                            axios.post("/api/history", {
                                nomor_dok: this.$route.params.nomor,
                                id_user: this.ambiluser.id,
                                notif: "PR nomor : " + this.$route.params.nomor + " di kirim ulang ke Purchasing",
                                keterangan: "PR di kirim ulang ke Purchasing",
                                jenis: "RSO",
                                tanggal: this.DateTime(),
                            })
                            this.$router.push({
                                name: 'Prcomponent'
                            });
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan permintaan ini :)',
                        'error'
                    )
                }
            })
        },
        createso() {
            this.listsoacc = this.listpr.filter(obj => obj.acc_purch.includes("Y"));
            if (this.listsoacc.length > 0) {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success ml-2',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Apakah anda yakin?',
                    text: "Hanya item yang sudah di Acc yang akan di pesankan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, Yakin!',
                    cancelButtonText: 'Tidak!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.put("/api/rso/" + this.$route.params.nomor, {
                                status: 'So'
                            })
                            .then(res => {
                                this.cso = {
                                    nomor_so: this.$route.params.nomor,
                                    tanggal_so: this.listpr[0].tanggal_rso,
                                    tanggal_kirim: this.listpr[0].tanggal_rso,
                                    nomor_rso: this.$route.params.nomor,
                                    status: 'Acc',
                                    statusso: 'tidaktersedia',
                                    pr: 'Y',
                                    distribusi: 'ambil',
                                    lokasi: 'PT.Ekatunggal Tunas Mandiri',
                                    alamat: 'Bogor',
                                    kode_groupso: 'GR',
                                    id_user: this.ambiluser.id
                                }
                                axios.post("/api/so", this.cso)
                                    .then(res => {
                                        for (let i = 0; i < this.listsoacc.length; i++) {
                                            this.uplist = {
                                                nomor_so: this.listsoacc[i].lno_rso,
                                                kode_barang: this.listsoacc[i].lkode_barang,
                                                harga: this.listsoacc[i].harga,
                                                id_custprice: this.listsoacc[i].id_custprice,
                                                qty: this.listsoacc[i].qty,
                                                qtyrso: this.listsoacc[i].qty,
                                                catatan: this.listsoacc[i].catatan,
                                                statusso: "Tidak Tersedia",
                                                tanggal_datang: this.listsoacc[i].tgl_datang,
                                                openso: "Y",
                                                pr: "Y",
                                                accdate: this.DateTime(),
                                                sisapo: this.listsoacc[i].qty,
                                            }
                                            axios.post("/api/listso", this.uplist)
                                        }
                                        swalWithBootstrapButtons.fire(
                                            'Terkirim!',
                                            'Berhasil mengirim permintaan.',
                                            'success'
                                        )
                                        axios.post("/api/history", {
                                            nomor_dok: this.$route.params.nomor,
                                            id_user: this.ambiluser.id,
                                            notif: "Pemesanan barang pr " + this.$route.params.nomor,
                                            keterangan: "Proses permintaan buka PO",
                                            jenis: "RSO",
                                            tanggal: this.DateTime(),
                                        })
                                        this.$router.push({
                                            name: 'Prcomponent'
                                        });
                                    });
                            })
                    } else if (
                        result.dismiss === Swal.DismissReason.cancel
                    ) {
                        swalWithBootstrapButtons.fire(
                            'Cancelled',
                            'Batal mengirim permintaan :)',
                            'error'
                        )
                    }
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Tidak ada item yang di terima purchasing!',
                })
            }
        },
        hapuspr() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus PR ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete("/api/rso/" + this.$route.params.nomor)
                        .then(res => {
                            swalWithBootstrapButtons.fire(
                                'Deleted!',
                                'PR berhasil di hapus.',
                                'success'
                            )
                            this.$router.push({
                                name: 'Prcomponent'
                            });
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus PR ini :)',
                        'error'
                    )
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

</style>
