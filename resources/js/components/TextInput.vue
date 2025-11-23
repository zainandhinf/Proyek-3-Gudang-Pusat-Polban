<script setup>

import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="input input-bordered w-full w-full pr-4 rounded-xl bg-white border-none placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-none text-gray-700"
        v-model="model"
        ref="input"
    />
</template>