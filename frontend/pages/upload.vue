<script setup lang="ts">
useSeoMeta({ title: "Upload File" });

definePageMeta({
  middleware: ["$auth"],
});


import { ref, onMounted } from 'vue'
import { useSanctumForm, useSanctum } from '#imports'
const route = useRoute()


const config = useRuntimeConfig();
console.log('base url is' , config.public.API_URL)

// const docTypes = ref<any[]>([])
const { user, refreshUser } = useSanctum<User>();
const token = useCookie('XSRF-TOKEN');

const docTypes = computed(() => user.value?.doc_types ?? null);


interface UploadForm {
    id_employee: number
    id_doc_type: number
    doc_number: string
    doc_date: string
    file: File | null
}

const form = useSanctumForm<UploadForm>('post', '/api/emp-documents', {
  id_doc_type: 0,
  doc_number: '',
  doc_date: '',
  parameter: '',
  file: null,
})
// console.log('route.params.doc_type:' . route.params.doc_type);

onMounted(async () => {
    // if (user.value && user.value.id) {
    //     employee.value = user.value.employee
    //     const res = await $fetch(config.public.API_URL + '/api/doc-types/' + employee.value.employment_status)
    //     docTypes.value = res.data
    // }
    // form.id_doc_type.value = this.$route.query.doc_type;
    form.id_doc_type = route.query.doc_type;
    
})

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files) {
    form.file = target.files[0]
    form.forgetError('file')
  }
}

async function submitForm() {
  if (form.processing) return

  const formData = new FormData()
  formData.append('id_doc_type', form.id_doc_type.toString())
  formData.append('doc_number', form.doc_number)
  formData.append('parameter', form.parameter)
  formData.append('doc_date', form.doc_date)
  formData.append('id_employee', employee.value.id)
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
    alert('Dokumen berhasil diunggah')
    form.reset()
  } catch (err) {
    console.error(err)
  }
}

const employee = computed(() => user.value?.employee ?? null);
</script>


<template>
    <div class="min-h-screen bg-gray-50 pt-20 px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6 sm:p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Upload Dokumen Pegawai</h2>
  
        <form @submit.prevent="submitForm" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen</label>
            <select v-model="form.id_doc_type" class="w-full border rounded px-3 py-2">
              <option value="" disabled>Pilih jenis dokumen</option>
              <option v-for="doc in docTypes" :key="doc.id" :value="doc.id">
                {{ doc.type_name }}
              </option>
            </select>
          </div>
  
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Dokumen</label>
            <input
              v-model="form.doc_number"
              type="text"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300"
              placeholder="Contoh: 123/ABC/2024"
            />
          </div>
  
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Dokumen</label>
            <input
              v-model="form.doc_date"
              type="date"
              class="w-full border rounded px-3 py-2"
            />
          </div>
  
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload File PDF</label>
            <input
              type="file"
              accept="application/pdf"
              @change="handleFileChange"
              class="w-full"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Parameter Dokumen</label>
            <input
              v-model="form.parameter"
              type="text"
              class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300"
              placeholder="Parameter tergantung Tipe, Contoh (Ijazah):S1 (Tahun):2022 (Anak Ke-):1"
            />
          </div>
  
          <button
            type="submit"
            class="px-5 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md transition"
          >
            Upload
          </button>
        </form>
      </div>
    </div>
  </template>
  
