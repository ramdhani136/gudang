<template>
<div class="wrapper">
    <div class="row diam" v-scroll="handleScroll">
        <div class="col-11 font-weight-bold title mt-4 mb-5">
            List Sales Order
        </div>
        <div class="col-1 mt-4">
            <button @click="showFillter()" class="btn btn-grey">Filter</button>
        </div>
    </div>
    <div v-for="(dso,index) in so" :key="index">
        <div class="row font-weight-bold bc align-items-center ">
            <div class="col-2">{{dso.nomor_so}}</div>
            <div class="col-5">{{dso.customer}}</div>
            <div class="col-3">{{dso.nomor_so}}</div>
            <div class="col-2">{{dso.sales}}</div>
        </div>
        <div class="row">
            <table class="table">
                <thead>
                    <th>Kode Barang</th>
                    <th>Item</th>
                    <th>Di pesan</th>
                    <th>Di terima</th>
                    <th>Harga</th>
                    <th>Sisa Pesanan</th>
                </thead>
                <tbody v-for="(lso,index) in listso[index]" :key="index">
                    <td>{{lso.kode_barang}}</td>
                    <td>{{lso.nama_barang}}</td>
                    <td>{{lso.qty}}</td>
                    <td>{{lso.bbk}}</td>
                    <td>{{lso.harga | currency}}</td>
                    <td style="font-size:0.9em; font-weight:bold;">{{lso.qty-lso.bbk}} {{lso.satuan}}</td>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal fade" id="modal-search" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fillter Pencarian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control">
                            <option value="all">Semua</option>
                            <option value="Acc">Open</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sales</label>
                        <select class="form-control" :disabled="ambiluser.susales===0">
                            <option :value="ambiluser.id">{{ambiluser.name}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Group Sales</label>
                        <select class="form-control" :disabled="ambiluser.susales===0">
                            <option :value="ambiluser.kode_groupso">{{ambiluser.kode_groupso}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <select v-model="jenistanggal" class="form-control">
                            <option value="N">Semua Tanggal</option>
                            <option value="Y">Filter Tanggal</option>
                        </select>
                    </div>
                    <div v-if="jenistanggal==='Y'" class="form-group">
                        <label>Mulai Tanggal</label>
                        <input type="date" class="form-control">
                    </div>
                    <div v-if="jenistanggal==='Y'" class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Save Change</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {

    props: ['ambiluser'],
    data() {
        return {
            listso: [],
            so: {},
            sales: this.ambiluser.id,
            groupso: this.ambiluser.kode_groupso,
            status: 'Acc',
            jenistanggal: 'N',
        }
    },
    created() {
        this.getso();
    },
    methods: {
        getso() {
            axios.get("/api/so/data/realso")
                .then(res => {
                    this.so = res.data.data;
                    for (let i = 0; i < this.so.length; i++) {
                        axios.get("/api/listso/" + this.so[i].nomor_so)
                            .then(res => {
                                this.listso.push(res.data.data);
                            })
                    }
                })
        },
        showFillter() {
            $("#modal-search").modal("show");
        },
        handleScroll: function (evt, el) {
            if (window.scrollY > 50) {
                el.setAttribute(
                    'style',
                    'top: 160px; '
                )
            } else {
                el.setAttribute(
                    'style',
                    'top: 190px; '
                )
            }
            // return window.scrollY > 100
        }
    }
}
</script>

<style scoped>
.wrapper {
    width: 84%;
    margin-left: 8%;
    font-size: 1em;
    margin-top: 100px;
}

.title {
    font-size: 1.5em;
}

.bc {
    background-color: rgb(233, 250, 233);
}

.btn-grey {
    border: solid 1px rgb(158, 158, 158);
    background-color: rgb(202, 202, 202);
}

.diam {
    margin-top: -105px;
    position: fixed;
    width: 71%;
    background-color: #fff;
    z-index: 1000;
    height: auto;
}
</style>
