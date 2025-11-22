<script setup>

import TextInput from '@/Components/TextInput.vue';


const model = defineModel({
    type: String,
    required: true,
});

</script>

<template>
    <label class="join w-full border border-gray-300 rounded-xl bg-white flex items-center gap-2 px-4 py-2 focus-within:ring-2 focus-within:ring-polban-light focus-within:border-transparent">
        <span class="btn px-4 py-2 bg-white text-gray-700 font-medium shadow-sm disabled:bg-base-200 disabled:border-base-300">
            <slot name="icon" />
        </span>

        <TextInput
            class="w-full"
            v-model="model"
            v-bind="$attrs"
        />
    </label>
</template>