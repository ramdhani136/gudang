<template>
    <div class="container">
        <button @click="showmodal()" class="btn btn-success my-3">+ Tambah RSO</button>
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>
        <div class="form-group col-3 my-3 float-right">
            <select name="status" v-model="status" class="form-control">
                <option value="Draft">Draft</option>
                <option value="Sent">Menunggu Konfirmasi</option>
                <option value="Confirmed">Confirmed</option>
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
                            <td v-if="rs.status=='Draft'" style="text-align:center">
                                <router-link :to="{name:'formrso',params:{id:rs.nomor_rso}}" class="btn btn-primary" >
                                    Input Detail
                                </router-link>
                                <button @click="deleteRso(rs)" class="btn btn-danger">Hapus</button>
                            </td>
                            <td v-if="rs.status=='Sent'" style="text-align:center">
                                <router-link :to="{name:'formrso',params:{id:rs.nomor_rso}}" class="btn btn-primary" >
                                    Lihat Detail
                                </router-link>
                            </td>
                            <td v-if="rs.status=='Confirmed'" style="text-align:center">
                                <router-link :to="{name:'formrso',params:{id:rs.nomor_rso}}" class="btn btn-primary" >
                                    Lihat Status
                                </router-link>
                                <button @click="deleteRso(rs)" class="btn btn-danger">Batalkan</button>
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
                        <input v-model="form.nomor_rso"  type="text" name="nomor_rso" autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input v-model="form.tanggal_rso" type="date" name="tanggal_rso"  autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Marketing</label>
                        <select v-model="form.nip_sales" class="form-control" name="user">
                            <option v-for="sl in sales" :key="sl.nip" :value="sl.nip">{{sl.nama}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <div class="autocomplete"></div>
                        <div class="input" @click="toggleVisible" v-text="custom ? custom.nama:''"></div>
                        <div class="placeholder" v-if="custom==null">Pilih Customer</div>
                        <div class="popover" v-show="visible">
                            <input type="text"
                            @keydown.up="up"
                            @keydown.down="down"
                            @keydown.enter="selectItem"
                            v-model="query"
                            placeholder="Masukan nama customer .."
                            >
                            <div class="option" ref="optionList">
                                <ul>
                                    <li v-for="(match,index) in matches" 
                                    :key="match.kode"
                                    v-text="match.nama"
                                    :class="{'selected':(selected==index)}"
                                    @click="itemClicked(index)"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea v-model="form.keterangan" name="keterangan"  class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="createRso()" class="btn btn-primary">Save RSO</button>
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
            status:'Draft',
            customer:[],
            form:{
                nomor_rso:'RSO-2020-',
                tanggal_rso:'',
                nip_sales:'',
                kode_customer:'',
                keterangan:'',
                status:'Draft'
            },
            sales:{},
            visible:false,
            query:'',
            selected:0,
            custom:null,
            itemHeight:39,
        }
    },
    created(){
        this.getRso()
        this.getCustomer()
        this.getSales()
    },
    computed:{
        FilterKategori(){
            if(this.search===""){
                if(this.status==="Draft"){
                    return this.rso.filter(elem=> elem.status==="Draft")
                }else if(this.status==="Sent"){
                    return this.rso.filter(elem=> elem.status==="Sent")
                }else if(this.status==="Confirmed"){
                    return this.rso.filter(elem=> elem.status==="Confirmed")
                }
            }else{
                return this.rso.filter(elem => {
                return elem.nomor_rso.toLowerCase().includes(this.search);
            });
            }
        },
        matches(){
            if(this.query==''){
                return [];
            }
            return this.customer.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
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
        getCustomer(){
            axios.get("/api/customer")
            .then(res=>this.customer=res.data.data)
        },
        createRso(){
            axios.post("/api/rso",this.form)
                .then((response)=>{
                    this.getRso();
                    this.$router.push({name:'rso'})
                    $("#modal-form").modal("hide");
                    this.resetForm()
                })
        },
        getSales(){
            axios.get("/api/sales")
            .then(res=>this.sales=res.data.data)
        },
        resetForm(){
            this.form.nomor_rso="RSO-2020-";
            this.form.tanggal_rso="";
            this.form.nip_sales="";
            this.form.kode_customer="";
            this.form.keterangan="";
            this.custom=null;
        },
        toggleVisible(){
            this.visible = !this.visible;
        },
        itemClicked(index){
            this.selected=index;
            this.selectItem();
        },
        selectItem(){
            this.custom = this.matches[this.selected];
            this.form.kode_customer= this.custom.kode
            this.visible=false;
        },
        up(){
            if(this.selected==0){
                return;
            }
            this.selected -= 1;
            this.scrollToItem();
        },
        down(){
            if(this.selected >= this.matches.length -1 ){
                return;
            }
            this.selected += 1;
            this.scrollToItem();
        },
        scrollToItem(){
            this.$refs.optionList.scrollTop = this.selected * this.itemHeight;
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


    .autocomplete{
        width: 100%;
        position: relative;
    }

    .input{
        min-height: 40px;
        border-radius: 3px;
        border: 1px solid lightgray;
        box-shadow: 0 0 10px #eceaea;
        padding-left: 10px;
        padding-top: 10px;
        cursor: text;
    }

    .popover{
        min-width: 100%;
        min-height: 50px;
        border:1px solid lightgray;
        position: relative;
        top:4px;
        left: 0;
        right: 0;
        background-color: #fff;
        border-radius: 3px;
        text-align: center;
    }

    .popover input{
        width:95%;
        margin-top: 5px;
        height: 40px;
        font-size: 14px;
        border-radius: 3px;
        border:solid 1px lightgray;
        padding-left: 8px;
    }

    .option{
        max-height: 150px;
        overflow-y: scroll;
        margin-top: 5px;
    }

    .option ul{
        list-style-type:none;
        text-align: left;
        padding-left:0;
    }

    .option ul li{
        border-bottom: 1px solid lightgrey;
        padding:10px;
        cursor: pointer;
        background-color: #f1f1f1;
    }

    .option ul li.selected{
        background-color: #58bd4c;
        color: #fff;
    }

    .placeholder{
        position: relative;
        color: #d0d0d0;
        pointer-events: none;
        margin-top: -30px;
        margin-left: 3%;
    }
    
</style>