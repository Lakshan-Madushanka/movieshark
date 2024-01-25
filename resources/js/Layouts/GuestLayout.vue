<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

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
            <nav class="flex px-2 space-x-4 mt-1 justify-between">
               <div>
                   <Link class="flex items-center" :href="route('home.index')">
                       <img src="/images/logo.avif" class="w-8 mr-1" alt="logo"/>
                       <span>Home</span>
                   </Link>
               </div>
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

<style>

</style>
