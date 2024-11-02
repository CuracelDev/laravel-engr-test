<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

defineProps({
    hmos: Array,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Select HMO
            </h2>
        </template>

        <div
            class="bg-white shadow max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 mt-4"
        >
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4"
            >
                <div
                    v-for="hmo in hmos"
                    :key="hmo.code"
                    class="bg-white rounded-lg shadow-lg p-4 border border-gray-200 flex flex-col justify-between"
                >
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            {{ hmo.name }}
                        </h2>
                        <p class="text-gray-600">{{ hmo.code }}</p>
                    </div>
                    <Link
                        :href="`/dashboard/orders/${hmo.id}`"
                        method="get"
                        as="button"
                        type="button"
                        class="mt-4 py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                        >Select</Link
                    >
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
