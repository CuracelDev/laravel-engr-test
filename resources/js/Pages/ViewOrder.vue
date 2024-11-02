<script setup>
defineProps({
    order: Object,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function formatMoney(amount) {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "NGN",
    }).format(amount);
}
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Order Details</h2>
        <hr />
        <div class="py-4">
            <div class="py-2 whitespace-nowrap">
                <div class="flex">
                    <div>
                        <div class="text-sm text-gray-500">Order Provider</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ order.provider_name }}
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <div class="py-2 whitespace-nowrap">
                <div class="flex">
                    <div>
                        <div class="text-sm text-gray-500">Encounter Date</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ formatDate(order.encounter_date) }}
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <div class="py-2 whitespace-nowrap">
                <div class="flex">
                    <div>
                        <div class="text-sm text-gray-500">Date Created</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ formatDate(order.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <div class="py-2 whitespace-nowrap">
                <div class="flex">
                    <div>
                        <div class="text-sm text-gray-500">HMO</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ order.hmo.name }} {{ order.hmo.code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-2 whitespace-nowrap">
                <div class="flex">
                    <div>
                        <div class="text-sm text-gray-500">Amount</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ formatMoney(order.amount) }}
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <h2 class="text-xl font-semibold mt-6 mb-4">Order Items</h2>
            <hr />

            <table class="divide-y divide-gray-200 overflow-x-auto w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Title
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit Price
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sub Total
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in order.items" :key="item.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ item.title }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ formatMoney(item.unit_price) }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ item.quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatMoney(item.sub_total) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
