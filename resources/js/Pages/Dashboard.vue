<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ListAll from "@/Components/Dashboard/WatchList/ListAll.vue";
import CreateMovie from "@/Components/Dashboard/WatchList/CreateMovie.vue";
import EditMovie from "@/Components/Dashboard/WatchList/EditMovie.vue";
import Index from "@/Components/Dashboard/Index.vue";
import {ref, watch} from "vue";

const props = defineProps({
    movie: Object,
    watchList: {},
    meta: Object,
    moviesHistory: Object,
})

const header = ref('header');

const setHeader = (name) => {
    header.value = name;
}

watch(() => route().current(), (currentRoute) => {
    switch (currentRoute) {
        case 'dashboard':
            setHeader('Dashboard');
            break;
        case 'watch-list-movies.index':
        case 'watch-list-movies.create':
        case   'watch-list-movies.edit':
            setHeader('Watch List');
            break;
        default:
            setHeader('Dashboard');
            break;
    }

}, {immediate: true})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl mb-0 text-gray-800 leading-tight">
                {{ header }}
            </h2>
        </template>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12 pt-0 sm:pt-12">
                <div class="text-black pb-4 bg-[#424b57] overflow-hidden shadow-xl sm:rounded-lg">
                    <Index v-if="route().current('dashboard')" :meta :moviesHistory :watchList/>
                    <ListAll v-if="route().current('watch-list-movies.index')" :watchList/>
                    <CreateMovie v-if="route().current('watch-list-movies.create')"/>
                    <EditMovie v-if="route().current('watch-list-movies.edit')" :movie/>
                </div>
            </div>
    </AppLayout>
</template>
