<template>

    <form @submit.prevent="submit">
        <input v-model="form.provider_name" placeholder="Provider Name" />
        <input v-model="form.insurer_code" placeholder="Insurer Code" />
        <input v-model="form.encounter_date" type="date" />
        <select v-model="form.specialty">
            <option value="cardiology">Cardiology</option>
            <option value="orthopedics">Orthopedics</option>
        </select>
        <select v-model="form.priority_level">
            <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
        </select>

        <div v-for="(item, i) in form.items" :key="i">
            <input v-model="item.name" placeholder="Item name" />
            <input v-model.number="item.unit_price" placeholder="Unit price" />
            <input v-model.number="item.quantity" placeholder="Quantity" />
            <span>Subtotal: {{ item.unit_price * item.quantity }}</span>
        </div>
        <button type="button" @click="addItem">Add Item</button>

        <div>Total: {{ totalAmount }}</div>

        <button type="submit">Submit</button>
    </form>
    <div v-if="response">
        <h3 class="mt-4">Claim Submitted!</h3>
        <p><strong>Claim ID:</strong> {{ response.claim_id }}</p>
        <p><strong>Insurer:</strong> {{ response.selected_insurer.code }}</p>
        (₦{{ response.selected_insurer.estimated_cost.toLocaleString() }})
        <div v-if="response.recommended_insurer" class="mt-3 text-yellow-600">
            <p>
                ⚠ A better insurer is available: <strong>{{ response.recommended_insurer.name }}</strong> (<strong>{{ response.recommended_insurer.code }}</strong>) -
                (₦{{ response.recommended_insurer.estimated_cost.toLocaleString() }})
            </p>
        </div>
    </div>

    <div v-if="error" class="mt-3 text-red-600">
        <p>Error: {{ error }}</p>
    </div>
</template>

<script setup>
import { reactive, computed, ref } from 'vue';
import axios from 'axios';

const form = reactive({
    provider_name: '',
    insurer_code: '',
    encounter_date: '',
    specialty: '',
    priority_level: 1,
    items: [{ name: '', unit_price: 0, quantity: 1 }],
});

const response = ref(null);
const error = ref(null);
const loading = ref(false);

const totalAmount = computed(() =>
    form.items.reduce((sum, item) => sum + item.unit_price * item.quantity, 0)
);

function addItem() {
    form.items.push({ name: '', unit_price: 0, quantity: 1 });
}

async function submit() {
    error.value = null;
    response.value = null;
    loading.value = true;

    try {
        const res = await axios.post('/claims', form);
        response.value = res.data;
        alert(res.data.message);
    } catch (e) {
        if (e.response && e.response.data) {
            error.value = e.response.data.message || 'Submission failed.';
            alert(error.value);
        } else {
            error.value = 'Something went wrong.';
        }
    } finally {
        loading.value = false;
    }
}
</script>

