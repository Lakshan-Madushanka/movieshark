<script setup>
import {reactive, ref, watch} from "vue";
import {useForm, router} from "@inertiajs/vue3";
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
import Slider from "primevue/slider";
import {useToast} from "primevue/usetoast";
import Calender from "primevue/calendar";
import InputNumber from "primevue/inputnumber";
import NavLink from "@/Components/NavLink.vue";
import AnchorLink from "@/Components/AnchorLink.vue";
import MovieFiltersData from "@/Data/MovieFiltersData.js";
import moment from "moment";

const props = defineProps({
    watchList: {
        type: Object,
        default: {}
    },
});

const toast = useToast();

const initialColumns = [
    {header: 'IMDB Id', field: 'imdb_id'},
    {header: 'YTS Id', field: 'yts_id'},
    {header: 'Name', field: 'name'},
    {header: 'Image', field: 'image'},
    {header: 'Genres', field: 'genres'},
    {header: 'My Rating', field: 'my_rating'},
    {header: 'Preference', field: 'preference'},
    {header: 'Released Date', field: 'released_date'},
    {header: 'Downloaded status', field: 'downloaded_status'},
    {header: 'Watched Status', field: 'watched_status'},
    {header: 'Actions', field: 'actions'},
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
        preference: {
            from: '',
            to: '',
        },
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
        my_rating: {
            from: null,
            to: null
        }
    },
});

const tempFilters = reactive({
    my_rating: [0, 0],
    preference: 'All',
});

const editForm = useForm({});

const showColumns = reactive({
    imdb_id: true,
    yts_id: true,
    name: true,
    image: true,
    genres: true,
    my_rating: true,
    preference: true,
    released_date: true,
    downloaded_status: true,
    watched_status: true,
    actions: true,
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

const onCellEditComplete = (event) => {
    if (event.newValue === event.value) {
        return;
    }

    editForm
        .transform((data) => {
            return event.newData;
        })
        .put(route('movies-watch-list.update', {'watchList': event.newData.id}), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({severity: 'success', summary: 'Movie updated', detail: 'Success', life: 3000});
            },
            onError: (error) => {
                toast.add({severity: 'warn', summary: 'Invalid values', detail: Object.values(error)[0], life: 3000});
            }
        })
};

