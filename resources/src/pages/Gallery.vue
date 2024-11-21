<template>
    <div class="container">
        <div class="row">
            <template v-if="!loading">
                <card-picture
                    v-for="gallery in dataGallery"
                    :title="gallery.title"
                    :path="gallery.path"
                    :id="gallery.id"
                />
            </template>
        </div>
    </div>
</template>
<script setup>
    import CardPicture from "../components/Gallery/CardPicture.vue";
    import axios from "axios";
    import {ref} from "vue";

    let loading = ref(true);
    let dataGallery = ref([])
    const getAllPictures = () => {
        axios.get('/api/get-pictures')
            .then((response) => {
                if (response.data.success) {
                    dataGallery.value = response.data.items;
                }
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                loading.value = false;
            })
    };
    getAllPictures();

</script>
<style scoped lang="scss">

</style>
