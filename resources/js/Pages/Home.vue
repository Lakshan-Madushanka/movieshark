<script setup>
import {Link, useForm} from '@inertiajs/vue3';
import Paginator from 'primevue/paginator';
import Dropdown from 'primevue/dropdown';
import PrimeButton from 'primevue/button';
import InputText from 'primevue/inputtext';
import {reactive, ref, watch} from "vue";
import MovieFiltersData from "@/Data/MovieFiltersData";
import MovieTile from "@/Components/Movie/MovieTile.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';

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
    <GuestLayout title="Home">

        <div v-if="meta.movie_count === 0"
             class="flex justify-enter items-center min-h-screen text-center text-4xl font-extrabold">
            <h2 class="w-full">Nothing Here !</h2>
        </div>

        <div v-else class="space-y-6">
            <!-- Filters -->
            <section>
                <Accordion>
                    <AccordionTab header="Filters" headerClass="!p-[1px]">
                        <div class="space-y-6">
                            <!--    Search Bar-->
                            <div class="flex flex-col justify-center  sm:flex-row sm:items-start mx-2">
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
                                            size="small"
                                            @keydown.enter="search"
                                        />
                                    </span>
                                </div>
                                <PrimeButton @click="search"
                                             :loading="queryStringData.processing"
                                             size='small'
                                             type="button"
                                             label="Search"
                                             class="ms-0 sm:ms-4 justify-center sm:justify-start mt-2 sm:mt-0"
                                />
                            </div>
                            <!-- End of Search Bar-->

                            <!--    pagination-->
                            <div class="lg:mx-32">
                                <Paginator
                                    :template="{
                                    '640px': 'PrevPageLink CurrentPageReport NextPageLink',
                                    '960px': 'FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink',
                                    '1300px': 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink',
                                    default: 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink JumpToPageDropdown JumpToPageInput'
                                }"
                                    :rows=queryStringData.limit :totalRecords="meta.movie_count"
                                    :rowsPerPageOptions="[10, 20, 30]"
                                    v-model:first="offset" @page="setPage">

                                </Paginator>
                            </div>
                            <!-- End of pagination-->

                            <!--    Filters-->
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-full grid grid-cols-1 grid-rows-5 md:grid-cols-5 md:grid-rows-1 gap-x-2 gap-y-2 space mb-2 [&>div>div.p-dropdown]:w-full [&>div]:min-w-40">
                                    <!--        Quality-->
                                    <div class="">
                                        <p class="mb-1">Quality</p>
                                        <Dropdown
                                            v-model="queryStringData.quality"
                                            :options="filters.quality"
                                            placeholder="All"
                                        />
                                    </div>
                                    <div>
                                        <p class="mb-1">Genre</p>
                                        <Dropdown v-model="queryStringData.genre" :options="filters.genre"
                                                  placeholder="All"/>
                                    </div>
                                    <div>
                                        <p class="mb-1">Rating</p>
                                        <Dropdown
                                            v-model="queryStringData.minimum_rating"
                                            :options="filters.minimum_rating"
                                            placeholder="All"
                                        />
                                    </div>
                                    <div>
                                        <p class="mb-1">Sort by</p>
                                        <Dropdown
                                            v-model="queryStringData.sort_by"
                                            :options="filters.sort_by"
                                            optionValue="value"
                                            optionLabel="name" placeholder="All"
                                        />
                                    </div>
                                    <div>
                                        <p class="mb-1">Order by</p>
                                        <Dropdown
                                            v-model="queryStringData.order_by"
                                            :options="filters.order_by"
                                            optionValue="value"
                                            optionLabel="name" placeholder="All"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--End of Filters-->
                    </AccordionTab>
                </Accordion>
            </section>
            <!-- End of Filters -->

            <!--        Search Success Message-->
            <div v-if="searchStatus === 'success'">
                <p class="text-center text-4xl font-extrabold">Results &nbsp; [{{ meta.movie_count }}]</p>
            </div>

            <!--        Search Error Message-->
            <div v-if="searchStatus === 'nothing'">
                <p class="text-center text-4xl font-extrabold">Nothing found !</p>
            </div>

            <!--Content-->
            <section class="flex justify-center flex-wrap gap-x-16 gap-y-4 mx-auto px-12">
                <div
                    v-for="movie in movies" :key="movie['id']"
                    class="hover:cursor-pointer transition"
                >
                    <Link :href="route('movies.show', {id: movie['id']})">
                        <MovieTile :movie="movie"/>
                    </Link>
                </div>
            </section>
            <!--End of Content-->
        </div>
    </GuestLayout>
</template>`

<style>
.p-accordion-header-link {
    padding: 10px;
}
</style>
