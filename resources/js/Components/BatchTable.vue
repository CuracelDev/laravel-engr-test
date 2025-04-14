<template>
    <table class="w-full border-collapse my-4">
        <thead>
            <tr>
                <th class="border border-white px-2 py-1 text-center">Batch Name</th>
                <th class="border border-white px-2 py-1 text-center">Date</th>
                <th class="border border-white px-2 py-1 text-center">Status</th>
                <th class="border border-white px-2 py-1 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="batch in batches" :key="batch.id">
                <td class="border border-white px-2 py-1 text-center">{{ batch.name }}</td>
                <td class="border border-white px-2 py-1 text-center">{{ batch.date }}</td>
                <td class="border border-white px-2 py-1 text-center">{{ batch.status }}</td>
                <td class="border border-white px-2 py-1 text-center">
                    <button
                        @click="goToBatch(batch.id)"
                        class="border border-black px-2 py-1 cursor-pointer"
                    >
                        View
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { defineProps } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    batches: {
        type: Array,
        required: true,
    },
});

function goToBatch(batchId) {
    router.visit(route("batch.show", batchId) + "?limit=10");
}
</script>
