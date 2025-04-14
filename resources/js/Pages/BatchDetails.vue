<template>
    <Head title="Batch Claims" />

    <AuthenticatedLayout>
        <template #header>
            <h4 class="font-semibold text-xl text-gray-800 leading-tight">
                Claims for Batch: {{ batch.name }}
            </h4>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <InputLabel for="limit" class="mr-2"
                                >Limit:</InputLabel
                            >
                            <TextInput
                                id="limit"
                                type="number"
                                v-model="limit"
                                class="border px-2 py-1"
                                @change="updateLimit"
                            />
                        </div>

                        <table class="w-full border-collapse my-4">
                            <thead>
                                <tr>
                                    <th
                                        class="border border-gray-300 px-2 py-1 text-center"
                                    >
                                        Claim Name
                                    </th>
                                    <th
                                        class="border border-gray-300 px-2 py-1 text-center"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="border border-gray-300 px-2 py-1 text-center"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="claim in claims" :key="claim.id">
                                    <td
                                        class="border border-gray-300 px-2 py-1 text-center"
                                    >
                                        {{ claim.name }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-2 py-1 text-center"
                                    >
                                        {{ claim.date }}
                                    </td>
                                    <td
                                        class="border border-gray-300 px-2 py-1 text-center"
                                    >
                                        {{ claim.status }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <PrimaryButton @click="markAllProcessed" class="ms-4">
                            Mark All as Processed
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

const props = defineProps({
    batch: Object,
    claims: Object,
    defaultLimit: String,
});

const limit = ref(props.defaultLimit);

function updateLimit() {
    router.visit(route("batch.show", props.batch.id) + "?limit=" + limit.value);
}

function markAllProcessed() {
    const data = {
        claims: props.claims.map((claim) => claim.id),
    };

    router.patch(route("batch.update", props.batch.id), data)
        .catch((error) => {
            // Handle error if needed
            alert("Error marking claims as processed:", error);
        });
}
</script>
