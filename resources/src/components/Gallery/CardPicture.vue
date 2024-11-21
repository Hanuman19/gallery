<template>
    <div class="col-md-3 col-sm-4 col-xs-6 thumb">
        <div class="card" style="width: 18rem;">
            <img
                class="card-img-top"
                :src="path"
                alt="">
            <div class="card-body">
                <h5 class="card-title">{{title}}</h5>
                <button type="button" class="btn btn-primary" @click="showPicture()">Подробнее</button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {ref} from "vue";
    import { useRouter } from 'vue-router'

    const router = useRouter();
    const props = defineProps({
        title: {
            type: String
        },
        path: {
            type: String,
        },
        id: {
            type: Number,
        },
    });
    let title = ref(props.title);
    let path = ref(props.path);
    let id = ref(props.id);
    const showPicture = () => {
        router.push({name: 'view', params: {id: id.value}});
    };
</script>

<style scoped>
.thumb img {
    -webkit-filter: grayscale(0);
    filter: none;
    border-radius: 5px;
    background-color: #fff;
    border: 1px solid #ddd;
    padding: 5px;
}

.thumb img:hover {
    -webkit-filter: grayscale(1);
    filter: grayscale(1);
}

.thumb {
    padding: 5px;
}
</style>
