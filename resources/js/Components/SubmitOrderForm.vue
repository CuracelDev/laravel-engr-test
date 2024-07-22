<script setup>
import { reactive, computed, ref, watch } from 'vue'
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    hmos: {
        type: Array,
        required: true
    },
});

const isBusy = ref(false)
const successMessage = ref('')
const errorList = ref({})
const errorClass = computed(() => Object.keys(errorList.value).length > 0 ? '' : 'hidden')

const form = reactive({
    hmo_code: '',
    provider_name: '',
    encounter_date: '',
    items: [{ name: '', unit_price: 0, quantity: 0, sub_total: 0 }]
})

const addItem = () => {
    form.items.push({ name: '', unit_price: 0, quantity: 0, sub_total: 0 })
}

const removeItem = (index) => {
    console.log('removing item at index: ', index)
    form.items.splice(index, 1)
}

const calculateSubtotal = (item) => {
    item.sub_total = item.unit_price * item.quantity
}

const totalAmount = computed(() => {
    return form.items.reduce((total, item) => total + item.sub_total, 0)
})

const submitOrder = () => {
    isBusy.value = true
    axios.post(route('order.store'), form)
        .then(response => {
            successMessage.value = response.data.message;
            form.hmo_code = '';
            form.provider_name = '';
            form.encounter_date = '';
            form.items = [{ name: '', unit_price: 0, quantity: 0, sub_total: 0 }];
        }).catch(error => {
            errorList.value = error.errors;
        }).finally(() => {
            isBusy.value = false;
        });
}

watch(() => form.items, (items) => {
    items.forEach(calculateSubtotal)
}, { deep: true })

</script>

<template>
        <form @submit.prevent="submitOrder">
            <p class="text-center text-green-500 text-base">{{ successMessage }}</p>
            <div class="{{ errorClass }}">
                <div v-for="(er, key) in error">
                    <span class="text-red-500">{{ key }} :</span><span>{{ error[key][0] }}</span>
                </div>
            </div>
            <div class="card-body flex flex-col gap-6 py-10">
                <div class="">
                    <InputLabel value="Provider Name"/>
                    <TextInput v-model="form.provider_name" name="provider_name" class="w-full" autofocus />
                </div>
                <div class="flex gap-2">
                    <div class="w-2/3">
                        <InputLabel value="Select HMO"/>
                        <select v-model="form.hmo_code" name="hmo_code" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                            <option value="" disabled>Select HMO</option>
                            <option v-for="hmo in props.hmos" :key="hmo.id" :value="hmo.code">
                                {{ hmo.code }}
                            </option>
                        </select>
                    </div>
                    <div class="w-1/3">
                        <InputLabel value="Encounter Date"/>
                        <input type="date" v-model="form.encounter_date" name="encounter_date" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                    </div>
                </div>
            
                <div class="flex flex-col gap-2">
                    <div class="flex gap-5">
                        <InputLabel value="Item" class="w-3/12"/>
                        <InputLabel value="Unit Price" class="w-3/12"/>
                        <InputLabel value="Qty" class="w-2/12"/>
                        <InputLabel value="Sub Total" class="w-3/12"/>
                        <InputLabel value="" class="w-1/12"/>
                    </div>
                    <div v-for="(item, index) in form.items" :key="index" class="flex w-full gap-2 order-item">
                        <input v-model="item.name" placeholder="Item Name" name="name" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-5/12">
                        <input type="number" v-model.number="item.unit_price" name="unit_price" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-3/12" placeholder="Unit Price" required>
                        <input type="number" v-model.number="item.quantity" name="quantity" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-2/12" placeholder="Quantity" required>
                        <input type="number" v-model.number="item.sub_total" name="sub_total" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed read-only:opacity-50 rounded-md shadow-sm w-3/12" required readonly>
                        <button @click="removeItem(index)" id="remove-item" :disabled="form.items.length === 1" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-md text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out disabled:opacity-50 duration-150 w-1/12">
                            -
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <button @click="addItem" id="add-item" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 w-1/4">
                        Add Item
                    </button>
                    <div class="flex w-2/4 items-center justify-between">
                        <p class="">Total Amount:</p>
                        <input type="number" :value="totalAmount" id="total" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-75 rounded-md shadow-sm" required disabled>
                    </div>
                </div>
                
                <button type="submit" :disabled="isBusy" class="inline-flex items-center justify-center self-center px-4 py-2 bg-green-600 disabled:opacity-50 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 w-1/4">Submit Order</button>
            </div>
        </form>
</template>
