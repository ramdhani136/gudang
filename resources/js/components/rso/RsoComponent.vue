<template>
    <div class="container">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah RSO</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 float-right">
            <select name="status" v-model="status" class="form-control">
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="confirmed">Confirmed</option>
            </select>
        </div>
            <div id="overflow" class="border-top">
            <table id="thead" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor RSO</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rs , index) in filterderRso" :key="rs">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{rs.nomor_rso}}</td>
                            <td style="text-align:center">{{rs.tanggal_rso}}</td>
                            <td>{{rs.customer}}</td>
                            <td style="text-align:center">
                                <button @click="updateRso(rs)" class="btn btn-primary">Edit</button>
                                <button @click="deleteRso(rs)" class="btn btn-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
            </table>
            </div>
            <div class="modal fade" id="modal-form" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div id="modal-width" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form RSO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nomor RSO</label>
                        <input  type="text" name="nomor_rso" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_rso"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Marketing</label>
                        <select class="form-control" name="user">
                            <option value="Rotamba">Rotamba</option>
                            <option value="Miana">Miana</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <select class="form-control" name="customer">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan"  class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button"  class="btn btn-primary">Save RSO</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            search  : '',
            rso:[],
            status:'draft',
        }
    },
    created(){
        this.getRso()
    },
    computed:{
        filterderRso(){
            return this.rso.filter(elem => {
            return elem.nomor_rso.toLowerCase().includes(this.search);
            });
        }
    },
    methods:{
        getRso(){
            axios.get("/api/rso")
            .then(res=>this.rso=res.data.data)
        },
        deleteRso(rso){
            let keputusan=confirm('Apakah anda yakin?');
            if(keputusan===true){
                axios.delete("/api/rso/" + rso.nomor_rso)
                .then(response=>{
                    this.getRso();
                })
                .catch(error=>{
                    console.log(error)
                })
            }
        },
        showmodal(){
            $("#modal-form").modal("show");
        },
    }
}
</script>


<style>
    #overflow{
        width: 100%;
    height: 440px;
    overflow-y: scroll;
    }

    #thead thead tr th{
        text-align: center;
        border-bottom: none;
        position: sticky; top: 0; 
        background-color: #fff;
        top: -1px;
        border-collapse: collapse;
        box-shadow: inset 0 0 0 #dee2e6,
        inset 0 -1px 0 #dee2e6;
    }

    #modal-width{
    width: 120%;
    height: auto;
    right: 13%;
    }
    
</style>