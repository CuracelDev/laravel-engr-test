<template>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-md shadow-lg">
        <h1 class="text-3xl font-bold text-center mb-6">Submit a Claim</h1>

        <form @submit.prevent="submitClaim">
            <div class="mb-6">
                <label for="insurer-code" class="block text-lg font-semibold text-gray-800 mb-2">Insurer Code</label>
                <input type="text" id="insurer-code" v-model="claim.insurerCode" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="provider-name" class="block text-lg font-semibold text-gray-800 mb-2">Provider Name</label>
                <input type="text" id="provider-name" v-model="claim.providerName" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="encounter-date" class="block text-lg font-semibold text-gray-800 mb-2">Encounter Date</label>
                <input type="date" id="encounter-date" v-model="claim.encounterDate" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="specialty" class="block text-lg font-semibold text-gray-800 mb-2">Specialty</label>
                <input type="text" id="specialty" v-model="claim.specialty" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="priority-level" class="block text-lg font-semibold text-gray-800 mb-2">Priority Level</label>
                <input type="number" id="priority-level" v-model="claim.priorityLevel" min="1" max="5" class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Claim Item</h2>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
                    <div class="font-semibold text-center">Item</div>
                    <div class="font-semibold text-center">Unit Price</div>
                    <div class="font-semibold text-center">Quantity</div>
                    <div class="font-semibold text-center">Sub Total</div>
                </div>

                <div v-for="(item, index) in claim.items" :key="index" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4 items-center">
                    <input  type="text" v-model="item.name" class="p-3 border border-gray-300 rounded-md w-full" placeholder="Item Name" required />
                    <input  type="number" v-model="item.unitPrice" class="p-3 border border-gray-300 rounded-md w-full" placeholder="Unit Price" step="0.01" required />
                    <input  type="number" v-model="item.quantity" class="p-3 border border-gray-300 rounded-md w-full" placeholder="Quantity" required />
                    <input  type="number" :value="calculateSubTotal(item)" class="p-3 border border-gray-300 rounded-md w-full" placeholder="Sub Total" disabled />
                </div>

                <div class="flex justify-between mb-4">
                    <button @click="removeItem" :disabled="claim.items.length <= 1" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600 disabled:bg-gray-400">-</button>
                    <button type="button" @click="addItem" class="bg-green-500 text-white px-4 py-2 rounded-md text-sm hover:bg-green-600">+</button>
                </div>
            </div>

            <div class="mb-6">
                <label for="total" class="block text-lg font-semibold text-gray-800 mb-2">Total Amount</label>
                <input type="number" :value="totalAmount" class="w-full p-3 border border-gray-300 rounded-md shadow-sm" placeholder="Total" disabled>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700">Submit Claim</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            claim: {
                insurerCode: '',
                providerName: '',
                encounterDate: '',
                specialty: '',
                priorityLevel: 1,
                items: [
                    { name: '', unitPrice: 0, quantity: 1 }
                ]
            }
        };
    },
    computed: {
        totalAmount() {
            return this.claim.items.reduce((total, item) => total + this.calculateSubTotal(item), 0);
        }
    },
    methods: {
        addItem() {
            if (this.claim.items.length < 1) {
                alert("There must always be one claim item.");
                return;
            }
            this.claim.items.push({ name: '', unitPrice: 0, quantity: 1 });
        },
        removeItem() {
            if (this.claim.items.length > 1) {
                this.claim.items.pop();
            } else {
                alert("At least one claim item is required.");
            }
        },
        calculateSubTotal(item) {
            return item.unitPrice * item.quantity;
        },
        async submitClaim() {
            try {
                const response = await axios.post('/api/submit-claim', {
                    insurerCode: this.claim.insurerCode,
                    providerName: this.claim.providerName,
                    encounterDate: this.claim.encounterDate,
                    specialty: this.claim.specialty,
                    priorityLevel: this.claim.priorityLevel,
                    items: this.claim.items
                });

                if (response.data.status === 'success') {
                    alert(response.data.message);
                    window.location.reload();
                } else {
                    alert(response.data.message);
                }
            } catch (error) {
                if (error.response && error.response.data) {
                    alert(error.response.data.message);
                } else {
                    alert('An unknown error occurred');
                }
            }
        }
    }
};
</script>

<style scoped>
form {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9fafb;
    border-radius: 10px;
}

input,
button {
    font-size: 1rem;
    font-weight: 500;
}

button {
    cursor: pointer;
}

button:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease-in-out;
}

.grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

input {
    max-width: 100%;
}

@media (max-width: 640px) {
    .grid-cols-4 {
        grid-template-columns: 1fr;
    }

    input {
        max-width: 100%;
    }
}

button:disabled {
    cursor: not-allowed;
}

input[disabled] {
    background-color: #f1f1f1;
}
</style>
