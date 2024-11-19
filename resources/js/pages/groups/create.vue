<script setup>
import {onMounted, ref, reactive, getCurrentInstance} from "vue";
import {globals} from "@/app.js";
import {useRoute, useRouter} from "vue-router";
import TextInput from "@/components/TextInput.vue";
import LoadingButton from "@/components/LoadingButton.vue";
import SelectInput from "@/components/SelectInput.vue";
import Breadcrumb from "@/components/Breadcrumb.vue";
import Loader from "@/components/Loader.vue";

const app = getCurrentInstance()
const router = useRouter()
const route = useRoute();
const id = route.params.id;

const loading = ref(false);
const form = reactive({
  data: {
    name: "",
    type: "",
  },
  errors: {}
});


onMounted(() => {
  if (id !== 'create') {
    loadItem()
  }
});

function loadItem() {
  loading.value = true;
  globals.$http.get(Global.basePath + "/api/groups/" + id)
    .then(response => {
      form.data = response.data.data;
      loading.value = false;
    });
}

function submit() {
  loading.value = true;
  if (id === 'create') {
    globals.$http.post(Global.basePath + "/api/groups", form.data)
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
    globals.$http.put(Global.basePath + "/api/groups/" + id, form.data)
      .then(response => {
        console.log(response);
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
  <breadcrumb title="Create Plugin"/>

  <div class="max-w-full bg-white rounded-md shadow overflow-hidden relative">
    <div class="absolute w-full h-full flex items-center justify-center z-10 bg-opacity-65 bg-gray-100" v-if="loading">
      <loader/>
    </div>
    <form @submit.prevent="submit">

      <div class="flex flex-wrap  -mr-6 p-8">
        <text-input v-model="form.data.name" :error="form.errors.name" class="pb-2 pr-6 w-full " label="Name"/>
        <select-input v-model="form.data.type" :error="form.errors.type" class="pb-2 pr-6 w-full " label="Type">
          <option value="plugin">Plugin</option>
          <option value="feature">Feature</option>
        </select-input>

      </div>
      <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
        <loading-button :loading="loading" class="btn-indigo" type="submit">{{ id !== "create" ? "Edit" : "Create" }}</loading-button>
      </div>
    </form>
  </div>
</template>

