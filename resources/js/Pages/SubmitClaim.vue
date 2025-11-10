<template>
    <ClaimLayout>
        <Head title="Submit Claim" />

        <!-- Success/Error Messages -->
        <div v-if="success" class="mb-6 p-6 bg-green-50 border border-green-200 rounded-xl">
            <h3 class="text-lg font-semibold text-green-800">Claim Submitted Successfully</h3>
        </div>

        <div v-if="error" class="mb-6 p-6 bg-red-50 border border-red-200 rounded-xl">
            <div>
                <h3 class="text-lg font-semibold text-red-800 mb-1">Submission Failed</h3>
                <p class="text-red-700">{{ error }}</p>
            </div>
        </div>

        <form @submit.prevent="submitClaim" class="space-y-8">
            <!-- Basic Information Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Insurer Information -->
                <div class="space-y-4">
                    <div>
                        <label for="insurer_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Insurer Code *
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.insurer_code"
                                type="text"
                                id="insurer_code"
                                required
                                @input="onInsurerCodeChange"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                :class="{
                                    'border-green-500 bg-green-50/30': insurerDetails,
                                    'border-red-500 bg-red-50/30': insurerError
                                }"
                                placeholder="INS-A, INS-B, etc."
                            />
                            <div v-if="loadingInsurer" class="absolute right-3 top-3">
                                <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Insurer Details -->
                        <div v-if="insurerDetails" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="font-semibold text-green-800 text-sm">✓ {{ insurerDetails.name }}</p>
                        </div>

                        <!-- Insurer Error -->
                        <div v-if="insurerError" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {{ insurerError }}
                        </div>
                    </div>

                    <!-- Provider Name -->
                    <div>
                        <label for="provider_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Provider Name *
                        </label>
                        <input
                            v-model="form.provider_name"
                            type="text"
                            id="provider_name"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter your facility or practice name"
                        />
                    </div>
                </div>

                <!-- Medical Details -->
                <div class="space-y-4">
                    <!-- Encounter Date -->
                    <div>
                        <label for="encounter_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Encounter Date *
                        </label>
                        <input
                            v-model="form.encounter_date"
                            type="date"
                            id="encounter_date"
                            required
                            :max="maxDate"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        />
                    </div>

                    <!-- Specialty -->
                    <div>
                        <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">
                            Medical Specialty *
                        </label>
                        <select
                            v-model="form.specialty"
                            id="specialty"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                        >
                            <option value="">Select Specialty</option>
                            <option v-for="specialty in specialties" :key="specialty.value" :value="specialty.value">
                                {{ specialty.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Priority Level -->
                    <div>
                        <label for="priority_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority Level *
                        </label>
                        <select
                            v-model.number="form.priority_level"
                            id="priority_level"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white"
                        >
                            <option value="">Select Priority Level</option>
                            <option v-for="priority in priorities" :key="priority.value" :value="priority.value">
                                {{ priority.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Claim Items Section -->
            <div class="border-t pt-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Claim Items</h3>
                    <p class="text-gray-600 text-sm">Add all medical services and items for this claim</p>
                </div>

                <!-- Items List -->
                <div class="space-y-4 mb-6">
                    <div
                        v-for="(item, index) in form.items"
                        :key="index"
                        class="p-6 border border-gray-200 rounded-xl bg-white hover:border-gray-300 transition-colors"
                    >
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                            <!-- Item Name -->
                            <div class="lg:col-span-5">
                                <label :for="`item-name-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                    Item Name *
                                </label>
                                <input
                                    v-model="item.name"
                                    :id="`item-name-${index}`"
                                    type="text"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="e.g., Consultation, Laboratory Test, Medication"
                                />
                            </div>

                            <!-- Unit Price -->
                            <div class="lg:col-span-2">
                                <label :for="`item-price-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unit Price (₦)
                                </label>
                                <input
                                    v-model="item.unit_price"
                                    :id="`item-price-${index}`"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                />
                            </div>

                            <!-- Quantity -->
                            <div class="lg:col-span-2">
                                <label :for="`item-quantity-${index}`" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity
                                </label>
                                <input
                                    v-model="item.quantity"
                                    :id="`item-quantity-${index}`"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                />
                            </div>

                            <!-- Subtotal Display -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subtotal
                                </label>
                                <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-lg font-semibold text-gray-900">
                                    ₦{{ calculateSubtotal(item).toFixed(2) }}
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <div class="lg:col-span-1">
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="w-full px-4 py-3 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 transition-colors flex items-center justify-center"
                                    :disabled="form.items.length === 1"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Item & Total Row -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-6 bg-gray-50 border border-gray-200 rounded-xl">
                    <!-- Add Item Button -->
                    <button
                        type="button"
                        @click="addItem"
                        class="inline-flex items-center justify-center px-6 py-4 bg-white text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 focus:ring-2 focus:ring-blue-500 transition-colors font-medium"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Another Item
                    </button>

                    <!-- Total Amount -->
                    <div class="flex items-center space-x-6">
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-600 mb-1">Total Claim Amount</div>
                            <div class="text-3xl font-bold text-gray-900">₦{{ calculateTotal.toFixed(2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t pt-8">
                <button
                    type="submit"
                    :disabled="submitting"
                    class="w-full px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="submitting" class="flex items-center justify-center">
                        Processing Claim...
                    </span>
                    <span v-else class="flex items-center justify-center">
                        Submit Medical Claim
                    </span>
                </button>
            </div>
        </form>
    </ClaimLayout>
</template>


<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { Head } from "@inertiajs/vue3";
import ClaimLayout from "@/Layouts/ClaimLayout.vue";

// Reactive data
const submitting = ref(false)
const success = ref(null)
const error = ref(null)
const loadingInsurer = ref(false)
const insurerDetails = ref(null)
const insurerError = ref(null)
const specialties = ref([])
const priorities = ref([])

const maxDate = new Date().toISOString().split('T')[0]

const form = reactive({
    insurer_code: '',
    provider_name: '',
    encounter_date: '',
    specialty: '',
    priority_level: '',
    items: [
        { name: '', unit_price: 0, quantity: 1 }
    ]
})

// Methods
const calculateSubtotal = (item) => (parseFloat(item.unit_price) || 0) * (parseInt(item.quantity) || 0)

const calculateTotal = computed(() => form.items.reduce((total, item) => total + calculateSubtotal(item), 0))

const addItem = () => form.items.push({ name: '', unit_price: 0, quantity: 1 })

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1)
    }
}

// Fetch data from API
const fetchApiData = async (url) => {
    try {
        const response = await fetch(url)

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`)
        }

        return await response.json()
    } catch (err) {
        console.error(`Failed to load from ${url}:`, err)

        return null
    }
}

// Fetch specialties and priorities on component mount
onMounted(async () => {
    const specialtiesData = await fetchApiData('/api/specialties')

    specialties.value = specialtiesData?.data || []

    const prioritiesData = await fetchApiData('/api/priorities')

    priorities.value = prioritiesData?.data || []
})

// Insurer code auto-complete with debouncing
let insurerTimeout = null
const onInsurerCodeChange = () => {
    insurerDetails.value = null
    insurerError.value = null

    if (insurerTimeout) {
        clearTimeout(insurerTimeout)
    }

    if (form.insurer_code.length >= 3) {
        insurerTimeout = setTimeout(fetchInsurerDetails, 1000)
    }
}

const fetchInsurerDetails = async () => {
    if (!form.insurer_code.trim()) {
        return
    }

    loadingInsurer.value = true
    insurerError.value = null

    try {
        const response = await fetch(`/api/insurers/${form.insurer_code.toUpperCase()}`)
        const data = await response.json()

        if (response.ok) {
            insurerDetails.value = data.data
        } else {
            insurerError.value = data.message || 'Insurer not found'
        }
    } catch (err) {
        insurerError.value = 'Failed to fetch insurer details'
    } finally {
        loadingInsurer.value = false
    }
}

const getCsrfToken = () => {
    const element = document.querySelector('meta[name="csrf-token"]')
    return element?.getAttribute('content') || ''
}

const submitClaim = async () => {
    submitting.value = true
    error.value = null
    success.value = null

    try {
        const response = await fetch('/api/claims', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(form)
        })

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message || 'Failed to submit claim'
            return
        }

        success.value = data.message || 'Claim submitted successfully'
        resetForm()

        setTimeout(() => { success.value = null }, 5000)

    } catch (err) {
        error.value = err.message || 'An error occurred while submitting the claim'
    } finally {
        submitting.value = false
    }
}

const resetForm = () => {
    form.insurer_code = ''
    form.provider_name = ''
    form.encounter_date = ''
    form.specialty = ''
    form.priority_level = ''
    form.items = [{ name: '', unit_price: 0, quantity: 1 }]
    insurerDetails.value = null
    insurerError.value = null
}
</script>
