<script setup>
defineProps({
    title: String,
    value: String,
});
</script>

<template>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 dark:bg-dark-card dark:border-dark-border">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 dark:text-dark-muted mb-1">{{ title }}</div>
                <div class="text-2xl font-semibold text-polban-dark dark:text-white mb-2">{{ value }}</div>

                <slot name="footer" />
            </div>
            <div class="w-12 h-12 rounded-lg flex items-center justify-center">
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>