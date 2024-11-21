<template>
  <div class="flex flex-col overflow-x-auto mt-2">
    <div class="inline-block min-w-full p-2 ">
      <div class="overflow-x-auto">
        <table class="table" :class="{'loading' :loading}">
          <slot/>
          <tr>
            <td colspan="1000" v-if="count ===0 || loading" class="h-20 " style="text-align: center!important;">
              {{ loading ? "" : "No data to show" }}
            </td>
          </tr>
        </table>

      </div>
    </div>
  </div>

</template>

<script setup>
import {defineProps} from "vue";

defineProps({
  loading: {
    default: false,
    type: Boolean
  },
  count: Number,
})
</script>

