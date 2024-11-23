<template>
    <Head title="Submit Claim" />

    <div>
        <h1>Submit Claim</h1>
        <form @submit.prevent="submitClaim">
            <div>
                <label>Insurer Code:</label>
                <input
                    class="nonClaimsInp"
                    type="text"
                    v-model="claim.insurer_code"
                    required
                />
            </div>
            <div>
                <label>Provider Name:</label>
                <input
                    class="nonClaimsInp"
                    type="text"
                    v-model="claim.provider_name"
                    required
                />
            </div>
            <div>
                <label>Encounter Date:</label>
                <input
                    class="nonClaimsInp"
                    type="date"
                    v-model="claim.encounter_date"
                    required
                />
            </div>
            <br />
            <div>
                <label>Claim Items:</label>
                <button @click="addItem">Add Item</button>
                <table>
                    <thead v-if="claim.items.length > 0">
                        <tr>
                            <th>Name</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Sub Total</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(item, index) in claim.items" :key="index">
                            <th>
                                <input
                                    class="claimsInp"
                                    type="text"
                                    v-model="item.name"
                                    placeholder="Item Name"
                                />
                            </th>
                            <th>
                                <input
                                    class="claimsInp"
                                    type="number"
                                    v-model="item.unit_price"
                                    placeholder="Unit Price"
                                    @input="calculateSubtotals"
                                />
                            </th>
                            <th>
                                <input
                                    class="claimsInp"
                                    type="number"
                                    v-model="item.quantity"
                                    placeholder="Quantity"
                                    @input="calculateSubtotals"
                                />
                            </th>
                            <th>
                                <input
                                    class="claimsInp"
                                    type="number"
                                    v-model="item.sub_total"
                                    placeholder="Subtotal"
                                    readonly
                                />
                            </th>
                            <th>
                                <button @click="removeItem(index)">
                                    Remove
                                </button>
                            </th>
                        </tr>
                    </tbody>
                    <tfoot v-if="claim.items.length > 0">
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <input
                                    class="claimsInp"
                                    type="number"
                                    v-model="claim.total"
                                    placeholder="Total"
                                    readonly
                                />
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div>
                <label>Specialty:</label>
                <select class="nonClaimsInp" v-model="claim.specialty">
                    <option value="cardiology">Cardiology</option>
                    <option value="oncology">Oncology</option>
                    <option value="orthopedics">Orthopedics</option>
                </select>
            </div>
            <div>
                <label>Priority Level:</label>
                <select class="nonClaimsInp" v-model="claim.priority_level">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <button type="submit">Submit Claim</button>
        </form>
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

export default {
    data() {
        return {
            claim: {
                insurer_code: "",
                provider_name: "",
                encounter_date: "",
                items: [],
                specialty: "",
                priority_level: "",
                total: 0,
            },
        };
    },

    methods: {
        submitClaim() {
            
            axios
                .post("/api/claims", this.claim)
                .then((response) => {
                    alert(response.data);
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        addItem() {
            this.claim.items.push({
                name: "",
                unit_price: 0,
                quantity: 0,
                sub_total: 0,
            });
        },
        removeItem(index) {
            this.claim.items.splice(index, 1);
            this.calculateSubtotals();
        },
        calculateTotal() {
            this.claim.total = this.claim.items.reduce(
                (acc, item) => acc + item.sub_total,
                0
            );
        },
        calculateSubtotals() {
            this.claim.items.forEach((item) => {
                item.sub_total = item.unit_price * item.quantity;
            });
            this.calculateTotal();
        },
    },
};
</script>

<style scoped>
/* Basic Styles */
body {
    font-family: sans-serif;
    margin: 0;
    padding: 0;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    width: 600px; /* Adjust width as needed */
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.nonClaimsInp {
    width: auto;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    margin-bottom: 15px;
}

.claimsInp {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    margin-bottom: 15px;
}

button[type="submit"] {
    background-color: #4caf50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

button[type="submit"]:hover {
    background-color: #45a049;
}

/* Claim Items */
.claim-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.claim-items li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.claim-items li input[type="text"],
.claim-items li input[type="number"] {
    width: calc(50% - 20px); /* Adjust based on button width */
}

.claim-items li button {
    background-color: #f00;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.claim-items li button:hover {
    background-color: #c00;
}
</style>
