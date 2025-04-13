<template>
    <GuestLayout>
        <Head title="Submit Claim" />

        <div class="card-header">Submit A Claim</div>

        <div class="card-body">
            <form @submit.prevent="submit">
                <SelectInput
                    id="insurer"
                    v-model="form.insurer"
                    :options="
                        insurers.map((insurer) => ({
                            value: insurer.id,
                            label: insurer.name,
                        }))
                    "
                    placeholder="Select an Insurer"
                    @change="form.validate('insurer')"
                />
                <InputError class="mt-2" v-if="form.invalid('insurer')" :message="form.errors?.insurer" />

                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="John Doe"
                    @change="form.validate('name')"
                />
                <InputError class="mt-2" v-if="form.invalid('name')" :message="form.errors?.name" />

                <InputLabel for="date" value="Date" />
                <TextInput
                    id="date"
                    class="mt-1 block w-full"
                    v-model="form.date"
                    required
                    autofocus
                    type="date"
                    @change="form.validate('date')"
                />
                <InputError class="mt-2" v-if="form.invalid('date')" :message="form.errors?.date" />

                <InputLabel for="priority" value="Priority" />
                <SelectInput
                    v-model="form.priority"
                    id="priority"
                    :options="
                        priorities.map((priority) => ({
                            value: priority,
                            label: priority,
                        }))
                    "
                    @change="form.validate('priority')"
                />
                <InputError class="mt-2" v-if="form.invalid('priority')" :message="form.errors?.priority" />

                <InputLabel for="specility" value="Speciality" />
                <SelectInput
                    v-model="form.specialty"
                    id="specialty"
                    placeholder="Select a Specialty"
                    :options="
                        specialties.map((specialty) => ({
                            value: specialty,
                            label: specialty,
                        }))
                    "
                    @change="form.validate('specialty')"
                />
                <InputError class="mt-2" v-if="form.invalid('specialty')" :message="form.errors?.specialty" />

                <ClaimItem
                    v-model="form.items"
                    @change="form.validate('items')"
                />
                <InputError class="mt-2" v-if="form.invalid('items')" :message="form.errors?.items" />
                <InputError class="mt-2" :message="form.errors['items.0.name']" />
                <InputError class="mt-2" :message="form.errors['items.0.unitPrice']" />
                <InputError class="mt-2" :message="form.errors['items.0.quantity']" />

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Submit
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<script setup>
import { onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from 'laravel-precognition-vue'
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ClaimItem from "@/Components/ClaimItem.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";

const { insurers, priorities, specialties } = defineProps({
    insurers: Array,
    priorities: Array,
    specialties: Array,
});

const form = useForm('post', route("claim.store"), {
    name: "",
    insurer: null,
    priority: 1,
    specialty: null,
    items: [],
    date: "",
});

function submit() {
    form.submit();
}

console.log("submit claim page loaded");
</script>
