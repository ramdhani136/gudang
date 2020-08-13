<template>
    <div class="container">
        <div class="form-group col-3 my-3 float-right">
            <input v-model="search"  type="text" class="form-control" placeholder="Search">
        </div>  
            <div id="overflow" class="border-top">
            <table id="thead" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Estimasi Kedatangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(pl , index) in FilterKategori" :key="index">
                            <td style="text-align:center">{{index+1}}</td>
                            <td style="text-align:center">{{pl.nomor_po}}</td>
                            <td style="text-align:center">{{pl.tanggal_po}}</td>
                            <td>{{pl.supplier}}</td>
                            <td style="text-align:center">{{pl.tanggal_datang}}</td>
                            <td  style="text-align:center">
                                <router-link :to="{name:'poCreateView',params:{nomor:pl.nomor_po}}" class="btn btn-primary" >
                                    Lihat Detail
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
            search  : '',
            status:'Acc',
            po:[],
            load:true,
            visible:false,
            query:'',
            selected:0,
            supply:null,
            itemHeight:39,
            supplier:[],
            purchasing:{},
            upload:{
                nomor_po:this.po_nomor(),
                tanggal_po:this.now(),
                tanggal_datang:this.now(),
            },
            file:{}
        }
    },
    created(){
        this.getPo();
        this.getSupplier();
        this.getPurchasing();
    },
    computed:{
        FilterKategori(){
            if(this.search===""){
                if(this.status==="Draft"){
                    return this.po.filter(elem=> elem.status==="Draft")
                }else if(this.status==="Request"){
                    return this.po.filter(elem=> elem.status==="Request")
                }else if(this.status==="Acc"){
                    return this.po.filter(elem=> elem.status==="Acc")
                }else if(this.status==="Tolak"){
                    return this.po.filter(elem=> elem.status==="Tolak")
                }else if(this.status==="Selesai"){
                    return this.po.filter(elem=> elem.status==="Selesai")
                }
            }else{
                return this.po.filter(elem => {
                return elem.nomor_po.toLowerCase().includes(this.search);
            });
            }
        },
        matches(){
            if(this.query==''){
                return [];
            }
            return this.supplier.filter((item)=> item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    },
    methods:{
        getPo(){
            axios.get("/api/po")
            .then(res=>{this.po=res.data.data
                this.load=false;
            });
        },
        getSupplier(){
            axios.get("/api/supplier")
            .then(res=>{this.supplier=res.data.data;
            });
        },
        getPurchasing(){
            axios.get("/api/purchasing")
            .then(res=>{this.purchasing=res.data.data;
            });
        },
        showmodalPO(){
            $("#modal-po").modal("show");
        },now(){
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" + (day<10 ? '0' : '') + day;
            return output
            },
        validate(){
            if(this.form.tanggal_rso < this.now()){
                this.form.tanggal_rso=this.now();
            }
        },
        po_nomor(){
            var d = new Date();
            var month = d.getMonth()+1;

            var output = "PO-" + d.getFullYear() + "-" + (month<10 ? '0' : '') + month + "-" ;
            return output
        },
        toggleVisible(){
            this.visible = !this.visible;
        },
        itemClicked(index){
            this.selected=index;
            this.selectItem();
        },
        selectItem(){
            this.supply = this.matches[this.selected];
            this.upload.kode_supplier= this.supply.kode_supplier;
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
        },
        resetForm(){
            this.upload={
                nomor_po:this.po_nomor(),
                tanggal_po:this.now(),
                tanggal_datang:this.now(),
            };
            this.supply=null;
            this.visible=false;
        },
        CreatePo(){
            this.upload.status="Draft";
            axios.post('/api/po',this.upload)
            .then(res=>{
            $("#modal-po").modal("hide");
            this.$router.push({name:'poCreateView',params:{nomor:this.upload.nomor_po}})
            });
        },
        getHapus(pl){
            let jawab= confirm("Apakah anda yakin ingin menghapus PO Ini?");
            if(jawab===true){
                axios.delete("/api/po/"+pl.nomor_po)
                .then(res=>{
                    this.file.open_po="N";
                    this.file.harga_supplier=0;  
                    this.file.nomor_po="";
                        axios.put("/api/listrso/data/deletePo/"+pl.nomor_po,this.file)
                        .then(res=>{
                            this.getPo();
                            this.getSupplier();
                            this.getPurchasing();
                        });
                });
            }
        }
    }
}
</script>


<style>

</style>