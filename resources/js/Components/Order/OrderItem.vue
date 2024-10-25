<template>
    <div
        class="flex flex-row items-center justify-between pb-2 mb-4 text-gray-700"
    >
        <div class="basis-2/5 m-2">
            <input
                type="text"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Item"
                v-model="itemData.item"
            />
        </div>

        <div class="basis-1/5 text-center m-2">
            <input
                type="number"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-center"
                placeholder="Unit Price"
                v-model="itemData.unit_price"
                @input="updateSubtotal"
            />
        </div>

        <div class="basis-1/5 text-center m-2">
            <input
                type="number"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-center"
                placeholder="Qty"
                v-model="itemData.qty"
                @input="updateSubtotal"
            />
        </div>

        <div class="basis-1/5 text-center m-2">
            <input
                type="text"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right"
                :value="(itemData.unit_price * itemData.qty || 0).toFixed(2)"
                readonly
            />
        </div>

        <div class="basis-1/5 text-center m-2">
            <button
                @click="$emit('remove')"
                class="text-red-500 hover:text-red-700"
            >
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
    itemData: {
        type: Object,
        required: true,
    },
});

const updateSubtotal = () => {
    props.itemData.subtotal = (
        props.itemData.unit_price * props.itemData.qty || 0
    ).toFixed(2);
};
</script>
