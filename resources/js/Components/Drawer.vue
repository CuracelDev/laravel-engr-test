<template>
    <div>
        <transition name="fade">
            <div
                v-if="open"
                class="fixed inset-0 bg-black bg-opacity-50 z-10"
                @click="$emit('close')"
            ></div>
        </transition>

        <transition name="slide">
            <div
                v-if="open"
                class="fixed top-0 right-0 h-full bg-white shadow-lg z-20 min-w-96"
            >
                <div class="p-4 flex flex-col justify-between h-full">
                    <div>
                        <slot name="content" />
                    </div>
                    <button
                        @click="$emit('close')"
                        class="mt-4 p-2 bg-red-500 text-white rounded"
                    >
                        Close
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
defineProps({
    open: Boolean,
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}
.slide-enter {
    transform: translateX(100%);
}
.slide-leave-to {
    transform: translateX(100%);
}
</style>
