<script setup>
import {ref} from "vue";

const props = defineProps({
    movie: {},
    widthClass: '',
    heightClass: '',
});

const showMovieInfoForId = ref(-100000);
const setMovieInfoId = function (id) {
    showMovieInfoForId.value = id;
}

const getImageUrl = function () {
    return props.movie['cover_image'] === ' '? '/images/no_image.png' : props.movie['cover_image'];
}
</script>

<template>
    <div
        @mouseenter="setMovieInfoId(movie['id'])"
        @mouseleave="setMovieInfoId(-100000)"
        :class="'relative w-[12.5rem] h-[18rem] border-4 hover:border-green-500 overflow-hidden ' + widthClass + ' ' + heightClass"
    >
        <img
            :src="getImageUrl()"
            :alt="'cover image of movie ' + movie['name']"
            :class="['w-[12.5rem] h-[18rem] ' + widthClass + ' ' + heightClass, {'scale-125 transition ': showMovieInfoForId === movie['id']}]"
        >
        <p class="absolute m-1 right-1 top-0 z-20 text-black bg-white font-extrabold">
            <span class="p-1">{{ movie['rating'] }}</span>
        </p>
        <div
            v-if="showMovieInfoForId === movie['id']"
            class="@container absolute flex justify-center items-center text-center top-0 left-0 w-full h-full text-white font-bold bg-[rgba(56,54,54,0.7)]"
        >
            <div class="@[10rem]:text-2xl font-extrabold">
                <p v-for="genre in movie['genres'].slice(0, 2)" :key="genre">{{ genre }}</p>
                <p>{{ movie['year'] }}</p>
            </div>
        </div>
    </div>
    <div class="@container">
        <div :class="'flex text-sm @[10rem]:text-lg items-center mt-1 w-[12.5rem] ' + widthClass">
            <p class="mr-2 text-nowrap overflow-hidden text-ellipsis">{{ movie['name'] }}</p>
            <span>[{{ movie['language'] }}]</span>
        </div>
    </div>
</template>
