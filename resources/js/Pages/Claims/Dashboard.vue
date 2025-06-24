<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import SummaryCard from '@/Components/Claims/SummaryCard.vue';

const props = defineProps({
    claimsSummary: {
        type: Object,
        required: true,
    }
});
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-8xl mx-auto sm:p-14 lg:p-14">
            <div class="grid lg:grid-cols-4 sm:grid-cols-1">
                <div class="col-span-3">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hello, {{ $page.props.auth.user.name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">Welcome to your Claims dashboard</p>
                </div>
                <div class="grid grid-cols-2 gap-4 content-end">
                    <div>
                        <button @click="$inertia.visit(route('provider-claims.create'))" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded h-10">Create Claim</button>
                    </div>
                    <div>
                        <button class="outline outline-blue-600 hover:bg-blue-200 text-blue-600 font-bold py-2 px-4 rounded h-10">View Claims</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="min-h-screen bg-white py-6">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-2">
                <div class="grid grid-cols-1 gap-8 lg:px-14 lg:grid-cols-2 xl:grid-cols-4">
                    <SummaryCard title="Total Claims Cost" :value="`₦${claimsSummary.total_amount}`" />
                    <SummaryCard title="Queued" :value="claimsSummary.processed_batches_count"/>
                    <SummaryCard title="Pending " :value="claimsSummary.pending_batches_count" />
                    <SummaryCard title="All Claims" :value="claimsSummary.total_claims_count" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
