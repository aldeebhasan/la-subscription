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
  globals.$http.get(link ? link : Global.basePath + "/api/features")
    .then(response => {
      response = response.data;
      data.value = response.data;
      loading.value = false;
    });
}
</script>

<template>
  <breadcrumb title="Features"/>

  <div class="flex p-2 mt-2">
    <input type="text" class="form-input flex-grow border-0" placeholder="Search Here ..."/>
    <router-link to="/features/create" type="text" class="border-2 border-gray-200 hover:border-black px-2 py-1 rounded-r-md  transition duration-200 leading-8">
      Create
    </router-link>
  </div>

  <v-table :loading="loading" :count="data.items?.length ">
    <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Code</th>
      <th scope="col">Active</th>
      <th scope="col">Group</th>
      <th scope="col">Limited</th>
      <th scope="col">Actions</th>
    </tr>
    </thead>
    <tr v-for="item in data.items">
      <td>{{ item.name }}</td>
      <td>{{ item.code }}</td>
      <td>{{ item.active }}</td>
      <td>{{ item.group }}</td>
      <td>{{ item.limited }}</td>
      <td><a class="action" :href="'features/'+item.id"> Edit</a></td>
    </tr>
  </v-table>
  <pagination :links="data.meta?.links ?? []" @navigate="load"></pagination>
</template>

