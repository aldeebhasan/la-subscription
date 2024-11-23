<script setup>
import {onMounted, defineModel, ref, getCurrentInstance} from "vue";
import breadcrumb from "@/components/Breadcrumb.vue";
import {globals} from "@/app.js";
import VTable from "@/components/VTable.vue";
import Pagination from "@/components/Pagination.vue";
import Icon from "@/components/Icon.vue";

const data = ref({});
const loading = ref(true);
const keyword = defineModel("");

onMounted(() => load());

function load(link = "") {
  loading.value = true;
  globals.$http.get(link ? link : Global.basePath + "/api/groups", {params: {filters: {q: keyword.value}}})
    .then(response => {
      response = response.data;
      data.value = response.data;
      loading.value = false;
    });
}

const app = getCurrentInstance();

function deleteItem(id) {
  globals.confirmAlert(app, () => {
    globals.$http.delete(Global.basePath + "/api/groups/" + id)
      .then(() => load());
  })
}
</script>

<template>

  <breadcrumb title="Groups"/>

  <div class="flex p-2 mt-2">
    <div class="relative flex-grow ">
      <input v-model="keyword" @keyup.enter.native="load()" type="text" class="form-input border-0" placeholder="Search Here ..." autofocus/>
      <icon class="size-8 absolute  top-1 right-1 cursor-pointer hover:fill-gray-400 px-2" @click="load()" name="search"/>
    </div>
    <router-link to="/groups/create" type="text" class="border-2 border-gray-200 hover:border-black px-2 py-1 rounded-r-md  transition duration-200 leading-8">
      Create
    </router-link>
  </div>

  <v-table :loading="loading" :count="data.items?.length">
    <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Type</th>
      <th scope="col">Actions</th>
    </tr>
    </thead>
    <tr v-for="item in data.items">
      <td>{{ item.name }}</td>
      <td>{{ item.type }}</td>
      <td>
        <router-link class="action" :to="'/groups/'+item.id"> Edit</router-link>
        <a class="action delete " @click="deleteItem(item.id)">
          <icon name="trash" class="size-3 inline"/>
        </a>
      </td>
    </tr>
  </v-table>
  <pagination :links="data.meta?.links ?? []" @navigate="load"></pagination>

</template>

<style scoped>


</style>
