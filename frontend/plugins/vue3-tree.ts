import { defineNuxtPlugin } from '#app'
import Vue3Tree from 'vue3-tree'
import 'vue3-tree/dist/style.css'

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.component('Vue3Tree', Vue3Tree)
})