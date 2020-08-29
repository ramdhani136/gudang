import Vue from 'vue'
import VueRouter from 'vue-router'

import BarangComponent from '../components/barang/BarangComponent.vue'
import PurchaseBarangComponent from '../components/barang/PurchaseBarangComponent.vue'
import CustomerComponent from '../components/customer/CustomerComponent.vue'
import DivisiComponent from '../components/divisi/DivisiComponent.vue'
import SupplierComponent from '../components/supplier/SupplierComponent.vue'
import SalesComponent from '../components/sales/SalesComponent.vue'

import RsoComponent from '../components/rso/RsoComponent.vue'
import ListRsoComponent from '../components/rso/ListRsoComponent.vue'

import DicComponent from '../components/rso/DicCOmponent.vue'
import RequestRsoComponent from '../components/rso/RequestRsoComponent.vue'
import PurchaseComponent from '../components/purchase/PurchaseComponent.vue'
import ListPurchaseComponent from '../components/purchase/ListPurchaseComponent.vue'

import CreateSoComponent from '../components/so/CreateSoComponent.vue'
import FormCreateSoComponent from '../components/so/FormCreateSoComponent.vue'
import SoComponent from '../components/so/SoComponent.vue'
import FormSoComponent from '../components/so/FormSoComponent.vue'
import SoConfirmComponent from '../components/so/SoConfirmComponent.vue'
import ViewSoConfirmComponent from '../components/so/ViewSoConfirmComponent.vue'
import RSelesaiSoComponent from '../components/so/RSelesaiSoComponent.vue'

import CustPriceComponent from '../components/customer/CustPriceComponent.vue'
import ItemPriceComponent from '../components/customer/ItemPriceComponent.vue'
import viewPriceComponent from '../components/customer/viewPriceComponent.vue'
import RequestPriceComponent from '../components/customer/RequestPriceComponent.vue'

import PuchaseRequestComponent from '../components/purchase/PuchaseRequestComponent.vue'
import poCreateViewComponent from '../components/purchase/poCreateViewComponent.vue'
import poComponent from '../components/purchase/poComponent.vue'
import dicPoComponent from '../components/purchase/dicPoComponent.vue'
import PoSelesaiComponent from '../components/purchase/PoSelesaiComponent.vue'
import AccPoComponent from '../components/purchase/AccPoComponent.vue'
import poAccViewComponent from '../components/purchase/poAccViewComponent.vue'

import BbmComponent from '../components/bbm/BbmComponent.vue'
import CreateBbmComponent from '../components/bbm/CreateBbmComponent.vue'
import ViewBbmComponent from '../components/bbm/ViewBbmComponent.vue'

import BuktiCheckerInComponent from '../components/bc/BuktiCheckerInComponent.vue'
import CreateBcmComponent from '../components/bc/CreateBcmComponent.vue'
import ViewBcmComponent from '../components/bc/ViewBcmComponent.vue'
import RequestBcmComponent from '../components/bc/RequestBcmComponent.vue'
import ViewRequestComponent from '../components/bc/ViewRequestComponent.vue'

import SoComponentDic from '../components/distribusi/SoComponentDic.vue'

import BbkComponent from '../components/distribusi/bbk/BbkComponent.vue'
import CreateBbkComponent from '../components/distribusi/bbk/CreateBbkComponent.vue'
import ViewBbkComponent from '../components/distribusi/bbk/ViewBbkComponent.vue'

import ViewBckComponent from '../components/distribusi/bck/ViewBckComponent.vue'
import CreateBckComponent from '../components/distribusi/bck/CreateBckComponent.vue'
import BckComponent from '../components/distribusi/bck/BckComponent.vue'



import EkspedisiComponent from '../components/ekspedisi/EkspedisiComponent.vue'

import KendaraanComponent from '../components/kendaraan/KendaraanComponent.vue'
import JenisKendaraanComponent from '../components/kendaraan/JenisKendaraanComponent.vue'

import PrComponent from '../components/dicpr/PrComponent.vue'
import CreatePrcomponent from '../components/dicpr/CreatePrcomponent.vue'
import ViewPrcomponent from '../components/dicpr/ViewPrcomponent.vue'

import ReturComponent from '../components/retur/ReturComponent.vue'
import CreateReturComponent from '../components/retur/CreateReturComponent.vue'
import ViewReturComponent from '../components/retur/ViewReturComponent.vue'


