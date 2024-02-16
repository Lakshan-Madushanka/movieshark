<script setup>

import {Head, Link, usePage} from '@inertiajs/vue3';
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

const authenticated = page.props.auth?.user?.id;
const authName = page.props.auth?.user?.name;

</script>

<template>
    <div>
        <Toast />

        <Head :title="title" />

        <Banner />

        <!-- Page Content -->
        <div class="min-h-screen text-white">
            <nav class="flex px-2 space-x-4 justify-between items-center bg-[#424b57]">
               <div>
                   <Link class="flex items-center" :href="route('home.index')">
                       <ApplicationMark class="w-8 mr-1"/>
                       <span>Home</span>
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
                            <NavLink :href="route('dashboard')">{{authName}}</NavLink>
                        </div>
                        <div v-else>
                            <NavLink :href="route('dashboard')">{{truncate(authName)}}</NavLink>
                        </div>
                    </div>
                </div>
            </nav>
            <main>
                <slot/>
            </main>
        </div>
    </div>
</template>


