<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="Purch">Menunggu Acc</option>
            <option value="Confirmed">Confirmed</option>
            <option value="So">Open</option>
        </select>
    </div>
    <div class="row">
        <router-link to="/pr/create" class="btn btn-success my-3">+ Create Item Request</router-link>
    </div>
    <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor PR </th>
                    <th>Tanggal</th>
                    <th>Permintaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(lp,index) in FilteredPr" :key="index">
                    <td style="text-align:center">{{index+1}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'ViewPrcomponent',params:{nomor:lp.nomor_rso}}" class="btn btn-none">
                            {{lp.nomor_rso}}
                        </router-link>
                    </td>
                    <td style="text-align:center">{{lp.tanggal_rso}}</td>
                    <td style="text-align:center">{{lp.customer}}</td>
                    <td style="text-align:center">
                        <button @click="showhistory(lp)" class="btn btn-primary">
                            Lihat Rincian
                        </button>
                        <button @click="batalkan(lp)" class="btn btn-danger">
                            Batalkan
                        </button>
                        <button v-if="lp.status==='Tolak'||lp.status==='Draft'" class="btn btn-danger">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-history" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian History PR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="thead" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>Nomor PR</th>
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
</div>
</template>

<script>
import {
    Circle5
} from 'vue-loading-spinner'
export default {
    components: {
        Circle5
    },
    data() {
        return {
            search: '',
            load: false,
            status: 'Confirmed',
            pr: [],
            historyview: {},
            statusnya: '',
            listso: {},
            openpo: 0,
            history: {}
        }
    },
    created() {
        this.getPr();
    },
    computed: {
        FilteredPr() {
            if (this.search === "") {
                if (this.status === "Draft") {
                    return this.pr.filter(elem => elem.status === "Draft")
                } else if (this.status === "Purch") {
                    return this.pr.filter(elem => elem.status === "Purch")
                } else if (this.status === "Confirmed") {
                    return this.pr.filter(elem => elem.status === "Confirmed")
                } else if (this.status === "So") {
                    return this.pr.filter(elem => elem.status === "So")
                }
            } else {
                return this.pr.filter(elem => {
                    return elem.nomor_rso.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        },
    },
    methods: {
        getPr() {
            axios.get("/api/rso/data/pr")
                .then(res => {
                    this.pr = res.data.data;
                })
        },
        showhistory(lp) {
            $("#modal-history").modal("show");
            axios.get("/api/history/data/" + lp.nomor_rso + "/RSO")
                .then(res => {
                    this.historyview = res.data.data;
                })
        },
        batalkan(lp) {

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
                confirmButtonText: 'Iya, Yakin',
                cancelButtonText: 'Tidak!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (lp.status === "Purch") {
                        axios.delete("/api/rso/" + lp.nomor_rso)
                            .then(res => {
                                axios.get("/api/history/" + lp.nomor_rso)
                                    .then(res => {
                                        this.history = res.data.data;
                                        for (let j = 0; j < this.history.length; j++) {
                                            axios.delete("/api/history/" + this.history[j].id)
                                        }
                                    })
                                swalWithBootstrapButtons.fire(
                                    'Deleted!',
                                    'PR berhasil di hapus.',
                                    'success'
                                )
                                this.getPr();
                            })
                    } else {
                        axios.get("/api/listso/" + lp.nomor_rso)
                            .then(res => {
                                this.listso = res.data.data;
                                this.openpo = 0;
                                for (let i = 0; i < this.listso.length; i++) {
                                    this.openpo += this.listso[i].openpo;
                                }
                                if (this.openpo < 1) {
                                    axios.delete("/api/rso/" + lp.nomor_rso)
                                        .then(res => {
                                            axios.get("/api/history/" + lp.nomor_rso)
                                                .then(res => {
                                                    this.history = res.data.data;
                                                    for (let j = 0; j < this.history.length; j++) {
                                                        axios.delete("/api/history/" + this.history[j].id)
                                                    }
                                                })
                                            swalWithBootstrapButtons.fire(
                                                'Deleted!',
                                                'PR berhasil di batakan.',
                                                'success'
                                            )
                                            this.getPr();
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Tidak dapat membatalkan PR ini',
                                        text: 'Item sudah di bukakan PO!, Silahkan konfirmasi Purchasing',
                                    })
                                }
                            })
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Batal menghapus PR :)',
                        'error'
                    )
                }
            })
        }
    }
}
</script>

<style>

</style>
