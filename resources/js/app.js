/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./toggleMenu');
require('./accordionMenu');
require('./preview');
require('./confirm');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('search-work-list', require('./components/SearchWorkList.vue').default);
Vue.component('work-list', require('./components/WorkList.vue').default);
Vue.component('work-detail', require('./components/WorkDetail.vue').default);
Vue.component('comment-list', require('./components/CommentList.vue').default);
Vue.component('board-list', require('./components/BoardList.vue').default);
Vue.component('message-list', require('./components/MessageList.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
