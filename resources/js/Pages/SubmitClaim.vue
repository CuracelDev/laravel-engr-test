<template>
   
        <Head title="Curacel - Submit Claim" />
        
        <div class="min-h-screen bg-gray-50 py-12">
            <div class="w-[80%] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden rounded-lg border-[1px] border-[#1a1aff]">
                    <div class="p-4 sm:p-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-8">Curacel - Submit Medical Claim</h1>
                        
                        <!-- =====  alert ===== -->
                        <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-green-800">Claim Submitted Successfully!</h3>
                                    <p class="text-sm text-green-700 mt-1">{{ successMessage }}</p>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submitClaim" class="space-y-6">
                            <!-- ===== Provider Information ===== -->
                            <div class="p-4 rounded-lg">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Provider Information</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="provider_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Provider Name *
                                        </label>
                                        <input 
                                            id="provider_name"
                                            v-model="form.provider_name"
                                            type="text" 
                                            required
                                            class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Enter provider name"
                                        />
                                        <span v-if="errors.provider_name" class="text-red-500 text-sm">{{ errors.provider_name[0] }}</span>
                                    </div>

                                    <div>
                                        <label for="insurer_code" class="block text-sm font-medium text-gray-700 mb-2">
                                            Insurer Code *
                                        </label>
                                        <select 
                                            id="insurer_code"
                                            v-model="form.insurer_code"
                                            required
                                            class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Select an insurer</option>
                                            <option v-for="insurer in insurers" :key="insurer.code" :value="insurer.code">
                                                {{ insurer.code }} - {{ insurer.name }}
                                            </option>
                                        </select>
                                        <span v-if="errors.insurer_code" class="text-red-500 text-sm">{{ errors.insurer_code[0] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== Claim Details ===== -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Claim Details</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="encounter_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Encounter Date *
                                        </label>
                                        <input 
                                            id="encounter_date"
                                            v-model="form.encounter_date"
                                            type="date" 
                                            required
                                            class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        <span v-if="errors.encounter_date" class="text-red-500 text-sm">{{ errors.encounter_date[0] }}</span>
                                    </div>

                                    <div>
                                        <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">
                                            Specialty *
                                        </label>
                                        <select 
                                            id="specialty"
                                            v-model="form.specialty"
                                            required
                                                class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Select specialty</option>
                                            <option v-for="(label, value) in specialties" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <span v-if="errors.specialty" class="text-red-500 text-sm">{{ errors.specialty[0] }}</span>
                                    </div>

                                    <div>
                                        <label for="priority_level" class="block text-sm font-medium text-gray-700 mb-2">
                                            Priority Level *
                                        </label>
                                        <select 
                                            id="priority_level"
                                            v-model="form.priority_level"
                                            required
                                            class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                            <option value="">Select priority</option>
                                            <option value="1">1 - Low</option>
                                            <option value="2">2 - Normal</option>
                                            <option value="3">3 - Medium</option>
                                            <option value="4">4 - High</option>
                                            <option value="5">5 - Urgent</option>
                                        </select>
                                        <span v-if="errors.priority_level" class="text-red-500 text-sm">{{ errors.priority_level[0] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== Claim Items ===== -->
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-semibold text-gray-900">Claim Items</h2>
                                    <button 
                                        type="button"
                                        @click="addItem"
                                        class="text-[#1a1aff] px-4 py-2"
                                    >
                                        + Add Item
                                    </button>
                                </div>

                                <div v-if="form.items.length === 0" class="text-gray-500 text-center py-8 border-2 border-dashed border-[#1a1aff] rounded-lg">
                                    No items added yet. Click "Add Item" to get started.
                                </div>

                                <div v-else class="space-y-4">
                                    <div 
                                        v-for="(item, index) in form.items" 
                                        :key="index"
                                        class="p-4"
                                    >
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                            <div class="md:col-span-2">
                                                <label :for="'item_name_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Item Name *
                                                </label>
                                                <input 
                                                    :id="'item_name_' + index"
                                                    v-model="item.name"
                                                    type="text" 
                                                    required
                                                    class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="Enter item description"
                                                />
                                            </div>

                                            <div>
                                                <label :for="'item_price_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Unit Price *
                                                </label>
                                                <input 
                                                    :id="'item_price_' + index"
                                                    v-model="item.unit_price"
                                                    type="number" 
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                    @input="calculateSubtotal(index)"
                                                    class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="0.00"
                                                />
                                            </div>

                                            <div>
                                                <label :for="'item_quantity_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Quantity *
                                                </label>
                                                <input 
                                                    :id="'item_quantity_' + index"
                                                    v-model="item.quantity"
                                                    type="number" 
                                                    min="1"
                                                    required
                                                    @input="calculateSubtotal(index)"
                                                    class="w-full px-3 py-2 border border-[#1a1aff] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="1"
                                                />
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <div class="flex-1">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Subtotal
                                                    </label>
                                                    <div class="px-3 py-2 border border-[#1a1aff] rounded-md text-gray-900 font-medium">
                                                        ${{ formatCurrency(item.subtotal) }}
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-red-600 p-2 mt-4"
                                                    title="Remove item"
                                                >
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== Total Amount ===== -->
                                <div class="mt-4 p-4 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-medium text-gray-900">Total Claim Amount:</span>
                                        <span class="text-2xl font-bold text-blue-600">${{ formatCurrency(totalAmount) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== Error alert ===== -->
                            <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-md p-4">
                                <h3 class="text-sm font-medium text-red-800 mb-2">Please correct the following errors:</h3>
                                <ul class="text-sm text-red-700 space-y-1">
                                    <li v-for="(fieldErrors, field) in errors" :key="field">
                                        {{ fieldErrors[0] }}
                                    </li>
                                </ul>
                            </div>

                      
                            <div class="flex justify-end">
                                <button 
                                    type="submit"
                                    :disabled="processing || form.items.length === 0"
                                    class="bg-[#1a1aff] text-white px-8 py-3 rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="processing">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                    <span v-else>
                                        Submit Claim
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
  
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import axios from 'axios'


const form = ref({
    provider_name: '',
    insurer_code: '',
    encounter_date: '',
    specialty: '',
    priority_level: '',
    items: []
})

const insurers = ref([])
const specialties = ref({})
const errors = ref({})
const processing = ref(false)
const successMessage = ref('')

const totalAmount = computed(() => {
    return form.value.items.reduce((total, item) => {
        return total + (item.subtotal || 0)
    }, 0)
})

// actions
const addItem = () => {
    form.value.items.push({
        name: '',
        unit_price: '',
        quantity: 1,
        subtotal: 0
    })
}

const removeItem = (index) => {
    form.value.items.splice(index, 1)
}

const calculateSubtotal = (index) => {
    const item = form.value.items[index]
    const price = parseFloat(item.unit_price) || 0
    const quantity = parseInt(item.quantity) || 0
    item.subtotal = price * quantity
}

const formatCurrency = (amount) => {
    return (amount || 0).toFixed(2)
}

const loadInsurers = async () => {
    try {
        const response = await axios.get('/api/insurers')
        insurers.value = response.data.data
    } catch (error) {
        console.error('Failed to load insurers:', error)
    }
}

const loadSpecialties = async () => {
    try {
        const response = await axios.get('/api/specialties')
        specialties.value = response.data.data
    } catch (error) {
        console.error('Failed to load specialties:', error)
    }
}

const resetForm = () => {
    form.value = {
        provider_name: '',
        insurer_code: '',
        encounter_date: '',
        specialty: '',
        priority_level: '',
        items: []
    }
    errors.value = {}
}

const submitClaim = async () => {
    processing.value = true
    errors.value = {}
    successMessage.value = ''

    try {
        const response = await axios.post('/api/claims', form.value)
        
        if (response.data.success) {
            successMessage.value = `Claim submitted successfully! Batch ID: ${response.data.data.batch_identifier}. Total Amount: ${response.data.data.total_amount}. Estimated Processing Cost: ${response.data.data.estimated_processing_cost.toFixed(2)}`
            resetForm()
            
           
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {}
        } else {
            errors.value = { general: ['An error occurred while submitting the claim. Please try again.'] }
        }
        
        
        window.scrollTo({ top: 0, behavior: 'smooth' })
    } finally {
        processing.value = false
    }
}


onMounted(() => {
    loadInsurers()
    loadSpecialties()
    
    addItem()
})
</script>