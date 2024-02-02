<script setup>
import {onMounted, ref, watch} from "vue";
import {useToggleMovieFromWatchList} from "@/Composables/useToggleMovieFromWatchList.js";
import {useToast} from "primevue/usetoast";

const props = defineProps({
    movie: {},
    widthClass: '',
    heightClass: '',
    watchListIds: {
        type: Object
    },
    showWatchListButton: {
        type: Boolean,
        default: true,
    }
});

const toast = useToast();

const watchListHas = ref();
const {toggleFromWatchList, checkWatchListHas} = useToggleMovieFromWatchList(props.watchListIds);

onMounted(() => {
    watchListHas.value = checkWatchListHas(props.movie.id)
})

const showMovieInfoForId = ref(-100000);

const setMovieInfoId = function (id) {
    showMovieInfoForId.value = id;
}

const getImageUrl = function () {
    return props.movie['cover_image'] === ' ' ? '/images/no_image.png' : props.movie['cover_image'];
}

const watchListButtonClicked = () => {
    let status = toggleFromWatchList(props.movie);

    watch(status, (status) => {
        if (status.success) {
            watchListHas.value = !watchListHas.value
            toast.add({severity: 'Success', summary: status.summary, detail: status.details, life: 3000});
        }
    })
}
</script>

<template>
    <div
        @mouseenter="setMovieInfoId(movie['id'])"
        @mouseleave="setMovieInfoId(-100000)"
        :class="'@container relative w-[12.5rem] h-[18rem] border-4 hover:border-green-500 overflow-hidden ' + widthClass + ' ' + heightClass"
    >
        <img
            :src="getImageUrl()"
            :alt="'cover image of movie ' + movie['name']"
            :class="['w-[12.5rem] h-[18rem] ' + widthClass + ' ' + heightClass, {'scale-125 transition ': showMovieInfoForId === movie['id']}]"
        >
        <div
            :class="['text-xs @[6rem]:text-sm absolute w-full flex justify-between items-start right-0 top-0 z-20 text-black font-extrabold', {'!justify-end': !showWatchListButton}]">
            <span class="p-1 bg-white">{{ (movie['rating']).toFixed(1) }}</span>
            <i
                v-if="showWatchListButton"
                @click.prevent="watchListButtonClicked"
                :class="['pi p-1 @[6rem]:p-2 bg-white', {'pi-heart': !watchListHas, 'pi-heart-fill': watchListHas}]"
            />
        </div>
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
        <div :class="'flex justify-between text-xs @[10rem]:text-lg items-center mt-1 w-[12.5rem] ' + widthClass">
            <p class="text-nowrap overflow-hidden text-ellipsis">{{ movie['name'] }}</p>
            <span>[{{ movie['language'] }}]</span>
        </div>
    </div>
</template>
