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
            <tr v-for="(cs , index) in FilteredCustomer" :key="cs.kode">
                <td style="text-align:center">{{index+1}}</td>
                <td>{{cs.kode}}</td>
                <td>{{cs.nama}}</td>
                <td style="text-align:center">
                    <router-link :to="{name:'dataItem',params:{id:cs.kode}}" class="btn btn-success" >
                        Pilih Customer
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
            customer:[],
            load:true,
        }
    },
    created(){
        this.getCustomer()
    },
    computed:{
        FilteredCustomer(){
            return this.customer.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getCustomer(){
            axios.get("/api/customer")
            .then(res=>{this.customer=res.data.data
                this.load=false;
            });
        },
    }
}
</script>

