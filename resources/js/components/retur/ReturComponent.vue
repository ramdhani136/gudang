<template>
<div class="container">
    <div class="form-group col-3 my-3 float-right">
        <input v-model="search" type="text" class="form-control" placeholder="Search">
    </div>
    <div class="form-group col-3 my-3 ml-n3 float-left">
        <select name="status" v-model="status" class="form-control">
            <option value="request">Waiting List</option>
            <option value="acc">Open</option>
            <option value="tolak">Tolak</option>
            <option value="acc">Selesai</option>
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
                    <td style="text-align:center">{{rt.nomor_retur}}</td>
                    <td style="text-align:center">{{rt.tanggal}}</td>
                    <td style="text-align:center">{{rt.nomor_so}}</td>
                    <td>{{rt.customer}}</td>
                    <td style="text-align:center">
                        <router-link :to="{name:'viewbbm',params:{nomor:rt.nomor_retur}}" class="btn btn-primary">
                            Lihat Detail
                        </router-link>
                        <button class="btn btn-danger">Batalkan</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <Circle5 id="load" v-if="load"></Circle5>
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
            load: true,
            status: 'acc',
            retur: [],
        }
    },
    created() {
        this.getretur();
    },
    computed: {
        FilteredRetur() {
            if (this.search === "") {
                if (this.status === "request") {
                    return this.retur.filter(elem => elem.status === "request")
                } else if (this.status === "acc") {
                    return this.retur.filter(elem => elem.status === "acc")
                } else if (this.status === "tolak") {
                    return this.retur.filter(elem => elem.status === "tolak")
                }
            } else {
                return this.retur.filter(elem => {
                    return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
                });
            }
        },
    },
    methods: {
        getretur() {
            axios.get("/api/retur")
                .then(res => {
                    this.retur = res.data.data;
                    this.load = false;
                })
        },
    }
}
</script>

<style>

</style>
