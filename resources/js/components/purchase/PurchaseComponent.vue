<template>
    <div class="autocomplete">
        <div class="input" @click="toggleVisible" v-text="oncom ? oncom.nama:''"></div>
        <div class="placeholder" v-if="oncom == null"> Select One ..</div>
        <div class="form-group">
            <input type="text" class="form-control" :value="oncom ? oncom.kode:''">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" :value="oncom ? oncom.satuan:''">
        </div>
        <div class="popover" v-show="visible">
            <input type="text"
            @keydown.up="up"
            @keydown.down="down"
            @keydown.enter="selectedItem"
            v-model="query"
            placeholder="Start Typing ...">
                <div class="option" ref="optionList">
                    <ul>
                        <li v-for="(match,index) in matches" 
                        :key="match.nama"
                        @click="itemClicked(index)"
                        :class="{'selected': (selected==index)}"
                        v-text="match.nama"></li>
                    </ul>
                </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            itemHeight:39,
            user:[],
            visible:false,
            query:'',
            selected:0,
            oncom:null
        }
    },
    created(){
        this.getBarang()
    },
    methods:{
        toggleVisible(){
            this.visible= !this.visible;
        },
        itemClicked(index){
            this.selected=index;
            this.selectedItem();
        },
        selectedItem(){
            this.oncom= this.matches[this.selected];
            this.visible=false;
        },
        getBarang(){
            axios.get("/api/barang")
            .then(res=>this.user=res.data.data)
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
    },
    computed:{
        matches(){
            if(this.query==''){
                return [];
            }
            return this.user.filter((item)=>item.nama.toLowerCase().includes(this.query.toLowerCase()))
        }
    }
}
</script>

<style   scoped>

    .autocomplete{
        width: 100%;
        position: relative;
    }

    .input{
        min-height: 40px;
        border-radius: 3px;
        border: 2px solid lightgray;
        box-shadow: 0 0 10px #eceaea;
        font-size: 25px;
        padding-left: 10px;
        padding-top: 5px;
        cursor: text;
    }

    .popover{
        min-height: 50px;
        border:2px solid lightgray;
        position: absolute;
        top:46px;
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
        font-size: 16px;
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
        font-weight: 600;
    }

    .placeholder{
        position: absolute;
        top:2px;
        left: 10px;
        font-size: 25px;
        color: #d0d0d0;
        pointer-events: none;
    }

    
</style>