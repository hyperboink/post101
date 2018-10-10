
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./scripts');

window.Pusher = require('pusher-js');
import Echo from "laravel-echo";

//window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/*Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});*/



/*window.Echo = new Echo({
    broadcaster: '609764',
    key: '85d2f45a771b61091973',
    cluster: 'eu',
    encrypted: true
});

var notifications = [];*/

//...

/*$(document).ready(function() {
    if(window.authId) {
        //...
        window.Echo.private(`App.User.${window.authId}`)
            .notification((notification) => {
                //addNotifications([notification], '#notifications');
            });
    }
});
*/