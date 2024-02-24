/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
Echo.channel(`new-match`)
    .listen('.new.match', (e) => {
        console.log(e);
    });
Echo.channel(`new-message`)
    .listen('.new.message', (e) => {
        console.log(e);
    });
