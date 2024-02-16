<script setup>
import {useForm} from "@inertiajs/vue3";
import {computed, onMounted, reactive, ref, watch, watchEffect} from "vue";
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import {useToast} from "primevue/usetoast";
import {BarChart} from 'vue-chart-3';
import {Chart, registerables} from "chart.js";
import moment from "moment";
import Image from "primevue/image";
import AnchorLink from "@/Components/AnchorLink.vue";
import NavLink from "@/Components/NavLink.vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";

Chart.register(...registerables);

const props = defineProps({
    meta: Object,
    moviesHistory: Object,
    watchList: {
        type: Object,
        default: {}
    },
});

const toast = useToast();

const monthNames = moment.months();

const chartForm = useForm({
    year: moment().year().toString(),
    filter:     {name: 'All', value: 'all'},
});

const chartDataOptions = [
    {name: 'All', value: 'all'},
    {name: 'Watched', column: 'watched_status', value: 'true'},
    {name: 'Unwatched', column: 'watched_status', value: 'false'},
    {name: 'Downloaded', column: 'downloaded_status', value: 'true'},
    {name: 'NotDownloaded', column: 'downloaded_status', value: 'false'},
    {name: 'Highly Preferred', column: 'preference', value: {from: 67, to: 100}},
    {name: 'Medium Preferred', column: 'preference', value: {from: 34, to: 66}},
    {name: 'Low Preferred', column: 'preference', value: {from: 0, to: 33}},
];

const chartData = reactive({
    labels: [],
    datasets: [
        {
            label: 'Movie Count',
            data: [],
            borderColor: 'black',
            backgroundColor: '#686868',

        }
    ]
});

const chartOptions = computed(() => {
    return {responsive: true}
});

watchEffect(()  => {
    chartData.labels = props.moviesHistory?.map(data => monthNames[data.month - 1]);
    chartData.datasets[0].data = props.moviesHistory?.map(data => data.total);
});

const submitChartForm = () => {
    chartForm
        .transform((data) => {
            let tmpData = {year: moment(data.year).year()};

            if (data.filter && data.filter['name'] !== 'All') {
                console.log(data.filter.value)
                let filter = {[data.filter.column] : data.filter.value}
                tmpData = {...tmpData, filter: filter}
            }

            return tmpData;
        })
        .get(route('dashboard'), {
            preserveScroll: true, preserveState: true
        });
};

const test = () => {
    console.log(chartForm.year)
}
</script>

<template>
    <div class="text-white p-4 space-y-8" @click="test">
        <h2>Movies ({{ meta.all }}) Info</h2>
        <div class="flex justify-between flex-wrap">
            <Card class="!min-w-64 m-2">
                <template #title><h2>Watched Status</h2></template>
                <template #content>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span>Watched Movies</span>
                            <Tag severity="info">{{ props.meta.watched }}</Tag>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Un-Watched Movies</span>
                            <Tag severity="info">{{ props.meta.unWatched }}</Tag>
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="!min-w-64 m-2">
                <template #title><h2>Downloaded Status</h2></template>
                <template #content>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span>Downloaded Movies</span>
                            <Tag severity="info">{{ props.meta.downloaded }}</Tag>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Not-Downloaded Movies</span>
                            <Tag severity="info">{{ props.meta.notDownloaded }}</Tag>
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="!min-w-64 m-2">
                <template #title><h2>Preferred Status</h2></template>
                <template #content>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span>High</span>
                            <Tag severity="info">{{ props.meta.highPreferred }}</Tag>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Medium</span>
                            <Tag severity="info">{{ props.meta.mediumPreferred }}</Tag>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Low</span>
                            <Tag severity="info">{{ props.meta.lowPreferred }}</Tag>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <div>
            <h2>Watch History</h2>
            <div class="!bg-gray-300 rounded-lg overflow-hidden">
                <div class="mb-4 flex justify-between">
                    <Calendar v-model="chartForm.year" view="year" dateFormat="yy"
                              @update:modelValue="submitChartForm"/>
                    <Dropdown v-model="chartForm.filter" :options="chartDataOptions" optionLabel="name"
                              @update:modelValue="submitChartForm"/>
                </div>
                <BarChart
                    id="my-chart-id"
                    :chartData="chartData"
                    :options="chartOptions"
                />
            </div>
        </div>

        <div>
            <h2>Latest watch list records</h2>
            <DataTable
                :value="watchList"
                responsive-layout="scroll"
                striped-rows
                data-key="id"
                scrollable
                scrollHeight="500px"
                removableSort
                editMode="cell"
                class="text-nowrap"
            >

                <template #empty><h2 class="text-center">No records found !</h2></template>

                <Column
                    field="imdb_id"
                    header="IMDB Id"
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
                    :sortable="true"
                >
                    <template #body="slotProps">
                        <div>
                            <Tag severity="info">
                                {{ slotProps.data.my_rating }}
                            </Tag>
                        </div>
                    </template>
                </Column>
                <Column
                    field="released_date"
                    header="Released Date"
                    :sortable="true"
                    sortField="released_date"               >
                    <template #body="slotProps">
                        <div>
                            <span>{{ slotProps.data.released_date }}</span>
                        </div>
                    </template>
                </Column>
                <Column
                    field="downloaded_status"
                    header="Downloaded Status"
                    :sortable="true"
                    sortField="downloaded_status"
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
                >
                    <template #body="slotProps">
                        <div>
                            <span>{{ slotProps.data.watched_status }}</span>
                        </div>
                    </template>
                </Column>
                <Column
                    header="Actions"
                    field="actions"
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
                >
                    <template #body="slotProps">
                        <div v-if="slotProps.data.updated_status">
                            <span>{{ moment(slotProps.data.updated_status) }}</span>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>
