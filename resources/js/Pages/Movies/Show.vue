<script setup>
import {nextTick, onMounted, ref, watch} from "vue";
import {Link, router} from '@inertiajs/vue3';
import Image from 'primevue/image';
import PrimeButton from 'primevue/button';
import Chip from 'primevue/chip';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Splitter from 'primevue/splitter';
import SplitterPanel from 'primevue/splitterpanel';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Panel from 'primevue/panel';
import ProgressSpinner from 'primevue/progressspinner';
import {useToast} from "primevue/usetoast";
import MovieTile from "@/Components/Movie/MovieTile.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Anchor from "@/Components/Anchor.vue";
import moment from 'moment';
import {useToggleMovieFromWatchList} from "@/Composables/useToggleMovieFromWatchList.js";

const props = defineProps({
    'auth': Object,
    'movie': {},
    'suggestions': {},
    'watchListHas': Boolean,
    'allowTorrent': Boolean,
});

const authenticated = props.auth.user !== null;

const toast = useToast();

const plotLoading = ref(false)

const watchListToggleProcessing = ref(false);

const {toggleFromWatchList: toggleMovieFromWatchList} = useToggleMovieFromWatchList(props.watchListHas ? [props.movie.id] : []);

const showDownloadBox = ref(false);
const showTrailer = ref(false);

const commentsLoading = ref(false);
const comments = ref('');

const loadComments = () => {
    commentsLoading.value = true;
    fetch(`https://yts.mx/ajax/comments/${props.movie.id}?offset=100`)
        .then((response) => {
            return response.text()
        })
        .then((html) => {
            comments.value = html;
            console.log('response', html);

            if (!html) {
                comments.value = "No Comments Found!"
                return;
            }
            nextTick(() => {
                document.querySelectorAll('.icon.icon-heart2')
                    .forEach((el) => {
                        el.classList.add('pi', 'pi-heart-fill')
                        el.style.marginInlineStart = '4px'
                        el.style.color = 'green'
                    })

                document.querySelectorAll('.avatar-thumb')
                    .forEach((el) => {
                        el.setAttribute('href', '#')
                    })

                document.querySelectorAll('.comment-text span a')
                    .forEach((el) => {
                        el.setAttribute('href', '#')
                    })
            })

        }).finally(() => {
        commentsLoading.value = false;
    })
}

onMounted(() => {
    loadComments();
})

const downloadSubtitles = function (imdbId) {
    let url = `https://yifysubtitles.ch/movie-imdb/${imdbId}`;
    window.open(url, '_blank').focus();
}

const buildTrailerLink = function (videoId) {
    return `https://www.youtube.com/embed/${videoId}?rel=0&wmode=transparent&border=0&autoplay=0&iv_load_policy=3`
}

const buildMagnetLink = function (quality, type) {

    let torrent = props.movie.torrents.filter(function (torrent) {
        return torrent['quality'] === quality && torrent['type'] === type
    })

    torrent = torrent[0];

    let trackers = [
        'udp://glotorrents.pw:6969/announce',
        'udp://tracker.opentrackr.org:1337/announce',
        'udp://torrent.gresille.org:80/announce',
        'udp://tracker.openbittorrent.com:80',
        'udp://tracker.coppersurfer.tk:6969',
        'udp://tracker.leechers-paradise.org:6969',
        'udp://p4p.arenabg.ch:1337',
        'udp://tracker.internetwarriors.net:1337',
        'udp://open.stealth.si:80/announce',
        'udp://ipv4.tracker.harry.lu:80/announce',
        'https://opentracker.i2p.rocks:443/announce',
        'udp://tracker.dler.org:6969/announce',
        'udp://tracker.torrent.eu.org:451/announce',
        'udp://p4p.arenabg.com:1337/announce',
        'udp://open.tracker.cl:1337/announce'
    ]

    let name = encodeURIComponent(`${props.movie['name']}+(${props.movie['year']})+[${quality}]+[YTS.MX]`)

    let link = `magnet:?xt=urn:btih:${torrent['hash']}&dn=${name}&`;

    trackers.forEach(function (tracker) {
        link += `tr=${tracker}&`;
    })

    return link;
}

const downloadTorrent = (hash) => {
    window.open(`https://yts.mx/torrent/download/${hash}`, '_blank')
}

const showImdbPage = (imdbCode) => {
    window.open(`https://www.imdb.com/title/${imdbCode}`, '_blank')
}

const toggleFromWatchList = () => {
    if (watchListToggleProcessing.value) {
        return;
    }

    watchListToggleProcessing.value = true;

    let status = toggleMovieFromWatchList(props.movie);

    watch(status, (status) => {
        if (status.success) {
            toast.add({severity: 'success', summary: status.summary, detail: status.details, life: 3000});
        }

        watchListToggleProcessing.value = false;
    })
}
</script>

