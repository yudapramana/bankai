export default defineNuxtRouteMiddleware((to, from) => {
    // isAuthenticated() is an example method verifying if a user is authenticated

    const authStore = useAuthStore();

    if (authStore.is_login === false) {
      return navigateTo('/login');
    }
  })