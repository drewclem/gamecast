<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import Button from '@/Stencil/Button.vue'
import Typography from '@/Stencil/Typography.vue'
import SlideOut from '@/Stencil/SlideOut.vue'
import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Avatar from '@/Stencil/Avatar.vue'
import Table from '@/Stencil/Table.vue'
import Icon from '@/Stencil/Icon.vue'
import Modal from '@/Stencil/Modal.vue'

import FormTextInput from '@/Stencil/Forms/FormInput.vue'
import FormAvatar from '@/Stencil/Forms/FormAvatar.vue'
import FormColorPicker from '@/Stencil/Forms/FormColorPicker.vue'

const props = defineProps({
  auth: {
    type: Object,
    required: true,
  },
  hosts: {
    type: Array,
    required: true,
  },
  show: {
    type: Object,
    required: true,
  },
})

const isSlideoverOpen = ref(false)
const showDeleteModal = ref(false)

const form = useForm({
  name: '',
  email: '',
  avatar: null,
  color: '',
})

const isEditSlideoverOpen = ref(false)

const editForm = useForm({
  id: null,
  name: '',
  email: '',
  avatar: null,
  color: '',
  existing_avatar: null,
})

function createHost() {
  form.post(route('hosts.store', { show: props.show.id }), {
    preserveScroll: true,
    onSuccess: () => {
      isSlideoverOpen.value = false
      form.reset()
    },
  })
}

function closeSlideover() {
  isSlideoverOpen.value = false
  form.reset()
}

function editHost(host) {
  isEditSlideoverOpen.value = true
  showAvatarForm.value = host.avatar ? false : true

  editForm.id = host.id
  editForm.name = host.name
  editForm.email = host.user.email
  editForm.existing_avatar = host.avatar
  editForm.color = host.color
}
const showAvatarForm = ref(false)

function toggleAvatarForm() {
  showAvatarForm.value = !showAvatarForm.value
}

function updateHost() {
  editForm.post(route('hosts.update', { show: props.show.id, host: editForm.id }), {
    preserveScroll: true,
    onSuccess: () => {
      isEditSlideoverOpen.value = false
      editForm.reset()
    },
  })
}

function closeEditSlideover() {
  isEditSlideoverOpen.value = false
  editForm.reset()
}

// Add asset helper
const asset = (path) => {
  return path?.startsWith('http') ? path : `/storage/${path}`
}

const hostToDelete = ref(null)

function deleteHost(host) {
  hostToDelete.value = host
  showDeleteModal.value = true
}

function confirmDeleteHost() {
  router.delete(route('hosts.destroy', { host: hostToDelete.value.id }), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
    },
  })
}
</script>

