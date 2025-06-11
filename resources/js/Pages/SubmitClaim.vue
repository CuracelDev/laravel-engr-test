<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold mb-4">Submit New Claim</h1>
            <form @submit.prevent="submitClaim" class="space-y-6">
                <div>
                    <label class="block font-semibold mb-1">Insurer Code</label>
                    <input
                        v-model="form.insurer_code"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        :class="{ 'border-red-500': errors.insurer_code }"
                    />
                    <p
                        v-if="errors.insurer_code"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.insurer_code[0] }}
                    </p>
                </div>

                <div>
                    <label class="block font-semibold mb-1"
                        >Provider Name</label
                    >
                    <input
                        v-model="form.provider_name"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        :class="{ 'border-red-500': errors.provider_name }"
                    />
                    <p
                        v-if="errors.provider_name"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.provider_name[0] }}
                    </p>
                </div>

                <div>
                    <label class="block font-semibold mb-1"
                        >Encounter Date</label
                    >
                    <input
                        v-model="form.encounter_date"
                        type="date"
                        class="w-full border rounded px-3 py-2"
                        :class="{ 'border-red-500': errors.encounter_date }"
                    />
                    <p
                        v-if="errors.encounter_date"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.encounter_date[0] }}
                    </p>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Specialty</label>
                    <input
                        v-model="form.specialty"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        :class="{ 'border-red-500': errors.specialty }"
                    />
                    <p
                        v-if="errors.specialty"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.specialty[0] }}
                    </p>
                </div>

                <div>
                    <label class="block font-semibold mb-1"
                        >Priority Level</label
                    >
                    <input
                        v-model="form.priority_level"
                        type="number"
                        min="1"
                        max="5"
                        class="w-full border rounded px-3 py-2"
                        :class="{ 'border-red-500': errors.priority_level }"
                    />
                    <p
                        v-if="errors.priority_level"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ errors.priority_level[0] }}
                    </p>
                </div>

                <div>
                    <label class="block font-semibold mb-2">Claim Items</label>
                    <div>
                        <label class="block font-semibold mb-2"
                            >Claim Items</label
                        >
                        <div
                            class="grid grid-cols-4 gap-4 font-semibold text-sm mb-2"
                        >
                            <div>Item</div>
                            <div>Quantity</div>
                            <div>Unit Price</div>
                            <div>Subtotal</div>
                        </div>
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="grid grid-cols-4 gap-4 mb-2 items-center"
                        >
                            <input
                                v-model="item.name"
                                placeholder="Item name"
                                class="border px-2 py-1 rounded"
                            />
                            <input
                                v-model.number="item.quantity"
                                min="1"
                                type="number"
                                placeholder="Quantity"
                                class="border px-2 py-1 rounded"
                            />
                            <input
                                v-model.number="item.unit_price"
                                min="0"
                                type="number"
                                placeholder="Unit price"
                                class="border px-2 py-1 rounded"
                            />
                            <div class="text-sm">
                                {{
                                    (item.quantity * item.unit_price).toFixed(2)
                                }}
                            </div>
                            <button
                                v-if="form.items.length > 1"
                                type="button"
                                @click="removeItem(index)"
                                class="text-red-600 hover:underline text-xs col-span-4 text-left"
                            >
                                Remove
                            </button>
                        </div>
                        <button
                            type="button"
                            @click="addItem"
                            class="text-blue-600 hover:underline text-sm"
                        >
                            + Add another item
                        </button>
                        <p
                            v-if="errors.items"
                            class="text-red-500 text-sm mt-1"
                        >
                            {{ errors.items[0] }}
                        </p>
                    </div>
                    <div class="mt-4 text-right font-semibold text-lg">
                        Total: {{ totalAmount }}
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                >
                    <span v-if="loading"> Submitting </span>
                    <span v-else> Submit Claim </span>
                </button>
            </form>
            <div v-if="Object.keys(errors).length" class="text-red-500">
                <ul>
                    <li v-for="(fieldErrors, field) in errors" :key="field">
                        <div v-for="msg in fieldErrors" :key="msg">
                             {{ msg }}
                        </div>
                    </li>
                </ul>
            </div>
            <div
                v-if="successMessage"
                class="mt-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded"
            >
                {{ successMessage }}
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

import { ref, computed } from "vue";
import axios from "axios";

const loading = ref(false);

const form = ref({
    insurer_code: "",
    provider_name: "",
    encounter_date: "",
    specialty: "",
    priority_level: 1,
    items: [{ name: "", quantity: 1, unit_price: 0 }],
});

const errors = ref({});
const successMessage = ref("");
const responseErrors =ref([])

const validateForm = () => {
    const newErrors = {};
    if (!form.value.insurer_code)
        newErrors.insurer_code = ["Insurer code is required."];
    if (!form.value.provider_name)
        newErrors.provider_name = ["Provider name is required."];
    if (!form.value.encounter_date)
        newErrors.encounter_date = ["Encounter date is required."];
    if (!form.value.specialty) newErrors.specialty = ["Specialty is required."];
    if (!form.value.priority_level)
        newErrors.priority_level = ["Priority level is required."];
    if (
        !form.value.items.length ||
        form.value.items.some(
            (item) => !item.name || !item.quantity || !item.unit_price
        )
    ) {
        newErrors.items = ["All item fields must be filled."];
    }
    errors.value = newErrors;
    return Object.keys(newErrors).length === 0;
};

const totalAmount = computed(() => {
    return form.value.items
        .reduce((sum, item) => {
            return sum + item.quantity * item.unit_price;
        }, 0)
        .toFixed(2);
});

const addItem = () => {
    form.value.items.push({ name: "", quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    if (form.value.items.length > 1) {
        form.value.items.splice(index, 1);
    }
};

const submitClaim = async () => {
    if (!validateForm()) return;
    try {
        loading.value = true;
        const response = await axios.post("/api/claims", form.value);
        successMessage.value = response.data.message;
        errors.value = {};
        form.value = {
            insurer_code: "",
            provider_name: "",
            encounter_date: "",
            specialty: "",
            priority_level: 1,
            items: [{ name: "", quantity: 1, unit_price: 0 }],
        };
    } catch (error) {
        console.log(error, 'error');

        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};
            responseErrors.value = error.response.data.errors || {};
        } else {
            responseErrors.value = { general: ['An unexpected error occurred.'] };
        }
    } finally {
        loading.value = false;
    }
};

</script>

<style scoped>
input,
select {
    transition: border-color 0.2s ease-in-out;
}
</style>
