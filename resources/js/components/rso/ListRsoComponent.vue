<template>
    <div class="container">
        <div class="row row-cols-2">
            <div class="col-4">
                <div class="form-group">
                    <label>Nomor Rso :</label>
                    <input v-model="data"  type="text" class="form-control col-12" :disabled="disabled == 1">
                </div>
                <div class="form-group">
                    <label>Tanggal :</label>
                    <input v-model="form.tanggal_rso" type="date" class="form-control col-12" value="07-07-2020" :disabled="disabled == 1">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Customer :</label>
                    <select  v-model="form.kode_customer"  name="customer" class="col-12 form-control" :disabled="disabled == 1">
                        <option v-for="custom in customers" :key="custom.kode" :value="custom.kode" >{{custom.nama}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Marketing :</label>
                    <select v-model="form.id_user" name="marketing" class="col-12 form-control" :disabled="disabled == 1">
                        <option value="1">Rotamba</option>
                        <option value="2">Miana</option>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>keterangan</label>
                    <textarea v-model="form.keterangan"  name="keterangan" class="form-control col-12" :disabled="disabled == 1"></textarea>
                </div>
                <div class="form-group">
                    <button @click="getdisabled()" class="btn btn-primary col-4">{{tombol}}</button>
                    <button v-if="tbsukses" class="btn btn-success col-4 ml-1">Update</button>
                </div>
            </div>
        </div>
    </div>   
</template>

<script>
export default {
    data(){
        return {
            data:'',
            form:{},
            tbsukses:false,
            tombol:'Edit RSO',
            disabled:1,
            customers:[],
            
        }
    },
    created(){
        axios.get(`/api/rso/${this.$route.params.id}`)
        .then(res=>this.form=res.data.data)
    },
    mounted(){
        axios.get("/api/customer")
        .then(res=>this.customers=res.data.data)
    },
    methods:{
        getdisabled(){
            this.disabled = (this.disabled + 1) % 2;
            if(this.disabled==1){
                this.tombol="Edit Rso";
                this.tbsukses=false;
            }else{
                this.tombol="Close";
                this.tbsukses=true;
            }
        },
    },
}
</script>

<style scoped>

</style>