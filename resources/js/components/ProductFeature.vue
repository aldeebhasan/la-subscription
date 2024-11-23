<script setup>
import {defineProps, defineEmits, watch} from "vue";

const props = defineProps({
  features: Array,
  form: Object
})

const emit = defineEmits(['update'])

function changeInput(id, key, value) {
  if (Object.keys(props.form.data.features).includes(id.toString())) {
    props.form.data.features[id][key] = value;
  } else {
    props.form.data.features[id] = {};
    props.form.data.features[id][key] = value;
  }

  emit('update', props.form.data.features);
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="table ">
      <thead>
      <tr>
        <th>Feature</th>
        <th>Value</th>
        <th>Active</th>
      </tr>
      </thead>
      <tbody>
      <template v-for="(featureGroup,group) in features">
        <tr>
          <td colspan="3" class="bg-gray-200">{{ group }}</td>
        </tr>

        <tr v-for="feature in featureGroup">
          <td>{{ feature.name }}</td>
          <td>
            <input type="number" v-if="feature.limited"
                   @keyup="changeInput(feature.id,'value',$event.target.value)"
                   @change="changeInput(feature.id,'value',$event.target.value)"
                   class="w-40 h-8" placeholder="Value"
                   :value="form.data.features[feature.id]?.value || 0"
            />
          </td>
          <td>
            <input type="checkbox" @change="changeInput(feature.id,'active',$event.target.checked)"
                   :checked="form.data.features[feature.id]?.active || false"
                   placeholder="Active"/>
          </td>
        </tr>
      </template>
      </tbody>
    </table>
  </div>
</template>

<style scoped>

</style>
