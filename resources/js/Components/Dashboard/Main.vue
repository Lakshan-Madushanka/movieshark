<script setup>
import {reactive, ref, watch} from "vue";
import {useForm} from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Image from 'primevue/image';
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import Paginator from 'primevue/paginator';
import Dropdown from "primevue/dropdown";
import Calendar from 'primevue/calendar';
import Toolbar from "primevue/toolbar";
import Dialog from 'primevue/dialog';
import PrimeButton from "primevue/button";
import SelectButton from "primevue/selectbutton";
import MultiSelect from "primevue/multiselect";
import MovieFiltersData from "@/Data/MovieFiltersData.js";
import moment from "moment";

const props = defineProps({
    watchList: {
        type: Object,
        default: {}
    },
});

const initialColumns = [
    {header: 'IMDB Id', field: 'imdb_id'},
    {header: 'YTS Id', field: 'yts_id'},
    {header: 'Name', field: 'name'},
    {header: 'Image', field: 'image'},
    {header: 'Genres', field: 'genres'},
    {header: 'Released Date', field: 'released_date'},
    {header: 'Downloaded status', field: 'downloaded_status'},
    {header: 'Watched Status', field: 'watched_status'},
    {header: 'Created at', field: 'created_at'},
    {header: 'Updated at', field: 'updated_at'},
];

const showFiltersMenu = ref(false);
const paginationOffset = ref(0);

const filtersForm = useForm({
    page: 1,
    perPage: 10,
    sort: null,
    filter: {
        released_date: {
            from: '',
            to: ''
        },
        watched_date: {
            from: '',
            to: ''
        },
        downloaded_date: {
            from: '',
            to: ''
        },
    },
});

const showColumns = reactive({
    imdb_id: true,
    yts_id: true,
    name: true,
    image: true,
    genres: true,
    released_date: true,
    downloaded_status: true,
    watched_status: true,
    created_at: true,
    updated_at: true,
});
const columns = ref(initialColumns);
const selectedColumns = ref(initialColumns);

const watchStatusOptions = [
    {name: 'All', value: 'all'},
    {name: 'Watched', value: 'true'},
    {name: 'Unwatched', value: 'false'},
]
const downloadedStatusOptions = [
    {name: 'All', value: 'all'},
    {name: 'Downloaded', value: 'true'},
    {name: 'Not Downloaded', value: 'false'},
]

watch(props.watchList, function (watchList) {
    paginationOffset.value = watchList.per_page * (watchList.current_page - 1)
}, {immediate: true})

const onToggleColumns = (value) => {
    let selectedFields = [];

    value.forEach((val) => {
        selectedFields.push(val.field)
    })

    for (const [key2, val2] of Object.entries(showColumns)) {
        showColumns[key2] = selectedFields.includes(key2);
    }
}
const setPage = (event) => {
    filtersForm.page = event.page + 1;
    filtersForm.perPage = event.rows;

    sendFiltersRequest()
}

const onSortApplied = (event) => {
    if (event.sortOrder === -1) {
        filtersForm.sort = `-${event.sortField}`
    } else if (event.sortOrder === 1) {
        filtersForm.sort = event.sortField;
    } else {
        filtersForm.sort = null;
    }

    sendFiltersRequest();
}

const resetFilters = () => {
    for (const [key, value] of Object.entries(filtersForm.filter)) {
        if (value && typeof value === 'object') {
            for (const [key2, value2] of Object.entries(value)) {
                filtersForm.filter[key][key2] = '';
            }
            continue;
        }
        filtersForm.filter[key] = '';
    }
}
const sendFiltersRequest = (
    onSuccess = () => {},
    onError = () => {},
) => {
    filtersForm
        .transform((formFilters) => {
            let tempFormFilters = {};
            let tempFilters = {};

            for (const [key, value] of Object.entries(formFilters)) {
                if (!value || value.toString() === 'all') {
                    continue;
                }
                tempFormFilters[key] = value;
            }

            for (const [key, value] of Object.entries(formFilters.filter)) {
                if (!value || value.toString() === 'all') {
                    continue;
                }

                if (typeof value === 'object') {
                    let tempObject = {};
                    for (const [key2, value2] of Object.entries(value)) {
                        if (value2) {
                            tempObject[[key2]] = value2
                        }
                    }
                    if (Object.keys(tempObject).length !== 0) {
                        tempFilters[[key]] = tempObject;
                    }
                    continue;
                }
                tempFilters[key] = value;
            }

            return {...tempFormFilters, filter: tempFilters}
        })
        .get(route('movies-watch-list.index'), {
            preserveState: true,
            preserveScroll: true,
        })
};
</script>

