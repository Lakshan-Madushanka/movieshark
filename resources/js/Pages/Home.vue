<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import Paginator from 'primevue/paginator';
import Dropdown from 'primevue/dropdown';
import PrimeButton from 'primevue/button';
import InputText from 'primevue/inputtext';
import {reactive, ref, watch} from "vue";
import MovieFiltersData from "@/Data/MovieFiltersData";

const props = defineProps({
    'movies': {},
    'meta': {'movie_count': 0, 'limit': 0, 'page': 0},
});

const offset = ref(0);
const searchStatus = ref();
const showMovieInfoForId = ref(-100000);


const filters = reactive({
    ...MovieFiltersData,
});

const queryStringData = useForm({
    page: 1,
    limit: 20,
    query_term: '',
    genre: '',
    quality: '',
    minimum_rating: '',
    sort_by: '',
    order_by: '',
})

watch(() => props.meta, function (meta) {
    offset.value = meta.limit * (meta.page - 1)
    queryStringData.limit = meta.limit
}, {immediate: true})

const setMovieInfoId = function (id) {
    showMovieInfoForId.value = id;
}

const setPage = function (event) {
    queryStringData.page = event.page + 1;
    queryStringData.limit = event.rows;

    sendRequest();
}

const search = function () {
    queryStringData.page = 1;
    queryStringData.limit = 20;

    sendRequest(
        () => {
            if (props.meta.movie_count === 0) {
                searchStatus.value = 'nothing'
                return;
            }
            searchStatus.value = 'success';
            console.log('success');
        },
        (errors) => {
            console.log('errors', errors)
        }
    );
}

const sendRequest = function (
    onSuccess = () => {
    },
    onError = () => {
    }
) {
    const data = queryStringData.data();

    queryStringData.transform(function (data) {
        console.log(data);
        let query = {};

        for (const key in data) {
            if (data[key] !== '') {
                query[key] = data[key]
            }
        }
        return query;
    })
        .get(route('home.index'), {
            preserveState: true,
            onSuccess,
            onError,
        });
}
</script>

<template>
    <Head title="home"/>

    <div v-if="meta.movie_count === 0"
         class="flex justify-enter items-center min-h-screen text-center text-4xl font-extrabold">
        <h2 class="w-full">Nothing Here !</h2>
    </div>
    <!--    Navigation-->


    <!--    Search Bar-->
    <div v-else class="space-y-6">
        <div class="flex flex-col justify-center  sm:flex-row sm:items-start mt-4 mx-2">
            <div class="flex flex-col justify-center items-center w-full max-w-screen-lg">
                <span class="p-input-icon-left block w-full">
                    <i class="pi pi-search"/>
                    <InputText
                        v-model="queryStringData.query_term"
                        id="email"
                        type="search"
                        class="block w-full"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Search your favorite movie"
                        @keydown.enter="search"
                    />
                </span>
            </div>
            <PrimeButton @click="search"
                         :loading="queryStringData.processing"
                         size='small'
                         type="button"
                         label="Search"
                         class="ms-0 sm:ms-4 py-3 justify-center sm:justify-start mt-2 sm:mt-0"
            />
        </div>
        <!-- End of Search Bar-->

        <!--    pagination-->
        <div class="lg:mx-32 bg-green-500 border-2 border-green-500">
            <Paginator :rows=queryStringData.limit :totalRecords="meta.movie_count" :rowsPerPageOptions="[10, 20, 30]"
                       v-model:first="offset" @page="setPage"></Paginator>
        </div>
        <!-- End of pagination-->

        <!--    Filters-->
        <div class="flex flex-col items-center">
            <div
                class="grid grid-cols-3 grid-rows-2 md:grid-cols-5 md:grid-rows-1 gap-x-2 gap-y-2 flex-wrap space mb-2 [&>div>div.p-dropdown]:w-full [&>div]:min-w-40">
                <!--        Quality-->
                <div class="">
                    <p class="mb-1">Quality</p>
                    <Dropdown v-model="queryStringData.quality" :options="filters.quality" placeholder="All"/>
                </div>
                <div>
                    <p class="mb-1">Genre</p>
                    <Dropdown v-model="queryStringData.genre" :options="filters.genre" placeholder="All"/>
                </div>
                <div>
                    <p class="mb-1">Rating</p>
                    <Dropdown v-model="queryStringData.minimum_rating" :options="filters.minimum_rating"
                              placeholder="All"/>
                </div>

                <div>
                    <p class="mb-1">Sort by</p>
                    <Dropdown v-model="queryStringData.sort_by" :options="filters.sort_by" optionValue="value"
                              optionLabel="name" placeholder="All"/>
                </div>
                <div>
                    <p class="mb-1">Order by</p>
                    <Dropdown v-model="queryStringData.order_by" :options="filters.order_by" optionValue="value"
                              optionLabel="name" placeholder="All"/>
                </div>

            </div>
        </div>
        <!-- End of   Filters-->


        <!--        Search Success Message-->
        <div v-if="searchStatus === 'success'">
            <p class="text-center text-4xl font-extrabold">Results &nbsp; [{{ meta.movie_count }}]</p>
        </div>

        <!--        Search Error Message-->
        <div v-if="searchStatus === 'nothing'">
            <p class="text-center text-4xl font-extrabold">Nothing found !</p>
        </div>

        <!--    Content-->
        <section class="flex justify-center flex-wrap mx-auto max-w-6xl">
            <div
                v-for="movie in movies" :key="movie['id']"
                class="mr-12 mb-4 hover:cursor-pointer transition"
            >
                <div @mouseenter="setMovieInfoId(movie['id'])" @mouseleave="setMovieInfoId(-100000)"
                     class="relative w-[12.5rem] h-[18rem] border-4 hover:border-green-500 overflow-hidden"
                >
                    <img :src="movie['medium_cover_image']" :alt="'cover image of movie ' + movie['name']"
                         :class="{'scale-125 transition': showMovieInfoForId === movie['id']}"
                    >
                    <p class="absolute m-1 right-1 top-0 z-20 text-black bg-white font-extrabold">
                        <span class="p-1">{{ movie['rating'] }}</span>
                    </p>
                    <div
                        v-if="showMovieInfoForId === movie['id']"
                        class="absolute flex justify-center items-center text-center top-0 left-0 w-full h-full text-white font-bold text-lg bg-[rgba(56,54,54,0.7)]"
                    >
                        <div class="text-2xl font-extrabold">
                            <p v-for="genre in movie['genres'].slice(0, 2)" :key="genre">{{ genre }}</p>
                            <p>{{ movie['year'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center mt-1 w-[12.5rem]">
                    <p class="mr-2 text-nowrap overflow-hidden text-ellipsis">{{ movie['name'] }}</p>
                    <span>[{{ movie['language'] }}]</span>
                </div>
            </div>
        </section>
        <!--        End of Content-->
    </div>
</template>`

