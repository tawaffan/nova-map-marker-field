Nova.booting((Vue, router, store) => {
    Vue.component('index-nova-map-marker-field', require('./components/IndexField'))
    Vue.component('detail-nova-map-marker-field', require('./components/DetailField'))
    Vue.component('form-nova-map-marker-field', require('./components/FormField'))
})
