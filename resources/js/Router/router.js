import Vue from 'vue'
import VueRouter from 'vue-router'

import BarangComponent from '../components/barang/BarangComponent.vue'
import CustomerComponent from '../components/customer/CustomerComponent.vue'
import DivisiComponent from '../components/divisi/DivisiComponent.vue'

import RsoComponent from '../components/rso/RsoComponent.vue'
import ListRsoComponent from '../components/rso/ListRsoComponent.vue'

import DicComponent from '../components/rso/DicCOmponent.vue'
import RequestRsoComponent from '../components/rso/RequestRsoComponent.vue'




const routes=[
    {name:'barang', path:'/barang', component:BarangComponent},
    {name:'customer', path:'/customer', component:CustomerComponent},
    {name:'divisi', path:'/divisi', component:DivisiComponent},

    {name:'rso', path:'/rso', component:RsoComponent},
    {name:'formrso', path:'/formrso/:id', component:ListRsoComponent},

    {name:'dic', path:'/dic', component:DicComponent},
    {name:'dicform', path:'/dic/view/:id', component:RequestRsoComponent},
]

Vue.use(VueRouter)
const router = new VueRouter({
    routes,
    hasbang:false,
    mode:'history'
});

export default router