<template>
<div class="wrapper">
    <div class="row diam" v-scroll="handleScroll">
        <div class="col-11 font-weight-bold title mt-4 mb-5">
            List Sales Order
        </div>
        <div class="col-1 rig  mt-4">
            <button @click="showFillter()" class="btn btn-orange">Filter</button>
        </div>
    </div>
    <div class="panelbawah">
        <div class="col-2">
            <button class="btn-sm btn-none">Cetak Laporan</button>
        </div>
    </div>
    <div v-for=" (dso,index) in so" :key="index">
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
                        <select v-model="filter.status" class="form-control">
                            <option value="all">Semua</option>
                            <option value="Acc">Open</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible2" v-text="custom2 ? custom2.nama:''"></div>
                        <div class="placeholder" v-if="custom2==null" v-text="ket2.nama">Semua Customer</div>
                        <div class="popover" v-show="visible2">
                            <input type="text" @keydown.up="up2" @keydown.down="down2" @keydown.enter="selectItem2" v-model="query2" placeholder="Masukan nama customer ..">
                            <div class="optionbr" ref="optionList">
                                <ul>
                                    <li v-for="(match2,index) in matches2" :key="match2.kode" v-text="match2.nama" :class="{'selected':(selected2==index)}" @click="itemClicked2(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sales</label>
                        <select v-model="filter.sales" class="form-control" :disabled="ambiluser.susales===0">
                            <option v-for="(sl,index) in sales" :key="index" :value="sl.id">{{sl.name}}</option>
                        </select>
                    </div>
                    <div class=" form-group">
                        <label>Group Sales</label>
                        <select v-model="filter.groupso" class="form-control" :disabled="ambiluser.susales===0">
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
                        <input v-model="filter.mulaitanggal" type="date" class="form-control">
                    </div>
                    <div v-if="jenistanggal==='Y' && filter.mulaitanggal!==undifined" class="form-group">
                        <label>Sampai Tanggal</label>
                        <input v-model="filter.akhirtanggal" type="date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="filterdata()" type="button" class="btn btn-primary" data-dismiss="modal">Fillter Data</button>
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
            status: 'Acc',
            jenistanggal: 'N',
            visible2: false,
            query2: '',
            selected2: 0,
            itemHeight2: 39,
            custom2: null,
            ket2: {
                nama: "Semua Customer"
            },
            customer: [],
            filter: {
                groupso: this.ambiluser.kode_groupso,
                sales: this.ambiluser.id,
                status: 'all',
            },
            sales: {}
        }
    },
    created() {
        this.getso();
    },
    computed: {
        matches2() {
            if (this.query2 == '') {
                return [];
            }
            return this.customer.filter((item) => item.nama.toLowerCase().includes(this.query2.toLowerCase()))
        },
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
                                axios.get("/api/customer")
                                    .then(res => {
                                        this.customer = res.data.data;
                                        axios.get("/api/user")
                                            .then(res => {
                                                this.sales = res.data.data;
                                            })
                                    })
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
        },
        toggleVisible2() {
            this.visible2 = !this.visible2;
        },
        itemClicked2(index) {
            this.selected2 = index;
            this.selectItem2();
        },
        selectItem2() {
            this.custom2 = this.matches2[this.selected2];
            this.ket2.kode = this.custom2.kode;
            this.ket2.nama = this.custom2.nama;
            this.item = true;
            this.visible2 = false;
        },
        up2() {
            if (this.selected2 == 0) {
                return;
            }
            this.selected2 -= 1;
            this.scrollToItem2();
        },
        down2() {
            if (this.selected2 >= this.matches2.length - 1) {
                return;
            }
            this.selected2 += 1;
            this.scrollToItem2();
        },
        scrollToItem2() {
            this.$refs.optionList.scrollTop = this.selected2 * this.itemHeight2;
        },
        filterdata() {
            this.filter.kode_customer = this.ket2.kode;
            console.log(this.filter)
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

.panelbawah {
    border-top: solid 1px rgb(231, 229, 229);
    width: 84%;
    height: 50px;
    background-color: #fff;
    bottom: 0;
    position: fixed;
    z-index: 1001;
    padding-top: 10px;
    margin-left: -8%;
    padding-left: 6%;
}
</style>
