<script setup>

import { Head, Link} from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import MovieBrowser from "@/Components/Movie/MovieBrowser.vue";

defineProps({
    title: String,
});

</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <!-- Page Content -->
        <div class="min-h-screen text-white">
            <nav class="flex px-2 space-x-4 mt-1 justify-between items-center bg-[#424b57]">
               <div>
                   <Link class="flex items-center" :href="route('home.index')">
                       <img src="/images/logo.avif" class="w-8 mr-1" alt="logo"/>
                       <span>Home</span>
                   </Link>
               </div>
                <MovieBrowser inputClass="py-1"/>
                <div class="space-x-4">
                    <span>Login</span>
                    <span>Register</span>
                </div>
            </nav>
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>


