<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="draft">Draft</option>
                <option value="open">Open</option>
                <option value="close">Close</option>
            </select>
        </div> 
        <div class="row">
            <button class="btn btn-success my-3">+ Create BBM</button>
        </div>
        <div id="overflow" class="border-top">
                <table id="thead" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor BBM</th>
                        <th>Tanggal</th>
                        <th>Nomor PO</th>
                        <th>Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(bm,index) in FilteredBBM" :key="index">
                        <td style="text-align:center">{{index+1}}</td>
                        <td style="text-align:center">{{bm.bbm}}</td>
                        <td style="text-align:center">{{bm.tanggal}}</td>
                        <td style="text-align:center">{{bm.nomor_po}}</td>
                        <td style="text-align:center">{{bm.supplier}}</td>
                        <td style="text-align:center">
                            <button class="btn btn-primary">Lihat Detail</button>
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
            status:'draft',
            bbm:[],
        }
    },
    created(){
        this.getBbm();
    },
    computed:{
        FilteredBBM(){
            return this.bbm.filter(elem => {
            return elem.nomor_po.toLowerCase().includes(this.search);
        });
        },
    },
    methods:{
        getBbm(){
            axios.get("/api/bbm")
            .then(res=>{
                this.bbm=res.data.data;
            })
        }
    }
}
</script>


<style>

</style>