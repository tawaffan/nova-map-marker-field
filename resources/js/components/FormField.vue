<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <div class="rounded flex flex-col w-full"
                :style="'height: ' + field.height" 
                :class="mapErrorClasses">
                <l-map :id="field.name" ref="map"
                    :zoom="defaultZoom"
                    :center="mapCenter"
                    :options="mapOptions"
                    class="w-full h-full rounded z-10"
                    @move="handleMapMoved">
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
            <div class="flex">
                <div class="latitude nova-grid-wrapper">
                    <div class="nova-grid-field-label">
                        <label class="inline-block text-80 pt-2 leading-tight">Latitude</label>
                    </div>                    
                    <div class="w-full">
                        <input type="text" :name="latitudeFieldName" v-model="value.latitude" class="w-full form-control form-input-bordered">
                    </div>
                </div>
                <div class="longitude nova-grid-wrapper pr-0">
                    <div class="nova-grid-field-label">
                        <label class="inline-block text-80 pt-2 leading-tight">Longitude</label>
                    </div>                    
                    <div class="w-full">
                        <input type="text" :name="longitudeFieldName" v-model="value.longitude" class="w-full form-control form-input-bordered">
                    </div>
                </div>
            </div>
            <p v-if="hasLocationError" class="my-2 text-danger">
                {{ firstLocationError }}
            </p>
        </template>
    </default-field>
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
            defaultZoom: this.field.defaultZoom || 12,
            mapOptions: {
                doubleClickZoom: 'center',
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
        },
        latitudeFieldName: function () {
                return this.field.latitude || "latitude";
        },
        longitudeFieldName: function () {
            return this.field.longitude || "longitude";
        },
        hasLocationError() {
            return (this.errors.has(this.latitudeFieldName)
                    || this.errors.has(this.longitudeFieldName));
        },
        mapErrorClasses() {
            return this.hasLocationError ? this.errorClass : '';
        },
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
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append((this.field.latitude || 'latitude'), this.value.latitude)

            formData.append((this.field.longitude || 'longitude'), this.value.longitude)
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value.latitude = value.latitude
            this.value.longitude = value.longitude
        },

        /**
         * Update the field when
         * @param  {[type]} event [description]
         * @return {[type]}       [description]
         */
        handleMapMoved(event) {
            let coordinates = event.target.getCenter()

            this.value.latitude = coordinates.lat;
            this.value.longitude = coordinates.lng;
        }
    },
    created() {
        if (this.resourceId) {
            this.defaultZoom = this.field.defaultZoomDetail
        }
    }
}
</script>
<style scoped>
    @import "~leaflet/dist/leaflet.css";

    .latitude {
        padding-left: 0;
        padding-bottom: 0;
    }
    .longitude {
        padding-right: 0;
        padding-bottom: 0;
    }
</style>