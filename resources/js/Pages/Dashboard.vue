<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ListAll from "@/Components/Dashboard/WatchList/ListAll.vue";
import CreateMovie from "@/Components/Dashboard/WatchList/CreateMovie.vue";
import EditMovie from "@/Components/Dashboard/WatchList/EditMovie.vue";

defineProps({
    watchList: {},
})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="text-black pb-4 bg-[#424b57] overflow-hidden shadow-xl sm:rounded-lg">
                    <ListAll v-if="route().current('movies-watch-list.index')" :watchList/>
                    <CreateMovie v-if="route().current('movies-watch-list.create')"/>
                    <EditMovie v-if="route().current('movies-watch-list.edit')" :watchList/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
