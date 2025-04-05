<script setup>
import Button from "primevue/button";
import Dialog from 'primevue/dialog';
import {onBeforeMount, ref} from "vue";
import moment from "moment";


const show = ref(false);

onBeforeMount(() => {
    const showedAt = localStorage.getItem('popupShowedAt');

    if (!showedAt || (moment().unix() > moment.unix(parseInt(showedAt)).add(6, 'h').unix())) {
        setTimeout(() => {
            show.value = true;
            localStorage.setItem('popupShowedAt', moment().unix().toString());
        }, 5000);
    }
})
</script>

<template>
    <div class="flex justify-center">
        <Dialog v-model:visible="show" :dismissableMask="true" modal header="Header" :style="{ width: '50rem' }" class="glowing-box"
                :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <template #header>
            <span class="text-3xl text-purple-6f00 font-bold">Elegant Review System for Laravel</span>
        </template>
        <p class="m-0">
            <img class="w-full" src="/images/reviewer-screenshot.png" alt="Reviewer Screenshot">
        </p>
        <template #footer>
            <a href="https://truereviewer.netlify.app" target="_blank" rel="noopener">
                <Button label="Get it" size="small" />
            </a>
            <Button @click="show = false" label="Remind me later" size="small"/>
        </template>
        </Dialog>
    </div>
</template>

<style>
.glowing-box {
    --border-angle: 0deg;
    border-radius: 12px;
    box-shadow: 0px 2px 4px hsl(0 0% 0% / 25%);
    animation: border-angle-rotate 2s infinite linear;
    border: 5px solid transparent;
    background: linear-gradient(white, white) padding-box,
    conic-gradient(from var(--border-angle), hsl(296.31deg 73.78% 33.98%) 50%, white) border-box;
}

@keyframes border-angle-rotate {
    from {
        --border-angle: 0deg;
    }
    to {
        --border-angle: 360deg;
    }
}

@property --border-angle {
    syntax: "<angle>";
    initial-value: 0deg;
    inherits: false;
}
</style>
