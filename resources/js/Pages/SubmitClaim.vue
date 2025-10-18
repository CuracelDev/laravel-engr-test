<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Claim Submission Form -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Submit A Claim</h2>

                <form @submit.prevent="submitClaim" class="space-y-6">
                    <!-- Insurer Code -->
                    <div>
                        <label for="insurer_code" class="block text-sm font-medium text-gray-700">
                            Insurer Code *
                        </label>
                        <input
                            type="text"
                            id="insurer_code"
                            v-model="form.insurer_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        />
                        <p class="mt-1 text-sm text-gray-600">Available codes: INS-A, INS-B, INS-C, INS-D</p>
                        <p v-if="errors.insurer_code" class="mt-1 text-sm text-red-600">{{ errors.insurer_code }}</p>
                    </div>

                    <!-- Provider Name -->
                    <div>
                        <label for="provider_name" class="block text-sm font-medium text-gray-700">
                            Provider Name *
                        </label>
                        <input
                            type="text"
                            id="provider_name"
                            v-model="form.provider_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        />
                        <p v-if="errors.provider_name" class="mt-1 text-sm text-red-600">{{ errors.provider_name }}</p>
                    </div>

                    <!-- Encounter Date -->
                    <div>
                        <label for="encounter_date" class="block text-sm font-medium text-gray-700">
                            Encounter Date *
                        </label>
                        <input
                            type="date"
                            id="encounter_date"
                            v-model="form.encounter_date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        />
                        <p v-if="errors.encounter_date" class="mt-1 text-sm text-red-600">{{ errors.encounter_date }}</p>
                    </div>

                    <!-- Specialty -->
                    <div>
                        <label for="specialty" class="block text-sm font-medium text-gray-700">
                            Specialty *
                        </label>
                        <select
                            id="specialty"
                            v-model="form.specialty"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        >
                            <option value="">Select a specialty</option>
                            <option v-for="(label, value) in specialties" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <p v-if="errors.specialty" class="mt-1 text-sm text-red-600">{{ errors.specialty }}</p>
                    </div>

                    <!-- Priority Level -->
                    <div>
                        <label for="priority_level" class="block text-sm font-medium text-gray-700">
                            Priority Level *
                        </label>
                        <select
                            id="priority_level"
                            v-model.number="form.priority_level"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        >
                            <option value="">Select priority level</option>
                            <option v-for="(label, value) in priorityLevels" :key="value" :value="parseInt(value)">
                                {{ label }}
                            </option>
                        </select>
                        <p v-if="errors.priority_level" class="mt-1 text-sm text-red-600">{{ errors.priority_level }}</p>
                    </div>

                    <!-- Claim Items -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Claim Items *
                        </label>

                        <div v-for="(item, index) in form.items" :key="index" class="mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Item Name -->
                                <div class="md:col-span-2">
                                    <label :for="'item_name_' + index" class="block text-xs font-medium text-gray-700">
                                        Item Name
                                    </label>
                                    <input
                                        type="text"
                                        :id="'item_name_' + index"
                                        v-model="item.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required
                                    />
                                </div>

                                <!-- Unit Price -->
                                <div>
                                    <label :for="'unit_price_' + index" class="block text-xs font-medium text-gray-700">
                                        Unit Price
                                    </label>
                                    <input
                                        type="number"
                                        :id="'unit_price_' + index"
                                        v-model.number="item.unit_price"
                                        step="0.01"
                                        min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required
                                    />
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label :for="'quantity_' + index" class="block text-xs font-medium text-gray-700">
                                        Quantity
                                    </label>
                                    <input
                                        type="number"
                                        :id="'quantity_' + index"
                                        v-model.number="item.quantity"
                                        min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Subtotal -->
                            <div class="mt-2 flex justify-between items-center">
                                <p class="text-sm font-medium text-gray-700">
                                    Subtotal: ${{ calculateSubtotal(item).toFixed(2) }}
                                </p>
                                <button
                                    v-if="form.items.length > 1"
                                    type="button"
                                    @click="removeItem(index)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
                                    Remove Item
                                </button>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="addItem"
                            class="mt-2 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            + Add Item
                        </button>
                    </div>

                    <!-- Total Amount -->
                    <div class="bg-indigo-50 p-4 rounded-md">
                        <label class="block text-lg font-bold text-gray-900">
                            Total Claim Amount: ${{ totalAmount.toFixed(2) }}
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ submitting ? 'Submitting...' : 'Submit Claim' }}
                        </button>
                    </div>

                    <!-- Success/Error Messages -->
                    <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
                        <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                    </div>
                    <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
                        <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                    </div>
                </form>
            </div>

            <!-- Batches Display -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Batches</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Auto-refresh in {{ autoRefreshCountdown }}s</span>
                        <button
                            @click="refreshBatches"
                            :disabled="loadingBatches"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            {{ loadingBatches ? 'Refreshing...' : 'Refresh' }}
                        </button>
                    </div>
                </div>

                <div v-if="loadingBatches && batches.length === 0" class="text-center py-8">
                    <p class="text-gray-500">Loading batches...</p>
                </div>

                <div v-else-if="batches.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No batches found.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Batch ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Insurer
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Batch Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Claims
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template v-for="batch in batches" :key="batch.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ batch.identifier }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ batch.insurer.name }} ({{ batch.insurer.code }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ batch.batch_date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ batch.total_claims }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${{ batch.total_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusClass(batch.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ batch.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                            @click="toggleBatchExpand(batch.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            {{ expandedBatches.includes(batch.id) ? 'Hide' : 'View' }} Claims
                                        </button>
                                    </td>
                                </tr>
                                <!-- Expanded Claims View -->
                                <tr v-if="expandedBatches.includes(batch.id)">
                                    <td colspan="7" class="px-6 py-4 bg-gray-50">
                                        <div class="text-sm">
                                            <h4 class="font-semibold text-gray-900 mb-2">Claims in this batch:</h4>
                                            <div v-if="batch.claims.length === 0" class="text-gray-500">
                                                No claims in this batch yet.
                                            </div>
                                            <table v-else class="min-w-full divide-y divide-gray-200 mt-2">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Claim ID</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Provider</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Amount</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Priority</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Specialty</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr v-for="claim in batch.claims" :key="claim.id">
                                                        <td class="px-4 py-2 text-sm text-gray-900">{{ claim.id }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-500">{{ claim.provider_name }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-500">${{ claim.total_amount }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-500">{{ claim.priority_level }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-500">{{ claim.specialty }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-500">{{ claim.status }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    insurers: Array,
    specialties: Object,
    priorityLevels: Object,
});

// Form state
const form = ref({
    insurer_code: '',
    provider_name: '',
    encounter_date: '',
    priority_level: '',
    specialty: '',
    items: [
        { name: '', unit_price: 0, quantity: 1 }
    ]
});

const errors = ref({});
const submitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Batches state
const batches = ref([]);
const loadingBatches = ref(false);
const expandedBatches = ref([]);
const autoRefreshCountdown = ref(60);
let autoRefreshInterval = null;
let countdownInterval = null;

// Computed
const totalAmount = computed(() => {
    return form.value.items.reduce((total, item) => {
        return total + calculateSubtotal(item);
    }, 0);
});

// Methods
const calculateSubtotal = (item) => {
    return (item.unit_price || 0) * (item.quantity || 0);
};

const addItem = () => {
    form.value.items.push({ name: '', unit_price: 0, quantity: 1 });
};

const removeItem = (index) => {
    form.value.items.splice(index, 1);
};

const submitClaim = async () => {
    errors.value = {};
    successMessage.value = '';
    errorMessage.value = '';
    submitting.value = true;

    try {
        const response = await axios.post('/api/claims', form.value);
        
        if (response.data.success) {
            successMessage.value = response.data.message;
            
            // Reset form
            form.value = {
                insurer_code: '',
                provider_name: '',
                encounter_date: '',
                priority_level: '',
                specialty: '',
                items: [{ name: '', unit_price: 0, quantity: 1 }]
            };

            // Refresh batches after submission
            setTimeout(() => {
                refreshBatches();
            }, 1000);
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            errorMessage.value = error.response?.data?.message || 'An error occurred while submitting the claim.';
        }
    } finally {
        submitting.value = false;
    }
};

const fetchBatches = async () => {
    loadingBatches.value = true;
    try {
        const response = await axios.get('/api/batches');
        if (response.data.success) {
            batches.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching batches:', error);
    } finally {
        loadingBatches.value = false;
    }
};

const refreshBatches = () => {
    fetchBatches();
    resetAutoRefresh();
};

const toggleBatchExpand = (batchId) => {
    const index = expandedBatches.value.indexOf(batchId);
    if (index > -1) {
        expandedBatches.value.splice(index, 1);
    } else {
        expandedBatches.value.push(batchId);
    }
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'ready': 'bg-blue-100 text-blue-800',
        'notified': 'bg-purple-100 text-purple-800',
        'processed': 'bg-green-100 text-green-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const startAutoRefresh = () => {
    autoRefreshInterval = setInterval(() => {
        fetchBatches();
    }, 60000); // 60 seconds

    countdownInterval = setInterval(() => {
        autoRefreshCountdown.value--;
        if (autoRefreshCountdown.value <= 0) {
            autoRefreshCountdown.value = 60;
        }
    }, 1000);
};

const resetAutoRefresh = () => {
    autoRefreshCountdown.value = 60;
};

const stopAutoRefresh = () => {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
};

// Lifecycle
onMounted(() => {
    fetchBatches();
    startAutoRefresh();
});

onUnmounted(() => {
    stopAutoRefresh();
});
</script>
