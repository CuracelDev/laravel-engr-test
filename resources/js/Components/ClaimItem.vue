<template>
    <table class="w-full border-collapse my-4">
        <thead>
            <tr>
                <th class="border border-white px-2 py-1 text-center">Item</th>
                <th class="border border-white px-2 py-1 text-center">Unit Price</th>
                <th class="border border-white px-2 py-1 text-center">Qty</th>
                <th class="border border-white px-2 py-1 text-center">Sub Total</th>
                <th class="border border-white px-2 py-1 text-center"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in items" :key="index">
                <td class="border border-white px-2 py-1">
                    <input
                        type="text"
                        v-model="item.name"
                        placeholder="Item name"
                        @input="updateItems"
                        class="w-full border border-gray-300 p-1 box-border"
                    />
                </td>
                <td class="border border-white px-2 py-1">
                    <input
                        type="number"
                        v-model.number="item.unit_price"
                        placeholder="Unit price"
                        @input="updateItems"
                        class="w-full border border-gray-300 p-1 box-border"
                    />
                </td>
                <td class="border border-white px-2 py-1">
                    <input
                        type="number"
                        v-model.number="item.quantity"
                        placeholder="Quantity"
                        @input="updateItems"
                        class="w-full border border-gray-300 p-1 box-border"
                    />
                </td>
                <td class="border border-white px-2 py-1">
                    <input
                        type="text"
                        :value="calculateSubTotal(item)"
                        readonly
                        class="w-full border border-gray-300 p-1 box-border bg-gray-100"
                    />
                </td>
                <td class="border border-white px-2 py-1">
                    <button
                        @click="removeItem(index)"
                        class="border border-black px-2 py-1 cursor-pointer"
                    >
                        -
                    </button>
                </td>
            </tr>
            <tr>
                <td class="flex justify-start border border-white px-2 py-1">
                    <button
                        @click="addItem"
                        class="border border-black px-2 py-1 cursor-pointer"
                    >
                        +
                    </button>
                </td>
                <td class="border border-white px-2 py-1"></td>
                <td class="border border-white px-2 py-1 font-bold text-right">
                    Total:
                </td>
                <td class="border border-white px-2 py-1">
                    <input
                        type="text"
                        :value="calculateTotal"
                        readonly
                        class="w-full border border-gray-300 p-1 box-border bg-gray-100"
                    />
                </td>
                <td class="border border-white px-2 py-1"></td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { onMounted } from "vue";
import { ref, computed } from "vue";

const { modelValue } = defineProps({
    modelValue: {
        type: Array,
        required: true,
    },
});

onMounted(() => {
    console.log("modelValue: ", modelValue);
    if (!modelValue || !Array.isArray(modelValue)) {
        modelValue = [];
    }

    if (modelValue.length === 0) {
        addItem();
    }
});

const emit = defineEmits(["update:modelValue"]);
const items = ref(modelValue);

function addItem() {
    items.value.push({ name: "", unit_price: 0, quantity: 0 });
    updateItems();
}

function removeItem(index) {
    items.value.splice(index, 1);
    updateItems();
}

function calculateSubTotal(item) {
    return (item.unit_price * item.quantity).toFixed(2);
}

function updateItems() {
    emit("update:modelValue", items.value);
}

const calculateTotal = computed(() => {
    return items.value
        .reduce((total, item) => total + item.unit_price * item.quantity, 0)
        .toFixed(2);
});
</script>