<template>
    <GuestLayout :title="movie['name']">
        <div class="grid grid-cols-1 auto-rows-auto gap-y-12 items-start p-8 lg:px-24">
            <!--Intro-->
            <section
                class="grid sm:grid-cols-[30%_70%] sm:grid-rows-[minmax(0, auto)] lg:grid-cols-4 lg:grid-rows-1 gap-x-4 gap-y-12 lg:gap-x-8">
                <!--Poster-->
                <div class="col-span-2 md:col-span-1 justify-self-start sm:grid">
                    <figure class="flex flex-col items-center space-y-1">
                        <div class="relative">
                            <Image
                                class="border-4 w-[200px]"
                                :src="movie['cover_image']"
                                preview
                                onerror="this.onerror=null;this.src='/images/no_image.png'"
                            />
                            <div v-if="authenticated" @click="toggleFromWatchList"
                                 class="absolute right-1 top-1 p-1 bg-white flex justify-center items-center">
                                <i v-if="watchListToggleProcessing" class="pi pi-spin pi-spinner text-black text-xl"/>
                                <i v-else v-tooltip="'Toggle from watch list'"
                                   :class="['pi text-black text-xl cursor-pointer', {'pi-star': !props.watchListHas, 'pi-star-fill': props.watchListHas}]"/>
                            </div>
                        </div>
                        <PrimeButton
                            v-if="allowTorrent"
                            @click="showDownloadBox = !showDownloadBox"
                            label="Download"
                            icon="pi pi-download"
                            size="small"
                            class="p-2 w-[200px]"
                            title="Show move download options"
                        />
                    </figure>
                    <Dialog
                        v-model:visible="showDownloadBox"
                        modal
                        dismissableMask
                        header="Download Movie"
                        position="top"
                        class="!mt-4"
                        :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                    >
                        <div
                            class="flex flex-wrap justify-center md:justify-between lg:justify-center items-center [&>ul:not(:last-child)]:lg:border-r-2">
                            <ul
                                v-for="torrent in movie['torrents']"
                                :key="torrent['url']"
                                class="gap-y-6 flex flex-col justify-center items-center p-8"
                            >
                                <li><img :src="'/images/' + torrent['quality'] + '-quality.svg'" alt="image quality">
                                </li>
                                <li>{{ torrent['quality'] }}</li>
                                <li>{{ torrent['type'].toUpperCase() }}</li>
                                <li>{{ torrent['size'] }}</li>
                                <li>
                                    <Anchor :href="buildMagnetLink(torrent['quality'], torrent['type'])"
                                            class="flex items-center space-x-1 italic text-sm font-bold">
                                        <img class="w-4" src="/images/magnet.png" alt="magnet">
                                        <span class="text-green-600">Magnet Link</span>
                                    </Anchor>
                                </li>
                                <li>
                                    <PrimeButton
                                        @click="downloadTorrent(torrent['hash'])"
                                        class="py-1"
                                        label="Download"
                                        size="small"
                                        icon="pi pi-cloud-download"
                                        :title="'Download torrent for movie ' + movie['name']"
                                    />
                                </li>
                            </ul>
                        </div>
                    </Dialog>
                </div>
                <!--End of Poster-->
                <!-- Movie info -->
                <div class="col-span-2 md:col-span-1 lg:col-span-2 break-words space-y-4">
                    <h1 class="mb-4">{{ movie['name'] }}</h1>
                    <div class="flex space-x-1">
                        <Tag class="font-bold">{{ movie['year'] }}</Tag>
                        <Tag class="font-bold">
                            {{
                                movie['runtime'] === 0 ?
                                    'N/A' :
                                    moment.utc(moment.duration(movie['runtime'], 'minutes').asMilliseconds()).format('h[h] m[m]') ?? 'N/A'
                            }}
                        </Tag>
                        <Tag class="font-bold">{{ movie['language'] === '' ? 'N/A' : movie['language'] }}</Tag>
                        <Tag v-if="movie['mpa_rating']" class="font-bold">{{ movie['mpa_rating'] }}</Tag>
                    </div>
                    <p class="text-lg font-bold">{{ movie.genres.join(' / ') }}</p>
                    <div class="flex flex-wrap items-center">
                        <p class="mr-2">Available In:</p>
                        <div @if="movie['torrents']" class="flex flex-wrap gap-y-1 gap-x-1">
                            <div v-for="torrent in movie['torrents']" :key="torrent['url']">
                                <Chip :label="torrent['quality'] + '.' + torrent['type']"
                                      class="!py-0 rounded-sm text-sm"/>
                            </div>
                        </div>
                    </div>
                    <ul class="text-xl [&>li]:max-w-32 [&>li]:flex [&>li]:justify-between space-y-1">
                        <li>
                            <span><i class="pi pi-heart-fill text-xl text-purple-600"/></span>
                            <span>{{ movie['like_count'] }}</span>
                        </li>
                        <li>
                            <span @click="showImdbPage(movie['imdb_code'])"
                                  class="py-1 px-2 text-sm font-bold bg-yellow-300 text-black cursor-pointer">IMDB</span>
                            <span>{{ movie['rating'] }}</span>
                        </li>
                    </ul>
                    <div class="flex space-x-2">
                        <PrimeButton
                            @click="downloadSubtitles(movie['imdb_code'])"
                            label="Download Subtitles"
                            icon="pi pi-download"
                            size="small"
                            text
                            raised
                            class="py-2 bg-[#424b57]"
                            :title="'Download subtitle for the movie ' + movie['name']"
                        />
                        <PrimeButton
                            @click="showDownloadBox = !showDownloadBox" label="Download"
                            size="small"
                            class="p-2 block sm:hidden"
                            title="Show download options"
                        />
                    </div>
                </div>
                <!--End of Movie info -->
                <!-- Suggestions -->
                <div class="col-span-1 justify-self-start lg:justify-self-end">
                    <h2 class="lg:hidden">Suggestions</h2>
                    <div class="flex flex-wrap gap-6 lg:grid grid-cols-2 items-center md:justify-center">
                        <div v-for="suggestion in suggestions" class="justify-self-end hover:cursor-pointer">
                            <Link :href="route('movies.show', {id: suggestion['id']})">
                                <MovieTile
                                    :key="suggestion['id']"
                                    :movie=suggestion
                                    widthClass="!w-24"
                                    heightClass="!h-32"
                                    :showWatchListButton="false"
                                />
                            </Link>
                        </div>
                    </div>
                </div>
                <!-- End of Suggestions -->
            </section>
            <!--End of Intro-->

            <!--Trailer-->
            <section>
                <h2 class="lg:hidden">Trailer</h2>
                <div class="flex flex-wrap gap-4 m-auto">
                    <figure @click="showTrailer = !showTrailer" class="relative hover:cursor-pointer">
                        <Image :src="movie['image1']" width="350" imageClass="h-[150px]" preview/>
                        <figcaption
                            class="absolute top-0 left-0 w-full h-full flex-col gap-y-2 flex justify-center items-center">
                            <i class="pi pi-youtube bg-red-600 text-4xl border-4 rounded-full p-4"/>
                            <span class="font-bold">Trailer</span>
                        </figcaption>
                        <Dialog
                            v-model:visible="showTrailer"
                            modal
                            header="Trailer"
                            position="top"
                            class="!mt-4"
                            :style="{ width: '45rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                        >
                            <div class="w-full">
                                <iframe
                                    class="w-full min-h-[60vh]"
                                    :src="buildTrailerLink(movie['yt_trailer_code'])"
                                    title="YouTube video player"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                />
                            </div>
                        </Dialog>
                    </figure>
                    <Image :src="movie['image2']" width="350" imageClass="h-[150px]" preview/>
                    <Image :src="movie['image3']" width="350" imageClass="h-[150px]" preview/>
                </div>
            </section>
            <!--End of Trailer-->

            <!-- Description-->
            <section>
                <Panel toggleable>
                    <template #header>
                        <h2 class="mb-0">Plot Summary</h2>
                    </template>
                    <p class="m-0">
                        {{ movie['description_full'] }}
                    </p>
                    <ProgressSpinner v-if="plotLoading"/>
                </Panel>
            </section>
            <!--End of Description-->

            <!--Tech Details-->
            <section>
                <h2>Tech Details</h2>
                <TabView>
                    <TabPanel v-for="torrent in movie['torrents']" :key="torrent['url']"
                              :header="torrent['quality'] + '.' + torrent['type']">
                        <Splitter class="flex flex-col md:flex-row  w-full justify-around">
                            <SplitterPanel class="flex justify-center items-center">
                                <i class="hidden pi pi-folder text-center mr-2"/><span>{{ torrent['size'] }}</span>
                            </SplitterPanel>
                            <SplitterPanel class="flex justify-center items-center"><i
                                class="pi pi-video text-center mr-2"/><span>{{ torrent['video_codec'] }}</span>
                            </SplitterPanel>
                            <SplitterPanel class="flex justify-center items-center"><i
                                class="pi pi-volume-up mr-2"/><span>{{ torrent['audio_channels'] }}</span>
                            </SplitterPanel>
                            <SplitterPanel class="flex justify-center items-center"><span
                                class="mr-2">seeds</span><span>{{ torrent['seeds'] }}</span></SplitterPanel>
                            <SplitterPanel class="flex justify-center items-center"><span
                                class="mr-2">peers</span><span>{{ torrent['peers'] }}</span></SplitterPanel>
                        </Splitter>
                    </TabPanel>
                </TabView>
            </section>
            <!--End of Tech Details-->

            <!-- Comments -->
            <section>
                <Panel :toggleable="false">
                    <template #header>
                        <h2 class="mb-0">Comments</h2>
                    </template>
                    <div class="space-y-8 m-0 max-h-[70vh] overflow-auto" v-html="comments">
                    </div>
                    <ProgressSpinner v-if="commentsLoading"/>
                </Panel>
            </section>
        </div>
    </GuestLayout>
</template>`


<style>

.avatar-thumb img {
    width: 2.5rem;
    border-radius: 100%;
}

.comment {
    display: flex;
    column-gap: 0.5rem;
}

.avatar-thumb {
    flex-basis: 5%
}

.comment-text {
    flex: 1
}

.pull-right {
    float: right
}

.comment-likes {
    margin-inline-end: 1rem;
}

.comment-text > p {
    margin-block-start: 4px;
}

.comment-text > span {
    font-size: 1rem;
    color: rgb(128, 128, 128);
}
</style>
