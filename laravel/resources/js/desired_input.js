require('./bootstrap')

window.Vue = require('vue')

// Mixin


const files = require.context('./desired/input', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Common
Vue.component('hour-min-select', require('./components/HourMinSelect.vue').default)

const app = new Vue({
    el: '#desired_input'
})
