<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="w-full py-10 px-4">
            <h1 class="text-2xl font-bold mb-6">Submit a Claim</h1>

            <!-- Claim Form -->
            <form @submit.prevent="submitClaim">
                <!-- Insurer Code -->
                <div class="mb-4">
                    <label class="block font-medium">Insurer Code</label>
                    <input v-model="form.insurer_code" type="text" class="input" required />
                </div>

                <!-- Provider Name -->
                <div class="mb-4">
                    <label class="block font-medium">Provider Name</label>
                    <input v-model="form.provider_name" type="text" class="input" required />
                </div>

                <!-- Encounter Date -->
                <div class="mb-4">
                    <label class="block font-medium">Encounter Date</label>
                    <input v-model="form.encounter_date" type="date" class="input" required />
                </div>

                <!-- Specialty -->
                <div class="mb-4">
                    <label class="block font-medium">Specialty</label>
                    <input v-model="form.specialty" type="text" class="input" required />
                </div>

                <!-- Priority Level -->
                <div class="mb-4">
                    <label class="block font-medium">Priority Level (1-5)</label>
                    <input v-model.number="form.priority_level" type="number" min="1" max="5" class="input" required />
                </div>

                <!-- Claim Items -->
                <div class="mb-6">
                    <label class="inline font-medium mb-2">Claim Items</label>

                    <!-- Header Row -->
                        <div class="flex gap-4 font-semibold text-sm mb-2 pl-1">
                            <div class="w-40">Item</div>
                            <div class="w-32">Unit Price</div>
                            <div class="w-28">Qty</div>
                            <div class="w-36">Sub Total</div>
                            <div class="w-20"></div> 
                        </div>

                        <!-- Items Loop -->
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="flex gap-4 items-center mb-3"
                        >
                            <input
                                v-model="item.name"
                                type="text"
                                class="input w-40"
                                required
                            />
                            <input
                                v-model.number="item.unit_price"
                                type="number"
                                class="input w-32"
                                required
                            />
                            <input
                                v-model.number="item.quantity"
                                type="number"
                                class="input w-28"
                                required
                            />
                            <input
                                :value="formatCurrency(item.unit_price * item.quantity)"
                                class="input w-36 bg-gray-100"
                                readonly
                            />
                            <button
                                type="button"
                                @click="removeItem(index)"
                                class="text-red-600 text-sm"
                            >
                                Remove
                            </button>
                        </div>


                    <button
                        type="button"
                        @click="addItem"
                        class="mt-2 text-blue-600 text-sm"
                    >
                        + Add Item
                    </button>
                </div>


                <!-- Total Amount -->
                <div class="mb-6">
                    <label class="block font-medium">Total Amount</label>
                    <input :value="formatCurrency(totalAmount)" class="input bg-gray-100" readonly />
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="btn btn-primary">Submit Claim</button>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { reactive, computed } from 'vue'

const form = reactive({
    insurer_code: '',
    provider_name: '',
    encounter_date: '',
    specialty: '',
    priority_level: 1,
    items: [
        { name: '', unit_price: 0, quantity: 1 }
    ]
})

// Add a new blank item
function addItem() {
    form.items.push({ name: '', unit_price: 0, quantity: 1 })
}

// Remove item by index
function removeItem(index) {
    form.items.splice(index, 1)
}

// Compute total amount
const totalAmount = computed(() =>
    form.items.reduce((total, item) => total + (item.unit_price * item.quantity), 0)
)

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount)
}

// Submit to API
function submitClaim() {
    const payload = {
        ...form,
        total_amount: totalAmount.value
    }

    router.post('/api/claims', payload, {
        onSuccess: () => {
            alert('Claim submitted successfully')
        },
        onError: (errors) => {
            console.error(errors)
        }
    })
}
</script>

<style scoped>
.input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.btn {
    padding: 0.6rem 1.2rem;
    background-color: #2563eb;
    color: white;
    border: none;
    border-radius: 4px;
}
.btn:hover {
    background-color: #1d4ed8;
}
</style>
