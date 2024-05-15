<script setup>
import {onMounted, ref, watch} from "vue";
import {useToggleMovieFromWatchList} from "@/Composables/useToggleMovieFromWatchList.js";
import Button from 'primevue/button';
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
const processing = ref(false);

let toggleFromWatchList;
let checkWatchListHas;

onMounted(() => {
    if (props.showWatchListButton) {
        let getToggleMovieFromWatchList = useToggleMovieFromWatchList(props.watchListIds);
        toggleFromWatchList = getToggleMovieFromWatchList.toggleFromWatchList;
        checkWatchListHas = getToggleMovieFromWatchList.checkWatchListHas;

        watchListHas.value = checkWatchListHas(props.movie.id);
    }
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
    processing.value = true;

    watch(status, (status) => {
        processing.value = false;
        if (status.success) {
            watchListHas.value = !watchListHas.value
            toast.add({severity: 'success', summary: status.summary, detail: status.details, life: 3000});
        }
    })
}
</script>

<template>
    <div
        @mouseenter="setMovieInfoId(movie['id'])"
        @mouseleave="setMovieInfoId(-100000)"
        :class="'@container relative group w-[16rem] h-[21.5rem] md:w-[20rem] md:h-[25.5rem] lg:w-[12.5rem] lg:h-[18rem] border-4 hover:border-green-500 overflow-hidden ' + widthClass + ' ' + heightClass"
    >
        <div class="absolute opacity-0 z-10 bottom-[-2rem] left-[50%] translate-x-[-50%] group-hover:!bottom-[10%] duration-300 group-hover:!opacity-100 transition-all]">
            <Button class="text-xs bg-green-500 border-green-500 p-1 px-2 text-white" label="More Info" severity="info" size="small" raised/>
        </div>

        <img
            :src="getImageUrl()"
            :alt="'cover image of movie ' + movie['name']"
            :class="['w-full h-full group-hover:scale-125 duration-300 transition-all' + widthClass + ' ' + heightClass]"
            onerror="this.onerror=null;this.src='/images/no_image.png'"
        >
        <div
            :class="['text-xs @[6rem]:text-sm absolute w-full flex justify-between items-start right-0 top-0 z-20 text-black font-extrabold', {'!justify-end': !showWatchListButton}]">
            <span class="p-1 h-[1.6rem] flex justify-center items-center w-8 bg-white border border-gray-300">{{ (movie['rating']) }}</span>
            <div
                v-if="showWatchListButton"
                @click.prevent="watchListButtonClicked"
                class="flex justify-center items-center h-[1.6rem] w-8 bg-white border border-gray-300"
            >
                <i v-if="processing" class="pi pi-spin pi-spinner text-black" />
                <i v-else :class="['pi p-1 @[6rem]:p-1', {'pi-star': !watchListHas, 'pi-star-fill': watchListHas}]"/>
            </div>
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
        <div :class="'flex justify-between text-xs @[10rem]:text-lg items-center mt-1 w-full ' + widthClass">
            <p class="text-nowrap overflow-hidden text-ellipsis">{{ movie['name'] }}</p>
            <span>[{{ movie['language'] }}]</span>
        </div>
    </div>
</template>
