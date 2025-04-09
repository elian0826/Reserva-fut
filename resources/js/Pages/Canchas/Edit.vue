<template>
  <AppLayout title="Editar Cancha">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Editar Cancha
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
              <input
                type="text"
                id="nombre"
                v-model="form.nombre"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              >
            </div>

            <div>
              <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
              <textarea
                id="descripcion"
                v-model="form.descripcion"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              ></textarea>
            </div>

            <div>
              <label for="precio" class="block text-sm font-medium text-gray-700">Precio por Hora</label>
              <input
                type="number"
                id="precio"
                v-model="form.precio"
                min="0"
                step="0.01"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              >
            </div>

            <div class="flex justify-end space-x-4">
              <Link
                :href="route('canchas.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
              >
                Actualizar Cancha
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  cancha: {
    type: Object,
    required: true
  }
})

const form = useForm({
  nombre: props.cancha.nombre,
  descripcion: props.cancha.descripcion,
  precio: props.cancha.precio
})

const submit = () => {
  form.put(route('canchas.update', props.cancha.id))
}
</script>

  <AppLayout title="Editar Cancha">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Editar Cancha
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
              <input
                type="text"
                id="nombre"
                v-model="form.nombre"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              >
            </div>

            <div>
              <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
              <textarea
                id="descripcion"
                v-model="form.descripcion"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              ></textarea>
            </div>

            <div>
              <label for="precio" class="block text-sm font-medium text-gray-700">Precio por Hora</label>
              <input
                type="number"
                id="precio"
                v-model="form.precio"
                min="0"
                step="0.01"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
              >
            </div>

            <div class="flex justify-end space-x-4">
              <Link
                :href="route('canchas.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
              >
                Actualizar Cancha
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  cancha: {
    type: Object,
    required: true
  }
})

const form = useForm({
  nombre: props.cancha.nombre,
  descripcion: props.cancha.descripcion,
  precio: props.cancha.precio
})

const submit = () => {
  form.put(route('canchas.update', props.cancha.id))
}
</script>