<template>
  <Head title="Hosts" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <Typography class="text-xl font-semibold leading-tight text-gray-800"> Hosts </Typography>

        <Button icon="add" @click="isSlideoverOpen = true"> Add Host </Button>
      </div>
    </template>

    <Card>
      <Table>
        <template #columns>
          <tr class="text-left">
            <th class="w-12"></th>
            <th>Name</th>
            <th></th>
            <th></th>
          </tr>
        </template>
        <template #rows v-if="hosts.length > 0">
          <tr v-for="host in hosts" :key="host.id">
            <td class="font-medium">
              <div
                :style="{ backgroundColor: host.color ?? '#000' }"
                class="flex items-center justify-center w-[52px] h-[52px] rounded-full"
              >
                <Avatar v-if="host.avatar" size="large" :src="asset(host.avatar)" />
                <div
                  v-else
                  class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center"
                >
                  {{ host.name.charAt(0) + host.name.charAt(1) }}
                </div>
              </div>
            </td>
            <td>
              {{ host.name }}
            </td>
            <td></td>
            <td>
              <div class="flex space-x-4 justify-end items-center text-right">
                <button
                  v-if="auth.user.current_host_id !== host.id"
                  class="ml-auto"
                  theme="danger-outline"
                  icon="delete"
                  @click="deleteHost(host)"
                >
                  <Icon icon="delete" size="xsmall" class="text-gray-400 hover:text-red-500" />
                  <span class="sr-only">Remove host {{ host.name }}</span>
                </button>
                <Button theme="primary-outline" icon="edit" @click="editHost(host)" />
              </div>
            </td>
          </tr>
        </template>

        <template #empty v-else>
          <Stack space="medium">
            <Stack space="xsmall" class="text-center">
              <Typography variant="h3"> No hosts found </Typography>
            </Stack>
          </Stack>
        </template>
      </Table>
    </Card>

    <teleport to="body">
      <SlideOut :isOpen="isSlideoverOpen" title="Add Host" @close="closeSlideover">
        <form @submit.prevent="createHost">
          <Stack>
            <Stack space="medium">
              <FormTextInput
                label="Name"
                name="name"
                v-model="form.name"
                :error="form.errors.name"
                required
              />
              <FormTextInput
                label="Email"
                name="email"
                v-model="form.email"
                :error="form.errors.email"
                inputType="email"
                required
              />
              <FormAvatar
                label="Avatar"
                helper="*.jpg, *.jpeg, *.png | 2MB max"
                name="avatar"
                v-model="form.avatar"
                :error="form.errors.avatar"
                required
              />
              <FormColorPicker
                label="Color"
                name="color"
                v-model="form.color"
                :error="form.errors.color"
              />
            </Stack>

            <div class="flex justify-end">
              <Button icon="add" theme="primary-outline" type="submit"> Invite </Button>
            </div>
          </Stack>
        </form>
      </SlideOut>
    </teleport>

    <teleport to="body">
      <SlideOut :isOpen="isEditSlideoverOpen" title="Edit Host" @close="closeEditSlideover">
        <form @submit.prevent="updateHost">
          <Stack>
            <Stack space="medium">
              <FormTextInput
                label="Name"
                name="name"
                v-model="editForm.name"
                :error="editForm.errors.name"
                required
              />
              <FormTextInput
                label="Email"
                name="email"
                v-model="editForm.email"
                :error="editForm.errors.email"
                inputType="email"
                :isDisabled="true"
              />

              <div>
                <FormAvatar
                  v-if="showAvatarForm"
                  name="avatar"
                  v-model="editForm.avatar"
                  :error="editForm.errors.avatar"
                />
                <button
                  v-else
                  type="button"
                  @click="showAvatarForm = true"
                  class="hover:animate-shake"
                >
                  <Avatar
                    :src="asset(editForm.existing_avatar)"
                    :alt="editForm.name"
                    size="xxlarge"
                  />
                  <span class="sr-only">Update avatar</span>
                </button>
              </div>
              <FormColorPicker
                label="Color"
                name="color"
                v-model="editForm.color"
                :error="editForm.errors.color"
              />
            </Stack>

            <div class="flex justify-end">
              <Button theme="primary-outline" type="submit" :loading="editForm.processing">
                Update
              </Button>
            </div>
          </Stack>
        </form>
      </SlideOut>
    </teleport>

    <teleport to="body">
      <Modal title="Remove Host" :isOpen="showDeleteModal" @close="showDeleteModal = false">
        <div class="flex space-x-6 w-full">
          <Icon icon="danger" class="text-red-500" size="large" />
          <Stack class="w-full">
            <Stack space="small">
              <Typography variant="h1">
                Are you sure you want to remove
                {{ hostToDelete.name }}?
              </Typography>
              <Typography> This action cannot be undone. </Typography>
            </Stack>
            <div class="flex justify-end space-x-3">
              <Button theme="subdued" @click="showDeleteModal = false"> Cancel </Button>
              <Button theme="danger" @click="confirmDeleteHost"> Remove </Button>
            </div>
          </Stack>
        </div>
      </Modal>
    </teleport>
  </AuthenticatedLayout>
</template>

<style>
@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-5px);
  }
  75% {
    transform: translateX(5px);
  }
}

.animate-shake {
  animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
}
</style>
