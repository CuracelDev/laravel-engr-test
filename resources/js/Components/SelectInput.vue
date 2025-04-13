<template>
    <select
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
        v-model="model"
        ref="select"
    >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <option
            v-for="option in options"
            :key="option.value"
            :value="option.value"
        >
            {{ option.label }}
        </option>
    </select>
</template>

<script setup>
import { ref, onMounted } from "vue";

const model = defineModel({
    type: [String, Number],
    required: true,
});

const { placeholder, options } = defineProps({
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
    },
});

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute("autofocus")) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>
