<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="open">Open</option>
                <option value="close">Close</option>
            </select>
        </div> 
        <div id="overflow" class="border-top">
                <table id="thead" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Checker Keluar</th>
                        <th>Tanggal</th>
                        <th>Nomor SO</th>
                        <th>Customer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(bk,index) in FilteredBck" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td style="text-align:center">{{bk.bck}}</td>
                        <td style="text-align:center">{{bk.tanggal}}</td>
                        <td style="text-align:center">{{bk.nomor_so}}</td>
                        <td>{{bk.customer}}</td>
                        <td style="text-align:center">
                            <router-link :to="{name:'viewbck',params:{bck:bk.bck}}" class="btn btn-primary">
                                    Lihat Data
                            </router-link>
                            <button @click="print()" v-if="bk.status==='open'" class="btn btn-none">Print Bukti Checker</button>
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
import { jsPDF } from "jspdf";
import 'jspdf-autotable'

export default {
    components: {
        Circle5
    },
    data(){
        return{
            search:'',
            load:true,
            status:'open',
            bck:[],
        }
    },
    created(){
        this.getBck();
    },
    computed:{
        FilteredBck(){
            if(this.search===""){
                if(this.status==="open"){
                    return this.bck.filter(elem=> elem.status==="open")
                }else if(this.status==="close"){
                    return this.bck.filter(elem=> elem.status==="close")
                }
            }else{
                return this.bck.filter(elem => {
                return elem.nomor_so.toLowerCase().includes(this.search).toLowerCase();
            });
            }
        },
    },
    methods:{
        getBck(){
            axios.get("/api/bck")
            .then(res=>{
                this.bck=res.data.data;
                this.load=false;
            })
        },
        print(){
            const doc = new jsPDF({
            orientation: "landscape",
            unit: "in",
            });
            doc.setFontSize(16);
            doc.text("Form Bukti Checker Keluar", 0.3, 0.3);
            window.open(doc.output('bloburl'), '_blank')
            /*doc.save("bck.pdf"); */  
        }
    }
}
</script>


<style>

</style>