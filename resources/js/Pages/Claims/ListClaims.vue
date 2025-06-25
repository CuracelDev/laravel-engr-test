<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    claims: {
        type: Object,
        required: true,
    }
});

const tableHead = [
    'Insurer Code',
    'Encounter Date',
    'Submission Date',
    'Specialty',
    'Total Items',
    'Batched'
]
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-8xl mx-auto sm:p-14 lg:p-14">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs dark:text-gray-700 uppercase bg-gray-50 bg-white">
                    <tr>
                        <th scope="col" class="px-6 py-3"  v-for="head in tableHead" :key="index">
                            {{ head }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b bg-white dark:border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-100"
                        v-for="claim in claims" :key="claim.id">
                        <td class="px-6 py-4">
                            {{ claim.insurer.code }}
                        </td>
                        <td class="px-6 py-4">
                            {{ claim.encounter_date }}
                        </td>
                        <td class="px-6 py-4">
                            {{ claim.submission_date }}
                        </td>
                        <td class="px-6 py-4">
                            {{ claim.specialty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ claim.items_count }}
                        </td>
                        <td class="px-6 py-4">
                            {{ claim.processed_at === null ? 'No' : 'Yes' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
