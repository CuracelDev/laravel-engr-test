<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="card-header">Submit A Claim</div>

        <div class="card-body">

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="claim-submission p-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Submit New Claim</h3>

                            <form @submit.prevent="submitClaim" class="space-y-6">
                                <!-- Form Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Insurer Code -->
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Insurer Code</label>
                                        <input
                                            v-model="form.insurer_code"
                                            type="text"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                            placeholder="Enter insurer code"
                                        >
                                    </div>

                                    <!-- Provider Name -->
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Provider Name</label>
                                        <input
                                            v-model="form.provider_name"
                                            type="text"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                            placeholder="Enter provider name"
                                        >
                                    </div>

                                    <!-- Encounter Date -->
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Encounter Date</label>
                                        <input
                                            v-model="form.encounter_date"
                                            type="date"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        >
                                    </div>

                                    <!-- Specialty -->
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Specialty</label>
                                        <select
                                            v-model="form.specialty"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        >
                                            <option value="" disabled selected>Select specialty</option>
                                            <option value="cardiology">Cardiology</option>
                                            <option value="orthopedics">Orthopedics</option>
                                            <option value="neurology">Neurology</option>
                                        </select>
                                    </div>

                                    <!-- Priority Level -->
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority Level</label>
                                        <select
                                            v-model="form.priority_level"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        >
                                            <option value="" disabled selected>Select priority</option>
                                            <option v-for="n in 5" :value="n">{{ n }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Items Section -->
                                <div class="items-section border-t pt-6 mt-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Claim Items</h3>

                                    <div v-for="(item, index) in form.items" :key="index" class="item-row bg-gray-50 p-4 rounded-lg mb-3 border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
                                                <input
                                                    v-model="item.name"
                                                    placeholder="Enter item name"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                >
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price ($)</label>
                                                <input
                                                    v-model="item.unit_price"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                >
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                                <input
                                                    v-model="item.quantity"
                                                    type="number"
                                                    min="1"
                                                    placeholder="1"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                                >
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-medium text-gray-700 whitespace-nowrap">
                                                    Subtotal: ${{ (item.unit_price * item.quantity).toFixed(2) }}
                                                </span>
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        @click="addItem"
                                        class="mt-4 px-4 py-2 bg-green-500 text-white font-medium rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200"
                                    >
                                        + Add Item
                                    </button>
                                </div>

                                <!-- Total Amount -->
                                <div class="form-group border-t pt-6">
                                    <label class="block text-lg font-semibold text-gray-700 mb-2">Total Claim Amount</label>
                                    <input
                                        :value="totalAmount"
                                        type="text"
                                        readonly
                                        class="w-full max-w-xs px-3 py-2 border border-gray-300 bg-gray-100 rounded-md text-lg font-bold text-gray-800"
                                    >
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end pt-6 border-t">
                                    <button
                                        type="submit"
                                        :disabled="submitting"
                                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                                    >
                                        {{ submitting ? 'Submitting...' : 'Submit Claim' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";

console.log('submit claim page loaded')
import { ref, computed } from 'vue';

// Using Composition API (recommended for <script setup>)
const form = useForm({
    insurer_code: '',
    provider_name: '',
    encounter_date: '',
    specialty: '',
    priority_level: 3,
    items: [{ name: '', unit_price: 0, quantity: 1 }]
});

const submitting = ref(false);

const totalAmount = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (parseFloat(item.unit_price) * parseInt(item.quantity));
    }, 0).toFixed(2);
});

const addItem = () => {
    form.items.push({ name: '', unit_price: 0, quantity: 1 });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submitClaim = () => {
    submitting.value = true;

    form.post('/submit-claim', {
        preserveScroll: true,
        onSuccess: () => {
            alert('Claim submitted successfully!');
            resetForm();
        },
        onError: (errors) => {
            alert('Error submitting claim. Please check the form.');
            console.error('Submission errors:', errors);
        },
        onFinish: () => {
            submitting.value = false;
        }
    });
};

const resetForm = () => {
    form.reset();
    form.items = [{ name: '', unit_price: 0, quantity: 1 }];
};
</script>
