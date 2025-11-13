<script setup>
const model = defineModel({
    type: [String, Number],
    required: true,
});
</script>

<template>
    <select
        class="select select-bordered w-full focus:select-primary"
        v-model="model"
    >
        <slot /> </select>
</template>