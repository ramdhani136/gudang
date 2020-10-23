<template>
<div class="container" @keyup.esc="defaultfilter">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input @input="pilihstatus()" v-model="filter.nomor" type="text" class="form-control" placeholder="Cari nomor SO/RSO">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select @change="pilihstatus()" name="status" v-model="filter.status" class="form-control">
            <option value="Draft">Draft SO</option>
            <option value="Kordinator">Kordinator Sales</option>
            <option value="Sent">Sales SPV</option>
            <option value="Dic">Request DIC</option>
            <option value="Acc">Open</option>
            <option value="Tolak">Rejected</option>
            <option value="Selesai">Selesai</option>
            <option value="Di Selesaikan">Di Selesaikan</option>
        </select>
    </div>
    <!-- <div class="row">
            <router-link to="/so/create/" class="btn btn-success my-3" >+ Create SO</router-link>
        </div>  -->
    <div class="row">
        <router-link to="/so/new" class="btn btn-success my-3">+ Create SO</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor SO</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Tanggal Kirim</th>
                    <th>Sales</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rs , index) in FilterKategori" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewnewso',params:{id:rs.nomor_so}}" class="btn btn-none ">
                            {{rs.nomor_so}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{rs.tanggal_so}}</td>
                    <td>{{rs.customer}}</td>
                    <td style="text-align:center">{{rs.tanggal_kirim}}</td>
                    <td style="text-align:center">{{rs.sales}}</td>
                    <td style="text-align:center">
                        <button v-if="rs.status!=='Draft'" @click="showhistory(rs)" class="btn btn-primary">Lihat History</button>
                        <button @click="deleteSo(rs)" v-if="rs.status!=='Selesai' && rs.status!=='Di Selesaikan'  && ket.aktif[index]==true" class="btn btn-danger">Batalkan</button>
                        <button @click="requestselesai(rs)" v-if="rs.status==='Acc' && rs.rs==='N' && ket.aktif[index]==false" class="btn btn-orange">Request Selesai</button>
                        <button @click="batalselesai(rs)" v-if="rs.status==='Acc' && rs.rs==='Y'" class="btn btn-none">Batal Request</button>
                        <!-- <button @click="bukalagi(rs)" v-if="rs.status=='Di Selesaikan'" class="btn btn-orange">Reopen SO</button> -->
                        <button @click="infoTolak(rs)" v-if="rs.status=='Acc' && rs.rs==='T'" class="btn btn-danger">R. Selesai di tolak</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-selesai" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Permintaan Selesai SO</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="input.alasselesai" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="kirimselesai()" class="btn btn-primary">kirim permintaan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-tolak" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informasi Penolakan SO Selesai</h5>
                    <button @click="resetForm()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="alastolak" class="form-control" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click="requlang()" type="button" class="btn btn-orange">Request Ulang Selesai</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-history" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History SO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor SO</th>
                                <th>keterangan</th>
                                <th>Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(hs,index) in historyview" :key="index">
                                <td>{{hs.tanggal}}</td>
                                <td>{{hs.nomor_dok}}</td>
                                <td>{{hs.keterangan}}</td>
                                <td>{{hs.user}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-filter" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Filter Data</h5>
                    <button @click="defaultfilter()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="custom ? custom.nama:''"></div>
                        <div class="placeholder" v-if="custom==null" v-text="ket.nama">Pilih Customer</div>
                        <div class="popover" v-show="visible">
                            <input type="text" @keydown.up="up" @keydown.down="down" @keydown.enter="selectItem" v-model="query" placeholder="Masukan nama customer ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" :key="match.kode" v-text="match.nama" :class="{'selected':(selected==index)}" @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sales</label>
                        <select v-model="filter.id_user" class="form-control">
                            <option value="">Semua Sales</option>
                            <option :value="us.id" v-for="(us,index) in user" :key="index">{{us.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Group</label>
                        <select v-model="filter.kode_groupso" class="form-control" :disabled="ambiluser.susales!==1">
                            <option :value="gr.kode" v-for="(gr,index) in groupso" :key="index">{{gr.area}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Berdasarkan Tanggal</label>
                        <select @change="cektanggal()" v-model="filter.listtanggal" class="form-control">
                            <option value="so">Penerbitan SO</option>
                            <option value="rk">Rencana Kirim</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih Tanggal</label>
                        <select @change="cekjenis()" v-model="filter.jenistanggal" class="form-control">
                            <option value="Y">Bulan Berjalan</option>
                            <option value="N">Filter Tanggal</option>
                        </select>
                    </div>
                    <div v-if="filter.jenistanggal==='N'" class="form-group">
                        <label>Mulai Tanggal</label>
                        <input v-model="filter.mulaitanggal" type="date" class="form-control">
                    </div>
                    <div v-if="filter.jenistanggal==='N'&& filter.mulaitanggal!==undifined" class="form-group">
                        <label>Sampai Tanggal</label>
                        <input v-model="filter.sampaitanggal" type="date" class="form-control" :min="filter.mulaitanggal">
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="defaultfilter()" type="button" class="btn btn-success" data-dismiss="modal">Save Change</button>
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
            search: '',
            so: [],
            load: true,
            update: {},
            listrso: {},
            uplsoneg: {},
            uplsopos: {},
            nso: null,
            input: {},
            alastolak: null,
            ket: {
                aktif: [],
                nama: "Pilih Customer"
            },
            listso: {},
            bbk: 0,
            aktifin: true,
            hh: [],
            history: {},
            historyview: {},
            nrso: null,
            visible: false,
            query: '',
            selected: 0,
            custom: null,
            itemHeight: 39,
            customer: [],
            filter: {
                kode_customer: '',
                id_user: this.ambiluser.id,
                kode_groupso: this.ambiluser.kode_groupso,
                jenistanggal: 'Y',
                mulaitanggal: this.FirstDate(),
                sampaitanggal: this.today(),
                status: 'Acc',
                nomor: '',
                listtanggal: 'so',
            },
            groupso: {},
            user: {},
        }
    },
    created() {
        this.getSo();
        this.defaultfilter();
    },
    computed: {
        FilterKategori() {
            var vm = this,
                lists = vm.so
            return _.filter(lists, function (query) {
                // var tanggal = query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.sampaitanggal,
                var tanggal = vm.filter.listtanggal === 'so' ? query.tanggal_so >= vm.filter.mulaitanggal && query.tanggal_so <= vm.filter.sampaitanggal : query.tanggal_kirim >= vm.filter.mulaitanggal && query.tanggal_kirim <= vm.filter.sampaitanggal,
                    customer = vm.filter.kode_customer ? (query.kode_customer == vm.filter.kode_customer) : true,
                    sales = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    groupin = vm.filter.kode_groupso ? (query.kode_groupso == vm.filter.kode_groupso) : true,
                    nomorso = vm.filter.nomor ? (query.nomor_so.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    nomorrso = vm.filter.nomor ? (query.nomor_rso.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true;
                return tanggal && customer && sales && groupin && (nomorso || nomorrso) && status;
            })

            // if (this.search === "") {
            //     if (this.status === "Draft") {
            //         return this.so.filter(elem => elem.status === "Draft")
            //     } else if (this.status === "Acc") {
            //         return this.so.filter(elem => elem.status === "Acc")
            //     } else if (this.status === "Sent") {
            //         return this.so.filter(elem => elem.status === "Sent")
            //     } else if (this.status === "Tolak") {
            //         return this.so.filter(elem => elem.status === "Tolak")
            //     } else if (this.status === "Selesai") {
            //         return this.so.filter(elem => elem.status === "Selesai")
            //     } else if (this.status === "Di Selesaikan") {
            //         return this.so.filter(elem => elem.status === "Di Selesaikan")
            //     } else if (this.status === "Dic") {
            //         return this.so.filter(elem => elem.status === "Dic")
            //     } else if (this.status === "Kordinator") {
            //         return this.so.filter(elem => elem.status === "Kordinator")
            //     }
            // } else {
            //     return this.so.filter(elem => {
            //         return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
            //     });
            // }
        },
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods: {
        getSo() {
            if (this.ambiluser.susales === 1) {
                axios.get("/api/so/data/realso")
                    .then(res => {
                        this.so = res.data.data
                        this.filter.status = "Draft";
                        this.filter.status = "Acc";
                    });
            } else {
                axios.get("/api/so/data/view/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.so = res.data.data
                        this.filter.status = "Draft";
                        this.filter.status = "Acc";
                    });
            }
            axios.get("/api/customer")
                .then(res => {
                    this.customer = res.data.data;
                    axios.get("/api/groupso")
                        .then(res => {
                            this.groupso = res.data.data;
                            if (this.ambiluser.susales === 1) {
                                axios.get("/api/user/data/all")
                                    .then(res => {
                                        this.user = res.data.data;
                                        this.load = false;
                                    });
                            } else {
                                axios.get("/api/user/view/" + this.ambiluser.kode_groupso)
                                    .then(res => {
                                        this.user = res.data.data;
                                        this.load = false;
                                    });
                            }
                        })
                })

        },
        pilihstatus() {
            for (let i = 0; i < this.FilterKategori.length; i++) {
                axios.get("/api/listso/" + this.FilterKategori[i].nomor_so)
                    .then(res => {
                        this.listso = res.data.data;
                        this.bbk = 0;
                        for (let n = 0; n < this.listso.length; n++) {
                            this.bbk += parseInt(this.listso[n].bbk);
                        }
                        if (this.bbk < 1) {
                            this.aktifin = true;
                        } else {
                            this.aktifin = false;
                        }
                        this.ket.aktif[i] = this.aktifin;
                        this.load = true;
                        this.load = false;
                    })
            }
        },
        defaultfilter() {
            if (this.ambiluser.susales === 1) {
                axios.get("/api/so/data/realso")
                    .then(res => {
                        this.so = res.data.data;
                        this.hh = this.so.filter(elem => elem.status === "Acc")
                        this.FilterKategori.push(this.hh);
                        this.FilterKategori.splice(this.FilterKategori.length - 1, 1)
                        for (let i = 0; i < this.FilterKategori.length; i++) {
                            axios.get("/api/listso/" + this.FilterKategori[i].nomor_so)
                                .then(res => {
                                    this.listso = res.data.data;
                                    this.bbk = 0;
                                    for (let n = 0; n < this.listso.length; n++) {
                                        this.bbk += parseInt(this.listso[n].bbk);
                                    }
                                    if (this.bbk < 1) {
                                        this.aktifin = true;
                                    } else {
                                        this.aktifin = false;
                                    }
                                    this.ket.aktif[i] = this.aktifin;
                                    this.load = true;
                                    this.load = false;
                                })
                        }
                    });
            } else {
                axios.get("/api/so/data/view/" + this.ambiluser.kode_groupso)
                    .then(res => {
                        this.so = res.data.data;
                        this.hh = this.so.filter(elem => elem.status === "Acc")
                        this.FilterKategori.push(this.hh);
                        this.FilterKategori.splice(this.FilterKategori.length - 1, 1)
                        for (let i = 0; i < this.FilterKategori.length; i++) {
                            axios.get("/api/listso/" + this.FilterKategori[i].nomor_so)
                                .then(res => {
                                    this.listso = res.data.data;
                                    this.bbk = 0;
                                    for (let n = 0; n < this.listso.length; n++) {
                                        this.bbk += parseInt(this.listso[n].bbk);
                                    }
                                    if (this.bbk < 1) {
                                        this.aktifin = true;
                                    } else {
                                        this.aktifin = false;
                                    }
                                    this.ket.aktif[i] = this.aktifin;
                                    this.load = true;
                                    this.load = false;
                                })
                        }
                    });
            }
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
            this.filter.kode_customer = this.custom.kode
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
        showhistory(rs) {
            $("#modal-history").modal("show");
            axios.get("/api/history/data/" + rs.nomor_so + "/So")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
        deleteSo(rs) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin ingin menghapus SO ini?',
                text: "SO akan di hapus permanen dari database!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete("/api/so/" + rs.nomor_so)
                        .then(res => {
                            axios.get("/api/history/data/" + rs.nomor_so + "/So")
                                .then(res => {
                                    this.history = res.data.data;
                                    for (let n = 0; n < this.history.length; n++) {
                                        axios.delete("/api/history/" + this.history[n].id);
                                    }
                                })
                            axios.post("/api/history", {
                                nomor_dok: rs.nomor_rso,
                                nomor_ref: rs.nomor_so,
                                id_user: this.ambiluser.id,
                                notif: "So di hapus",
                                keterangan: "Membatalkan SO nomor : " + rs.nomor_so,
                                jenis: "RSO",
                                tanggal: this.DateTime(),
                                aktif: "N",
                            })
                            this.getSo();
                            swalWithBootstrapButtons.fire(
                                'Deleted!',
                                'SO berhasil di hapus.',
                                'success'
                            )
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal hapus SO ini :)',
                        'error'
                    )
                }
            })
        },
        requestselesai(rs) {
            this.nso = rs.nomor_so;
            this.nrso = rs.nomor_rso;
            $("#modal-selesai").modal("show");
        },
        kirimselesai() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menyelesaikan SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.input.rs = "Y";
                    axios.put("/api/so/" + this.nso, this.input)
                        .then(res => {
                            this.getSo();
                            this.input = {};
                            axios.post("/api/history", {
                                nomor_dok: this.nso,
                                nomor_ref: this.nrso,
                                id_user: this.ambiluser.id,
                                notif: "Anda mendapatkan permintaan Selesai SO baru",
                                keterangan: "Mengirim permintaan penyelesaian SO",
                                jenis: "So",
                                tanggal: this.DateTime(),
                            })
                            swalWithBootstrapButtons.fire(
                                'Sukses!',
                                'Berhasil melakukan request pembatalan SO.',
                                'success'
                            )
                            $("#modal-selesai").modal("hide");
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal melakukan request pembatalan:)',
                        'error'
                    )
                }
            })
        },
        batalselesai(rs) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin membatalkan request Selesai SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.input.rs = "N";
                    this.input.alasselesai = "";
                    axios.put("/api/so/" + rs.nomor_so, this.input)
                        .then(res => {
                            axios.post("/api/history", {
                                nomor_dok: rs.nomor_so,
                                nomor_ref: rs.nomor_rso,
                                id_user: this.ambiluser.id,
                                notif: "RSO pembatalan di batalkan",
                                keterangan: "Membatalkan permintaan penyelesaian SO",
                                jenis: "So",
                                tanggal: this.DateTime(),
                            })
                            swalWithBootstrapButtons.fire(
                                'Sukses!',
                                'Request Selesai berhasil dibatalkan.',
                                'success'
                            )
                            this.getSo();
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batalkan melakukan permintaan :)',
                        'error'
                    )
                }
            })
        },
        infoTolak(rs) {
            this.nso = rs.nomor_so;
            this.alastolak = rs.alastolakselesai;
            $("#modal-tolak").modal("show");
        },
        requlang() {
            $("#modal-tolak").modal("hide");
            $("#modal-selesai").modal("show");
        },
        bukalagi(rs) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin membuka kembali SO ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    axios.put("/api/so/" + rs.nomor_so, {
                            status: 'Acc',
                            rs: 'N',
                            alasselesai: '',
                            alastolakselesai: ''
                        })
                        .then(res => {
                            this.getSo();
                            swalWithBootstrapButtons.fire(
                                'Sukses!',
                                'Berhasil membuka ulang SO.',
                                'success'
                            )
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal membuka ulang SO :)',
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
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + "01";
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
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
            this.dates = this.year + "-" + (this.month < 10 ? '0' : '') + this.month + "-" + this.day;
            this.times = this.hours + ":" + this.minute + ":" + (this.seconds < 10 ? '0' : '') + this.seconds;
            this.datetimes = this.dates;
            return this.datetimes;
        },
        cekjenis() {
            this.filter.mulaitanggal = this.FirstDate();
            this.filter.sampaitanggal = this.today();
        }
    }
}
</script>

<style>

</style>
