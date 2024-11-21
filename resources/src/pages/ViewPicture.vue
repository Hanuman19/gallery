<template>
    <div class="container">
        <div class="row">
            <div class="col-md-9 col12">
                <div class="card" style="width: 100%;">
                    <img
                        class="card-img-top"
                        :src=path
                        alt="">
                    <div class="card-body">
                        <h5 class="card-title">{{dataGallery.title}}</h5>
                        <p class="card-text">{{dataGallery.description}}</p>
                        <button type="button" class="btn btn-primary" @click="goToHome()">Назад</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { useRoute, useRouter } from 'vue-router'
    const route = useRoute();
    const router = useRouter();
    import axios from "axios";
    import {ref} from "vue";
    let loading = ref(true);
    let dataGallery = ref([])
    let path = ref('');
    const getPictureById = () => {
        axios.get(`/api/get-picture/${+route.params.id}`)
            .then((response) => {
                console.log(response.data);
                if (response.data.success) {
                    dataGallery.value = response.data.item;
                }
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                loading.value = false;
                path.value = "../../../" + dataGallery.value.path
            })
    };
    const goToHome = () => {
        router.push({name: 'home'});
    };
    getPictureById();

</script>

<style scoped lang="scss">

</style>
