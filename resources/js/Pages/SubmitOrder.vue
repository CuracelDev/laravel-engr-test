<template>
    <GuestLayout>
        <Head title="Submit Order" />
        <h2 class="text-2xl font-bold mb-4 text-center">Submit an Order</h2>

        <p
            v-if="response.success"
            class="bg-green-100 border border-green-300 text-green-800 rounded-lg p-4 mb-4"
        >
            {{ response.successMessage }}
        </p>
        <form @submit.prevent="submitForm()">
            <div class="grid grid-cols-3 gap-4">
                <div class="mb-4">
                    <label
                        for="hmo_code"
                        class="block font-medium text-gray-700"
                        >Receiving HMO Code:</label
                    >
                    <input
                        v-model="order.hmo_code"
                        type="text"
                        id="hmo_code"
                        placeholder="Enter HMO code"
                        class="w-full mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"
                    />
                    <div v-if="errors.hmo_code" class="text-rose-500">
                        {{ errors.hmo_code }}
                    </div>
                </div>

                <div class="mb-4">
                    <label
                        for="provider_name"
                        class="block font-medium text-gray-700"
                        >Provider Name:</label
                    >
                    <input
                        v-model="order.provider_name"
                        type="text"
                        id="provider_name"
                        placeholder="Enter your name"
                        class="w-full mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"
                    />
                    <div v-if="errors.provider_name" class="text-rose-500">
                        {{ errors.provider_name }}
                    </div>
                </div>

                <div class="mb-4">
                    <label
                        for="encounter_date"
                        class="block font-medium text-gray-700"
                        >Encounter Date:</label
                    >
                    <input
                        v-model="order.encounter_date"
                        type="date"
                        id="encounter_date"
                        class="w-full mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"
                    />
                    <div v-if="errors.encounter_date" class="text-rose-500">
                        {{ errors.encounter_date }}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2">Order Items</h3>

                <div class="flex items-center gap-2 mb-2">
                    <label class="w-1/3">Item</label>
                    <label class="w-1/5">Unit Price</label>
                    <label class="w-1/5">Qty</label>
                    <label class="w-1/5">Sub Total</label>
                </div>
                <div v-for="(item, index) in order.items" :key="index">
                    <div class="flex items-center gap-2 mb-2">
                        <input
                            v-model="item.title"
                            type="text"
                            placeholder="Item Name"
                            class="w-1/3 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"
                        />
                        <input
                            v-model.number="item.unit_price"
                            type="number"
                            placeholder="Unit Price"
                            class="w-1/5 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"
                        />
                        <input
                            v-model.number="item.quantity"
                            type="number"
                            placeholder="Quantity"
                            class="w-1/5 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300"
                        />
                        <input
                            :value="calculateSubtotal(item)"
                            type="number"
                            readonly
                            class="w-1/5 p-2 border border-gray-200 rounded bg-gray-100 text-gray-500"
                        />
                        <button
                            @click="removeItem(index)"
                            type="button"
                            class="text-red-500 hover:bg-red-700 hover:text-white rounded font-bold px-4 py-2 h-full border border-gray-200"
                        >
                            -
                        </button>
                    </div>
                    <div
                        v-if="errors[`items.${index}.title`]"
                        class="text-rose-500"
                    >
                        {{ errors[`items.${index}.title`] }}
                    </div>
                    <div
                        v-if="errors[`items.${index}.unit_price`]"
                        class="text-rose-500"
                    >
                        {{ errors[`items.${index}.unit_price`] }}
                    </div>
                    <div
                        v-if="errors[`items.${index}.quantity`]"
                        class="text-rose-500"
                    >
                        {{ errors[`items.${index}.quantity`] }}
                    </div>
                    <div
                        v-if="errors[`items.${index}.sub_total`]"
                        class="text-rose-500"
                    >
                        {{ errors[`items.${index}.sub_total`] }}
                    </div>
                </div>
            </div>

            <div class="flex flex-row items-center justify-between mb-4">
                <button
                    @click="addItem"
                    type="button"
                    class="mt-2 bg-blue-500 text-white py-3 px-6 rounded hover:bg-blue-600"
                    title="Add new order item"
                >
                    +
                </button>
                <div class="flex gap-4 items-center justify-end">
                    <label class="block font-medium text-gray-700"
                        >Order Amount:</label
                    >
                    <input
                        :value="calculateOrderAmount()"
                        type="number"
                        readonly
                        class="w-6/12 mt-1 p-2 border border-gray-300 rounded bg-gray-100 text-gray-500"
                    />
                </div>
            </div>

            <button
                class="px-4 py-2 bg-green-500 text-white font-semibold rounded flex items-center justify-center float-end"
                :class="{ 'bg-blue-300 cursor-not-allowed': order.processing }"
                :disabled="order.processing"
                type="submit"
            >
                <span
                    v-if="order.processing"
                    class="animate-spin border-2 border-t-transparent border-white rounded-full w-4 h-4 mr-2"
                ></span>
                <span v-if="!order.processing"> Submit Order</span>
                <span v-else>Loading...</span>
            </button>
        </form>
    </GuestLayout>
</template>
<script setup>
import { Head } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { reactive } from "vue";

defineProps({
    errors: Object,
});

const response = reactive({
    success: false,
    successMessage: "",
});

const order = useForm({
    hmo_code: "",
    provider_name: "",
    encounter_date: "",
    items: [{ title: "", unit_price: 0, quantity: 1, sub_total: 0 }],
    amount: 0,
});

function addItem() {
    order.items.push({ name: "", unit_price: 0, quantity: 1 });
}

function removeItem(index) {
    order.items.splice(index, 1);
}

function calculateSubtotal(item) {
    let subTotal = item.unit_price * item.quantity;
    item.sub_total = subTotal;
    return subTotal;
}

function calculateOrderAmount() {
    let orderAmount = order.items.reduce(
        (total, item) => total + calculateSubtotal(item),
        0
    );

    order.amount = orderAmount;
    return orderAmount;
}

function submitForm() {
    order.post("/provider/order", {
        onSuccess: (res) => {
            response.success = true;
            response.successMessage = "Order created successfully.";
            this.order = useForm({
                hmo_code: "",
                provider_name: "",
                encounter_date: "",
                items: [
                    { title: "", unit_price: 0, quantity: 1, sub_total: 0 },
                ],
                amount: 0,
            });
        },
    });
}
</script>
