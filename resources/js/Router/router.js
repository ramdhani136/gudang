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


import CustPriceComponent from '../components/customer/CustPriceComponent.vue'
import ItemPriceComponent from '../components/customer/ItemPriceComponent.vue'
import viewPriceComponent from '../components/customer/viewPriceComponent.vue'

import PuchaseRequestComponent from '../components/purchase/PuchaseRequestComponent.vue'
import poCreateViewComponent from '../components/purchase/poCreateViewComponent.vue'
import poComponent from '../components/purchase/poComponent.vue'
import dicPoComponent from '../components/purchase/dicPoComponent.vue'

import BbmComponent from '../components/bbm/BbmComponent.vue'
import CreateBbmComponent from '../components/bbm/CreateBbmComponent.vue'
import ViewBbmComponent from '../components/bbm/ViewBbmComponent.vue'

import BuktiCheckerInComponent from '../components/bc/BuktiCheckerInComponent.vue'
import CreateBcmComponent from '../components/bc/CreateBcmComponent.vue'
import ViewBcmComponent from '../components/bc/ViewBcmComponent.vue'
import RequestBcmComponent from '../components/bc/RequestBcmComponent.vue'
import ViewRequestComponent from '../components/bc/ViewRequestComponent.vue'

import SoComponentDic from '../components/distribusi/SoComponentDic.vue'
import BckComponent from '../components/distribusi/BckComponent.vue'
import BbkComponent from '../components/distribusi/BbkComponent.vue'





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

    {name:'datacust', path:'/price/customer', component:CustPriceComponent},
    {name:'dataItem', path:'/price/:id', component:ItemPriceComponent},
    {name:'viewItem', path:'/price/:customer/:barang', component:viewPriceComponent},

    {name:'pr', path:'/purchase/pr', component:PuchaseRequestComponent},
    {name:'poCreateView', path:'/purchase/po/view/:nomor', component:poCreateViewComponent},
    {name:'po', path:'/purchase/po/', component:poComponent},
    {name:'dpo', path:'/purchase/dpo', component:dicPoComponent},

    {name:'ingoods', path:'/dic/incoming', component:BbmComponent},
    {name:'creategoods', path:'/dic/incoming/view', component:CreateBbmComponent},
    {name:'viewbbm', path:'/dic/incoming/view/:nomor', component:ViewBbmComponent},

    {name:'bcmcomponent', path:'/dic/bcm', component:BuktiCheckerInComponent},
    {name:'createbcm', path:'/dic/bcm/view', component:CreateBcmComponent},
    {name:'viewbcm', path:'/dic/bcm/view/:nomor', component: ViewBcmComponent},
    {name:'rbcm', path:'/dic/rbcm', component: RequestBcmComponent},
    {name:'viewrbcm', path:'/dic/rbcm/view/:nomor', component: ViewRequestComponent},


    {name:'distribusiso', path:'/distribusi/so', component:SoComponentDic},
    {name:'distribusibck', path:'/distribusi/bck', component:BckComponent},
    {name:'distribusibbk', path:'/distribusi/bbk', component: BbkComponent},
]


Vue.use(VueRouter)
const router = new VueRouter({
    routes,
    hasbang:false,
    mode:'history'
});

export default router