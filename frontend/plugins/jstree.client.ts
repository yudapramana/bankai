import jQuery from 'jquery'
import 'jstree'
import 'jstree/dist/themes/default/style.css'

// Biar jQuery dikenal secara global
if (typeof window !== 'undefined') {
  window.$ = jQuery
  window.jQuery = jQuery
}

export default defineNuxtPlugin(nuxtApp => {
  // jsTree siap digunakan, nggak perlu return apa-apa
})
