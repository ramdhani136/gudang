<template>
<div class="container">
    <div class="form-group  ml-n4 col-1 my-3 float-right">
        <button @click="showfilter()" class="btn btn-trans">Filter</button>
    </div>
    <div class="form-group col-3 my-3 float-right">
        <input v-model="filter.nomor" type="text" class="form-control" placeholder="Nomor SO/Retur">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="filter.status" class="form-control">
            <option value="Kordinator">Request Kordinator</option>
            <option value="Supervisor">Request Supervisor</option>
            <option value="Dic">Request DIC</option>
            <option value="Acc">Open</option>
            <option value="Tolak">Reject</option>
            <option value="Selesai">Selesai</option>
        </select>
    </div>
    <div class="row">
        <router-link to="/retur/create" class="btn btn-success my-3">+ Create Retur</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Retur</th>
                    <th>Tanggal</th>
                    <th>Nomor SO</th>
                    <th>Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rt,index) in FilteredRetur" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewretur',params:{nomor:rt.nomor_retur}}" class="btn btn-none">
                            {{rt.nomor_retur}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{rt.tanggal}}</td>
                    <td style="text-align:center">{{rt.nomor_so}}</td>
                    <td>{{rt.customer}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(rt)" class="btn btn-primary">
                            Lihat History
                        </button>
                        <button @click="hapus(rt)" class="btn btn-danger">Batalkan</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History Retur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor Retur</th>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                    <button @click="cek()" type="button" class="btn btn-success" data-dismiss="modal">Save Change</button>
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
            retur: [],
            historyview: {},
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
            },
            groupso: {},
            user: {},
            ket: {
                nama: "Pilih Customer"
            },
        }
    },
    created() {
        this.getretur();
    },
    computed: {
        FilteredRetur() {
            var vm = this,
                lists = vm.retur
            return _.filter(lists, function (query) {
                var tanggal = query.tanggal >= vm.filter.mulaitanggal && query.tanggal <= vm.filter.sampaitanggal,
                    customer = vm.filter.kode_customer ? (query.kode_customer == vm.filter.kode_customer) : true,
                    sales = vm.filter.id_user ? (query.id_user == vm.filter.id_user) : true,
                    groupin = vm.filter.kode_groupso ? (query.kode_groupso == vm.filter.kode_groupso) : true,
                    nomorso = vm.filter.nomor ? (query.nomor_so.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    nomorretur = vm.filter.nomor ? (query.nomor_retur.toLowerCase().includes(vm.filter.nomor.toLowerCase())) : true,
                    status = vm.filter.status ? (query.status == vm.filter.status) : true;
                return tanggal && customer && sales && groupin && (nomorso || nomorretur) && status;
            })
        },
        matches() {
            if (this.query == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods: {
        getretur() {
            axios.get("/api/retur")
                .then(res => {
                    this.retur = res.data.data;
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
                                            })
                                    } else {
                                        axios.get("/api/user/view/" + this.ambiluser.kode_groupso)
                                            .then(res => {
                                                this.user = res.data.data;
                                                this.load = false;
                                            })
                                    }
                                })
                        })
                })
        },
        showhistory(rt) {
            $("#modal-form").modal("show");
            axios.get("/api/history/data/" + rt.nomor_retur + "/Rt")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
        hapus(rt) {
            this.load = true;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ml-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus data retur ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya, Yakin!',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete("/api/retur/" + rt.nomor_retur)
                        .then(res => {
                            this.getretur();
                            this.load = false;
                            swalWithBootstrapButtons.fire(
                                'Deleted!',
                                'Data berhasil di hapus.',
                                'success'
                            )
                        })
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    this.load = false;
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus data ini :)',
                        'error'
                    )
                }
            })
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
        cek() {
            console.log(this.filter);
        }
    }
}
</script>

<style>

</style>
