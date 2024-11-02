<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Drawer from "@/Components/Drawer.vue";
import ViewOrder from "./ViewOrder.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { reactive } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    orders: Array,
    hmo: Object,
    filter: Object
});

const response = reactive({
    success: false,
    successMessage: "",
});

const viewedOrder = reactive({
    status: false,
    order: null
});

function closeDrawer() {
    this.viewedOrder.status = false;
}

function handleViewOrder(order) {
    this.viewedOrder.status = true;
    this.viewedOrder.order = order;
}


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

function processBatch() {
    const batch = useForm({
        batch_by: 'encounter_date',
        start_date: props.filter.start_date,
        end_date: props.filter.end_date
    });
    batch.post(`/dashboard/orders/${ props.hmo.id}/process`, {
        onSuccess: (res) => {
            response.success = true;
            response.successMessage = "Order processed successfully.";
        },
    });
}

</script>

<template>

    <Head title="Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"> {{ hmo.name }}'s Orders</h2>
        </template>



        <div class="bg-white shadow max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 mt-4">
            <div class="w-full mx-auto p-4 flex gap-4 items-end">
                <!-- Dropdown Select -->
                <div class="mt-4 w-fit">
                    <label for="batchType" class="block text-sm font-medium text-gray-700 mb-2">Select Batch
                        Type</label>
                    <select id="batchType" v-model="filter.batch_by"
                        class="block w-80 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="encounter_date" selected>Batch by Encounter Date</option>
                        <option value="date_created">Batch by Date Created</option>
                    </select>
                </div>

                <!-- Date Range Selector -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date Range</label>
                    <div class="flex space-x-4">
                        <!-- Start Date -->
                        <input type="date" id="startDate" v-model="filter.start_date"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Start Date">
                        <!-- End Date -->
                        <input type="date" id="endDate" v-model="filter.end_date"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="End Date">
                    </div>
                </div>
                <Link
                    :href="`/dashboard/orders/${hmo.id}?batch_by=${filter.batch_by}&start_date=${filter.start_date}&end_date=${filter.end_date}`"
                    class=" py-2 px-4 bg-blue-400 text-white font-semibold rounded-md shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Apply</Link>
            </div>
            <p v-if="response.success" class="bg-green-100 border border-green-300 text-green-800 rounded-lg p-4 mb-4">
                {{ response.successMessage }}
            </p>
            <div class="flex flex-row items-center mt-10">
                <table class="divide-y divide-gray-200 overflow-x-auto w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provide Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Encounter Data
                            </th>

                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Created
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ order.provider_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Total Items: {{ order.items_count }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ formatDate(order.encounter_date) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatMoney(order.amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" @click.prevent="handleViewOrder(order)"
                                    class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>

                        <!-- More rows... -->
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-8">
                <button @click="processBatch()"
                    class=" w-36 py-2 px-4 bg-blue-400 text-white font-semibold rounded-md shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Process
                    Batch</button>
            </div>
        </div>
        <div class="flex space-x-2 mt-4 justify-center">
            <Link v-for="link in orders.links" :key="link.url" :href="link.url"
                :class="{ 'bg-blue-500 text-white': parseInt(link.label) === orders.current_page, 'bg-gray-200': link.url !== orders.path }"
                class="px-3 py-1 rounded hover:bg-gray-300">
            <span v-html="link.label"></span>
            </Link>
        </div>
        <drawer :open="viewedOrder.status" @close="closeDrawer()">
            <template #content>
                <view-order :order="viewedOrder.order" />
            </template>
        </drawer>
    </AuthenticatedLayout>
</template>
