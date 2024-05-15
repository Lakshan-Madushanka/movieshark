<script setup>
import {Link, usePage, router} from '@inertiajs/vue3';
import Badge from 'primevue/badge';
import Checkbox from 'primevue/checkbox';
import Dropdown from 'primevue/dropdown';
import PrimeButton from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dialog from "primevue/dialog";
import { useToast } from 'primevue/usetoast';
import {onMounted, reactive, ref, watch} from "vue";
import MovieFiltersData from "@/Data/MovieFiltersData";
import MovieTile from "@/Components/Movie/MovieTile.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import Paginator from 'primevue/paginator'
import {debounce} from "lodash";
import {truncate} from "@/Helpers.js"

const props = defineProps({
    'movies': {},
    'meta': {'movie_count': 0, 'limit': 0, 'page': 0},
    watchListIds: {},
});

const toast = useToast();

const page = usePage();
const authenticated = !!page.props.auth?.user?.id;

const showPreferredFilters = ref(false);
const preferredFiltersApplied = ref(false);

const offset = ref(0);
const searchStatus = ref('');

const filters = reactive({
    ...MovieFiltersData,
});

const pagination = reactive({
    page: 1,
    limit: 20,
})

const preferredFilters = reactive({
    query_term: '',
    genre: '',
    quality: '',
    minimum_rating: '',
    sort_by: '',
    order_by: '',
    auto_apply: '',
})

const queryStringData = reactive({
    query_term: '',
    genre: '',
    quality: '',
    minimum_rating: '',
    sort_by: '',
    order_by: '',
})

const noOfAppliedFilters = ref(0);

onMounted(() => setupPreferredFilters());

watch(() => props.meta, function (meta) {
    offset.value = meta.limit * (meta.page - 1)
    pagination.limit = meta.limit
}, {immediate: true})

watch(queryStringData, (newQuery, oldQuery) => {
    if (newQuery.query_term !== oldQuery.query_term) {
        debounce(() => search(), 500)()
        return;
    }
    search();

    noOfAppliedFilters.value = getActiveQueriesCount();
})

const setPage = function (event) {
    pagination.page = event.page + 1;
    pagination.limit = event.rows;

    sendRequest();
}

const search = function () {
    pagination.page = 1;
    pagination.limit = 20;

    sendRequest(
        () => {
            if (getActiveQueriesCount() === 0) {
                searchStatus.value = '';
                return;
            }

            if (props.meta.movie_count === 0) {
                searchStatus.value = 'nothing'
                return;
            }
            searchStatus.value = 'success';
        },
        (errors) => {
        }
    );
}

const applyPreferredFilters = () => {
    const tmpQueryString = {};
    for (const key in queryStringData) {
        tmpQueryString[key] = preferredFilters[key];
    }

    Object.assign(queryStringData, {...queryStringData, ...tmpQueryString});

    preferredFiltersApplied.value = true;
};

const clearPreferredFilters = () => {
    for (const key in preferredFilters) {
        if (key === 'auto_apply') {
            continue;
        }
        queryStringData[key] = '';
    }
}

const setupPreferredFilters = () => {
    const pf = JSON.parse(localStorage.getItem('preferredFilters'));

    if (pf) {
        for (const key in preferredFilters) {
            preferredFilters[key] = pf[key];
        }
    }

    if (preferredFilters["auto_apply"]) {
        applyPreferredFilters();
    }
}

const savePreferredFilters = () => {
    localStorage.setItem('preferredFilters', JSON.stringify(preferredFilters));

    if (preferredFilters.auto_apply) {
        for (const key in queryStringData) {
            queryStringData[key] = preferredFilters[key];
        }

        preferredFiltersApplied.value = true;
    }

    showPreferredFilters.value = false;

    toast.add({ severity: 'success', summary: 'Success', detail: 'Preferred filters saved successfully.', life: 3000 });
}

const resetPreferredFilters = () => {
    localStorage.removeItem('preferredFilters');

    for (const key in preferredFilters) {
        preferredFilters[key] = ''
    }

    clearPreferredFilters();

    toast.add({ severity: 'success', summary: 'Success', detail: 'Preferred filters cleared successfully.', life: 3000 });
}

const onPreferredFilterCheckboxChanged = (event) => {
    if (preferredFiltersApplied.value) {
        applyPreferredFilters();

        return;
    }

    clearPreferredFilters();
}

const sendRequest = function (
    onSuccess = () => {
    },
    onError = () => {
    }
) {
    let data = {...queryStringData, ...pagination};
    let query = {};

    for (const key in data) {
        if (data[key] !== '') {
            query[key] = data[key];
        }
    }

    router.get(route('home.index'), query, {
        preserveState: true,
        onSuccess,
        onError,
    })
}

const clearFilters = () => {
    queryStringData.query_term = '';
    queryStringData.genre = '';
    queryStringData.quality = '';
    queryStringData.minimum_rating = '';
    queryStringData.sort_by = '';
    queryStringData.order_by = '';

    noOfAppliedFilters.value = 0;
    preferredFiltersApplied.value = false;

}

const getActiveQueries = () => {
    return Object.entries(queryStringData).reduce((a, [k, v]) => (v === '' ? a : (a[k] = v, a)), {})
}

const getActiveQueriesCount = () => {
    return Object.keys(getActiveQueries()).length
}

</script>

