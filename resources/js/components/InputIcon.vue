<script setup>
import TextInput from '@/Components/TextInput.vue';

const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <label class="join w-full">
        <span class="join-item btn btn-ghost disabled:bg-base-200 disabled:border-base-300">
            <slot name="icon" />
        </span>
        <TextInput
            class="join-item w-full"
            v-model="model"
            v-bind="$attrs" 
        />
    </label>
</template>