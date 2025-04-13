<template>
    <GuestLayout>
        <Head title="Claims"/>

        <div class="card-header">Claim</div>
        <Link :href="route('claim.create')">Create Claim</Link>

        <div class="card-body">Your Claims</div>
    </GuestLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

console.log("claims page loaded");
</script>
