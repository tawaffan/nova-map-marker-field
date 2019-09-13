<template>
    <panel-item :field="field">
        <div slot="value">
            <div class="rounded flex flex-col w-full"
                :style="'height: ' + field.height">
                <l-map :id="field.name" ref="map"
                    :zoom="defaultZoom"
                    :center="mapCenter"
                    :options="mapOptions"
                    class="w-full h-full rounded z-10">
                    <l-tile-layer
                        layerType="base" 
                        :name="tileProvider.name" 
                        :attribution="tileProvider.attribution" 
                        :url="tileProvider.url"
                        :key="tileProvider.name">
                    </l-tile-layer>
                    <l-marker :options="markerOptions" :lat-lng="mapCenter"></l-marker>
                </l-map>
            </div>
        </div>
    </panel-item>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import L from 'leaflet';
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

export default {
    components: {
        LMap, LTileLayer, LMarker
    },

    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            defaultLatitude: this.field.defaultLatitude || 0,
            defaultLongitude: this.field.defaultLongitude || 0,
            defaultZoom: this.field.defaultZoomDetail || 12,
            mapOptions: {
                boxZoom: false,
                doubleClickZoom: 'center',
                dragging: false,
                scrollWheelZoom: 'center',
                touchZoom: 'center',
            },
            markerOptions: {
                interactive: false
            },
            tileProvider: {
                name: 'OpenStreetMap',
                attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
            }
        }
    },

    computed: {
        mapCenter() {
            if (this.value.latitude === undefined) {
                this.setInitialValue();
            }

            return [
                this.value.latitude || this.defaultLatitude,
                this.value.longitude || this.defaultLongitude
            ]
        }
    },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = { 
                latitude: this.field.value[this.field.latitude || 'latitude'] || 0,
                longitude: this.field.value[this.field.longitude || 'longitude'] || 0,
            }

            if (this.value.latitude == 0) {
                this.value.latitude = this.defaultLatitude;
            }

            if (this.value.longitude == 0) {
                this.value.longitude = this.defaultLongitude;
            }
        }
    },
}
</script>
<style scoped>
    @import "~leaflet/dist/leaflet.css";
</style>