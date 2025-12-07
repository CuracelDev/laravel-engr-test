<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; font-size: 1.5rem; font-weight: 600;">
            Submit A Claim
        </div>

        <div class="card-body" style="padding: 2rem; max-width: 900px; margin: 0 auto; width: 100%;">
            <form @submit.prevent="submitClaim">
                <div style="margin-bottom: 1.5rem;">
                    <div style="width: 100%;">
                        <label for="provider_name" class="form-label fw-semibold" style="display: block; margin-bottom: 0.5rem;">Provider Name</label>
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            id="provider_name"
                            v-model="form.provider_name"
                            placeholder="Enter provider name"
                            required
                            style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%; padding: 0.5rem 0.75rem;"
                        />
                        <div v-if="errors.provider_name" class="text-danger mt-1">{{ errors.provider_name }}</div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div style="flex: 1 1 calc(50% - 0.5rem); min-width: 250px;">
                        <label for="insurer_code" class="form-label fw-semibold" style="display: block; margin-bottom: 0.5rem;">Insurer Code</label>
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            id="insurer_code"
                            v-model="form.insurer_code"
                            placeholder="e.g., INS-A"
                            required
                            style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%; padding: 0.5rem 0.75rem;"
                        />
                        <small class="text-muted" style="display: block; margin-top: 0.25rem;">Available: INS-A, INS-B, INS-C, INS-D</small>
                        <div v-if="errors.insurer_code" class="text-danger mt-1">{{ errors.insurer_code }}</div>
                    </div>

                    <div style="flex: 1 1 calc(50% - 0.5rem); min-width: 250px;">
                        <label for="encounter_date" class="form-label fw-semibold" style="display: block; margin-bottom: 0.5rem;">Encounter Date</label>
                        <input
                            type="date"
                            class="form-control form-control-lg"
                            id="encounter_date"
                            v-model="form.encounter_date"
                            :max="today"
                            required
                            style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%; padding: 0.5rem 0.75rem;"
                        />
                        <div v-if="errors.encounter_date" class="text-danger mt-1">{{ errors.encounter_date }}</div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div style="flex: 1 1 calc(50% - 0.5rem); min-width: 250px;">
                        <label for="specialty" class="form-label fw-semibold" style="display: block; margin-bottom: 0.5rem;">Specialty</label>
                        <select
                            class="form-select form-select-lg"
                            id="specialty"
                            v-model="form.specialty"
                            required
                            style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%; padding: 0.5rem 0.75rem;"
                        >
                            <option value="">Select specialty</option>
                            <option value="cardiology">Cardiology</option>
                            <option value="orthopedics">Orthopedics</option>
                            <option value="neurology">Neurology</option>
                            <option value="dermatology">Dermatology</option>
                            <option value="general">General</option>
                        </select>
                        <div v-if="errors.specialty" class="text-danger mt-1">{{ errors.specialty }}</div>
                    </div>

                    <div style="flex: 1 1 calc(50% - 0.5rem); min-width: 250px;">
                        <label for="priority_level" class="form-label fw-semibold" style="display: block; margin-bottom: 0.5rem;">Priority Level (1-5)</label>
                        <input
                            type="number"
                            class="form-control form-control-lg"
                            id="priority_level"
                            v-model.number="form.priority_level"
                            min="1"
                            max="5"
                            placeholder="3"
                            style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%; padding: 0.5rem 0.75rem;"
                        />
                        <div v-if="errors.priority_level" class="text-danger mt-1">{{ errors.priority_level }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size: 1.1rem; color: #333;">Claim Items</label>

                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                        <div v-for="(item, index) in form.items" :key="index"
                             style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <div class="claim-item-row">
                                <div class="claim-item-field claim-item-name">
                                    <label class="form-label fw-semibold" style="font-size: 0.875rem; margin-bottom: 0.25rem; display: block;">Item</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="item.name"
                                        placeholder="Item name"
                                        required
                                        style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%;"
                                    />
                                </div>
                                <div class="claim-item-field claim-item-price">
                                    <label class="form-label fw-semibold" style="font-size: 0.875rem; margin-bottom: 0.25rem; display: block;">Unit Price</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model.number="item.unit_price"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        required
                                        @input="calculateSubtotal(index)"
                                        style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%;"
                                    />
                                </div>
                                <div class="claim-item-field claim-item-qty">
                                    <label class="form-label fw-semibold" style="font-size: 0.875rem; margin-bottom: 0.25rem; display: block;">Qty</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model.number="item.quantity"
                                        min="1"
                                        placeholder="1"
                                        required
                                        @input="calculateSubtotal(index)"
                                        style="border: 2px solid #e0e0e0; border-radius: 8px; width: 100%;"
                                    />
                                </div>
                                <div class="claim-item-field claim-item-subtotal">
                                    <label class="form-label fw-semibold" style="font-size: 0.875rem; margin-bottom: 0.25rem; display: block;">Sub Total</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="formatCurrency(item.subtotal)"
                                        readonly
                                        style="background: #e9ecef; font-weight: 600; border: 2px solid #e0e0e0; border-radius: 8px; width: 100%;"
                                    />
                                </div>
                                <div class="claim-item-field claim-item-remove">
                                    <label class="form-label fw-semibold" style="font-size: 0.875rem; margin-bottom: 0.25rem; display: block; visibility: hidden;">Remove</label>
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        @click="removeItem(index)"
                                        :disabled="form.items.length === 1"
                                        style="transition: 0.3s; background: rgb(233, 236, 239); border-radius: 8px; width: 100%; height: 45px; padding: 0px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"
                                    >
                                        −
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary" @click="addItem"
                                style="width: 100%; padding: 0.75rem; font-weight: 600; border: 2px dashed #6c757d; background: transparent; color: #6c757d; transition: all 0.3s; border-radius: 8px;"
                                onmouseover="this.style.background='#6c757d'; this.style.color='white';"
                                onmouseout="this.style.background='transparent'; this.style.color='#6c757d';">
                            + Add Another Item
                        </button>
                        <div v-if="errors.items" class="text-danger mt-2">{{ errors.items }}</div>
                    </div>
                </div>

                <div class="mb-4" style="display: flex; align-items: center; justify-content: space-between;">
                    <label class="form-label fw-bold" style="font-size: 1.2rem; color: #333; margin: 0;">Total Claim Amount</label>
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        :value="formatCurrency(totalAmount)"
                        readonly
                        style="background: rgb(233, 236, 239); color: black; font-size: 1.5rem; font-weight: bold; text-align: center; border: none; border-radius: 8px; width: 200px; flex-shrink: 0;"
                    />
                </div>

                <div v-if="successMessage" class="alert alert-success" style="border-radius: 8px; border-left: 4px solid #28a745;">
                    <strong>Success!</strong> {{ successMessage }}
                </div>

                <div v-if="errorMessage" class="alert alert-danger" style="border-radius: 8px; border-left: 4px solid #dc3545;">
                    <strong>Error!</strong> {{ errorMessage }}
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="isSubmitting"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 1rem; font-size: 1.2rem; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); transition: all 0.3s;"
                        onmouseover="if(!this.disabled) { this.style.background='white'; this.style.color='black'; this.style.border='2px solid #667eea'; }"
                        onmouseout="if(!this.disabled) { this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.style.color='white'; this.style.border='none'; }">
                    {{ isSubmitting ? 'Submitting Claim...' : 'Submit Claim' }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import axios from 'axios';

const form = ref({
    insurer_code: '',
    provider_name: '',
    encounter_date: '',
    specialty: '',
    priority_level: 3,
    items: [
        { name: '', unit_price: 0, quantity: 1, subtotal: 0 }
    ]
});

const errors = ref({});
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const today = computed(() => {
    return new Date().toISOString().split('T')[0];
});

const totalAmount = computed(() => {
    return form.value.items.reduce((sum, item) => sum + (item.subtotal || 0), 0);
});

function calculateSubtotal(index) {
    const item = form.value.items[index];
    item.subtotal = (item.unit_price || 0) * (item.quantity || 0);
}

function addItem() {
    form.value.items.push({ name: '', unit_price: 0, quantity: 1, subtotal: 0 });
}

function removeItem(index) {
    if (form.value.items.length > 1) {
        form.value.items.splice(index, 1);
    }
}

function formatCurrency(value) {
    return '$' + parseFloat(value || 0).toFixed(2);
}

async function submitClaim() {
    errors.value = {};
    successMessage.value = '';
    errorMessage.value = '';
    isSubmitting.value = true;

    try {
        const response = await axios.post('/api/claims', form.value);

        successMessage.value = response.data.message + ' (Claim ID: ' + response.data.data.claim_id + ')';

        form.value = {
            insurer_code: '',
            provider_name: '',
            encounter_date: '',
            specialty: '',
            priority_level: 3,
            items: [{ name: '', unit_price: 0, quantity: 1, subtotal: 0 }]
        };
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
            errorMessage.value = 'Please check the form for errors.';
        } else {
            errorMessage.value = error.response?.data?.message || 'An error occurred while submitting the claim.';
        }
    } finally {
        isSubmitting.value = false;
    }
}
</script>
