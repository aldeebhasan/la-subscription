<script setup>
import {onMounted, ref, reactive, getCurrentInstance} from "vue";
import {globals} from "@/app.js";
import {useRoute, useRouter} from "vue-router";
import TextInput from "@/components/TextInput.vue";
import LoadingButton from "@/components/LoadingButton.vue";
import SelectInput from "@/components/SelectInput.vue";
import Breadcrumb from "@/components/Breadcrumb.vue";
import Loader from "@/components/Loader.vue";
import TextareaInput from "@/components/TextareaInput.vue";

const app = getCurrentInstance()
const router = useRouter()
const route = useRoute();
const id = route.params.id;

const loading = ref(false);
const form = reactive({
  data: {
    group_id: "",
    name: "",
    description: "",
    code: "",
    active: 0,
    limited: 0,
  },
  errors: {}
});

const groups = ref([])


onMounted(() => {
  loadItem()
});

function loadItem() {
  loading.value = true;
  const path = (id === 'create') ? "create" : id + "/edit";
  globals.$http.get(Global.basePath + "/api/features/" + path)
    .then(response => {
      let item = response.data.data.item;
      form.data = item || form.data;
      groups.value = response.data.data.groups;
      loading.value = false;
    });
}

function submit() {
  loading.value = true;
  if (id === 'create') {
    globals.$http.post(Global.basePath + "/api/features", form.data)
      .then(response => {
        loading.value = false;
        router.back();
      })
      .catch((error) => {
        loading.value = false;
        globals.errorAlert(app, error.response.data.message);
        form.errors = error.response.data.errors;
      });
  } else {
    globals.$http.put(Global.basePath + "/api/features/" + id, form.data)
      .then(response => {
        loading.value = false;
        router.back();
      })
      .catch((error) => {
        loading.value = false;
        globals.errorAlert(app, error.response.data.message);
        form.errors = error.response.data.errors;
      });
  }

}

</script>

<template>
  <breadcrumb :title="(id !== 'create' ? 'Edit' : 'Create')+ 'Feature'"/>

  <div class="max-w-full bg-white rounded-md shadow overflow-hidden relative">
    <div class="absolute w-full h-full flex items-center justify-center z-10 bg-opacity-65 bg-gray-100" v-if="loading">
      <loader/>
    </div>
    <form @submit.prevent="submit">

      <div class="flex flex-wrap -mr-6 p-8">
        <select-input v-model="form.data.group_id" :error="form.errors.group_id" class="pb-2 pr-6 w-full " label="Group">
          <option v-for="group of groups" :value="group.id">{{ group.name }}</option>
        </select-input>
        <text-input v-model="form.data.name" :error="form.errors.name" class="pb-2 pr-6 w-full " label="Name"/>
        <textarea-input v-model="form.data.description" :error="form.errors.name" class="pb-2 pr-6 w-full " label="Description"/>
        <text-input v-model="form.data.code" :error="form.errors.code" class="pb-2 pr-6 w-full " label="Code"/>
        <select-input v-model="form.data.active" :error="form.errors.active" class="pb-2 pr-6 w-full " label="Active">
          <option value="0">No</option>
          <option value="1">Yes</option>
        </select-input>

        <select-input v-model="form.data.limited" :error="form.errors.active" class="pb-2 pr-6 w-full " label="Limited">
          <option value="0">No</option>
          <option value="1">Yes</option>
        </select-input>

      </div>
      <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100 ">
        <loading-button :loading="loading" class="btn-indigo" type="submit">{{ id !== "create" ? "Edit" : "Create" }}</loading-button>
      </div>
    </form>
  </div>
</template>