const resetFilters = () => {
    filtersForm.reset('filter');
    tempFilters.my_rating = [0, 0];
    tempFilters.preference = 'All';
}
const sendFiltersRequest = (
    onSuccess = () => {
    },
    onError = () => {
    },
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

const showCreateMoviePage = () => {
    router.visit(route('movies-watch-list.create'))
}
</script>

<template>
    <div class="space-y-4">
        <!--Start-->
        <Toolbar>
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
                        <div>
                            <label for="filter-name">Name</label>
                            <span class="p-input-icon-right w-full">
                              <i class="pi pi-search"/>
                              <InputText
                                  v-model="filtersForm.filter['name']"
                                  id="filter-name"
                                  class="w-full"
                                  type="text"
                                  placeholder="Search"
                              />
                            </span>
                        </div>
                        <div>
                            <label for="filter-genre">Genre</label>
                            <div class="w-full">
                                <Dropdown
                                    v-model="filtersForm.filter.genre"
                                    :options="MovieFiltersData.genre"
                                    id="filter-genre"
                                    class="w-full"
                                    placeholder="All"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center w-full mb-4">
                                <span>My Rating</span>
                                <div>
                                    <InputText
                                        v-model="tempFilters.my_rating[0]"
                                        @update:modelValue="(value) => {
                                            filtersForm['filter']['my_rating']['from'] = value;
                                        }"
                                        class="max-w-8 px-1 py-1 mx-2 text-xs"
                                    />
                                    <InputText
                                        v-model="tempFilters.my_rating[1]"
                                        @update:modelValue="(value) => {
                                            filtersForm['filter']['my_rating']['to'] = value;
                                        }"
                                        class="max-w-8 px-1 py-1 text-xs"
                                    />
                                </div>
                            </div>
                            <div class="w-full flex flex-col">
                                <Slider
                                    v-model="tempFilters['my_rating']"
                                    @update:modelValue="(value) => {
                                       filtersForm['filter']['my_rating']['from'] = value[0];
                                       filtersForm['filter']['my_rating']['to'] = value[1];
                                    }"
                                    range
                                />
                            </div>
                        </div>
                        <div>
                            <label for="filter-preference">Preference</label>
                            <div class="w-full">
                                <Dropdown
                                    v-model="tempFilters.preference"
                                    :options="['All', 'High', 'Medium', 'Low']"
                                    @update:modelValue="(value) => {
                                        if(value === 'Low') {
                                            filtersForm['filter']['preference']['from'] = '0';
                                            filtersForm['filter']['preference']['to'] = 33;
                                        }
                                        else if(value === 'Medium') {
                                            filtersForm['filter']['preference']['from'] = 34;
                                            filtersForm['filter']['preference']['to'] = 66;
                                        } else {
                                            filtersForm['filter']['preference']['from'] = 67;
                                            filtersForm['filter']['preference']['to'] = 100;
                                      }
                                    }"
                                    class="w-full"
                                    id="filter-preference"
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
                            <PrimeButton icon="pi pi-filter" label="Apply Filters" size="small" @click="sendFiltersRequest"/>
                        </div>
                        <div class="justify-self-end">
                            <PrimeButton icon="pi pi-filter-slash" label="Clear Filters" size="small" @click="resetFilters"/>
                        </div>
                    </div>
                </Dialog>
            </template>
            <template #end>
                <PrimeButton @click="showCreateMoviePage" label="Add Movie" size="small"/>
            </template>
        </Toolbar>
        <!--End of Start-->

        <!--Data Table-->
        <DataTable
            :value="watchList?.data"
            :loading="filtersForm.processing || editForm.processing"
            responsive-layout="scroll"
            striped-rows
            data-key="id"
            scrollable
            scrollHeight="800px"
            @sort="onSortApplied"
            removableSort
            editMode="cell"
            @cell-edit-complete="onCellEditComplete"
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

            <template #empty><h2 class="text-center">No records found !</h2></template>

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
                <template #editor="{ data, field }">
                    <div>
                        <InputText v-model="data[field]"/>
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
                <template #editor="{ data, field }">
                    <div>
                        <InputText v-model="data[field]"/>
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
                <template #editor="{ data, field }">
                    <div>
                        <InputText v-model="data[field]"/>
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
                        <Image :src="slotProps.data.image" imageClass="!w-10 !h-10" preview/>
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
                        <Tag v-if="slotProps.data.genres?.length > 1" severity="info">
                            {{ slotProps.data.genres[0] }}/...
                        </Tag>
                        <Tag v-else severity="info">
                            {{ slotProps.data.genres?.[0] }}
                        </Tag>
                    </div>
                </template>
            </Column>
            <Column
                field="preference"
                header="Preference"
                :hidden="!showColumns['preference']"
            >
                <template #body="slotProps">
                    <div>
                        <Tag v-if="slotProps.data.my_rating < 34" severity="danger">
                            <span>Low</span>
                        </Tag>
                        <Tag v-else-if="slotProps.data.my_rating > 33 && slotProps.data.my_rating < 67"
                             severity="warning">
                            <span>Medium</span>
                        </Tag>
                        <Tag v-else severity="success">
                            <span>High</span>
                        </Tag>
                    </div>
                </template>
            </Column>
            <Column
                field="my_rating"
                header="My Rating (%)"
                :hidden="!showColumns['my_rating']"
                :sortable="true"
            >
                <template #body="slotProps">
                    <div>
                        <Tag severity="info">
                            {{ slotProps.data.my_rating }}
                        </Tag>
                    </div>
                </template>
                <template #editor="{ data, field }">
                    <div>
                        <InputNumber v-model="data[field]"/>
                    </div>
                </template>
            </Column>
            <Column
                field="released_date"
                header="Released Date"
                :sortable="true"
                sortField="released_date"
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
                <template #editor="{ data, field }">
                    <div>
                        <Calender v-model="data[field]"/>
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
                <template #editor="{ data, field }">
                    <div>
                        <Calender v-model="data[field]"/>
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
                <template #editor="{ data, field }">
                    <div>
                        <Calender v-model="data[field]"/>
                    </div>
                </template>
            </Column>
            <Column
                header="Actions"
                field="actions"
                :hidden="!showColumns['actions']"
            >
                <template #body="slotProps">
                    <div class="flex justify-end">
                        <AnchorLink v-if="slotProps.data.imdb_id"
                                    :href="route('movies.show', {id: slotProps.data.imdb_id})"
                                    target="_blank" class="text-green-500">
                            <i class="pi pi-eye text-xs"/>
                            <span>View</span>
                        </AnchorLink>
                        <div class="w-[2px] bg-green-500"></div>
                        <NavLink :href="route('movies-watch-list.edit', {watchList: slotProps.data.id})"
                                 class="text-green-500">
                            <i class="pi pi-file-edit text-xs"/> <span>Edit</span>
                        </NavLink>
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
                field="updated_at"
                header="Updated at"
                :sortable="true"
                sortField="updated_at"
                :hidden="!showColumns['updated_at']"
            >
                <template #body="slotProps">
                    <div v-if="slotProps.data.updated_at">
                        <span>{{ moment(slotProps.data.updated_at).format('YYYY-MM-DD/hh:mm')  }}</span>
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
