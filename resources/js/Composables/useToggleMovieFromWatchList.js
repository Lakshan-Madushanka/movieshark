import {useForm} from "@inertiajs/vue3";
import {reactive, ref} from "vue";

const watchList = ref([]);

export function useToggleMovieFromWatchList(watchListIds) {
    watchList.value = watchListIds;
    return {checkWatchListHas, toggleFromWatchList};
}

const checkWatchListHas = (movieId) => {
    return watchList.value.includes(movieId);
};

const toggleFromWatchList = (movie) => {
    const status = reactive({
        success: false,
        summary: '',
        details: '',
    });

    useForm({
        'name': movie.name,
        'imdb_id': movie.imdb_code,
        'yts_id': movie.id,
        'image': movie.cover_image,
        'genres': movie.genres,
        'released_date': movie.year?.toString()
    }).post(route('movies-watch-list.toggle'), {
        onSuccess: () => {
            let index = watchList.value.indexOf(movie.id);

            if (index === -1) {
                watchList.value.push(movie.id)
            } else {
                watchList.value.splice(index, 1)
            }

            let summary = 'Movie Added';
            let details = 'Movie added to the watch list';

            if (! checkWatchListHas(movie.id)) {
                summary = 'Movie Removed';
                details = 'Movie removed from watch list';
            }
            status.success = true;
            status.summary = summary;
            status.details = details;
        }
    });

    return status;
};
