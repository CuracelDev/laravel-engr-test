<script setup>
import { reactive, computed, ref, watch } from 'vue'
import { Head, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import SubmitOrderForm from "@/Components/SubmitOrderForm.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

console.log('submit order page loaded')

const props = defineProps({
    hmos: {
        type: Array,
        required: true
    },
});

const form = reactive({
    hmo_code: '',
    provider_name: '',
    encounter_date: '',
    items: [{ name: '', unit_price: 0, quantity: 0, sub_total: 0 }]
})

watch(() => form.items, (items) => {
    items.forEach(calculateSubtotal)
}, { deep: true })

</script>

<template>
    <GuestLayout>
        <Head title="Submit Order" />

        <SubmitOrderForm :hmos="hmos" />
    </GuestLayout>
</template>
