<template>
    <div class="mt-2" v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1 justify-center">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" :key="key"
                     class="mb-1 mr-1 px-4 py-3 text-gray-400 text-sm leading-4 border rounded" v-html="link.label"/>
                <a v-else :key="`link-${key}`" class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded"
                   :class="{ 'bg-white': link.active }"
                   href="#" v-html="link.label"
                   @click="emit('navigate',link.url)"
                />
            </template>
        </div>
    </div>
</template>

<script setup>

import {defineProps, defineEmits} from "vue";

defineProps({
    links: Array,
});
const emit = defineEmits(['navigate '])

</script>
