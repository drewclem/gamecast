<template>
  <Stack space="xsmall">
    <Stack :space="label ? 'small' : 'none'">
      <Typography v-if="label" as="label" variant="label" :for="id">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </Typography>

      <div :class="{ 'border border-gray-200 rounded-lg': !disabled }">
        <div v-if="!hideToolbar" class="flex space-x-3 border-b border-gray-200">
          <RichTextEditorToolButton @click="editor.chain().focus().toggleBold().run()">
            <RiBold class="h-4 w-4" />
          </RichTextEditorToolButton>

          <RichTextEditorToolButton @click="editor.chain().toggleUnderline().run()">
            <RiUnderline class="h-4 w-4" />
          </RichTextEditorToolButton>

          <RichTextEditorToolButton
            @click="editor.chain().focus().toggleItalic().run()"
            class="italic"
          >
            <RiItalic class="h-4 w-4" />
          </RichTextEditorToolButton>

          <RichTextEditorToolButton
            @click="editor.chain().focus().toggleStrike().run()"
            class="line-through"
          >
            <RiStrikethrough class="h-4 w-4" />
          </RichTextEditorToolButton>

          <RichTextEditorToolButton @click="setLink()" class="line-through">
            <RiLink class="h-4 w-4" />
          </RichTextEditorToolButton>

          <RichTextEditorToolButton @click="editor.chain().focus().toggleBulletList().run()">
            <RiListUnordered class="h-4 w-4" />
          </RichTextEditorToolButton>

          <RichTextEditorToolButton @click="editor.chain().focus().toggleOrderedList().run()">
            <RiListOrdered2 class="h-4 w-4" />
          </RichTextEditorToolButton>
        </div>

        <EditorContent :editor="editor" />
      </div>
    </Stack>
    <InputError v-if="error" :error="error" />
  </Stack>
</template>

<script setup>
import { watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Mention from '@tiptap/extension-mention'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import suggestion from '@/Utils/suggestion.js'

import RichTextEditorToolButton from '@/Stencil/Forms/RichText/RichTextEditorToolButton.vue'
import MentionItem from '@/Stencil/Forms/RichText/Mention.vue'
import Stack from '@/Stencil/Stack.vue'
import Typography from '@/Stencil/Typography.vue'
import InputError from './InputError.vue'

import {
  RiBold,
  RiItalic,
  RiStrikethrough,
  RiUnderline,
  RiListUnordered,
  RiListOrdered2,
  RiLink,
} from '@remixicon/vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  required: {
    type: Boolean,
    default: false,
  },
  hideToolbar: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'base',
    validator: (value) => {
      return ['small', 'base', 'large'].includes(value)
    },
  },
  modelValue: {
    type: [Object, String],
    default: () => ({
      type: 'doc',
      content: [
        {
          type: 'paragraph',
        },
      ],
    }),
  },
  content: {
    type: [Object, String],
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
    transform: (value) => {
      if (typeof value === 'string') {
        try {
          return JSON.parse(value)
        } catch (e) {
          return value
        }
      }
      return value
    },
  },
})

const editor = useEditor({
  editable: !props.disabled,
  editorProps: {
    attributes: {
      class: `${
        !props.disabled
          ? 'prose max-w-none px-3 py-2 min-h-[100px] focus:ring-primary-default'
          : 'prose max-w-none'
      } ${props.size === 'small' ? 'prose max-w-none text-sm' : 'prose max-w-none'}`,
    },
  },
  extensions: [
    StarterKit,
    Mention.configure({
      HTMLAttributes: {
        class: 'text-blue-500 bg-gray-100 p-1 rounded-md',
      },
      renderHTML({ options, node }) {
        return `${options.suggestion.char}${node.attrs.id.name}`
      },
      suggestion,
      deleteTriggerWithBackspace: true,
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
  content: props.modelValue,
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getJSON())
  },
})

watch(
  () => props.modelValue,
  (newValue) => {
    if (editor.value && JSON.stringify(editor.value.getJSON()) !== JSON.stringify(newValue)) {
      editor.value.commands.setContent(newValue)
    }
  }
)

function setLink() {
  const url = window.prompt('URL')

  if (!url) {
    return
  }

  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }

  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}
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
}
</style>
