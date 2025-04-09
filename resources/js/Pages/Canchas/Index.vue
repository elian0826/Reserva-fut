<template>
    <AppLayout>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Canchas Disponibles</h2>
                    <Link v-if="$page.props.auth.user.is_admin"
                          :href="route('canchas.create')"
                          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Nueva Cancha
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="cancha in canchas" :key="cancha.id"
                         class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ cancha.nombre }}</h3>
                            <p class="text-gray-600 mb-4">{{ cancha.descripcion }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-gray-800">${{ cancha.precio }}/hora</span>
                                <button @click="reservar(cancha)"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition">
                                    Reservar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    canchas: {
        type: Array,
        required: true
    }
})

const reservar = (cancha) => {
    // Implementar lógica de reserva
}
</script>

    <AppLayout>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Canchas Disponibles</h2>
                    <Link v-if="$page.props.auth.user.is_admin"
                          :href="route('canchas.create')"
                          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Nueva Cancha
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="cancha in canchas" :key="cancha.id"
                         class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ cancha.nombre }}</h3>
                            <p class="text-gray-600 mb-4">{{ cancha.descripcion }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-gray-800">${{ cancha.precio }}/hora</span>
                                <button @click="reservar(cancha)"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition">
                                    Reservar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    canchas: {
        type: Array,
        required: true
    }
})

const reservar = (cancha) => {
    // Implementar lógica de reserva
}
</script>
