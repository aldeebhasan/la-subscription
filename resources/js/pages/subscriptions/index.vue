<script setup>
import {onMounted, ref} from "vue";
import breadcrumb from "@/components/Breadcrumb.vue";
import {globals} from "@/app.js";
import VTable from "@/components/VTable.vue";
import Pagination from "@/components/Pagination.vue";

const data = ref({});
const loading = ref(true);

onMounted(() => load())

function load(link = "") {
    loading.value = true;
    globals.$http.get(link ? link : Global.basePath + "/api/subscriptions")
        .then(response => {
            response = response.data;
            data.value = response.data;
            loading.value = false;
        });
}
</script>

<template>

    <breadcrumb title="Plugins"/>

    <v-table :loading="loading" :count="data.items?.length">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Plan</th>
            <th scope="col">Start At</th>
            <th scope="col">End At</th>
            <th scope="col">Suppressed At</th>
            <th scope="col">Canceled At</th>
            <th scope="col">Unlimited</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tr v-for="item in data.items">
            <td>{{ item.id }}</td>
            <td>{{ item.plan }}</td>
            <td>{{ item.start_at }}</td>
            <td>{{ item.end_at }}</td>
            <td>{{ item.suppressed_at }}</td>
            <td>{{ item.canceled_at }}</td>
            <td>{{ item.unlimited }}</td>
            <td><a class="action" :href="'subscriptions/'+item.id"> View</a></td>
        </tr>
    </v-table>
    <pagination :links="data.meta?.links ?? []" @navigate="load"></pagination>

</template>

<style scoped>


</style>
