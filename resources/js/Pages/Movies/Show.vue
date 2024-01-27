<script setup>
import {ref} from "vue";
import {Link} from '@inertiajs/vue3';
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
import MovieTile from "@/Components/Movie/MovieTile.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Anchor from "@/Components/Anchor.vue";
import moment from 'moment';

const props = defineProps({
    'movie': {},
    'suggestions': {},
});

const showDownloadBox = ref(false);
const showTrailer = ref(false);

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
</script>

<template>

    <GuestLayout :title="movie['name']">
        <div class="grid grid-cols-1 auto-rows-auto gap-y-8 items-start py-8 px-4 lg:px-24">
            <!--Intro-->
            <section
                class="grid sm:grid-cols-[30%_70%] sm:grid-rows-[minmax(0, auto)] lg:grid-cols-4 lg:grid-rows-1 gap-x-4 gap-y-6 lg:gap-x-8">
                <!--Poster-->
                <div class="justify-start items-center hidden sm:grid">
                    <figure class="flex flex-col space-y-1">
                        <Image class="border-4" :src="movie['cover_image']" width="200" preview/>
                        <PrimeButton
                            @click="showDownloadBox = !showDownloadBox"
                            label="Download"
                            size="small"
                            class="p-2"
                            title="Show move download options"
                        />
                    </figure>
                    <Dialog
                        v-model:visible="showDownloadBox"
                        modal header="Download Movie"
                        position="top"
                        class="!mt-4"
                        :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
                    >
                        <div
                            class="flex flex-wrap justify-center items-center gat-y-4 [&>ul:not(:last-child)]:!border-r-2">
                            <ul v-for="torrent in movie['torrents']" :key="torrent['url']"
                                class="[&>li]:mb-8 flex flex-col justify-center items-center p-8">
                                <li>{{ torrent['quality'] }}</li>
                                <li>{{ torrent['type'].toUpperCase() }}</li>
                                <li>{{ torrent['size'] }}</li>
                                <li>
                                    <Anchor :href="buildMagnetLink(torrent['quality'], torrent['type'])"/>
                                </li>
                                <li>
                                    <PrimeButton
                                        class="py-1"
                                        label="Download"
                                        size="small"
                                        :title="'Download torrent for movie ' + movie['name']"
                                    />
                                </li>
                            </ul>
                        </div>
                    </Dialog>
                </div>
                <!--End of Poster-->
                <!-- Movie info -->
                <div class="lg:col-span-2 break-words space-y-4">
                    <h2>{{ movie['name'] }}</h2>
                    <div class="flex space-x-1">
                        <Tag class="font-bold">{{ movie['year'] }}</Tag>
                        <Tag class="font-bold">
                            {{
                                moment.utc(moment.duration(movie['runtime'], 'minutes').asMilliseconds()).format('h[h] m[m]')
                            }}
                        </Tag>
                        <Tag class="font-bold">{{ movie['language'] }}</Tag>
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
                            <span><i class="pi pi-heart text-xl text-purple-600"/></span>
                            <span>{{ movie['like_count'] }}</span>
                        </li>
                        <li>
                            <span class="py-1 px-2 text-sm font-bold bg-yellow-300 text-black">IMDB</span>
                            <span>{{ movie['rating'] }}</span>
                        </li>
                    </ul>
                    <div class="flex space-x-2">
                        <PrimeButton
                            @click="downloadSubtitles(movie['imdb_code'])"
                            label="Download Subtitles"
                            icon="pi pi-download" size="small"
                            text raised class="py-2 bg-[#424b57]"
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
                <div class="col-span-2 lg:col-span-1 justify-self-start lg:justify-self-end">
                    <h2 class="lg:hidden">Suggestions</h2>
                    <div class="flex flex-wrap gap-x-2 lg:grid grid-cols-2 items-center md:justify-center">
                        <div v-for="suggestion in suggestions" class="justify-self-end hover:cursor-pointer">
                            <Link :href="route('movies.show', {id: suggestion['id']})">
                                <MovieTile
                                    :key="suggestion['id']"
                                    :movie=suggestion
                                    widthClass="!w-24"
                                    heightClass="!h-32"
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
                <div class="flex flex-wrap m-auto">
                    <figure @click="showTrailer = !showTrailer" class="relative hover:cursor-pointer">
                        <Image :src="movie['image1']" width="350" imageClass="h-[150px]" preview/>
                        <figcaption class="absolute top-0 left-0 w-full h-full flex-col gap-y-2 flex justify-center items-center">
                                <i class="pi pi-youtube text-4xl border-4 rounded-full p-4"/>
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
                        <h2 class="mb-0">Ploat Summary</h2>
                    </template>
                    <p class="m-0">
                        {{ movie['description_full'] }}
                    </p>
                </Panel>
            </section>
            <!--End of Description-->

            <!--Tech Details-->
            <section>
                <h2>Tech Details</h2>
                <TabView>
                    <TabPanel v-for="torrent in movie['torrents']" :key="torrent['url']" :header="torrent['quality']">
                        <Splitter class="flex w-full justify-around">
                            <SplitterPanel class="flex justify-center items-center"><i
                                class="pi pi-folder text-center mr-2"/><span>{{ torrent['size'] }}</span>
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
        </div>
    </GuestLayout>
</template>`


