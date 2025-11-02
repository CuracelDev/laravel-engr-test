<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="py-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Submit A Claim</h2>

                        <form @submit.prevent="submitClaim" class="space-y-6">
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800">Provider Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel for="insurer_code" value="Insurer Code *" />
                                        <select
                                            id="insurer_code"
                                            v-model="form.insurer_code"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            :class="{ 'border-red-500': errors.insurer_code }"
                                            required
                                        >
                                            <option value="">Select Insurer</option>
                                            <option v-for="insurer in insurers" :key="insurer.id" :value="insurer.code">
                                                {{ insurer.code }} - {{ insurer.name }}
                                            </option>
                                        </select>
                                        <InputError :message="errors.insurer_code" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="provider_name" value="Provider Name *" />
                                        <TextInput
                                            id="provider_name"
                                            v-model="form.provider_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': errors.provider_name }"
                                            required
                                        />
                                        <InputError :message="errors.provider_name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="provider_email" value="Provider Email *" />
                                        <TextInput
                                            id="provider_email"
                                            v-model="form.provider_email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': errors.provider_email }"
                                            required
                                        />
                                        <InputError :message="errors.provider_email" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800">Claim Details</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel for="encounter_date" value="Encounter Date *" />
                                        <TextInput
                                            id="encounter_date"
                                            v-model="form.encounter_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': errors.encounter_date }"
                                            required
                                        />
                                        <InputError :message="errors.encounter_date" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="specialty" value="Specialty *" />
                                        <TextInput
                                            id="specialty"
                                            v-model="form.specialty"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :class="{ 'border-red-500': errors.specialty }"
                                            required
                                        />
                                        <InputError :message="errors.specialty" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="priority_level" value="Priority Level *" />
                                        <select
                                            id="priority_level"
                                            v-model="form.priority_level"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            :class="{ 'border-red-500': errors.priority_level }"
                                            required
                                        >
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                        <InputError :message="errors.priority_level" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-800">Claim Items</h3>
                                    <button
                                        type="button"
                                        @click="addItem"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm"
                                    >
                                        + Add Item
                                    </button>
                                </div>

                                <div v-if="form.items.length === 0" class="text-gray-500 text-center py-4">
                                    No items added. Click "Add Item" to add claim items.
                                </div>

                                <div v-else class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (₦)</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal (₦)</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(item, index) in form.items" :key="index">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <TextInput
                                                        v-model="item.name"
                                                        type="text"
                                                        class="w-full"
                                                        :class="{ 'border-red-500': errors[`items.${index}.name`] }"
                                                        required
                                                    />
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <TextInput
                                                        v-model.number="item.quantity"
                                                        type="number"
                                                        min="1"
                                                        class="w-full"
                                                        :class="{ 'border-red-500': errors[`items.${index}.quantity`] }"
                                                        required
                                                    />
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <TextInput
                                                        v-model.number="item.price"
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        class="w-full"
                                                        :class="{ 'border-red-500': errors[`items.${index}.price`] }"
                                                        required
                                                    />
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap font-semibold">
                                                    ₦{{ calculateSubtotal(item).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <button
                                                        type="button"
                                                        @click="removeItem(index)"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                    >
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900">Total:</td>
                                                <td class="px-4 py-3 font-bold text-lg text-indigo-600">₦{{ totalAmount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                {{ successMessage }}
                            </div>

                            <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                {{ errorMessage }}
                            </div>

                            <div class="flex justify-end">
                                <PrimaryButton :disabled="processing || form.items.length === 0">
                                    {{ processing ? 'Submitting...' : 'Submit Claim' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = ref({
    insurer_code: '',
    provider_name: '',
    provider_email: '',
    encounter_date: '',
    specialty: '',
    priority_level: 'medium',
    items: [
        { name: '', quantity: 1, price: 0 }
    ],
});

const insurers = ref([]);
const errors = ref({});
const processing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const totalAmount = computed(() => {
    return form.value.items.reduce((total, item) => {
        return total + calculateSubtotal(item);
    }, 0);
});

function calculateSubtotal(item) {
    const quantity = Number(item.quantity) || 0;
    const price = Number(item.price) || 0;
    return quantity * price;
}

function addItem() {
    form.value.items.push({ name: '', quantity: 1, price: 0 });
}

function removeItem(index) {
    if (form.value.items.length > 1) {
        form.value.items.splice(index, 1);
    }
}

async function loadInsurers() {
    try {
        const response = await axios.get('/api/claims/insurers');
        if (response.data.success) {
            insurers.value = response.data.data;
        }
    } catch (error) {
        console.error('Failed to load insurers:', error);
    }
}

async function submitClaim() {
    processing.value = true;
    errors.value = {};
    successMessage.value = '';
    errorMessage.value = '';

    try {
        const response = await axios.post('/api/claims', {
            ...form.value,
            items: form.value.items.filter(item => item.name && item.quantity > 0 && item.price > 0),
        });

        if (response.data.success) {
            successMessage.value = `Claim submitted successfully! Reference: ${response.data.data.claim.reference_code}. Batch: ${response.data.data.batch.code}`;
            
            form.value = {
                insurer_code: '',
                provider_name: '',
                provider_email: '',
                encounter_date: '',
                specialty: '',
                priority_level: 'medium',
                items: [{ name: '', quantity: 1, price: 0 }],
            };
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            errorMessage.value = error.response?.data?.message || 'Failed to submit claim. Please try again.';
        }
    } finally {
        processing.value = false;
    }
}

onMounted(() => {
    loadInsurers();
});
</script>