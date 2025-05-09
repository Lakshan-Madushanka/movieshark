<script setup>
import {useForm} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import InputText from "primevue/inputtext";
import Card from 'primevue/card';
import MultiSelect from "primevue/multiselect";
import PrimeButton from "primevue/button";
import Calender from "primevue/calendar";
import Textarea from 'primevue/textarea';
import Image from 'primevue/image';
import {useToast} from "primevue/usetoast";
import MovieFiltersData from "@/Data/MovieFiltersData.js";
import {capitalizeFirstLetter} from "@/Helpers.js";
import InputNumber from "primevue/inputnumber";
import moment from "moment";

const props = defineProps({
    movie: {
        type: Object,
    }
})

const toast = useToast();

const form = useForm({
    imdb_id: props.movie.imdb_id,
    yts_id: props.movie.yts_id,
    name: props.movie.name,
    image: props.movie.image,
    genres: [],
    my_rating: props.movie.my_rating,
    released_date: props.movie.released_date,
    downloaded_status: props.movie.downloaded_status,
    watched_status: props.movie.watched_status,
    description: props.movie.description,
})

onMounted(() => {
    form.genres = props.movie.genres?.map((genre) => capitalizeFirstLetter(genre));
})

const columns = ref([])

onMounted(() => {
    let genres = MovieFiltersData.genre;
    genres.splice(0, 1);

    genres.forEach((gen) => {
        columns.value.push({header: gen, value: gen.toLowerCase()})
    })

    let selectedGenres = [];

    form.genres.forEach(sg => {
        selectedGenres.push({header: sg, value: sg.toLowerCase()})
    })

    form.genres = selectedGenres;
})

const editMovie = () => {
    if (form.processing) {
        return;
    }
    form
        .transform((data) => {
            let tempGenres = [];

            for (const [key, value] of Object.entries(data)) {
                if (key === 'genres') {
                    value.forEach(genre => {
                        if (genre) {
                            tempGenres.push(genre.value);
                        }
                    })
                }

                if (key === 'released_date' && value) {
                    data[key] = moment(value).year()
                }

                if ((key === 'downloaded_status' || key === 'watched_status') && value) {
                    data[key] = moment(value).format('YYYY-MM-DD')
                }
            }
            return {...data, genres: tempGenres}
        })
        .put(route('watch-list-movies.update', {'movieId': props.movie.id}), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                form.clearErrors();
                toast.add({severity: 'success', summary: 'Movie updated', detail: 'Success', life: 3000});
            },
            onError: (error) => {
                toast.add({severity: 'error', summary: Object.values(error)[0], detail: 'Error', life: 3000});
            }
        })
};
</script>

<template>
    <Card>
        <template #title><h1>Edit Movie</h1></template>
        <template #content>
            <form @submit.prevent="editMovie" @keydown.enter="editMovie" class="grid [&>div]:col-span-2 [&>div]:md:col-span-1 grid-cols-2 gap-8">
                <div>
                    <label for="input-imdbId">IMDB Id</label>
                    <InputText
                        v-model="form.imdb_id"
                        id="input-imdbId"
                        class="w-full"
                        type="text"
                        placeholder="IMDB Id"
                    />
                    <span v-if="form.errors.imdb_id" class="text-sm text-red-500">{{ form.errors.imdb_id }}</span>
                </div>
                <div>
                    <label for="input-ytsId">YTS Id</label>
                    <InputText
                        v-model="form.yts_id"
                        id="input-ytsId"
                        class="w-full"
                        type="text"
                        placeholder="IMDB Id"
                    />
                    <span v-if="form.errors.yts_id" class="text-sm text-red-500">{{ form.errors.yts_id }}</span>
                </div>
                <div>
                    <label for="input-name">Name <i class="pi pi-star-fill text-xs text-red-500"/></label>
                    <InputText
                        v-model="form.name"
                        id="input-name"
                        class="w-full"
                        type="text"
                        placeholder="Name"
                    />
                    <span v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</span>
                </div>
                <div>
                    <label for="input-genres">Genres</label>
                    <MultiSelect
                        v-model="form.genres"
                        :options="columns"
                        optionLabel="header"
                        display="chip"
                        placeholder="Select Genres"
                        class="w-full"
                        inputId="input-genres"
                    />
                    <span v-if="form.errors.genres" class="text-sm text-red-500">{{ form.errors.genres }}</span>
                </div>
                <div class="flex flex-col">
                    <label for="input-my_rating">My Rating</label>
                    <InputNumber v-model="form.my_rating" inputId="input-my_rating" :min="0" :max="100"/>
                    <span v-if="form.errors.my_rating" class="text-sm text-red-500">{{form.errors.my_rating}}</span>
                </div>
                <div class="flex flex-col">
                    <label for="input-released_date">Released Date</label>
                    <Calender view="year" dateFormat="yy" v-model="form.released_date" inputId="input-released_date"/>
                    <span v-if="form.errors.released_date"
                          class="text-sm text-red-500">{{ form.errors.released_date }}</span>
                </div>
                <div class="flex flex-col">
                    <label for="input-downloaded_status">Downloaded Date</label>
                    <Calender v-model="form.downloaded_status" inputId="input-downloaded_status"/>
                    <span v-if="form.errors.downloaded_status"
                          class="text-sm text-red-500">{{ form.errors.downloaded_status }}</span>
                </div>
                <div class="flex flex-col col-span-2">
                    <label for="input-watched_status">Watched Date</label>
                    <Calender v-model="form.watched_status" inputId="input-watched_status"/>
                    <span v-if="form.errors.watched_status"
                          class="text-sm text-red-500">{{ form.errors.watched_status }}</span>
                </div>
                <div>
                    <label for="input-url">Image URL</label>
                    <InputText
                        v-model="form.image"
                        id="input-url"
                        class="w-full"
                        type="url"
                        placeholder="Image URL"
                    />
                    <span v-if="form.errors.image" class="text-sm text-red-500">{{ form.errors.image }}</span>
                </div>
                <div class="mt-6">
                    <Image
                        :src="form.image"
                        class="w-64"
                        preview
                    />
                    <span v-if="form.errors.image" class="text-sm text-red-500">{{ form.errors.image }}</span>
                </div>
                <div class="flex flex-col">
                    <label for="input-description">Description</label>
                    <Textarea v-model="form.description" id="input-description"/>
                    <span v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</span>
                </div>
                <hr class="col-span-2"/>
                <PrimeButton type="submit" icon="pi pi-save" label="Edit" :loading="form.processing"/>
                <PrimeButton type="button" icon="pi pi-times" label="Reset" @click="form.reset()"
                             :loading="form.processing"/>
            </form>
        </template>
    </Card>
</template>
