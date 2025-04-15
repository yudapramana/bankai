<script setup lang="ts">
useSeoMeta({ title: "Daftar Dokumen Pegawai" })
definePageMeta({ middleware: ["$auth"] })

import { ref, computed, onMounted } from 'vue'
import { useSanctum } from '#imports'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faEye, faUpload } from '@fortawesome/free-solid-svg-icons'

const icons = { faEye, faUpload }


const config = useRuntimeConfig()
const { user } = useSanctum<User>()

const employee = computed(() => user.value?.employee ?? null)
const docTypes = ref<any[]>([])
const uploadedDocs = ref<any[]>([])

onMounted(async () => {
  if (employee.value) {
    const res = await $fetch(config.public.API_URL + '/api/doc-types/' + employee.value.employment_status)
    docTypes.value = res.data

    const uploaded = await $fetch(config.public.API_URL + `/api/emp-documents/by-employee/${employee.value.id}`)
    uploadedDocs.value = uploaded.data
  }
})

const showModal = ref(false)
const pdfUrl = ref("")

const openModal = (filePath: string) => {
  pdfUrl.value = config.public.API_URL + "/storage/" + filePath
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  pdfUrl.value = ""
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 pt-20 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-md p-6 sm:p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Dokumen Pegawai</h2>
  
        <div class="overflow-x-auto">
          <table class="min-w-full border border-gray-300 text-sm table-auto">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-3 py-2 text-left w-8">#</th>
                <th class="border px-3 py-2 text-left">Jenis Dokumen</th>
                <th class="border px-3 py-2 text-left">File & Status</th>
                <th class="border px-3 py-2 text-left w-24">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <template v-for="(doc, index) in docTypes" :key="doc.id">
                <!-- Baris utama dokumen -->
                <tr class="bg-gray-50">
                  <td class="border px-3 py-2 font-medium align-top">{{ index + 1 }}</td>
                  <td class="border px-3 py-2 font-medium align-top" colspan="3">{{ doc.type_name }}</td>
                </tr>

                <!-- Baris per file -->
                <tr
                  v-for="file in uploadedDocs.filter(f => f.id_doc_type === doc.id)"
                  :key="file.id"
                >
                  <td class="border px-3 py-2"></td>
                  <td class="border px-3 py-2"></td>

                  <!-- File & Status -->
                  <td class="border px-3 py-2 text-xs">
                    <div class="flex flex-col gap-1">
                      <div class="flex items-center justify-between">
                        <span class="text-blue-600">{{ file.file_name }}</span>
                        <div class="flex items-center gap-2">
                          <button
                            @click="openModal(file.file_path)"
                            class="text-blue-600 hover:text-blue-800"
                            title="Lihat Dokumen"
                          >
                            <FontAwesomeIcon :icon="icons.faEye" />
                          </button>
                          <NuxtLink
                            :to="{ path: '/upload', query: { doc_type: doc.id } }"
                            class="text-green-600 hover:text-green-800"
                            title="Reupload Dokumen"
                          >
                            <FontAwesomeIcon :icon="icons.faUpload" />
                          </NuxtLink>
                        </div>
                      </div>

                      <div class="mt-1 text-xs">
                        <span
                          class="inline-block px-2 py-0.5 rounded-full border font-medium"
                          :class="{
                            'bg-yellow-100 border-yellow-300 text-yellow-800': file.status === 'Pending',
                            'bg-green-100 border-green-300 text-green-800': file.status === 'Approved',
                            'bg-red-100 border-red-300 text-red-800': file.status === 'Rejected',
                          }"
                        >
                          {{ file.status }}
                        </span>
                        <div
                          v-if="file.status === 'Rejected' && file.verif_notes"
                          class="text-red-600 italic mt-1"
                        >
                          {{ file.verif_notes }}
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Kolom aksi dikosongkan -->
                  <td class="border px-3 py-2"></td>
                </tr>

                <!-- Baris jika belum ada file -->
                <tr v-if="!uploadedDocs.some(f => f.id_doc_type === doc.id)">
                  <td class="border px-3 py-2"></td>
                  <td class="border px-3 py-2"></td>
                  <td class="border px-3 py-2 italic text-gray-400 text-xs">Belum diunggah</td>
                  <td class="border px-3 py-2 text-xs">
                    <NuxtLink
                      :to="{ path: '/upload', query: { doc_type: doc.id } }"
                      class="text-blue-600 hover:underline flex items-center gap-1"
                    >
                      <FontAwesomeIcon :icon="icons.faUpload" />
                      Upload
                    </NuxtLink>
                  </td>
                </tr>
              </template>
            </tbody>

          </table>
        </div>
      </div>
  
      <!-- Modal PDF Viewer -->
      <div v-if="showModal" class="fixed z-50 inset-0 bg-black bg-opacity-60 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-3xl overflow-hidden">
          <div class="flex justify-between items-center px-4 py-2 bg-gray-100 border-b">
            <h3 class="text-sm font-medium text-gray-700">Pratinjau Dokumen</h3>
            <button @click="closeModal" class="text-gray-600 hover:text-red-600 text-xl font-bold">&times;</button>
          </div>
          <iframe :src="pdfUrl" class="w-full h-[80vh]" frameborder="0"></iframe>
        </div>
      </div>
    </div>
  </template>
  
