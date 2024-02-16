<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ListAll from "@/Components/Dashboard/WatchList/ListAll.vue";
import CreateMovie from "@/Components/Dashboard/WatchList/CreateMovie.vue";
import EditMovie from "@/Components/Dashboard/WatchList/EditMovie.vue";
import Index from "@/Components/Dashboard/Index.vue";
import {ref, watch} from "vue";

const props = defineProps({
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
        case 'movies-watch-list.index':
        case 'movies-watch-list.create':
        case   'movies-watch-list.edit':
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ header }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="text-black pb-4 bg-[#424b57] overflow-hidden shadow-xl sm:rounded-lg">
                    <Index v-if="route().current('dashboard')" :meta :moviesHistory :watchList/>
                    <ListAll v-if="route().current('movies-watch-list.index')" :watchList/>
                    <CreateMovie v-if="route().current('movies-watch-list.create')"/>
                    <EditMovie v-if="route().current('movies-watch-list.edit')" :watchList/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