<template>
    <div class="text-black space-y-4 pb-4 bg-[#424b57]">
        <!--Start-->
        <Toolbar class="mb-4">
            <template #start>
                <PrimeButton @click="showFiltersMenu = !showFiltersMenu" label="Filters" size="small"/>
                <Dialog
                    v-model:visible="showFiltersMenu"
                    modal
                    sortable
                    :dismissableMask="true"
                    header="Available Filters"
                    :style="{ width: '50rem' }"
                    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                    @keydown.enter="sendFiltersRequest"
                >
                    <div
                        class="grid grid-cols-2 gap-y-4 gap-x-4 [&>div]:flex [&>div]:flex-col [&>div>label]:mb-1 [&>div]:items-start">
                        <div>
                            <label for="filter-imdbId">IMDB Id</label>
                            <span class="p-input-icon-right w-full">
                              <i class="pi pi-search"/>
                              <InputText
                                  v-model="filtersForm.filter['imdb_id']"
                                  id="filter-imdbId"
                                  class="w-full"
                                  type="text"
                                  placeholder="Search"
                              />
                            </span>
                        </div>
                        <div>
                            <label for="filter-ytsId">YTS Id</label>
                            <span class="p-input-icon-right w-full">
                              <i class="pi pi-search"/>
                              <InputText
                                  v-model="filtersForm.filter['yts_id']"
                                  id="filter-ytsId"
                                  class="w-full"
                                  type="text"
                                  placeholder="Search"
                              />
                            </span>
                        </div>
                        <div class="col-span-2">
                            <label for="filter-genre">Genre</label>
                            <div class="w-full">
                                <Dropdown
                                    v-model="filtersForm.filter.genre"
                                    :options="MovieFiltersData.genre"
                                    id="filter-genre"
                                    class="w-1/2"
                                    placeholder="All"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="filter-releases-date-from">Released from</label>
                            <Calendar v-model="filtersForm.filter.released_date.from" class="w-full"/>
                        </div>
                        <div>
                            <label for="filter-genre">Released to date</label>
                            <Calendar v-model="filtersForm.filter.released_date.to" class="w-full"/>
                        </div>
                        <div class="col-span-2">
                            <div class="flex justify-between w-full">
                                <div>
                                    <label>Watched Status</label>
                                    <SelectButton
                                        v-model="filtersForm.filter.watched_status"
                                        :options="watchStatusOptions"
                                        optionLabel="name"
                                        optionValue="value"
                                    />
                                </div>
                                <div>
                                    <label>Downloaded Status</label>
                                    <SelectButton
                                        v-model="filtersForm.filter.downloaded_status"
                                        :options="downloadedStatusOptions"
                                        optionLabel="name"
                                        optionValue="value"
                                    />
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="filter-releases-date-from">Watched from</label>
                            <Calendar v-model="filtersForm.filter.watched_date.from" class="w-full"/>
                        </div>
                        <div>
                            <label for="filter-genre">Watched to date</label>
                            <Calendar v-model="filtersForm.filter.watched_date.to" class="w-full"/>
                        </div>
                        <div>
                            <label for="filter-releases-date-from">Downloaded from</label>
                            <Calendar v-model="filtersForm.filter.downloaded_date.from" class="w-full"/>
                        </div>
                        <div>
                            <label for="filter-genre">Downloaded to date</label>
                            <Calendar v-model="filtersForm.filter.downloaded_date.to" class="w-full"/>
                        </div>

                        <hr class="col-span-2 mt-4 mb-2"/>
                        <div>
                            <PrimeButton icon="pi pi-filter" label="Apply Filters" @click="sendFiltersRequest"/>
                        </div>
                        <div class="justify-self-end">
                            <PrimeButton icon="pi pi-filter-slash" label="Clear Filters" @click="resetFilters"/>
                        </div>
                    </div>
                </Dialog>
            </template>
        </Toolbar>
        <!--End of Start-->

        <!--Data Table-->
        <DataTable
            :value="watchList?.data"
            :loading="filtersForm.processing"
            responsive-layout="scroll"
            striped-rows
            data-key="id"
            scrollable
            scrollHeight="800px"
            @sort="onSortApplied"
            removableSort
            class="text-nowrap"
        >
            <template #header>
                <div style="text-align:left">
                    <MultiSelect
                        v-model="selectedColumns"
                        :options="columns"
                        @update:modelValue="onToggleColumns"
                        display="chip"
                        optionLabel="header"
                        placeholder="Select Columns"
                        class="max-w-md overflow-auto"
                    />
                </div>
            </template>

            <template #empty><h2 class="text-center">No records found !</h2> </template>

            <Column
                field="imdb_id"
                header="IMDB Id"
                :hidden="!showColumns['imdb_id']"
            >
                <template #body="slotProps">
                    <div class="relative">
                       <span>
                           {{ slotProps.data.imdb_id }}
                       </span>
                    </div>
                </template>
            </Column>
            <Column
                field="yts_id"
                header="YTS Id"
                :hidden="!showColumns['yts_id']"
            >
                <template #body="slotProps">
                    <div>
                       <span>
                           {{ slotProps.data.yts_id }}
                       </span>
                    </div>
                </template>
            </Column>
            <Column
                field="name"
                header="Name"
                :sortable="true"
                sortField="name"
                :hidden="!showColumns['name']"
            >
                <template #body="slotProps">
                    <div>
                       <span>
                           {{ slotProps.data.name }}
                       </span>
                    </div>
                </template>
            </Column>
            <Column
                field="image"
                header="Image"
                :hidden="!showColumns['image']"
            >
                <template #body="slotProps">
                    <div>
                        <Image :src="slotProps.data.image" imageClass="!w-12" preview/>
                    </div>
                </template>
            </Column>
            <Column
                field="genres"
                header="Genres"
                :hidden="!showColumns['genres']"
            >
                <template #body="slotProps">
                    <div>
                        <Tag v-if="slotProps.data.genres?.length > 1">
                            {{ slotProps.data.genres[0] }}/...
                        </Tag>
                        <Tag v-else>
                            {{ slotProps.data.genres?.[0] }}
                        </Tag>
                    </div>
                </template>
            </Column>
            <Column
                field="released_date"
                header="Released Date"
                :sortable="true"
                sortField="true"
                :hidden="!showColumns['released_date']"
            >
                <template #body="slotProps">
                    <div>
                        <span>{{ slotProps.data.released_date }}</span>
                    </div>
                </template>
                <template #filter>
                    <div class="min-w-64 flex justify-between w-full space-x-1">
                        <Calendar v-model="filtersForm.filter.released_date.from"/>
                        <Calendar v-model="filtersForm.filter.released_date.to"/>
                    </div>
                </template>
            </Column>
            <Column
                field="downloaded_status"
                header="Downloaded Status"
                :sortable="true"
                sortField="downloaded_status"
                :hidden="!showColumns['downloaded_status']"
            >
                <template #body="slotProps">
                    <div>
                        <span>{{ slotProps.data.downloaded_status }}</span>
                    </div>
                </template>
            </Column>
            <Column
                field="watched_status"
                header="Watched Status"
                :sortable="true"
                sortField="watched_status"
                :hidden="!showColumns['watched_status']"
            >
                <template #body="slotProps">
                    <div>
                        <span>{{ slotProps.data.watched_status }}</span>
                    </div>
                </template>
            </Column>
            <Column
                field="created_at"
                header="Created at"
                :sortable="true"
                sortFiled="created_at"
                :hidden="!showColumns['created_at']"
            >
                <template #body="slotProps">
                    <div>
                        <span>{{ moment(slotProps.data.created_at).format('YYYY-MM-DD/hh:mm') }}</span>
                    </div>
                </template>
            </Column>
            <Column
                field="updated_status"
                header="Updated at"
                :sortable="true"
                sortField="updated_at"
                :hidden="!showColumns['updated_at']"
            >
                <template #body="slotProps">
                    <div v-if="slotProps.data.updated_status">
                        <span>{{ moment(slotProps.data.updated_status) }}</span>
                    </div>
                </template>
            </Column>
        </DataTable>
        <!--End of Data Table-->

        <!--Paginator-->
        <div class="lg:mx-32">
            <Paginator
                :template="{
                            '640px': 'PrevPageLink CurrentPageReport NextPageLink',
                            '960px': 'FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink',
                            '1300px': 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink',
                             default: 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown JumpToPageInput'
                            }"
                :rows=watchList.per_page :totalRecords="watchList.total"
                :rowsPerPageOptions="[10, 20, 30]"
                v-model:first="paginationOffset"
                @page="setPage"
            />
        </div>
        <!--End of Paginator-->
    </div>
</template>
