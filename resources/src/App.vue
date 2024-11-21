<template>
    <div>
        <load-pictures />
        <main class="main">
            <router-view />
        </main>
    </div>
</template>
<script setup>
    import LoadPictures from "./components/LoadPictures.vue";
</script>
<style lang="scss">

</style>