const routes=[
    {name:'barang', path:'/barang', component:BarangComponent},
    {name:'purchasebarang', path:'/purchase/barang/', component:PurchaseBarangComponent},
    {name:'customer', path:'/customer', component:CustomerComponent},
    {name:'divisi', path:'/divisi', component:DivisiComponent},
    {name:'supplier', path:'/supplier', component:SupplierComponent},
    {name:'sales', path:'/sales', component:SalesComponent},

    {name:'rso', path:'/rso', component:RsoComponent},
    {name:'formrso', path:'/formrso/:id', component:ListRsoComponent},

    {name:'dic', path:'/dic', component:DicComponent},
    {name:'dicform', path:'/dic/view/:id', component:RequestRsoComponent},

    {name:'purchase', path:'/purchase', component:PurchaseComponent},
    {name:'listpurchase', path:'/purchase/view/:id', component:ListPurchaseComponent},

    {name:'createso', path:'/so/create/:id', component:CreateSoComponent},
    {name:'formcreateso', path:'/so/create/', component:FormCreateSoComponent},
    {name:'soconfirm', path:'/confirmso', component:SoConfirmComponent},
    {name:'viewso', path:'/so/confirm/:id', component:ViewSoConfirmComponent},
    {name:'so', path:'/so', component:SoComponent},
    {name:'formso', path:'/so/form/:id', component:FormSoComponent},
    {name:'requestselesaiso', path:'/so/data/selesai', component:RSelesaiSoComponent},

    {name:'datacust', path:'/price/customer', component:CustPriceComponent},
    {name:'dataItem', path:'/price/:id', component:ItemPriceComponent},
    {name:'viewItem', path:'/price/:customer/:barang', component:viewPriceComponent},
    {name:'rprice', path:'/data/price/request', component:RequestPriceComponent},

    {name:'pr', path:'/purchase/pr', component:PuchaseRequestComponent},
    {name:'poCreateView', path:'/purchase/po/view/:nomor', component:poCreateViewComponent},
    {name:'po', path:'/purchase/po/', component:poComponent},
    {name:'dpo', path:'/purchase/dpo', component:dicPoComponent},
    {name:'porequestselesai', path:'/purchase/po/selesai', component:PoSelesaiComponent},
    {name:'accpo', path:'/purchase/po/acc', component:AccPoComponent},
    {name:'poAccView', path:'/purchase/po/acc/:nomor', component:poAccViewComponent},
    

    {name:'ingoods', path:'/dic/incoming', component:BbmComponent},
    {name:'creategoods', path:'/dic/incoming/view', component:CreateBbmComponent},
    {name:'viewbbm', path:'/dic/incoming/view/:nomor', component:ViewBbmComponent},

    {name:'bcmcomponent', path:'/dic/bcm', component:BuktiCheckerInComponent},
    {name:'createbcm', path:'/dic/bcm/view', component:CreateBcmComponent},
    {name:'viewbcm', path:'/dic/bcm/view/:nomor', component: ViewBcmComponent},
    {name:'rbcm', path:'/dic/rbcm', component: RequestBcmComponent},
    {name:'viewrbcm', path:'/dic/rbcm/view/:nomor', component: ViewRequestComponent},


    {name:'distribusiso', path:'/distribusi/so', component:SoComponentDic},

    {name:'distribusibbk', path:'/distribusi/bbk', component: BbkComponent},
    {name:'createbbk', path:'/distribusi/bbk/create', component: CreateBbkComponent},
    {name:'viewbbk', path:'/distribusi/bbk/view/:bbk', component: ViewBbkComponent},

    {name:'bck', path:'/distribusi/bck', component:BckComponent},
    {name:'createbck', path:'/bck/create', component:CreateBckComponent},
    {name:'viewbck', path:'/bck/view/:bck', component:ViewBckComponent},

    {name:'ekspedisi', path:'/ekspedisi', component: EkspedisiComponent},

    {name:'kendaraan', path:'/distribusi/kendaraan', component: KendaraanComponent},
    {name:'jkendaraan', path:'/distribusi/jkendaraan', component: JenisKendaraanComponent},
    
    {name:'Prcomponent', path:'/pr', component:PrComponent},
    {name:'CreatePrcomponent', path:'/pr/create', component:CreatePrcomponent},
    {name:'ViewPrcomponent', path:'/pr/view/:nomor', component:ViewPrcomponent},

    {name:'returcomponent', path:'/retur', component:ReturComponent},
    {name:'createretur', path:'/retur/create', component:CreateReturComponent},
    {name:'viewretur', path:'/retur/view/:nomor', component:ViewReturComponent},  
]


Vue.use(VueRouter)
const router = new VueRouter({
    routes,
    hasbang:false,
    mode:'history'
});

export default router