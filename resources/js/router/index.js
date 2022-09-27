import Vue from 'vue';
import VueRouter from 'vue-router';
import NewsIndex from '../pages/NewsIndex'

Vue.use(VueRouter);

const routes = [
    {
      path: '/',
      name: 'NewsIndex',
      component: NewsIndex
    },
    // {
    //   path: '/search',
    //   name: 'BookSearch',
    //   component: BookSearch
    // },
]

const router = new VueRouter({
    mode: 'history',
    routes
}
)
 export default router;