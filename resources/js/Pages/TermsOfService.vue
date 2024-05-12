<script setup>
import {Head} from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import GuestLayout from "@/Layouts/GuestLayout.vue";

defineProps({
    terms: String,
});
</script>

<template>
    <GuestLayout title="Terms of Service">
        <div class="flex flex-col items-center">
            <div class="text-white max-w-2xl p-4 font-sans antialiased w-full space-y-6">
                <h1 class="mt-8 text-center text-purple-500 font-bold">Terms of Service</h1>
                <div>
                    <p>This Agreement contains the complete terms and conditions that apply to your participation in our
                        site. If you wish to use the site including its tools and services please read these terms of
                        use
                        carefully. By accessing this site or using any part of the site or any content or services
                        hereof,
                        you agree to become bound by these terms and conditions. If you do not agree to all the terms
                        and
                        conditions, then you may not access the site or use the content or any services in the site.
                    </p>
                </div>

                <div>
                    <h2>Important</h2>
                    <p>
                        This site is for personal usage only. For any commercial usage the developer of that site
                        should be contacted via the email epmadushanka@gmail.com
                    </p>
                </div>

                <div>
                    <h2>Use of the site</h2>
                    <p>
                        The site utilizes the external API of <a href="https://yts.mx/terms">YTS.am</a>, thus users of
                        this site may be bound by its terms and conditions.
                    </p>
                    <br>
                    <p>
                        This site supports a torrent feature. If that feature is enabled and torrenting is banned in
                        your country, you should refrain from accessing this feature. We highly encourage users to
                        utilize this site solely for obtaining movie information and managing them using our admin
                        panel. If you wish to acquire movies, please purchase them legally. Let's not forget the
                        considerable efforts and financial investment behind the creation of a movie.
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>

</template>
