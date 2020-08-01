<template>
    <div class="container">
        <button class="btn btn-success my-3">+ Create PO</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div id="overflow" class="border-top">
        <table id="thead" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Diminta</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(prlist , index) in FilteredPr" :key="index">
                <td style="text-align:center">{{index+1}}</td>
                <td style="text-align:center">{{prlist.kode_barang}}</td>
                <td>{{prlist.nama}}</td>
                <td style="text-align:center">{{prlist.jumlah}}</td>
                <td style="text-align:center">{{prlist.satuan}}</td>
                <td style="text-align:center">
                    <button @click="showmodal(prlist)" class="btn btn-primary">Lihat Rincian</button>
                </td>
            </tr>
        </tbody>
    </table>
    <Circle5 id="load" v-if="load"></Circle5>
    </div>
    <div class="modal fade" id="modal-pr" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div  class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rincian Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="scrollList">
                        <table  id="thead" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center">No</th>
                                    <th style="text-align:center">Nomor SO</th>
                                    <th>Customer</th>
                                    <th style="text-align:center">Jumlah</th>
                                    <th style="text-align:center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(prl,index) in prlist" :key="index">
                                    <td style="text-align:center">{{index+1}}</td>
                                    <td style="text-align:center">{{prl.nomor_so}}</td>
                                    <td>{{prl.nama_customer}}</td>
                                    <td style="text-align:center">{{prl.qty_tdktersedia}}</td>
                                    <td style="text-align:center">{{prl.satuan}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-orange">Create PO</button>
                </div>
                </div>
            </div>
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
            pr:[],
            load:true,
            listPr:{},
            tujuan:{},
            prlist:{}
        }
    },
    created(){
        this.getPr()
    },
    computed:{
        FilteredPr(){
            return this.pr.filter(elem => {
            return elem.nama.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getPr(){
            axios.get("/api/listrso/data/group")
            .then(res=>{this.pr=res.data.data
                this.load=false;
            });
        },
        showmodal(prlist){
            this.tujuan.kode=prlist.kode_barang;
            axios.get("/api/listrso/data/group/"+this.tujuan.kode)
            .then(res=>{this.prlist=res.data.data;

            });
            $("#modal-pr").modal("show");
        },
    }
}
</script>


<style>
    #scrollList{
        width: 100%;
        max-height:400px ;
        overflow-y: scroll;
        font-size: 0.9em;
    }
</style>