<template>
  <section ref="editor"></section>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount, onUnmounted } from 'vue'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

import Quill from 'quill'

// Default options for the Quill editor
const defaultOptions = {
  theme: 'snow',
  boundary: document.body,
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote', 'code-block'],
      [{ header: 1 }, { header: 2 }],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ script: 'sub' }, { script: 'super' }],
      [{ indent: '-1' }, { indent: '+1' }],
      [{ direction: 'rtl' }],
      [{ size: ['small', false, 'large', 'huge'] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }],
      [{ font: [] }],
      [{ align: [] }],
      ['clean'],
      ['link', 'image', 'video']
    ]
  },
  placeholder: 'Insert content here ...',
  readOnly: false
}

// Props (replace with actual props in parent component)
const props = defineProps({
  content: String,
  value: String,
  disabled: {
    type: Boolean,
    default: false
  },
  options: {
    type: Object,
    default: () => ({})
  }
})

// Emits
const emit = defineEmits(['ready', 'change', 'input', 'blur', 'focus', 'update:value'])

const observeDOMChanges = (element) => {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach(() => {
      // handle your mutation here
    })
  })

  observer.observe(element, {
    childList: true,
    subtree: true
  })

  return observer
}

const editor = ref(null)
let quill = null
let _content = ''

const mergeOptions = (def, custom) => {
  for (const key in custom) {
    if (!def[key] || key !== 'modules') {
      def[key] = custom[key]
    } else {
      mergeOptions(def[key], custom[key])
    }
  }
  return def
}

const initialize = () => {
  if (editor.value) {
    // Merge options
    const editorOption = mergeOptions(defaultOptions, props.options)
    editorOption.readOnly = props.disabled ? true : false

    // Initialize Quill editor
    quill = new Quill(editor.value, editorOption)

    // Set initial content
    if (props.value) {
      quill.clipboard.dangerouslyPasteHTML(props.value)
    }

    // Handle selection-change (blur, focus)
    quill.on('selection-change', range => {
      if (!range) {
        emit('blur', quill)
      } else {
        emit('focus', quill)
      }
    })

    // Handle text-change
    quill.on('text-change', () => {
      if (props.disabled) {
        quill.enable(false)
      }
      let html = editor.value.children[0].innerHTML
      const text = quill.getText()
      if (html === '<p><br></p>') html = ''
      _content = html
      emit('update:value', _content)
      emit('change', { html, text, quill })
    })

    emit('ready', quill)
  }
}

// Watchers
watch(
  () => props.value,
  (val) => {
    if (quill && val !== _content) {
      _content = val
      quill.clipboard.dangerouslyPasteHTML(val)
    } else if (!val) {
      quill.setText('')
    }
  }
)

watch(
  () => props.disabled,
  (val) => {
    if (quill) {
      quill.enable(!val)
    }
  }
)

// Lifecycle hooks
onMounted(() => {
  initialize()

  // Observe editor for DOM changes
  if (editor.value) {
    const observer = observeDOMChanges(editor.value)
    onUnmounted(() => {
      observer.disconnect()
    })
  }
})

onBeforeUnmount(() => {
  const editorToolbar = editor.value.previousSibling
  if (editorToolbar && editorToolbar.nodeType === 1 && editorToolbar.className.indexOf('ql-toolbar') > -1) {
    editorToolbar.parentNode.removeChild(editorToolbar)
  }
})

onUnmounted(() => {
  quill = null
})

</script>
