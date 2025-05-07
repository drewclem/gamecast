<template>
  <div>
    <EditorContent :editor="editor" />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Mention from '@tiptap/extension-mention'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  content: {
    type: Object,
    default: () => {
      return {
        type: 'doc',
        content: [
          {
            type: 'paragraph',
          },
        ],
      }
    },
  },
  size: {
    type: String,
    default: 'base',
    validator: (value) => {
      return ['small', 'base', 'large'].includes(value)
    },
  },
})

const editor = useEditor({
  editable: false,
  editorProps: {
    attributes: {
      class: props.size === 'small' ? 'prose max-w-none text-sm' : 'prose max-w-none',
    },
  },
  extensions: [
    StarterKit,
    Mention.configure({
      HTMLAttributes: {
        class: 'text-blue-500  p-1 rounded-md',
      },
      renderHTML({ node }) {
        // Handle any possible structure consistently
        const name =
          node.attrs.id && typeof node.attrs.id === 'object'
            ? node.attrs.id.name
            : node.attrs.label || node.attrs.id
        const id = typeof node.attrs.id === 'object' ? node.attrs.id.id : node.attrs.id

        if (node.type === 'text' && node.text === null) {
          return 'nbsp;'
        }

        return [
          'span',
          {
            class: 'text-blue-500 bg-blue-50 p-1 rounded-md',
            'data-mention-id': id,
          },
          `@${name}`,
        ]
      },
    }),
    Link.configure({
      HTMLAttributes: {
        class: 'text-primary-default mr-1 underline',
      },
      openOnClick: false,
      autolink: true,
    }),
    Underline,
  ],
  content: props.content,
})
</script>

<style lang="postcss">
.is-active {
  @apply bg-gray-100;
}

.ProseMirror:focus,
.ProseMirror:focus-visible {
  @apply outline-none ring-2 ring-primary-default rounded;
}

.ProseMirror {
  > ul {
    @apply list-disc pl-4;
  }

  > ol {
    @apply list-decimal pl-4;
  }

  p.is-empty::before {
    @apply text-gray-400;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
  }
}
</style>
