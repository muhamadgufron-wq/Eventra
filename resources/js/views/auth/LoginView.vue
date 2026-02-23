<template>
    <div class="min-h-screen flex flex-col lg:flex-row overflow-hidden bg-white">
        <!-- Left Side: Branding & Marketing -->
        <div class="hidden lg:flex lg:w-1/2 bg-emerald-800 relative flex-col justify-center px-16 xl:px-24">
            <div class="space-y-12 relative z-10">
                <!-- Logo Branding -->
                <div class="mb-12">
                    <div class="w-64 h-32 rounded-[1rem] bg-white/10 backdrop-blur-xl p-2 border border-white/20 shadow-2xl overflow-hidden flex items-center justify-center">
                        <img src="/images/logo.png" alt="Logo" class="w-full h-full object-contain filter brightness-0 invert scale-125">
                    </div>
                </div>

                <div class="max-w-md space-y-6">
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                        Elevate your wedding planning experience.
                    </h1>
                    <p class="text-white/80 text-lg leading-relaxed font-medium">
                        Kelola vendor, jadwal, dan klien dalam satu dashboard terintegrasi yang dirancang khusus untuk Wedding Organizer profesional.
                    </p>
                    
                    <!-- Decorative Lines -->
                    <div class="flex gap-4 pt-4">
                        <div class="h-1 w-20 bg-white/20 rounded-full"></div>
                        <div class="h-1 w-24 bg-white/50 rounded-full"></div>
                        <div class="h-1 w-20 bg-white/20 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Footer Branding (Bottom) -->
            <div class="absolute bottom-10 left-16 xl:left-24 right-10 flex items-center justify-between text-[10px] font-black uppercase tracking-[0.2em] text-white/40">
                <div class="flex items-center gap-8">
                    <span>Enterprise Ready</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
                    <span>Secure Cloud Sync</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
                    <span>Multi-User Access</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex-1 flex flex-col justify-center items-center bg-[#f8fafc] p-6 lg:p-12 relative">
            <div class="w-full max-w-[420px] space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <!-- Login Card -->
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-surface-200/50 p-10 lg:p-12 border border-surface-100">
                    <div class="text-center mb-10">
                        <h2 class="text-2xl font-bold text-surface-900 mb-2">Masuk ke Akun Anda</h2>
                        <p class="text-sm text-surface-400 font-medium">Silakan masukkan detail akun Anda di bawah ini.</p>
                    </div>

                    <form @submit.prevent="handleLogin" class="space-y-6">
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-surface-500 ml-1">Email</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-surface-400 group-focus-within:text-primary-600 transition-colors">
                                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <input v-model="form.email" type="email" required
                                       class="block w-full pl-11 pr-4 py-3 bg-surface-50 border border-surface-100 rounded-xl text-surface-900 text-sm font-semibold placeholder-surface-300 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-500 transition-all"
                                       placeholder="admin@womanager.com" />
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between px-1">
                                <label class="block text-xs font-bold text-surface-500">Kata Sandi</label>
                                <a href="#" class="text-[10px] font-black text-emerald-600 uppercase tracking-wider hover:underline">Lupa password?</a>
                            </div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-surface-400 group-focus-within:text-primary-600 transition-colors">
                                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                                       class="block w-full pl-11 pr-11 py-3 border rounded-xl text-surface-900 text-sm font-semibold placeholder-surface-300 focus:outline-none focus:ring-4 focus:ring-primary-500/10 transition-all"
                                       :class="error ? 'bg-red-50/50 border-red-200' : 'bg-surface-50 border-surface-100 focus:bg-white focus:border-primary-500'"
                                       placeholder="••••••••" />
                                <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-surface-400 hover:text-surface-600 transition-colors">
                                    <svg v-if="!showPassword" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.301 9.573 7.108 6 12 6c4.892 0 8.699 3.573 9.964 5.678.11.232.11.503 0 .736C20.699 14.427 16.892 18 12 18c-4.892 0-8.699-3.573-9.964-5.678Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <!-- Inline Error Message -->
                            <div v-if="error" class="flex items-center gap-1.5 px-1 py-1 text-[11px] font-bold text-red-500 animate-in fade-in slide-in-from-top-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <span>{{ error }}</span>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center px-1">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" class="w-4 h-4 rounded border-surface-300 text-emerald-600 focus:ring-emerald-500/20 transition-all" />
                                <span class="text-xs font-semibold text-surface-500 group-hover:text-surface-700">Ingat saya</span>
                            </label>
                        </div>

                        <button type="submit" :disabled="authStore.loading"
                                class="w-full flex items-center justify-center gap-2 py-4 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-700 text-white rounded-2xl font-bold shadow-xl shadow-emerald-500/20 transition-all transform active:scale-[0.98]">
                            <span v-if="authStore.loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white/50" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang Masuk...
                            </span>
                            <span v-else>Masuk ke Akun Anda</span>
                        </button>
                    </form>

                    <!-- Footer Link -->
                    <div class="mt-10 text-center">
                        <p class="text-xs font-semibold text-surface-400">
                            Belum memiliki akun? <a href="#" class="text-emerald-600 font-bold hover:underline">Hubungi sales kami</a>
                        </p>
                    </div>
                </div>

                <!-- Copyright Footer (Bottom of Column) -->
                <div class="text-center pt-4">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-surface-400">
                        &copy; 2026 WO MANAGER. ALL RIGHTS RESERVED.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    email: '',
    password: '',
})

const error = ref('')
const showPassword = ref(false)

const handleLogin = async () => {
    error.value = ''
    try {
        await authStore.login(form)
        router.push({ name: 'dashboard' })
    } catch (e) {
        error.value = e.response?.data?.message || 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.'
    }
}
</script>
