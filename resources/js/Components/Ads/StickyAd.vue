<script setup>
import AnchorLink from "@/Components/AnchorLink.vue";

</script>

<template>
    <div class="fixed text-center max-h-[3rem] bottom-0 w-full z-50 top bg-purple-600 hover:bg-purple-800 transition">
        <AnchorLink href="http://truereviewer.netlify.app" class="!border-0 !p-0">
            <img
                src="/images/reviewer-logo.png"
                alt="Reviewer logo"
                class="w-12"
            >
            <span class="!text-white text-sm md:text-base whitespace-nowrap">Fantastic review system specially designed for Laravel apps.</span>
        </AnchorLink>
    </div>
</template>
