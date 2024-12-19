<script setup>

import {Head, Link, router, usePage} from '@inertiajs/vue3';
import {ref} from "vue";
import Banner from '@/Components/Banner.vue';
import MovieBrowser from "@/Components/Movie/MovieBrowser.vue";
import NavLink from "@/Components/NavLink.vue";
import {truncate} from "@/Helpers.js";
import Toast from "primevue/toast";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Button from "@/Components/Button.vue"

defineProps({
    title: String,
});

const page = usePage();

const showNavDropdown = ref(false);

const authenticated = page.props.auth?.user?.id;
const authName = page.props.auth?.user?.name;

const logout = () => {
    router.post(route('logout'), {}, {preserveState: false});
};

</script>

<template>
    <div>
        <Toast/>

        <Head :title="title"/>

        <Banner/>

        <!-- Page Content -->
        <main class="min-h-screen text-white">
            <nav class="bg-[#424b57]">
                <div class="flex md:hidden justify-between items-center p-1">
                    <Link class="flex items-center" :href="route('home.index')">
                        <ApplicationMark class="w-8 mr-1"/>
                        <span class="hidden">Home</span>
                    </Link>
                    <div>
                        <i
                            @click="showNavDropdown = !showNavDropdown"
                            :class="[{'pi-bars': !showNavDropdown, 'pi-times': showNavDropdown}, 'pi rounded-full p-1 border-2 cursor-pointer']"
                        />
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
                    <div class="flex items-center space-x-4">
                        <div>
                            <NavLink class="flex items-center" :active="route().current() === 'home.about'" :href="route('home.about')">
                                <span>About</span>
                            </NavLink>
                        </div>
                        <div>
                            <NavLink class="flex items-center" :active="route().current() === 'home.terms'" :href="route('home.terms')">
                                <span>Terms</span>
                            </NavLink>
                        </div>
                        <div v-if="!authenticated" class="space-x-4">
                            <NavLink :href="route('login')" :active="route().current() === 'login'">Login</NavLink>
                            <NavLink :href="route('register')" :active="route().current() === 'register'">Register</NavLink>
                        </div>
                        <div v-else class="flex items-center space-x-4">
                            <form method="POST" @submit.prevent="logout">
                                <Button>
                                    Log Out
                                </Button>
                            </form>
                            <div v-if="authName?.length < 10">
                                <NavLink :href="route('dashboard')" :active="route().current() === 'register'">{{ authName }}</NavLink>
                            </div>
                            <div v-else>
                                <NavLink :href="route('dashboard')" :active="route().current() === 'register'"><span v-html="truncate(authName)"></span></NavLink>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="showNavDropdown"
                    class="flex flex-col md:hidden py-2 bg-gray-500 transition-all"
                >
                    <div class="mb-2">
                        <MovieBrowser class="w-full" inputClass="py-1 w-full"/>
                    </div>
                    <div>
                        <div v-if="!authenticated">
                            <NavLink :href="route('login')" :active="route().current() === 'login'">Login</NavLink>
                        </div>
                        <div v-if="!authenticated">
                            <NavLink :href="route('register')" :active="route().current() === 'register'">Register</NavLink>
                        </div>
                        <div v-else>
                            <div v-if="authName?.length < 10">
                                <NavLink :href="route('dashboard')" :active="route().current() === 'dashboard'">{{ authName }}</NavLink>
                            </div>
                            <div v-else>
                                <NavLink :href="route('dashboard')" :active="route().current() === 'dashboard'"><span v-html="truncate(authName)"></span></NavLink>
                            </div>
                        </div>
                        <div>
                            <NavLink :href="route('home.about')" :active="route().current() === 'home.about'">
                                <span>About</span>
                            </NavLink>
                        </div>
                        <div>
                            <NavLink :href="route('home.terms')" :active="route().current() === 'home.terms'">
                                <span>Terms</span>
                            </NavLink>
                        </div>
                    </div>
                </div>
                <!--Responsive-->
            </nav>
            <main>
                <slot/>
            </main>
        </main>
    </div>
</template>

