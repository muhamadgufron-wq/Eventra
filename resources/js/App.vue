<template>
    <router-view />
    <Toaster />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { Toaster } from '@/components/ui/sonner'
import 'vue-sonner/style.css'

const authStore = useAuthStore()

onMounted(async () => {
    // Auth is handled by router guards in @/router/index.js
})
</script>

<style>
#app {
    min-height: 100vh;
}
</style>
