
import VueRouter from 'vue-router';
import routes from './user-routes';

const router = new VueRouter({
    mode: 'history',
    routes
})

const user = new Vue({
    el: '#user-module',
    router
});
