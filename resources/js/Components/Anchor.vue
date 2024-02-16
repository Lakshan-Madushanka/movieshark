<script setup>
defineProps({
    href: String,
})
</script>

<template>
    <a
        class="hover:underline"
        :href="href"
    >
        <slot/>
    </a>
</template>
