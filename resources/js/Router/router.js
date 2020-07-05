import Vue from 'vue'
import VueRouter from 'vue-router'

import BarangComponent from '../components/barang/BarangComponent.vue'

import CustomerComponent from '../components/customer/CustomerComponent.vue'

import DivisiComponent from '../components/Divisi/DivisiComponent.vue'

const routes=[
    {name:'barang', path:'/barang', component:BarangComponent},

    {name:'customer', path:'/customer', component:CustomerComponent},

    {name:'divisi', path:'/divisi', component:DivisiComponent},
]

Vue.use(VueRouter)
const router = new VueRouter({
    routes,
    hasbang:false,
    mode:'history'
});

export default router