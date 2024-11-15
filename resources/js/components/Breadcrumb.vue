<template>
    <header class="flex item-center justify-between bg-gray-200 px-2 py-4">
        <div class="text-lg">{{ title }}</div>
        <div>
            <router-link class="bg-gray-700 text-white rounded px-2 py-1" to="/dashboard">
               <span class="text-sm" >  Back To Dashboard</span>
            </router-link>
        </div>
    </header>

</template>

<script setup>
import {defineProps} from "vue";

defineProps({
    title: String,
})
</script>

