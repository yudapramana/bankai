<script setup lang="ts">
useSeoMeta({ title: "Daftar Dokumen Pegawai" })
definePageMeta({ middleware: ["$auth"] })

import { ref, computed, onMounted } from 'vue'
import { useSanctum, useSanctumForm } from '#imports'

const config = useRuntimeConfig()
const { user } = useSanctum<User>()
const token = useCookie('XSRF-TOKEN');

const employee = computed(() => user.value?.employee ?? null)
const docTypes = ref<any[]>([])
const uploadedDocs = ref<any[]>([])
const treeData = ref<any[]>([])
const refTree = ref<HTMLDivElement | null>(null)

const showModal = ref(false)
const pdfUrl = ref("")

const showUploadModal = ref(false)
const selectedDocTypeId = ref<number | null>(null)
const selectedDocTypeName = ref("")

interface UploadForm {
  id_employee: number
  id_doc_type: number
  doc_number: string
  doc_date: string
  parameter: string
  file: File | null
}

const form = useSanctumForm<UploadForm>('post', '/api/emp-documents', {
  id_employee: 0,
  id_doc_type: 0,
  doc_number: '',
  doc_date: '',
  parameter: '',
  file: null
})

const openModal = (filePath: string) => {
  pdfUrl.value = config.public.API_URL + "/storage/" + filePath
  showModal.value = true
}
const closeModal = () => {
  showModal.value = false
  pdfUrl.value = ""
}

const openUploadModal = (docTypeId: number, docTypeName: string) => {
  if (!employee.value) return
  form.reset()
  form.id_doc_type = docTypeId
  form.id_employee = employee.value.id
  selectedDocTypeName.value = docTypeName
  selectedDocTypeId.value = docTypeId
  showUploadModal.value = true
}

const closeUploadModal = () => {
  showUploadModal.value = false
  form.reset()
}

async function submitForm() {
  if (!employee.value || form.processing) return

  const formData = new FormData()
  formData.append('id_doc_type', form.id_doc_type.toString())
  formData.append('doc_number', form.doc_number)
  formData.append('parameter', form.parameter)
  formData.append('doc_date', form.doc_date)
  formData.append('id_employee', employee.value.id.toString())
  if (form.file) formData.append('file', form.file)

  try {
    await $fetch(config.public.API_URL + '/api/emp-documents', {
      credentials: "include",
      method: 'POST',
      body: formData,
      headers: {
        'X-XSRF-TOKEN': token.value as string,
      }
    })

    const uploaded = await $fetch(config.public.API_URL + `/api/emp-documents/by-employee/${employee.value.id}`)
    uploadedDocs.value = uploaded.data
    generateTreeData()

    $(refTree.value!).jstree(true).settings.core.data = treeData.value
    $(refTree.value!).jstree(true).refresh()

    closeUploadModal()
    alert('Dokumen berhasil diunggah')
  } catch (err) {
    console.error(err)
    alert('Gagal mengunggah dokumen!')
  }
}

// const generateTreeData = () => {
//   const data = docTypes.value.map((doc: any, index: number) => {
//     const children = uploadedDocs.value
//       .filter((file: any) => file.id_doc_type === doc.id)
//       .map((file: any) => ({
//         id: `file-${file.id}`,
//         text: `${file.file_name} (${file.status})`,
//         file_path: file.file_path,
//         icon: "jstree-file"
//       }))

//     return {
//       id: `doc-${doc.id}`,
//       text: `${index + 1}. ${doc.type_name}`,
//       doc_type_id: doc.id,
//       children,
//       icon: "jstree-folder"
//     }
//   })

//   treeData.value = data
// }

const generateTreeData = () => {
  const data = docTypes.value.map((doc: any, index: number) => {
    const children = uploadedDocs.value
      .filter((file: any) => file.id_doc_type === doc.id)
      .map((file: any) => ({
        id: `file-${file.id}`,
        text: `${file.file_name} (${file.status})`,
        file_path: file.file_path,
        icon: "jstree-file",
        state: { opened: true } // 👈 Biar file juga langsung keliatan
      }))

    return {
      id: `doc-${doc.id}`,
      text: `${index + 1}. ${doc.type_name}`,
      doc_type_id: doc.id,
      children,
      icon: "jstree-folder",
      state: { opened: true } // 👈 Ini bikin folder-nya langsung kebuka
    }
  })

  treeData.value = data
}


onMounted(async () => {
  if (employee.value) {
    const res = await $fetch(config.public.API_URL + '/api/doc-types/' + employee.value.employment_status)
    docTypes.value = res.data

    const uploaded = await $fetch(config.public.API_URL + `/api/emp-documents/by-employee/${employee.value.id}`)
    uploadedDocs.value = uploaded.data

    generateTreeData()

    setTimeout(() => {
      if (refTree.value) {
        $(refTree.value).jstree("destroy").empty()

        $(refTree.value).jstree({
          core: {
            data: treeData.value
          },
          plugins: ["wholerow", "contextmenu"],
          contextmenu: {
            items: function (node: any) {
              const isFile = node.original.file_path != null
              const isFolder = !isFile

              const items: any = {}

              if (isFolder) {
                items.upload = {
                  label: "Upload Dokumen",
                  action: () => openUploadModal(node.original.doc_type_id, node.text)
                }
              }

              if (isFile) {
                items.view = {
                  label: "Lihat",
                  action: () => openModal(node.original.file_path)
                }
              }

              return items
            }
          },
          themes: {
            stripes: true
          }
        })

        $(refTree.value).on("select_node.jstree", (e, data) => {
          const node = data.node.original
          if (node.file_path) openModal(node.file_path)
        })
      }
    }, 0)
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 pt-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-md p-6 sm:p-8">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Dokumen Pegawai</h2>
      <div ref="refTree" class="jstree-default"></div>
    </div>

    <!-- Modal Pratinjau PDF -->
    <div v-if="showModal" class="fixed z-50 inset-0 bg-black bg-opacity-60 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-3xl overflow-hidden">
        <div class="flex justify-between items-center px-4 py-2 bg-gray-100 border-b">
          <h3 class="text-sm font-medium text-gray-700">Pratinjau Dokumen</h3>
          <button @click="closeModal" class="text-gray-600 hover:text-red-600 text-xl font-bold">&times;</button>
        </div>
        <iframe :src="pdfUrl" class="w-full h-[80vh]" frameborder="0"></iframe>
      </div>
    </div>

    <!-- Modal Upload -->
    <div v-if="showUploadModal" class="fixed z-50 inset-0 bg-black bg-opacity-60 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-md p-6">
        <h3 class="text-lg font-semibold mb-4">Upload Dokumen</h3>
        <p class="text-sm mb-2 text-gray-600">Tipe Dokumen: <strong>{{ selectedDocTypeName }}</strong></p>

        <div class="space-y-3">
          <input type="text" v-model="form.doc_number" placeholder="Nomor Dokumen" class="w-full border rounded px-3 py-1" />
          <input type="date" v-model="form.doc_date" class="w-full border rounded px-3 py-1" />
          <input type="text" v-model="form.parameter" placeholder="Parameter Tambahan" class="w-full border rounded px-3 py-1" />
          <input type="file" @change="e => form.file = (e.target as HTMLInputElement).files?.[0] || null" />
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button @click="closeUploadModal" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Batal</button>
          <button @click="submitForm" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.jstree-default .jstree-icon {
  background-size: contain;
}
</style>
