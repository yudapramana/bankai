export const useAuthStore  = defineStore('websiteStore', {
    state: () => ({
      user: [],
      is_login: false,
    }),
    actions: {
      async fetch() {
        // const infos = await $fetch('https://api.nuxt.com/modules/pinia')
      }
    }
  })