<script setup>
const links = [
  { href: '/claims', label: 'Submit Claim' },
  { href: '/batches', label: 'View Batches' },
]
const isActive = (href) => window.location.pathname === href
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">
    <!-- Navbar -->
    <header class="bg-white border-b">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <a href="/" class="text-lg font-semibold">Curacel Test</a>
        <nav class="flex gap-4">
          <a
            v-for="l in links" :key="l.href" :href="l.href"
            class="px-3 py-1.5 rounded-md text-sm"
            :class="isActive(l.href)
              ? 'bg-gray-900 text-white'
              : 'text-gray-700 hover:bg-gray-100'"
          >
            {{ l.label }}
          </a>
        </nav>
      </div>
    </header>

    <!-- Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <slot />
    </main>
  </div>
</template>