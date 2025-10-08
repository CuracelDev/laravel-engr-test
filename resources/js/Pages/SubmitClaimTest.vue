<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <!-- Main container with more open space -->
        <div class="flex justify-center items-center min-h-screen p-6">
            <div class="w-full max-w-2xl p-8 rounded-3xl">
                <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 mb-8 text-center">Submit a Claim</h2>

                <form @submit.prevent="submitClaim" class="space-y-6">

                    <!-- Insurer Code -->
                    <div>
                        <label for="insurer_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Insurer Code:</label>
                        <input
                            type="text"
                            v-model="insurerCode"
                            id="insurer_code"
                            required
                            class="w-full p-4 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                        />
                    </div>

                    <!-- Provider Name -->
                    <div>
                        <label for="provider_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provider Name:</label>
                        <input
                            type="text"
                            v-model="providerName"
                            id="provider_name"
                            required
                            class="w-full p-4 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                        />
                    </div>

                    <!-- Encounter Date -->
                    <div>
                        <label for="encounter_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Encounter Date:</label>
                        <Flatpickr
                            v-model="encounterDate"
                            id="encounter_date"
                            :config="flatpickrConfig"
                            class="w-full p-4 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                        />
                    </div>

                    <!-- Specialty Dropdown -->
                    <div>
                        <label for="specialty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specialty:</label>
                        <select
                            v-model="specialty"
                            id="specialty"
                            required
                            class="w-full p-4 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                        >
                            <option value="">Select Specialty</option>
                            <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.name">
                                {{ specialty.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Claim Items Section -->
                    <div v-for="(item, index) in claimItems" :key="index" class="space-y-4">
                        <h3 class="font-medium text-lg text-gray-800 dark:text-gray-200">Item {{ index + 1 }}</h3>

                        <!-- Inline Item Input Fields -->
                        <div class="flex space-x-4 items-center mb-4">
                            <!-- Item Name -->
                            <div class="flex-1">
                                <input
                                    type="text"
                                    v-model="item.name"
                                    required
                                    placeholder="Item Name"
                                    class="w-full p-2 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                                />
                            </div>

                            <!-- Unit Price -->
                            <div class="flex-1">
                                <input
                                    type="number"
                                    v-model="item.unitPrice"
                                    required
                                    placeholder="Unit Price"
                                    class="w-full p-2 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                                    @input="updateSubtotal(index)"
                                />
                            </div>

                            <!-- Quantity -->
                            <div class="flex-1">
                                <input
                                    type="number"
                                    v-model="item.quantity"
                                    required
                                    placeholder="Quantity"
                                    class="w-full p-2 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                                    @input="updateSubtotal(index)"
                                />
                            </div>

                            <!-- Subtotal -->
                            <div class="flex-1">
                                <input
                                    type="text"
                                    :value="item.subtotal"
                                    readonly
                                    placeholder="Subtotal"
                                    class="w-full p-2 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                />
                            </div>

                            <!-- Remove Item -->
                            <div class="flex-0">
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="bg-red-500 text-white px-4 py-2 rounded-full">
                                    -
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Item Button -->
                    <div class="text-center">
                        <button
                            type="button"
                            @click="addItem"
                            class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all duration-300 ease-in-out">
                            Add Item
                        </button>
                    </div>

                    <!-- Total Claim Amount -->
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Total Claim Amount:</label>
                        <input
                            type="text"
                            :value="totalAmount"
                            readonly
                            class="w-full p-4 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                        />
                    </div>

                    <!-- Priority Level -->
                    <div>
                        <label for="priority_level" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Priority Level (1-5):</label>
                        <input
                            type="number"
                            v-model="priorityLevel"
                            id="priority_level"
                            min="1"
                            max="5"
                            required
                            class="w-full p-4 border border-gray-300 rounded-xl dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-300"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button
                            type="submit"
                            class="w-full py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-all duration-300 ease-in-out focus:ring-4 focus:ring-green-300">
                            Submit Claim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import Flatpickr from 'vue-flatpickr-component'; // Import the vue wrapper for flatpickr
import 'flatpickr/dist/flatpickr.min.css';  // Import flatpickr styles globally

import GuestLayout from '@/Layouts/GuestLayout.vue';

const flatpickrConfig = {
    enableTime: false, // Disable time selection if only date is needed
    dateFormat: "Y-m-d", // Set the date format as per your needs
};

const insurerCode = ref('');
const providerName = ref('');
const encounterDate = ref('');
const specialty = ref('');
const priorityLevel = ref(1);
const claimItems = ref([{ name: '', quantity: 1, unitPrice: 0 }]);
const totalAmount = ref(0);
const specialties = ref([]);
const errors = ref({});

// Fetch the list of specialties from the API
onMounted(async () => {
    try {
        const response = await axios.get('/api/v1/specialties');
        specialties.value = response.data;
    } catch (error) {
        console.error('Error fetching specialties:', error);
    }
});

// Add item to the claim
const addItem = () => {
    claimItems.value.push({ name: '', quantity: 1, unitPrice: 0 });
};

// Remove item from the claim
const removeItem = (index) => {
    claimItems.value.splice(index, 1);
};

// Update subtotal for the item
const updateSubtotal = (index) => {
    const item = claimItems.value[index];
    item.subtotal = item.unitPrice * item.quantity;
    updateTotalAmount(); // Recalculate total when item subtotal changes
};

// Update total amount based on subtotals
const updateTotalAmount = () => {
    totalAmount.value = claimItems.value.reduce((total, item) => total + item.subtotal, 0);
};

// Submit the claim
const submitClaim = async () => {
    errors.value = {}; // Clear previous errors

    // Prepare the claim data
    const claimData = {
        insurer_code: insurerCode.value,
        provider_name: providerName.value,
        encounter_date: encounterDate.value,
        specialty: specialty.value,
        priority_level: priorityLevel.value,
        items: claimItems.value.map(item => ({
            name: item.name,
            quantity: item.quantity,
            unitPrice: item.unitPrice,
        })),
    };

    try {
        // Make the API call
        const response = await axios.post('/api/v1/submit-claim', claimData);

        // Handle success
        console.log('Claim submitted successfully:', response.data);
        alert('Claim submitted successfully!');

        // Reset the form
        insurerCode.value = '';
        providerName.value = '';
        encounterDate.value = '';
        specialty.value = '';
        priorityLevel.value = 1;
        claimItems.value = [{ name: '', quantity: 1, unitPrice: 0 }];
    } catch (error) {
        // Handle errors
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        } else {
            console.error('There was an error submitting the claim:', error);
            alert('There was an error submitting the claim.');
        }
    }
};
</script>
