<template>
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-12 mx-auto">
                <div class="row load">
                    <div class="mb-3">
                        <label for="name" class="form-label">Название</label>
                        <input v-model="name" type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <input v-model="description" type="text" class="form-control" id="description">
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Описание</label>
                        <input @change="uploadFile" type="file" class="form-control" id="file">
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" @click="load()">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
    import {ref} from "vue";
    import axios from "axios";

    let name = ref(null);
    let description = ref(null);
    let file = ref(null);
    let item = ref([]);

    const uploadFile = (event) => {
        file.value = event.target.files[0];
    }
    const load = () => {
        if (file.value.size > 5 * 1024 * 1024) { //больше чем 5 мегов
            console.log('Слишком большой файл');
            return;
        }
        let form = new FormData;
        form.append("image", file.value);
        form.append("title", name.value);
        form.append("description", description.value);
        axios.post(`/api/upload`, form, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
            .then((response) => {
                if (response.data.success) {
                    item.value = response.data.item;
                    file.value = null;
                    name.value = null;
                    description.value = null;
                } else {
                    console.log(response.data.masseges);
                }
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {

            })
    }

</script>
<style scoped>
    .load {
        text-align: justify;
    }
</style>
