<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="open">List BBK</option>
            </select>
        </div> 
        <div class="row">
            <router-link to="/distribusi/bbk/create" class="btn btn-success my-3" >+ Create BBK</router-link>  
        </div>
        <div id="overflow" class="border-top">
                <table id="thead" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor BBK</th>
                        <th>Tanggal</th>
                        <th>Nomor BCK</th>
                        <th>Nomor SO</th>
                        <th>Customer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(bk,index) in FilteredBbk" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td style="text-align:center">{{bk.bbk}}</td>
                        <td style="text-align:center">{{bk.tanggal}}</td>
                        <td style="text-align:center">{{bk.nomor_bck}}</td>
                        <td style="text-align:center">{{bk.nomor_so}}</td>
                        <td>{{bk.customer}}</td>
                        <td style="text-align:center">
                            <router-link :to="{name:'viewbbk',params:{bbk:bk.bbk}}" class="btn btn-primary">
                                    Lihat Data
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
            search:'',
            load:true,
            status:'open',
            bbk:[],
        }
    },
    created(){
        this.getBbk();
    },
    computed:{
        FilteredBbk(){
            if(this.search===""){
                if(this.status==="open"){
                    return this.bbk.filter(elem=> elem.status==="open")
                }else if(this.status==="close"){
                    return this.bbk.filter(elem=> elem.status==="close")
                }
            }else{
                return this.bbk.filter(elem => {
                return elem.nomor_so.toLowerCase().includes(this.search.toLowerCase());
            });
            }
        },
    },
    methods:{
        getBbk(){
            axios.get("/api/bbk")
            .then(res=>{
                this.bbk=res.data.data;
                this.load=false;
            })
        },
    }
}
</script>


<style>

</style>