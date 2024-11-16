<template>
  <div class="relative">
    <label v-if="label" class="form-label">{{ label }}:</label>
    <div class="form-input p-0" :class="{ error: error }">
      <input ref="file" type="file" :accept="accept" class="hidden" @change="change"/>
      <div v-if="!file" class="p-2">
        <button type="button" class="px-4 py-1 text-white text-xs font-medium bg-gray-500 hover:bg-gray-700 rounded-sm" @click="browse">Browse</button>

        <div v-if="loading" class="absolute top-0 right-0 left-0 bottom-0 cursor-not-allowed z-10 flex items-center justify-end me-4">
          <icon class="w-10 h-10 " name="loader"/>
        </div>

        <div v-if="modelValue" class="absolute top-0 end-0 bottom-0 z-0 flex items-center justify-end me-2">
          <img v-if="isImage" :src="modelValue" class="w-8 h-8 bg-gray-50"/>
          <icon v-if="!isImage" name="file" class="w-8 h-8 bg-gray-50"/>
        </div>

      </div>
      <div v-else class="flex items-center justify-between p-2">
        <div class="flex-1 pr-1">
          {{ file.name }} <span class="text-gray-500 text-xs">({{ filesize(file.size) }})</span>
        </div>
        <button type="button" class="px-4 py-1 text-white text-xs font-medium bg-gray-500 hover:bg-gray-700 rounded-sm" @click="remove">Remove</button>
      </div>
    </div>
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import {router} from "@inertiajs/vue3";
import axios from "axios";
import Icon from "@/Shared/Icon.vue";

export default {
  components: {Icon},
  props: {
    modelValue: String,
    label: String,
    accept: String,
    isImage: {
      type: Boolean,
      default: true
    },
    width: {
      type: Number,
      default: undefined
    },
    height: {
      type: Number,
      default: undefined
    },
    resource: {
      type: String,
      default: "general"
    },
    error: String,
  },
  data() {
    return {
      file: null,
      loading: false
    }
  },
  emits: ['update:modelValue'],
  watch: {
    file(value) {
      if (!value) {
        this.$refs.file.value = ''
      }
    },
  },
  methods: {
    filesize(size) {
      if (!size) return "Variable";
      const i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
    },
    browse() {
      this.$refs.file.click()
    },
    change(e) {
      this.loading = true;
      this.file = e.target.files[0];
      const formData = new FormData();
      formData.append('file', this.file);
      formData.append('resource', this.resource);
      if (this.width) {
        formData.append('options[width]', this.width);
      }
      if (this.height) {
        formData.append('options[height]', this.height);
      }
      const url = this.isImage ? '/files/upload-image' : '/files/upload-file';
      axios.post(url, formData, {
        'Content-Type': 'multipart/form-data'
      }).then(res => {
        const response = res.data;
        this.$emit('update:modelValue', response.data.url);
        this.loading = false;
      }).catch(res => {
        this.loading = false;
      })
    },
    remove() {
      this.file = null;
      this.$emit('update:modelValue', null)
    },
  },
}
</script>
