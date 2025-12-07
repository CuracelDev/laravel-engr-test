<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const form = ref({
    provider_name: '',
    insurer_code: '',
    encounter_date: '',
    specialty: '',
    priority: 1,
    items: [
        { name: '', unit_price: 0, quantity: 1 }
    ]
});

const status = ref('');
const error = ref('');

const addItem = () => {
    form.value.items.push({ name: '', unit_price: 0, quantity: 1 });
};

const removeItem = (index) => {
    form.value.items.splice(index, 1);
};

const totalAmount = computed(() => {
    return form.value.items.reduce((sum, item) => {
        return sum + (item.unit_price * item.quantity);
    }, 0);
});

const submit = async () => {
    status.value = 'Submitting...';
    error.value = '';
    
    try {
        await axios.post('/api/claims', form.value);
        status.value = 'Claim submitted successfully!';
        // Reset form
        form.value = {
            provider_name: '',
            insurer_code: '',
            encounter_date: '',
            specialty: '',
            priority: 1,
            items: [{ name: '', unit_price: 0, quantity: 1 }]
        };
    } catch (e) {
        status.value = '';
        error.value = e.response?.data?.message || 'An error occurred';
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold mb-6">Submit a Claim</h1>

        <div v-if="status" class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ status }}</div>
        <div v-if="error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ error }}</div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Provider Name</label>
                    <input v-model="form.provider_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Insurer Code</label>
                    <input v-model="form.insurer_code" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. BC001" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Encounter Date</label>
                    <input v-model="form.encounter_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Specialty</label>
                    <input v-model="form.specialty" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cardiology" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority (1-5)</label>
                    <input v-model="form.priority" type="number" min="1" max="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
            </div>

            <div class="border-t pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Claim Items</h2>
                    <button type="button" @click="addItem" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">+ Add Item</button>
                </div>

                <div v-for="(item, index) in form.items" :key="index" class="flex gap-4 mb-4 items-end bg-gray-50 p-3 rounded">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500">Item Name</label>
                        <input v-model="item.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-medium text-gray-500">Unit Price</label>
                        <input v-model="item.unit_price" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium text-gray-500">Quantity</label>
                        <input v-model="item.quantity" type="number" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="w-32 text-right pb-2">
                        <span class="text-sm font-bold">${{ (item.unit_price * item.quantity).toFixed(2) }}</span>
                    </div>
                    <button type="button" @click="removeItem(index)" class="pb-2 text-red-600 hover:text-red-800" v-if="form.items.length > 1">
                        &times;
                    </button>
                </div>
            </div>

            <div class="border-t pt-4 flex justify-between items-center">
                <div class="text-xl font-bold">Total: ${{ totalAmount.toFixed(2) }}</div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-700 shadow">
                    Submit Claim
                </button>
            </div>
        </form>
    </div>
</template>
