<script setup>
defineProps({
    href: String,
})
</script>

<template>
    <a
        class="hover:underline"
        :href="href">
        Magnet Link
    </a>
</template>
