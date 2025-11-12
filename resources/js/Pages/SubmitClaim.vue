<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4">
            <div class="w-full max-w-5xl">
                <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                        <h1 class="text-3xl font-bold text-white">Submit Healthcare Claim</h1>
                        <p class="text-blue-100 mt-1">Complete the form below to submit your claim</p>
                    </div>

                    <div class="p-8">

                        <form @submit.prevent="submitClaim" class="space-y-8">
                            <!-- Basic Information Section -->
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Basic Information
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Insurer Code *</label>
                                        <select v-model="form.insurer_code" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                            <option value="">Select Insurer</option>
                                            <option v-for="insurer in insurers" :key="insurer.code" :value="insurer.code">
                                                {{ insurer.code }} - {{ insurer.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Provider Name *</label>
                                        <input v-model="form.provider_name" type="text" placeholder="Enter provider name" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Encounter Date *</label>
                                        <input v-model="form.encounter_date" type="date" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Specialty *</label>
                                        <select v-model="form.specialty" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                            <option value="">Select Specialty</option>
                                            <option value="cardiology">🫀 Cardiology</option>
                                            <option value="orthopedics">🦴 Orthopedics</option>
                                            <option value="neurology">🧠 Neurology</option>
                                            <option value="general">🏥 General</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Priority Level *</label>
                                        <select v-model="form.priority_level" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                            <option value="">Select Priority</option>
                                            <option v-for="n in 5" :key="n" :value="n">Priority {{ n }} {{ n === 5 ? '(Highest)' : n === 1 ? '(Lowest)' : '' }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Claim Items Section -->
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Claim Items
                                </h2>
                                
                                <div v-for="(item, index) in form.items" :key="index" class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5 mb-4 hover:border-blue-300 transition">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-sm font-semibold text-gray-600">Item #{{ index + 1 }}</span>
                                        <button v-if="form.items.length > 1" @click="removeItem(index)" type="button" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Item Name</label>
                                            <input v-model="item.name" type="text" placeholder="e.g., Consultation" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Unit Price</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                                <input v-model.number="item.unit_price" type="number" step="0.01" min="0" placeholder="0.00" class="w-full border-2 border-gray-300 rounded-lg pl-7 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required @input="calculateSubtotal(index)">
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Quantity</label>
                                            <input v-model.number="item.quantity" type="number" min="1" placeholder="1" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required @input="calculateSubtotal(index)">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Subtotal</label>
                                            <input :value="formatCurrency(item.subtotal || 0)" type="text" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 font-semibold" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <button @click="addItem" type="button" class="flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Another Item
                                </button>
                            </div>

                            <!-- Total Amount -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 p-6 rounded-xl">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-semibold text-gray-800">Total Claim Amount:</span>
                                    <span class="text-3xl font-bold text-blue-600">{{ formatCurrency(totalAmount) }}</span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-4">
                                <button type="submit" :disabled="submitting" class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-400 disabled:to-gray-500 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                                    <svg v-if="!submitting" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <svg v-else class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ submitting ? 'Submitting...' : 'Submit Claim' }}
                                </button>
                            </div>
                        </form>

                        <!-- Success/Error Messages -->
                        <div v-if="message" class="mt-6 p-4 rounded-xl shadow-md" :class="messageType === 'success' ? 'bg-green-50 border-2 border-green-200 text-green-800' : 'bg-red-50 border-2 border-red-200 text-red-800'">
                            <div class="flex items-start gap-3">
                                <svg v-if="messageType === 'success'" class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <svg v-else class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="font-medium">{{ message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue'
import { Head } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import axios from 'axios'

const props = defineProps({
    insurers: Array
})

const form = ref({
    insurer_code: '',
    provider_name: '',
    encounter_date: '',
    specialty: '',
    priority_level: '',
    items: [
        { name: '', unit_price: 0, quantity: 1, subtotal: 0 }
    ]
})

const submitting = ref(false)
const message = ref('')
const messageType = ref('success')

const totalAmount = computed(() => {
    return form.value.items.reduce((sum, item) => sum + (item.subtotal || 0), 0)
})

const addItem = () => {
    form.value.items.push({ name: '', unit_price: 0, quantity: 1, subtotal: 0 })
}

const removeItem = (index) => {
    form.value.items.splice(index, 1)
}

const calculateSubtotal = (index) => {
    const item = form.value.items[index]
    item.subtotal = (item.unit_price || 0) * (item.quantity || 0)
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount)
}

const submitClaim = async () => {
    submitting.value = true
    message.value = ''
    
    try {
        const response = await axios.post('/api/claims', form.value)
        messageType.value = 'success'
        message.value = `✅ Claim submitted successfully! Batch ID: ${response.data.batch_id}`
        
        // Reset form
        form.value = {
            insurer_code: '',
            provider_name: '',
            encounter_date: '',
            specialty: '',
            priority_level: '',
            items: [{ name: '', unit_price: 0, quantity: 1, subtotal: 0 }]
        }
        
        // Scroll to top to show success message
        window.scrollTo({ top: 0, behavior: 'smooth' })
    } catch (error) {
        messageType.value = 'error'
        if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat()
            message.value = '❌ ' + errors.join(', ')
        } else {
            message.value = '❌ An error occurred while submitting the claim'
        }
        window.scrollTo({ top: 0, behavior: 'smooth' })
    } finally {
        submitting.value = false
    }
}
</script>
