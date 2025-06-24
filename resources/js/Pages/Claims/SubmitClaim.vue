<template>
    <AuthenticatedLayout>

        <Head title="Submit Claim" />
        <div class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Submit Claim</h2>
            <hr class="mb-4" />

            <div class="mb-2">
                <InputLabel for="insurer_code" value="Insurer" />
                <select v-model="form.insurer_code" @change="form.validate('insurer_code')" class="w-full p-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option disabled value="">Select Insurer</option>
                    <option v-for="insurer in insurers" :key="insurer.id" :value="insurer.code">{{ `${insurer.name} (${insurer.code}) `}}</option>
                </select>
                <InputError v-if="form.invalid('insurer_code')" :message="form.errors.insurer_code" />
            </div>
            <div class="mb-2">
                <InputLabel for="encounter_date" value="Encounter Date" />
                <TextInput id="encounter_date" v-model="form.encounter_date" type="date"
                    @change="form.validate('encounter_date')" class="w-full p-2" />
                <InputError v-if="form.invalid('encounter_date')" :message="form.errors.encounter_date" />
            </div>
            <div>
                <InputLabel for="claim_items" value="Claim Items" />
                <ItemRow 
                v-for="(item, index) in form.items" 
                :key="index" 
                :item="item" 
                :index="index" 
                :form="form"
                :canRemove="form.items.length > 1" 
                @remove="removeItem(index)" 
                />
                <div class="flex justify-between text-sm">
                    <span>Total: <strong>N{{ claimAmount.toFixed(2) }}</strong></span>
                    <button type="button" @click="addItem" class="bg-gray-200 px-3 py-1 rounded text-sm">+ Add
                        Item</button>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <div class="w-1/2 flex flex-col">
                    <InputLabel value="Specialty" />
                    <select v-model="form.specialty" @change="form.validate('specialty')" class="w-full p-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option disabled value="">Select Specialty</option>
                        <option v-for="specialty in specialties" :key="specialty" :value="specialty">{{ specialty }}</option>
                    </select>
                    <InputError class="h-4" v-if="form.invalid('specialty')" :message="form.errors.specialty" />
                </div>
                <div class="w-1/2 flex flex-col">
                    <InputLabel value="Priority Level" />
                    <TextInput v-model.number="form.priority_level" @change="form.validate('priority_level')" type="number" placeholder="1-5"/>
                    <InputError class="h-4" v-if="form.invalid('priority_level')" :message="form.errors.priority_level" />
                </div>
            </div>

            <button @click="submitClaim" :disabled="form.processing"
                class="mt-6 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                {{ form.processing ? 'Submitting...' : 'Submit' }}
            </button>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from 'laravel-precognition-vue-inertia'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import ItemRow from '@/Components/Claims/ItemRow.vue'

const props = defineProps({
    insurers: {
        type: Object,
        required: true,
    },
    specialties: {
        type: Object,
        required: true,
    },
});

const form = useForm('post', '/provider-claims/submit', {
    insurer_code: '',
    encounter_date: '',
    items: [{ name: '', unit_price: 0, quantity: 1 }],
    specialty: '',
    priority_level: '',
})

const submitClaim = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
})

const claimAmount = computed(() =>
    form.items.reduce((sum, i) => sum + (i.unit_price * i.quantity), 0)
)

const addItem = () => form.items.push({ name: '', unit_price: 0, quantity: 1 })
const removeItem = (index) => form.items.length > 1 ? form.items.splice(index, 1) : null
</script>
