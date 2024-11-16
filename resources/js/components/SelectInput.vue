<template>
  <div :class="$attrs.class">
    <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
    <select :id="id" ref="input" v-model="selected" v-bind="{ ...$attrs, class: null }" class="form-select" :class="{ error: error }">
      <slot/>
    </select>
    <div v-if="error" class="form-error">{{ Array.isArray(error) ? error.join(",") : error }}</div>
  </div>
</template>

<script>

import {getCurrentInstance, toRefs, watch} from "vue";

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `select-input-${Math.random()}`
      },
    },
    error: String | Array,
    label: String,
    modelValue: [String, Number, Boolean],
  },
  setup(props) {
    const instance = getCurrentInstance();
    watch(toRefs(props).modelValue, (val, _) => {
      instance.data.selected = val;
    });
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selected: this.modelValue,
    }
  },
  watch: {
    selected(selected) {
      console.log(this);

      this.$emit('update:modelValue', this.selected)
    }
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
  },
}
</script>
