import Vue from 'vue'
import VueRouter from 'vue-router'

import BarangComponent from '../components/barang/BarangComponent.vue'

const routes=[
    {name:'barang', path:'/barang', component:BarangComponent},
]

Vue.use(VueRouter)
const router = new VueRouter({
    routes,
    hasbang:false,
    mode:'history'
});

export default router