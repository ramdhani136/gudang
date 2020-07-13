<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 ml-n3 float-left">
            <select name="status" v-model="status" class="form-control">
                <option value="Sent">Request Masuk</option>
                <option value="Confirmed">Confirm DIC</option>
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
                            <tr v-for="(rs , index) in FilterKategori" :key="rs.nomor_rso">
                                <td style="text-align:center">{{index+1}}</td>
                                <td style="text-align:center">{{rs.nomor_rso}}</td>
                                <td style="text-align:center">{{rs.tanggal_rso}}</td>
                                <td>{{rs.customer}}</td>
                                <td style="text-align:center">
                                    <router-link :to="{name:'dicform',params:{id:rs.nomor_rso}}" class="btn btn-primary" >
                                        Detail RSO
                                    </router-link>
                                    <button @click="ConfirmRso(rs)" class="btn btn-success">Confirm</button>
                                </td>
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>
</template>

<script>
export default {
    data(){
        return{
            search  : '',
            rso:[],
            status:'Sent',
            urso:{}
        }
    },
    created(){
        this.getRso()
    },
    computed:{
        FilterKategori(){
            if(this.search===""){
                if(this.status==="Sent"){
                    return this.rso.filter(elem=> elem.status==="Sent")
                }else if(this.status==="Confirmed"){
                    return this.rso.filter(elem=> elem.status==="Confirmed")
                }
            }else{
                return this.rso.filter(elem => {
                return elem.nomor_rso.toLowerCase().includes(this.search);
            });
            }
        }
    },
    methods:{
        getRso(){
            axios.get("/api/rso")
            .then(res=>this.rso=res.data.data)
        },
        ConfirmRso(rs){
            this.getRso()
            this.urso.kode_customer=rs.kode_customer
            this.urso.status="Confirmed"
            this.urso.nomor_rso=rs.nomor_rso
            this.urso.id_user=rs.id_user
            this.urso.tanggal_rso=rs.tanggal_rso
            axios.put(`/api/rso/`+this.urso.nomor_rso, this.urso)
            .then(console.log("berhasil"))
        }
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