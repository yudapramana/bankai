<script setup lang="ts">
useSeoMeta({ title: "Profile" });

definePageMeta({
  middleware: ["$auth"],
});

const { user, refreshUser } = useSanctum<User>();

interface UserProfileForm {
  name: string;
  email: string;
  avatar: File | null;
}

const form = useSanctumForm<UserProfileForm>("patch", "/api/profile", {
  name: user.value?.name ?? "",
  email: user.value?.email ?? "",
  avatar: null,
});

async function updateProfile() {
  if (form.processing) return;
  try {
    await form.submit();
    await refreshUser();
    alert("Profil berhasil diperbarui.");
  } catch (err) {
    console.error(err);
  }
}

function resetForm() {
  form.reset();
}

const employee = computed(() => user.value?.employee ?? null);
</script>

<template>
  <div class="min-h-screen bg-gray-100 pt-24 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
      <!-- Judul -->
      <h1 class="text-2xl font-semibold text-gray-800 mb-6">Profil Saya</h1>

      <!-- Info User -->
      <div class="flex items-center gap-4 mb-6">
        <div>
          <div
            v-if="!user?.avatar"
            class="flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full text-xl font-bold text-gray-600"
          >
            {{ user?.name?.charAt(0).toUpperCase() }}
          </div>
          <img
            v-else
            :src="user.avatar"
            alt="Avatar"
            class="w-16 h-16 rounded-full object-cover"
          />
        </div>
        <div>
          <p class="text-lg font-semibold">{{ user?.name }}</p>
          <p class="text-sm text-gray-600">{{ user?.email }}</p>
        </div>
      </div>

      <!-- Form Ubah Profil -->
      <form @submit.prevent="updateProfile" class="space-y-4 mb-8">
        <div>
          <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar</label>
          <input
            id="avatar"
            type="file"
            @change="handleAvatarChange"
            class="mt-1 block w-full text-sm border rounded-md px-3 py-2 border-gray-300"
          />
          <p v-if="form.invalid('avatar')" class="text-sm text-red-600">
            {{ form.errors.avatar }}
          </p>
        </div>

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            @input="form.forgetError('name')"
            class="mt-1 block w-full px-3 py-2 text-sm border rounded-md border-gray-300"
            :class="{ 'border-red-500': form.invalid('name') }"
          />
          <p v-if="form.invalid('name')" class="text-sm text-red-600">
            {{ form.errors.name }}
          </p>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            @input="form.forgetError('email')"
            class="mt-1 block w-full px-3 py-2 text-sm border rounded-md border-gray-300"
            :class="{ 'border-red-500': form.invalid('email') }"
          />
          <p v-if="form.invalid('email')" class="text-sm text-red-600">
            {{ form.errors.email }}
          </p>
        </div>

        <div class="flex gap-2">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-600 disabled:opacity-50"
          >
            Simpan
          </button>
          <button
            type="button"
            @click="resetForm"
            class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300"
          >
            Reset
          </button>
        </div>
      </form>

      <!-- Informasi Employee -->
      <div v-if="employee" class="space-y-3 border-t pt-6 text-sm text-gray-800">
        <h2 class="text-lg font-semibold mb-3">Informasi Pegawai</h2>

        <div>
          <p class="text-gray-500">NIP</p>
          <p class="font-medium">{{ employee.nip }}</p>
        </div>
        <div>
          <p class="text-gray-500">Nama Lengkap</p>
          <p class="font-medium">{{ employee.full_name }}</p>
        </div>
        <div>
          <p class="text-gray-500">Email Dinas</p>
          <p class="font-medium">{{ employee.email }}</p>
        </div>
        <div>
          <p class="text-gray-500">Jenis Kelamin</p>
          <p class="font-medium">
            {{ employee.gender === 'M' ? 'Laki-laki' : 'Perempuan' }}
          </p>
        </div>
        <div>
          <p class="text-gray-500">Tanggal Lahir</p>
          <p class="font-medium">{{ employee.date_of_birth }}</p>
        </div>
        <div>
          <p class="text-gray-500">Jabatan</p>
          <p class="font-medium">{{ employee.job_title }}</p>
        </div>
        <div>
          <p class="text-gray-500">Status Kepegawaian</p>
          <p class="font-medium">{{ employee.employment_status }}</p>
        </div>
        <div>
          <p class="text-gray-500">TMT Pangkat</p>
          <p class="font-medium">{{ employee.tmt_pangkat }}</p>
        </div>
        <div>
          <p class="text-gray-500">TMT Jabatan</p>
          <p class="font-medium">{{ employee.tmt_jabatan }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
