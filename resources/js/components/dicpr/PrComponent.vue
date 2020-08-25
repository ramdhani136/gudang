<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="Draft">Draft</option>
                <option value="Purch">Menunggu Acc</option>
                <option value="Confirmed">Open</option>
            </select>
        </div> 
        <div class="row">
            <router-link to="/pr/create" class="btn btn-success my-3" >+ Create Item Request</router-link>  
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
                        <td style="text-align:center">{{lp.nomor_rso}}</td>
                        <td style="text-align:center">{{lp.tanggal_rso}}</td>
                        <td style="text-align:center">{{lp.customer}}</td>
                        <td style="text-align:center">
                            <router-link :to="{name:'viewbcm',params:{nomor:lp.nomor_rso}}" class="btn btn-primary">
                                    Lihat Data
                            </router-link>
                            <button v-if="lp.status==='Tolak'||lp.status==='Draft'" class="btn btn-danger">Hapus</button>
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
            search:'',
            load:false,
            status:'Confirmed',
            pr:[],
        }
    },
    created(){
        this.getPr();
    },
    computed:{
        FilteredPr(){
            if(this.search===""){
                if(this.status==="Draft"){
                    return this.pr.filter(elem=> elem.status==="Draft")
                }else if(this.status==="Purch"){
                    return this.pr.filter(elem=> elem.status==="Purch")
                }else if(this.status==="Confirmed"){
                    return this.pr.filter(elem=> elem.status==="Confirmed")
                }
            }else{
                return this.pr.filter(elem => {
                return elem.nomor_rso.toLowerCase().includes(this.search.toLowerCase());
            });
            }
        },
    },
    methods:{
        getPr(){
            axios.get("/api/rso/data/pr")
            .then(res=>{
                this.pr=res.data.data;
            })
        },
    }
}
</script>


<style>

</style>