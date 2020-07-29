<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(br , index) in FilteredBarang" :key="br.kode">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{br.kode}}</td>
                <td>{{br.nama}}</td>
                <td style="text-align:center">
                    <router-link :to="{name:'viewItem',params:{customer:customer,barang:br.kode}}" class="btn btn-success" >
                        Pilih Barang
                    </router-link>
                </td>
            </tr>
        </tbody>
    </table>
    <Circle5 id="load" v-if="load"></Circle5>
    </div>
    </div>
</template>

<script>
import {Circle5} from 'vue-loading-spinner'
export default {
    components: {
        Circle5
    },
    data(){
        return{
            search  : '',
            barang:[],
            load:true,
            customer:this.$route.params.id,
        }
    },
    created(){
        this.getBarang()
    },
    computed:{
        FilteredBarang(){
            return this.barang.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getBarang(){
            axios.get("/api/barang")
            .then(res=>{this.barang=res.data.data
                this.load=false;
            });
        },
    }
}
</script>

