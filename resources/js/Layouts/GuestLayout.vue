<template>
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900 transition-all duration-300 ease-in-out">
        <!-- Navbar Section with Logo and Dark Mode Toggle -->
        <div class="w-full bg-gray-600 dark:bg-gray-900 p-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <Link href="/" class="flex items-center">
                    <ApplicationLogo
                        class="w-20 h-20 fill-current text-gray-500 dark:text-gray-200 transition-all duration-300 ease-in-out transform hover:scale-110"
                    />
                </Link>

                <!-- Dark Mode Toggle -->
                <button @click="toggleTheme" class="p-3 rounded-md bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 transition-all duration-300 ease-in-out hover:bg-gray-300 dark:hover:bg-gray-500 focus:ring-2 focus:ring-blue-500">
                    <!-- Sun icon for light mode, Moon icon for dark mode -->
                    <SunIcon v-if="!isDarkMode" class="w-6 h-6 text-yellow-500" />
                    <MoonIcon v-if="isDarkMode" class="w-6 h-6 text-gray-200" />
                </button>
            </div>
        </div>

        <!-- Main Content Section (Centered Card Layout) -->
        <div class="flex justify-center items-center w-full py-10">
            <div class="w-full sm:max-w-4xl">
                <!-- Slot for page content, allowing it to dynamically display content from child components -->
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { SunIcon, MoonIcon } from '@heroicons/vue/24/solid';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const isDarkMode = ref(document.documentElement.classList.contains('dark'));

const toggleTheme = () => {
    const currentTheme = document.documentElement.classList.contains('dark');
    if (currentTheme) {
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
    }

    // Update dark mode state
    isDarkMode.value = !currentTheme;
};
</script>
