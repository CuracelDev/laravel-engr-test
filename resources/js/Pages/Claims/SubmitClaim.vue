<script setup>
import { computed, ref } from "vue";
import { useForm, Head } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    insurers: Object,
});

const form = ref({
    provider_name: "",
    insurer_code: "",
    encounter_date: "",
    specialty: "",
    priority_level: "",
    items: [],
});

const addItem = () => {
    form.value.items.push({ name: "", unit_price: 0, quantity: 1 });
};

const removeItem = (index) => {
    if (form.value.items?.length > 1) {
        form.value.items?.splice(index, 1);
    }
};

const claimTotal = computed(() => {
    return form.value.items.reduce(
        (sum, item) => sum + item.unit_price * item.quantity,
        0
    );
});

const clearForm = () => {
    form.value = {
        provider_name: "",
        insurer_code: "",
        encounter_date: "",
        specialty: "",
        priority_level: "",
        items: [],
    };
};

const isLoading = ref(false);
const errors = ref({});
const hasSuccess = ref(false);
const successMessage = ref("");

const submit = async () => {
    isLoading.value = true;
    errors.value = {};

    try {
        const response = await axios.post(
            route("api.claims.store"),
            form.value
        );

        if (response.data.success) {
            hasSuccess.value = true;
            successMessage.value = response.data.message;
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
        clearForm();
    } catch (error) {
        console.error("Submission failed", error);
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Submit Claim" />
    <div class="min-h-screen p-2 sm:p-0 bg-gray-100">
        <div
            v-if="hasSuccess"
            class="max-w-3xl mx-auto py-8 bg-emerald-700 text-white my-1 shadow-md rounded-xl p-6"
        >
            <p class="text-sm font-semibold">{{ successMessage }}</p>
        </div>

        <div
            class="max-w-3xl mx-auto py-8 bg-white mb-6 shadow-md rounded-xl p-6"
        >
            <h1 class="text-3xl font-bold mb-6 text-center">
                Healthcare Claim Submission
            </h1>
            <p class="text-center text-gray-600">
                Please fill out the form below to submit your healthcare claim.
            </p>

            <p class="text-center mt-1 text-gray-600">
                Kindly use some of this insurer codes for testing:
            </p>
            <div class="flex flex-wrap gap-2 justify-center mt-2">
                <span
                    v-for="(insurer, index) in insurers"
                    :key="index"
                    class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-3.5 py-1.5 rounded-full"
                >
                    {{ insurer.name }}: <strong>{{ insurer.code }}</strong>
                </span>
            </div>
        </div>
        <div class="max-w-3xl mx-auto py-8 bg-white shadow-md rounded-xl p-6">
            <h2 class="text-2xl font-semibold mb-4">Submit Healthcare Claim</h2>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Provider and Insurer -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium"
                            >Provider Name</label
                        >
                        <input
                            v-model="form.provider_name"
                            type="text"
                            class="mt-1 w-full border rounded-xl px-3 py-2"
                            required
                        />
                        <p
                            class="text-red-600 text-sm mt-1"
                            v-if="errors.provider_name"
                        >
                            {{ errors.provider_name[0] }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium"
                            >Insurer Code</label
                        >
                        <input
                            v-model="form.insurer_code"
                            type="text"
                            class="mt-1 w-full border rounded-xl px-3 py-2"
                            placeholder="e.g INS-B"
                            required
                        />
                        <p
                            class="text-red-600 text-sm mt-1"
                            v-if="errors.insurer_code"
                        >
                            {{ errors.insurer_code[0] }}
                        </p>
                    </div>
                </div>

                <!-- Encounter Date -->
                <div>
                    <label class="block text-sm font-medium"
                        >Encounter Date</label
                    >
                    <input
                        v-model="form.encounter_date"
                        type="date"
                        class="mt-1 w-full border rounded-xl px-3 py-2"
                        required
                    />
                    <p
                        class="text-red-600 text-sm mt-1"
                        v-if="errors.encounter_date"
                    >
                        {{ errors.encounter_date[0] }}
                    </p>
                </div>

                <!-- Specialty and Priority -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium"
                            >Specialty</label
                        >
                        <input
                            v-model="form.specialty"
                            type="text"
                            class="mt-1 w-full border rounded-xl px-3 py-2"
                            required
                        />
                        <p
                            class="text-red-600 text-sm mt-1"
                            v-if="errors.specialty"
                        >
                            {{ errors.specialty[0] }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium"
                            >Priority Level (1-5)</label
                        >
                        <input
                            v-model.number="form.priority_level"
                            type="number"
                            min="1"
                            max="5"
                            class="mt-1 w-full border rounded-xl px-3 py-2"
                            required
                        />
                        <p
                            class="text-red-600 text-sm mt-1"
                            v-if="errors.priority_level"
                        >
                            {{ errors.priority_level[0] }}
                        </p>
                    </div>
                </div>

                <!-- Items -->
                <div>
                    <div
                        class="space-x-6 flex justify-between items-center mb-2"
                    >
                        <h3 class="text-lg font-semibold mb-2">Claim Items</h3>
                        <h3 class="text-base font-semibold mb-2">
                            ({{ form.items.length }}) Item{{
                                form.items.length > 1 ? "s" : ""
                            }}
                        </h3>
                    </div>

                    <div
                        v-for="(item, index) in form.items"
                        :key="index"
                        class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-2 items-center"
                    >
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium"
                                >Item name</label
                            >
                            <input
                                v-model="item.name"
                                type="text"
                                placeholder="Item name"
                                class="col-span-1 border rounded-xl px-2 py-1"
                                required
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium"
                                >Unit price</label
                            >
                            <input
                                v-model.number="item.unit_price"
                                type="number"
                                placeholder="Unit price"
                                class="col-span-1 border rounded-xl px-2 py-1"
                                required
                            />
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium"
                                >Quantity</label
                            >
                            <input
                                v-model.number="item.quantity"
                                type="number"
                                placeholder="Qty"
                                class="col-span-1 border rounded-xl px-2 py-1"
                                required
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium"
                                >Sub Total</label
                            >
                            <input
                                :value="
                                    (item.unit_price * item.quantity).toFixed(2)
                                "
                                type="text"
                                class="col-span-1 border rounded-xl px-2 py-1 bg-gray-100"
                                readonly
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium"
                                >Remove Item</label
                            >
                            <!-- Remove button -->
                            <button
                                type="button"
                                @click="removeItem(index)"
                                class="col-span-1 bg-red-500 text-white px-2 py-1 rounded-xl hover:bg-red-600"
                                :disabled="form.items.length === 1"
                            >
                                -
                            </button>
                        </div>
                        <!-- <hr class="col-span-5 my-2" /> -->
                    </div>

                    <label class="block text-sm font-medium"
                        >Add New Item</label
                    >
                    <button
                        type="button"
                        @click="addItem"
                        class="mt-2 bg-blue-500 text-white px-3 py-1 rounded-xl hover:bg-blue-600"
                    >
                        +
                    </button>
                </div>

                <!-- Total -->
                <div>
                    <label class="block text-sm font-medium"
                        >Total Amount</label
                    >
                    <input
                        :value="claimTotal.toFixed(2)"
                        type="text"
                        class="mt-1 w-full border rounded-xl px-3 py-2 bg-gray-100"
                        readonly
                    />
                </div>

                <!-- Submit -->
                <div class="pt-4">
                    <button
                        type="submit"
                        class="w-full bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ isLoading ? "Submitting..." : "Submit Claim" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
input:disabled,
input[readonly] {
    background-color: #f9fafb;
}
</style>
