<script setup lang="ts">
import { ref } from 'vue'
const props = defineProps<{
  docTypes: any[]
  uploadedDocs: any[]
  employeeId: number
}>()

const config = useRuntimeConfig()
const previewFile = ref<string | null>(null)
const showModal = ref(false)

const openPreview = (filePath: string) => {
  previewFile.value = config.public.API_URL + '/storage/' + filePath
  showModal.value = true
}

const closePreview = () => {
  showModal.value = false
  previewFile.value = null
}
</script>

<template>
  <div>
    <table class="table-auto w-full border text-sm">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="border p-2">No</th>
          <th class="border p-2">Jenis Dokumen</th>
          <th class="border p-2">File</th>
          <th class="border p-2">Status</th>
          <th class="border p-2">Catatan</th>
          <th class="border p-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(doc, index) in docTypes" :key="doc.id" class="border">
          <td class="border p-2 align-top">{{ index + 1 }}</td>
          <td class="border p-2 align-top">{{ doc.type_name }}</td>
          <td class="border p-2 align-top">
            <div
              v-for="file in uploadedDocs.filter(f => f.id_doc_type === doc.id)"
              :key="file.id"
              class="mb-1"
            >
              <button
                @click="openPreview(file.file_path)"
                class="text-blue-600 hover:underline"
              >
                {{ file.file_name }}
              </button>
            </div>
          </td>
          <td class="border p-2 align-top">
            <div
              v-for="file in uploadedDocs.filter(f => f.id_doc_type === doc.id)"
              :key="'status-' + file.id"
            >
              <span
                :class="{
                  'text-yellow-600': file.status === 'Pending',
                  'text-green-600': file.status === 'Approved',
                  'text-red-600': file.status === 'Rejected',
                }"
              >
                {{ file.status }}
              </span>
            </div>
          </td>
          <td class="border p-2 align-top">
            <div
              v-for="file in uploadedDocs.filter(f => f.id_doc_type === doc.id)"
              :key="'note-' + file.id"
              class="text-gray-700 italic"
            >
              {{ file.verif_notes || '-' }}
            </div>
          </td>
          <td class="border p-2 align-top">
            <div
              v-for="file in uploadedDocs.filter(f => f.id_doc_type === doc.id)"
              :key="'action-' + file.id"
            >
              <NuxtLink
                v-if="file.status === 'Rejected'"
                :to="{ path: '/upload', query: { doc_type: doc.id } }"
                class="text-sm text-white bg-red-600 hover:bg-red-700 rounded px-2 py-1 inline-block"
              >
                Upload Ulang
              </NuxtLink>
            </div>
            <NuxtLink
              v-if="!uploadedDocs.some(f => f.id_doc_type === doc.id)"
              :to="{ path: '/upload', query: { doc_type: doc.id } }"
              class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded px-2 py-1 inline-block"
            >
              Upload
            </NuxtLink>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal Preview -->
    <div v-if="showModal" class="fixed z-50 inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white w-11/12 md:w-3/4 h-4/5 rounded shadow-lg relative">
        <button @click="closePreview" class="absolute top-2 right-2 text-gray-700 text-xl">&times;</button>
        <iframe
          v-if="previewFile"
          :src="previewFile"
          class="w-full h-full border-none"
        ></iframe>
      </div>
    </div>
  </div>
</template>
