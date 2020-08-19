<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="Draft">Draft SO</option>
                <option value="Sent">Waiting list</option>
                <option value="Acc">Open</option>
                <option value="Tolak">Rejected</option>
                <option value="Selesai">Selesai</option>
            </select>
        </div>
        <div class="row">
            <router-link to="/so/create/" class="btn btn-success my-3" >+ Create SO</router-link>
        </div> 
            <div id="overflow" class="border-top">
            <table id="thead" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor SO</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Tanggal Kirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rs , index) in FilterKategori" :key="rs.nomor_so">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{rs.nomor_so}}</td>
                            <td style="text-align:center">{{rs.tanggal_so}}</td>
                            <td>{{rs.customer}}</td>
                            <td style="text-align:center">{{rs.tanggal_kirim}}</td>
                            <td  style="text-align:center">
                                <router-link :to="{name:'formso',params:{id:rs.nomor_so,rso:rs.nomor_rso}}" class="btn btn-primary" >
                                    Lihat Detail
                                </router-link>
                                <button @click="deleteSo(rs)" v-if="rs.status=='Draft' || rs.status=='Tolak'" class="btn btn-danger">Hapus</button>
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
            status:'Acc',
            so:[],
            load:true,
            update:{},
            listrso:{},
            uplsoneg:{},
            uplsopos:{},
        }
    },
    created(){
        this.getSo();
    },
    computed:{
        FilterKategori(){
            if(this.search===""){
                if(this.status==="Draft"){
                    return this.so.filter(elem=> elem.status==="Draft")
                }else if(this.status==="Acc"){
                    return this.so.filter(elem=> elem.status==="Acc")
                }else if(this.status==="Sent"){
                    return this.so.filter(elem=> elem.status==="Sent")
                }
                else if(this.status==="Tolak"){
                    return this.so.filter(elem=> elem.status==="Tolak")
                }
                else if(this.status==="Selesai"){
                    return this.so.filter(elem=> elem.status==="Selesai")
                }
            }else{
                return this.so.filter(elem => {
                return elem.nomor_so.toLowerCase().includes(this.search);
            });
            }
        },
    },
    methods:{
        getSo(){
            axios.get("/api/so")
            .then(res=>{this.so=res.data.data
                this.load=false;
            });
        },
        deleteSo(rs){
            let keputusan=confirm('yakin ingin menghapus SO ini?');
            if(keputusan==true){
                if(rs.statusso==="tersedia"){
                    this.update.status="Confirmed";
                    axios.put("/api/rso/"+rs.nomor_rso,this.update)
                    .then(res=>{
                        axios.get("/api/listrso/"+rs.nomor_rso)
                        .then(res=>{
                            this.listrso=res.data.data;
                            for(let i=0;i<this.listrso.length;i++){
                                if(this.listrso[i].qty_tersedia>0){
                                    this.uplsoneg.so_tersedia="N";
                                    axios.put("/api/listrso/"+this.listrso[i].id,this.uplsoneg)
                                        .then(res=>{
                                        });
                                    }else{
                                        this.uplsopos.so_tersedia="Y";
                                        axios.put("/api/listrso/"+this.listrso[i].id,this.uplsopos)
                                            .then(res=>{
                                        });
                                    }
                            }
                            axios.delete("/api/so/"+rs.nomor_so)
                            .then(res=>{
                                this.getSo();
                            })
                        });
                    });
                }else{
                    this.update.status="Confirmed";
                    axios.put("/api/rso/"+rs.nomor_rso,this.update)
                    .then(res=>{
                        axios.get("/api/listrso/"+rs.nomor_rso)
                        .then(res=>{
                            this.listrso=res.data.data;
                            for(let i=0;i<this.listrso.length;i++){
                                if(this.listrso[i].qty_tdktersedia>0 && this.listrso[i].acc_purch==="Y"){
                                    this.uplsopos.so_tdktersedia="N";
                                    axios.put("/api/listrso/"+this.listrso[i].id,this.uplsopos)
                                }else if(this.listrso[i].qty_tdktersedia>0 && this.listrso[i].acc_purch==="N"){
                                    this.uplsoneg.so_tdktersedia="Y";
                                    axios.put("/api/listrso/"+this.listrso[i].id,this.uplsoneg)
                                }
                            }
                            axios.delete("/api/so/"+rs.nomor_so)
                            .then(res=>{
                                this.getSo();
                            })
                        });
                    });
                    
                }
            }
        }
    }
}
</script>


<style>

</style>