<template>
    <GuestLayout title="Home">
        <div class="space-y-6">
            <!-- Filters -->
            <section>
                <Accordion>
                    <AccordionTab headerClass="!p-[0px]">
                        <template #header>
                            <div class="flex items-center space-x-4">
                                <div class="flex space-x-1">
                                    <span>Filters</span>
                                    <div v-if="noOfAppliedFilters>0">
                                        <Badge>{{ noOfAppliedFilters }}</Badge>
                                    </div>
                                </div>
                                <div v-if="noOfAppliedFilters > 0" class="hidden lg:flex items-center space-x-2">
                                    <div
                                        v-for="(value, key) in getActiveQueries()"
                                        :key="key"
                                        class="text-xs"
                                    >
                                        <div class="flex space-x-2 items-center">
                                            <span>{{ key }}</span>
                                            <span class="bg-gray-600 px-1 rounded" v-html="truncate(value)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
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
                            </div>
                            <!-- End of Search Bar-->

                            <!--    Filters-->
                            <div class="flex flex-col items-center space-y-4">
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
                                        <Dropdown
                                            v-model="queryStringData.genre"
                                            :options="filters.genre"
                                            placeholder="All"
                                        />
                                    </div>
                                    <div>
                                        <p class="mb-1">Rating</p>
                                        <Dropdown
                                            v-model="queryStringData.minimum_rating"
                                            :options="filters.minimum_rating"
                                            optionLabel="label"
                                            optionValue="value"
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

                                <div class="flex flex-col justify-center items-center gap-y-2">
                                    <div class="flex gap-x-4 items-center">
                                        <span>Apply preferred filters</span>
                                        <Checkbox v-model="preferredFiltersApplied"
                                                  @change="onPreferredFilterCheckboxChanged" :binary="true"/>
                                    </div>
                                    <PrimeButton
                                        @click="showPreferredFilters=true"
                                        label="Manage Preferred  Filters"
                                        icon="pi pi-cog"
                                        link
                                    >
                                    </PrimeButton>
                                </div>

                                <div>
                                    <PrimeButton
                                        @click="clearFilters"
                                        label="Clear All"
                                        size="small"
                                        icon="pi pi-times"
                                    />
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

            <div v-if="meta.movie_count === 0 && searchStatus === ''"
                 class="flex justify-enter items-center min-h-screen text-center text-4xl font-extrabold">
                <h2 class="w-full">Nothing Here !</h2>
            </div>

            <!--Content-->
            <section
                v-else
                class="flex justify-center md:justify-between lg:justify-center flex-wrap 16 lg:gap-x-16 gap-y-4 mx-auto px-12"
            >
                <div
                    v-for="movie in movies" :key="movie['id']"
                    class="hover:cursor-pointer transition"
                >
                    <Link :href="route('movies.show', {id: movie['id']})">
                        <MovieTile
                            :movie="movie"
                            :watchListIds="watchListIds"
                            :showWatchListButton="authenticated"
                        />
                    </Link>
                </div>
            </section>
            <!--End of Content-->

            <section class="!mt-8 !mb-4 lg:mx-32">
                <!--    pagination-->
                <Paginator
                    :rows=pagination.limit
                    :totalRecords="meta.movie_count"
                    :rowsPerPageOptions="[10, 20, 30]"
                    v-model:first="offset"
                    :template="{
                                '640px': 'PrevPageLink CurrentPageReport NextPageLink',
                                '960px': 'FirstPageLink PrevPageLink CurrentPageReport NextPageLink RowsPerPageDropdown LastPageLink',
                                '1300px': 'FirstPageLink PrevPageLink PageLinks NextPageLink RowsPerPageDropdown LastPageLink',
                                 default: 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown JumpToPageDropdown JumpToPageInput'
                                }"
                    @page="setPage"
                />

                <!-- End of pagination-->
            </section>
        </div>

        <Dialog
            v-model:visible="showPreferredFilters"
            modal
            header="Manage Preferred Filters"
            position="top"
            class="!mt-4"
            :style="{ width: '45rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <div class="w-full space-y-4">
                <div
                    class="flex flex-col lg:flex-row w-full justify-between items-center flex-wrap gap-4 space mb-2 [&>div>div.p-dropdown]:w-full [&>div]:min-w-64">
                    <!--        Quality-->
                    <div class="w-full lg:w-auto">
                        <p class="mb-1">Quality</p>
                        <Dropdown
                            v-model="preferredFilters.quality"
                            :options="filters.quality"
                            placeholder="All"
                        />
                    </div>
                    <div class="w-full lg:w-auto">
                        <p class="mb-1">Genre</p>
                        <Dropdown
                            v-model="preferredFilters.genre"
                            :options="filters.genre"
                            placeholder="All"
                        />
                    </div>
                    <div class="w-full lg:w-auto">
                        <p class="mb-1">Rating</p>
                        <Dropdown
                            v-model="preferredFilters.minimum_rating"
                            :options="filters.minimum_rating"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="All"
                        />
                    </div>
                    <div class="w-full lg:w-auto">
                        <p class="mb-1">Sort by</p>
                        <Dropdown
                            v-model="preferredFilters.sort_by"
                            :options="filters.sort_by"
                            optionValue="value"
                            optionLabel="name" placeholder="All"
                        />
                    </div>
                    <div class="w-full lg:w-auto">
                        <p class="mb-1">Order by</p>
                        <Dropdown
                            v-model="preferredFilters.order_by"
                            :options="filters.order_by"
                            optionValue="value"
                            optionLabel="name" placeholder="All"
                        />
                    </div>
                </div>

                <div class="flex gap-x-4 items-center">
                    <span>Auto apply filters</span>
                    <Checkbox v-model="preferredFilters.auto_apply" :binary="true"/>
                </div>

                <div class="flex space-x-4">
                    <PrimeButton @click="resetPreferredFilters" label="Reset" size="small"/>
                    <PrimeButton @click="savePreferredFilters" label="save" size="small"/>
                </div>
            </div>
        </Dialog>
    </GuestLayout>
</template>`

<style>
.p-accordion-header-link {
    padding: 10px;
}
</style>
