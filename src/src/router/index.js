import Vue from 'vue'
import Router from 'vue-router'
import startup from '@/components/geoapp/startup/app'
import portal from '@/components/geoapp/portal/app'
import login from '@/components/geoapp/login/app'
import data from '@/components/geoapp/data/app'
import basemap from '@/components/geoapp/basemap/app'
import datalayer from '@/components/geoapp/datalayer/app'
import datamap from '@/components/geoapp/datamap/app'
import mapedit from '@/components/geoapp/mapedit/app'
import about from '@/components/geoapp/about/app'
import registration from '@/components/geoapp/registration/app'

Vue.use(Router)

export default new Router({
  routes: [
    {path: '/', name: 'startup', component: startup, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }},
    {path: '/portal', name: 'portal', component: portal, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }},
    {path: '/login', name: 'login', component: login, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }},
    {path: '/data', name: 'data', component: data, meta: { requiresAuth: true, AuthCheck: false, AuthClear: false }},
    {path: '/datalayer', name: 'datalayer', component: datalayer, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }},
    {path: '/datamap', name: 'datamap', component: datamap, meta: { requiresAuth: true, AuthCheck: false, AuthClear: false }},
    {path: '/mapedit', name: 'mapedit', component: mapedit, meta: { requiresAuth: true, AuthCheck: false, AuthClear: false }},
    {path: '/basemap', name: 'basemap', component: basemap, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }},
    {path: '/about', name: 'about', component: about, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }},
    {path: '/registration', name: 'registration', component: registration, meta: { requiresAuth: false, AuthCheck: false, AuthClear: false }}
  ]
})
