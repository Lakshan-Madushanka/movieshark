<script setup>
import {nextTick, ref, watch} from "vue";
import InputText from 'primevue/inputtext';
import Menu from 'primevue/menu';
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
const componentRef = ref();
const menu = ref();
const results = ref([
    {
        label: 'Results',
        items: [],
    }
]);

watch(() => page.props.browsedMovieData, function (movies) {
    let tempItems = [];

    if (Array.isArray(movies) && movies.length > 0) {

        movies.forEach(function (movie) {
            tempItems.push({
                'label': movie['name'],
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
}, 1000);

const loading = ref(false);

</script>

<template>
    <div ref="componentRef">
        <div class="flex justify-content-center">
            <span :class="[{'p-input-icon-left': !form.processing, 'p-input-icon-right': form.processing}]">
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
            />
        </div>

    </div>
</template>
