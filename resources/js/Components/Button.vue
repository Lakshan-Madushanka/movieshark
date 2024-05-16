<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit'
    },
})
</script>

<template>
    <button :type class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-200 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out'">
        <slot />
    </button>
</template>
