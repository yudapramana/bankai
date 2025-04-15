<template>
    <div ref="treeContainer" id="jstree"></div>
  </template>
  
  <script setup lang="ts">
  import { onMounted, ref, watch } from 'vue'
  import $ from 'jquery'
  
  const props = defineProps({
    data: {
      type: Array,
      required: true
    }
  })
  
  const treeContainer = ref()
  
  onMounted(() => {
    $(treeContainer.value).jstree({
      core: {
        data: props.data,
        themes: {
          dots: true,
          icons: true
        }
      }
    })
  
    // Event klik node
    $(treeContainer.value).on('select_node.jstree', function (e, data) {
      const nodeData = data.node.original
      if (nodeData.file_path) {
        // Emit ke parent
        treeContainer.value?.dispatchEvent(new CustomEvent('fileselected', { detail: nodeData }))
      }
    })
  })
  </script>
  