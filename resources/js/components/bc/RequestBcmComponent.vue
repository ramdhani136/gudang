<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="sent">Request Acc</option>
                <option value="open">Open</option>
                <option value="close">Close</option>
            </select>
        </div> 
        <div id="overflow" class="border-top">
                <table id="thead" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Checker Masuk</th>
                        <th>Tanggal</th>
                        <th>Nomor PO</th>
                        <th>Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(bm,index) in FilteredBCM" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td style="text-align:center">{{bm.bcm}}</td>
                        <td style="text-align:center">{{bm.tanggal}}</td>
                        <td style="text-align:center">{{bm.nomor_po}}</td>
                        <td>{{bm.supplier}}</td>
                        <td style="text-align:center">
                            <router-link :to="{name:'viewrbcm',params:{nomor:bm.bcm}}" class="btn btn-primary">
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
            load:false,
            status:'sent',
            bcm:[],
        }
    },
    created(){
        this.getBcm();
    },
    computed:{
        FilteredBCM(){
            if(this.search===""){
                if(this.status==="open"){
                    return this.bcm.filter(elem=> elem.status==="open")
                }else if(this.status==="open"){
                    return this.bcm.filter(elem=> elem.status==="close")
                }else if(this.status==="sent"){
                    return this.bcm.filter(elem=> elem.status==="sent")
                }
            }else{
                return this.bcm.filter(elem => {
                return elem.nomor_po.toLowerCase().includes(this.search);
            });
            }
        },
    },
    methods:{
        getBcm(){
            axios.get("/api/bcm")
            .then(res=>{
                this.bcm=res.data.data;
            })
        },
    }
}
</script>


<style>

</style>