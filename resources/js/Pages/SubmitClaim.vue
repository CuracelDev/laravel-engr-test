<script setup>
import { reactive, computed } from 'vue'
import axios from 'axios'
import GuestLayout from '../Layouts/GuestLayout.vue'
defineOptions({ layout: GuestLayout })

const form = reactive({
  insurer_code: '',
  provider_name: '',
  encounter_date: '',
  submission_date: '',
  specialty: '',
  priority_level: 3,
  items: [
    { name: '', unit_price: 0, quantity: 1 }
  ],
})

const errors = reactive({})
const loading = reactive({ submitting: false })
const success = reactive({ message: '' })

const itemSubtotal = (it) => {
  const price = Number(it.unit_price || 0)
  const qty = Number(it.quantity || 0)
  return +(price * qty).toFixed(2)
}

const totalValue = computed(() =>
  form.items.reduce((sum, it) => sum + itemSubtotal(it), 0).toFixed(2)
)

function addItem() {
  form.items.push({ name: '', unit_price: 0, quantity: 1 })
}
function removeItem(idx) {
  if (form.items.length > 1) form.items.splice(idx, 1)
}

async function submit() {
  success.message = ''
  Object.keys(errors).forEach(k => delete errors[k])
  loading.submitting = true
  try {
    const payload = {
      insurer_code: form.insurer_code,
      provider_name: form.provider_name,
      encounter_date: form.encounter_date,
      submission_date: form.submission_date,
      specialty: form.specialty,
      priority_level: Number(form.priority_level),
      items: form.items.map(it => ({
        name: it.name,
        unit_price: Number(it.unit_price),
        quantity: Number(it.quantity),
      })),
    }
    const { data } = await axios.post('/api/claims', payload)
    success.message = data.message || 'Submitted!'
    form.encounter_date = ''
    form.submission_date = ''
    form.specialty = ''
    form.priority_level = 3
    form.items = [{ name: '', unit_price: 0, quantity: 1 }]
  } catch (e) {
    if (e.response?.status === 422) {
      Object.assign(errors, e.response.data.errors || {})
    } else {
      errors._global = e.response?.data?.message || e.message
    }
  } finally {
    loading.submitting = false
  }
}
</script>

<template>
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Submit Claim</h1>

    <div v-if="success.message" class="p-3 mb-4 bg-green-100 text-green-800 rounded">
      {{ success.message }}
    </div>
    <div v-if="errors._global" class="p-3 mb-4 bg-red-100 text-red-800 rounded">
      {{ errors._global }}
    </div>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Insurer Code</label>
        <input v-model="form.insurer_code" type="text" class="mt-1 w-full border rounded p-2" placeholder="e.g. INS-A" />
        <p v-if="errors.insurer_code" class="text-red-600 text-sm mt-1">{{ errors.insurer_code[0] }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium">Provider Name</label>
        <input v-model="form.provider_name" type="text" class="mt-1 w-full border rounded p-2" placeholder="e.g. Provider A" />
        <p v-if="errors.provider_name" class="text-red-600 text-sm mt-1">{{ errors.provider_name[0] }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Encounter Date</label>
          <input v-model="form.encounter_date" type="date" class="mt-1 w-full border rounded p-2" />
          <p v-if="errors.encounter_date" class="text-red-600 text-sm mt-1">{{ errors.encounter_date[0] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">Submission Date</label>
          <input v-model="form.submission_date" type="date" class="mt-1 w-full border rounded p-2" />
          <p v-if="errors.submission_date" class="text-red-600 text-sm mt-1">{{ errors.submission_date[0] }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Specialty</label>
          <input v-model="form.specialty" type="text" class="mt-1 w-full border rounded p-2" placeholder="e.g. cardiology" />
          <p v-if="errors.specialty" class="text-red-600 text-sm mt-1">{{ errors.specialty[0] }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium">Priority Level (1–5)</label>
          <input v-model.number="form.priority_level" type="number" min="1" max="5" class="mt-1 w-full border rounded p-2" />
          <p v-if="errors.priority_level" class="text-red-600 text-sm mt-1">{{ errors.priority_level[0] }}</p>
        </div>
      </div>

      <div class="mt-6">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-lg font-medium">Items</h2>
          <button type="button" @click="addItem" class="px-3 py-1 border rounded">+ Add Item</button>
        </div>

        <div class="space-y-3">
          <div v-for="(it, idx) in form.items" :key="idx" class="grid grid-cols-12 gap-2 items-end">
            <div class="col-span-4">
              <label class="block text-sm font-medium">Name</label>
              <input v-model="it.name" type="text" class="mt-1 w-full border rounded p-2" placeholder="ECG" />
              <p v-if="errors[`items.${idx}.name`]" class="text-red-600 text-sm mt-1">
                {{ errors[`items.${idx}.name`][0] }}
              </p>
            </div>
            <div class="col-span-3">
              <label class="block text-sm font-medium">Unit Price</label>
              <input v-model.number="it.unit_price" type="number" min="0" step="0.01" class="mt-1 w-full border rounded p-2" />
              <p v-if="errors[`items.${idx}.unit_price`]" class="text-red-600 text-sm mt-1">
                {{ errors[`items.${idx}.unit_price`][0] }}
              </p>
            </div>
            <div class="col-span-2">
              <label class="block text-sm font-medium">Qty</label>
              <input v-model.number="it.quantity" type="number" min="1" class="mt-1 w-full border rounded p-2" />
              <p v-if="errors[`items.${idx}.quantity`]" class="text-red-600 text-sm mt-1">
                {{ errors[`items.${idx}.quantity`][0] }}
              </p>
            </div>
            <div class="col-span-2">
              <label class="block text-sm font-medium">Subtotal</label>
              <input :value="itemSubtotal(it).toFixed(2)" type="text" readonly class="mt-1 w-full border rounded p-2 bg-gray-50" />
            </div>
            <div class="col-span-1 flex justify-end">
              <button v-if="form.items.length > 1" type="button" @click="removeItem(idx)" class="px-3 py-2 border rounded">✕</button>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-between">
        <div class="w-48">
            <label class="block text-sm font-medium">Total Amount</label>
            <input :value="totalValue" type="number" readonly class="mt-1 w-full border rounded p-2 bg-gray-50" />
        </div>
        <button :disabled="loading.submitting" class="px-4 py-2 bg-black text-white rounded">
            {{ loading.submitting ? 'Submitting...' : 'Submit Claim' }}
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
</style>