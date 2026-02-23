<template>
    <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

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
