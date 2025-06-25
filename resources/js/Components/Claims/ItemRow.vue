<template>
    <div class="flex gap-2 items-start mb-3">
        <div class="w-1/3 flex flex-col">
            <InputLabel value="Name" /> 
            <TextInput v-model="item.name" type="text" @change="validateItem(index, 'name')" placeholder="Dietician"
                class="w-full" />
            <InputError class="h-4" v-if="form.invalid(`items.${index}.name`)"
                :message="form.errors[`items.${index}.name`]" />
        </div>

        <div class="w-1/3 flex flex-col">
            <InputLabel value="Unit Price" />
            <TextInput v-model.number="item.unit_price" type="number" @change="validateItem(index, 'unit_price')"
                placeholder="5000" class="w-full" />
            <InputError class="h-4" v-if="form.invalid(`items.${index}.unit_price`)"
                :message="form.errors[`items.${index}.unit_price`]" />
        </div>

        <div class="w-1/3 flex flex-col">
            <InputLabel value="Quantity" />
            <TextInput v-model.number="item.quantity" type="number" @change="validateItem(index, 'quantity')"
                placeholder="1" class="w-full" />
            <InputError class="h-4" v-if="form.invalid(`items.${index}.quantity`)"
                :message="form.errors[`items.${index}.quantity`]" />
        </div>
        <button v-if="form.items.length > 1" @click="$emit('remove')"
            class="text-red-600 font-semibold bg-red-100 px-3 py-1 rounded hover:bg-red-200 mt-6">
            -
        </button>
    </div>
</template>

<script setup>
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
    item: Object,
    index: Number,
    form: Object,
    canRemove: {
        type: Boolean,
        default: true,
    }
})

const validateItem = (index, field) => props.form.validate(`items.${index}.${field}`)
</script>