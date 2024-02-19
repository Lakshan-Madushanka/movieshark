<script setup>

import {Head, Link, usePage} from '@inertiajs/vue3';
import {ref} from "vue";
import Banner from '@/Components/Banner.vue';
import MovieBrowser from "@/Components/Movie/MovieBrowser.vue";
import NavLink from "@/Components/NavLink.vue";
import {truncate} from "@/Helpers.js";
import Toast from "primevue/toast";
import ApplicationMark from "@/Components/ApplicationMark.vue";

defineProps({
    title: String,
});

const page = usePage();

const showNavDropdown = ref(false);

const authenticated = page.props.auth?.user?.id;
const authName = page.props.auth?.user?.name;

</script>

<template>
    <div>
        <Toast/>

        <Head :title="title"/>

        <Banner/>

        <!-- Page Content -->
        <div class="min-h-screen text-white">
            <nav class="bg-[#424b57]">
                <div class="flex md:hidden justify-between items-center p-1">
                    <ApplicationMark class="w-8 mr-1"/>
                    <div>
                        <i
                            v-if="authenticated"
                            @click="showNavDropdown = !showNavDropdown"
                            :class="[{'pi-bars': !showNavDropdown, 'pi-times': showNavDropdown}, 'pi rounded-full p-1 border-2']"
                        />
                        <NavLink v-else :href="route('login')">Login</NavLink>
                    </div>
                </div>
                <div class="hidden md:flex px-2 space-x-4 justify-between items-center">
                    <div>
                        <Link class="flex items-center" :href="route('home.index')">
                            <ApplicationMark class="w-8 mr-1"/>
                            <span class="hidden">Home</span>
                        </Link>
                    </div>
                    <MovieBrowser inputClass="py-1"/>
                    <div class="space-x-4">
                        <div v-if="!authenticated">
                            <NavLink :href="route('login')">Login</NavLink>
                            <NavLink :href="route('register')">Register</NavLink>
                        </div>
                        <div v-else>
                            <div v-if="authName?.length < 10">
                                <NavLink :href="route('dashboard')">{{ authName }}</NavLink>
                            </div>
                            <div v-else>
                                <NavLink :href="route('dashboard')">{{ truncate(authName) }}</NavLink>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="showNavDropdown"
                    class="flex flex-col md:hidden py-2 transition-all">
                    <MovieBrowser class="w-full border-l-4 border-green-500" inputClass="py-1 w-full"/>
                    <div>
                        <div v-if="!authenticated">
                            <NavLink :href="route('login')">Login</NavLink>
                            <NavLink :href="route('register')">Register</NavLink>
                        </div>
                        <div v-else class="px-2 p-1 bg-[#111827] border-l-4 border-green-500">
                            <div v-if="authName?.length < 10">
                                <NavLink :href="route('dashboard')" class="w-full">{{ authName }}</NavLink>
                            </div>
                            <div v-else>
                                <NavLink :href="route('dashboard')">{{ truncate(authName) }}</NavLink>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Responsive-->
            </nav>
            <main>
                <slot/>
            </main>
        </div>
    </div>
</template>

