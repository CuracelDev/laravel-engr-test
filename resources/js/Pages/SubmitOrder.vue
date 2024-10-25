<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-3xl min-h-[500px]">
            <div>
                <Link href="/">
                    <ApplicationLogo
                        class="w-20 h-20 fill-current text-gray-500"
                    />
                </Link>
            </div>
            <div class="p-4 bg-white shadow-md rounded-md">
                <div
                    v-if="successMessage"
                    class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg"
                >
                    {{ successMessage }}
                </div>

                <form @submit.prevent="submitOrder">
                    <div class="flex gap-3 flex-row">
                        <div class="mb-4">
                            <label
                                for="hmo"
                                class="block text-sm font-medium text-gray-700"
                                >Select HMO</label
                            >
                            <select
                                id="hmo"
                                v-model="form.hmo_code"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option disabled value="">
                                    -- Select an HMO --
                                </option>
                                <option
                                    v-for="hmo in hmos"
                                    :key="hmo.code"
                                    :value="hmo.code"
                                >
                                    {{ hmo.name }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                for="provider"
                                class="block text-sm font-medium text-gray-700"
                                >Select Provider</label
                            >
                            <select
                                id="provider"
                                v-model="form.provider_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option disabled value="">
                                    -- Select a Provider --
                                </option>
                                <option
                                    v-for="provider in providers"
                                    :key="provider.id"
                                    :value="provider.id"
                                >
                                    {{ provider.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 flex-row mt-4">
                        <div class="mb-4">
                            <label
                                for="encounter-date"
                                class="block text-sm font-medium text-gray-700"
                                >Encounter date</label
                            >
                            <input
                                type="date"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Select date"
                                v-model="form.encounter_date"
                            />
                        </div>
                    </div>

                    <hr class="m-4" />

                    <div
                        class="flex flex-row items-center justify-between pb-2 mb-4 text-gray-700 font-semibold text-lg"
                    >
                        <div class="basis-2/5">Item</div>
                        <div class="basis-1/5 text-center">Unit Price</div>
                        <div class="basis-1/5 text-center">Qty</div>
                        <div class="basis-1/5 text-right">Sub Total</div>
                        <div class="basis-1/5 text-right"></div>
                    </div>

                    <div v-for="(item, index) in form.items" :key="index">
                        <OrderItem
                            :itemData="item"
                            @remove="removeItem(index)"
                        />
                    </div>

                    <div class="flex flex-row justify-between mt-4">
                        <button
                            type="button"
                            @click="addItem"
                            class="text-green-500 hover:text-green-700 text-sm shadow p-4 rounded"
                        >
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                        <div>
                            <h3>Total</h3>
                            <input
                                type="text"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right"
                                :value="calculateTotal()"
                                placeholder="Total"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="w-full flex justify-end mt-4">
                        <button
                            type="submit"
                            class="bg-black text-white shadow px-4 py-2 rounded"
                            :disable="form.processing"
                        >
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import OrderItem from "@/Components/Order/OrderItem.vue";

const props = defineProps({
    hmos: {
        type: Object,
        required: true,
    },
    providers: {
        type: Object,
        required: true,
    },
});

const successMessage = ref("");

const form = useForm({
    encounter_date: "",
    provider_id: "",
    hmo_code: "",
    total: 0,
    items: [{ item: "", unit_price: 0, qty: 1, subtotal: 0 }],
});

const addItem = () => {
    form.items.push({ item: "", unit_price: 0, qty: 1, subtotal: 0 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const calculateTotal = () => {
    return form.items
        .reduce((total, item) => total + (item.unit_price * item.qty || 0), 0)
        .toFixed(2);
};

const generateRandomOrderId = () => {
    return "order-" + Math.random().toString(36).substr(2, 9).toUpperCase();
};

const submitOrder = () => {
    form.order_id = generateRandomOrderId();

    form.items = form.items.map((item) => ({
        item: item.item,
        unit_price: item.unit_price,
        qty: item.qty,
        subtotal: (item.unit_price * item.qty).toFixed(2),
    }));

    form.total = calculateTotal();

    form.post("/submitOrder", {
        onSuccess: () => {
            successMessage.value = "Order submitted successfully!";
            resetForm();
        },
    });
};

const resetForm = () => {
    form.reset({
        encounter_date: "",
        provider_id: "",
        hmo_code: "",
        total: 0,
        items: [{ item: "", unit_price: 0, qty: 1, subtotal: 0 }],
    });

    setTimeout(() => {
        successMessage.value = "";
    }, 3000);
};
</script>

<style>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");
</style>
