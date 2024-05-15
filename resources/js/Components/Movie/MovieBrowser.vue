<script setup>
import {nextTick, ref, watch} from "vue";
import InputText from 'primevue/inputtext';
import Menu from 'primevue/menu';
import ProgressSpinner from 'primevue/progressspinner';
import {useForm, usePage, router} from "@inertiajs/vue3";
import {debounce} from 'lodash';

const props = defineProps({
    inputClass: ''
});

const page = usePage();
const form = useForm({
    'query_term': '',
})

const inputRef = ref();
const menu = ref();
const results = ref([
    {
        items: [],
    }
]);

watch(() => page.props.browsedMovieData, function (movies) {
    let tempItems = [];

    if (Array.isArray(movies) && movies.length > 0) {
        movies.forEach(function (movie) {
            tempItems.push({
                'label': movie['name'],
                'image': movie['cover_image'],
                'year': movie['year'],
                command: () => {
                    router.get(route('movies.show', {'id': movie['id']}));
                }
            })
        })

    }

    results.value[0].items = tempItems;

}, {immediate: true})

const showResults = (event, show = true) => {
    if (show) {
        menu.value.show(event);
        nextTick(() => {
            inputRef.value.$el.focus();
        })
        return;
    }
    menu.value.hide(event);
};

const submit = (event) => {
    if (form.query_term === '') {
        page.props.browsedMovieData = [];
        showResults(event, false);
        return;
    }
    if (results.value[0].items.length > 0) {

    }

    showResults(event);
    sendRequest();
}

const sendRequest = debounce(() => {
    if (form.query_term === '') {
        return;
    }
    form.get(route('movies.browse'), {preserveState: true})
}, 500);

const loading = ref(false);

</script>

<template>
        <div class="flex justify-center">
            <span :class="[{'p-input-icon-left': !form.processing, 'p-input-icon-right': form.processing}, 'w-full md:w-auto']">
            <i :class="{'pi pi-search': !form.processing, 'pi pi-spin pi-spinner': form.processing}"/>
            <InputText
                ref="inputRef"
                v-model="form.query_term"
                @input="submit"
                :class="inputClass"
                icon="pi pi-ellipsis-v"
                aria-haspopup="true"
                aria-controls="overlay_menu"
                placeholder="Browse Movies"
            />
            </span>
            <Menu
                ref="menu"
                :model="results"
                :popup="true"
                id="overlay_menu"
                class="max-w-32 max-h-64 overflow-y-auto"
                :pt="{
                    menuitem: {
                        class: ['border-gray-600 border-b last:border-b-0']
                    }
                }"
            >
                <template #start>
                    <div v-if="page.props.browsedMovieData?.length > 0" class="px-4 pt-4 font-bold">
                        <span>Results ({{page.props.browsedMovieData?.length}})</span>
                    </div>
                    <div v-if="form.processing" class="flex justify-center items-center">
                        <ProgressSpinner class="w-8 h-8"/>
                    </div>
                </template>

                <template #item="{item}">
                    <div class="flex space-x-4 start p-2 cursor-pointer">
                        <img class="w-12" :src="item.image" alt="movie cover image">
                        <div class="flex flex-col space-y-1 text-sm font-bold">
                            <span>{{item['label']}}</span>
                            <span class="text-xs !text-slate-400">{{item['year']}}</span>
                        </div>
                    </div>
                </template>

                <template #end>
                    <div v-if="page.props.browsedMovieData?.length === 0 && !form.processing" class="text-center mb-2">
                        <span>Nothing found !</span>
                    </div>
                </template>
            </Menu>
        </div>
</template>
