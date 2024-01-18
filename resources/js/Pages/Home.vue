<script setup>
import { Head, Link } from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {ref} from "vue";

defineProps({
    'movies': {},
});

const showMovieInfoForId = ref(-100000);

const setMovieInfoId = function (id) {
    console.log('hello')
    showMovieInfoForId.value = id;
}

</script>

<template>
    <Head title="home" />
<!--    Navigation-->
<!--    Search Bar-->
    <div class="space-y-6">
        <div class="flex flex-col justify-center  sm:flex-row sm:items-start mt-4 mx-2">
            <div class="flex flex-col justify-center items-center w-full max-w-screen-lg">
                <TextInput
                    id="email"
                    type="search"
                    class="block w-full"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Search your favorite movie"
                />
                <InputError class="mt-2" message=" search error"/>
            </div>
            <PrimaryButton class="ms-0 sm:ms-4 py-3 justify-center sm:justify-start mt-2 sm:mt-0">
                Search
            </PrimaryButton>
        </div>

<!--    pagination-->
        <div class="lg:mx-32">
        </div>

        <!--    Content-->
        <div class="flex justify-center flex-wrap mx-auto max-w-6xl">
            <div
                v-for="movie in movies" :key="movie['id']"
                class="mr-12 mb-4 hover:cursor-pointer transition"
            >
                <div @mouseenter="setMovieInfoId(movie['id'])" @mouseleave="setMovieInfoId(-100000)"
                     class="relative w-[12.5rem] border-4 hover:border-green-500 overflow-hidden"
                >
                    <img :src="movie['medium_cover_image']" :alt="'cover image of movie ' + movie['name']"
                         :class="{'scale-125 transition': showMovieInfoForId === movie['id']}"
                    >
                    <p class="absolute m-1 right-1 top-0 z-20 text-black bg-white font-extrabold">
                        <span class="p-1">{{ movie['rating'] }}</span>
                    </p>
                    <div
                        v-if="showMovieInfoForId === movie['id']"
                        class="absolute flex justify-center items-center top-0 left-0 w-full h-full text-white font-bold text-lg bg-[rgba(56,54,54,0.7)]"
                    >
                        <div class="text-2xl font-extrabold">
                            <p v-for="genre in movie['genres'].slice(0, 2)" :key="genre">{{genre}}</p>
                            <p class="text-center">{{movie['year']}}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center mt-1 w-[12.5rem]">
                    <p class="mr-2 text-nowrap overflow-hidden text-ellipsis">{{ movie['name'] }}</p>
                    <span>[{{ movie['language'] }}]</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ingo-background {
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgb(28 36 32 / 76%) 100%);
}
</style>
