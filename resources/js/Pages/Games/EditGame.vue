<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import draggable from 'vuedraggable'

import Button from '@/Stencil/Button.vue'
import Typography from '@/Stencil/Typography.vue'
import SlideOut from '@/Stencil/SlideOut.vue'
import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Modal from '@/Stencil/Modal.vue'
import Icon from '@/Stencil/Icon.vue'
import Switch from '@/Stencil/Switch.vue'

import FormTextInput from '@/Stencil/Forms/FormInput.vue'
import FormTextarea from '@/Stencil/Forms/FormTextarea.vue'
import FormSelect from '@/Stencil/Forms/FormSelect.vue'

const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  show: {
    type: Object,
    required: true,
  },
  hosts: {
    type: Array,
    required: true,
  },
})

const showDeleteModal = ref(false)
const isSlideoverOpen = ref(false)

const form = useForm({
  title: props.game.data.title,
  access_code: props.game.data.access_code,
  description: props.game.data.description,
  votable_host_1_id: props.game.data.votableHost1?.id,
  votable_host_2_id: props.game.data.votableHost2?.id,
})

function getHostOptions(host) {
  return props.hosts.filter((h) => h.id !== host)
}

const formQuestion = useForm({
  question: '',
})

const questionOrder = useForm({
  order: props.game.data.questions.map((question) => question),
})

function createGame() {
  form.post(route('games.store', { show: props.show.id }))
}

function deleteGame() {
  form.delete(route('games.destroy', props.game.data))
}

function createQuestion() {
  formQuestion.post(route('games.questions.store', props.game.data), {
    onSuccess: () => {
      isSlideoverOpen.value = false
    },
    preserveState: false,
    preserveScroll: true,
  })
}

function handleQuestionReorder(event) {
  const newOrder = event.moved.newIndex
  const oldOrder = event.moved.oldIndex

  // Get the question ID from the new order array
  const questionId = questionOrder.order[newOrder]
  const question = props.game.data.questions.find((q) => q.id === questionId)
  const newOrderIndex = newOrder + 1 // Adding 1 since order_index starts at 1

  router.put(
    route('games.questions.update', {
      game: props.game.data.slug,
      question: questionId,
    }),
    {
      order_index: newOrderIndex,
    },
    {
      preserveScroll: true,
      preserveState: true,
    }
  )
}

function toggleQuestionActive(questionId, currentStatus) {
  router.put(
    route('games.questions.update', {
      game: props.game.data.slug,
      question: questionId,
    }),
    {
      is_active: currentStatus,
    }
  )
}

const showDeleteQuestionModal = ref(false)
const questionToDelete = ref({})

function openDeleteQuestionModal(question) {
  questionToDelete.value = question
  showDeleteQuestionModal.value = true
}

function closeDeleteQuestionModal() {
  showDeleteQuestionModal.value = false
  questionToDelete.value = {}
}

function deleteQuestion() {
  router.delete(
    route('games.questions.destroy', {
      game: props.game.data.slug,
      question: questionToDelete.value.id,
    }),
    {
      preserveScroll: true,
      preserveState: false,
    }
  )
}

function submit() {
  form.put(route('games.update', props.game.data))
}
</script>

<template>
  <Head :title="game.data.title" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <Typography class="text-xl font-semibold leading-tight text-gray-800">
          {{ game.data.title }}
        </Typography>

        <div class="grid grid-cols-2 gap-4">
          <button
            class="ml-auto"
            theme="danger-outline"
            icon="delete"
            @click="showDeleteModal = true"
          >
            <Icon icon="delete" size="xsmall" class="text-gray-400 hover:text-red-500" />
            <span class="sr-only">Delete game: {{ game.data.title }}</span>
          </button>

          <Button
            :href="route('games.control-panel', game.data.slug)"
            theme="primary"
            icon="settings"
          >
            Control Panel
          </Button>
        </div>
      </div>
    </template>

    <div class="flex gap-12">
      <div class="w-1/3 shrink-0">
        <Stack space="medium">
          <Typography variant="h2"> Game Details </Typography>

          <form @submit.prevent="submit">
            <Stack space="medium">
              <div>
                <FormTextInput
                  v-model="form.title"
                  label="Title"
                  :error="form.errors.title"
                  required
                />
              </div>

              <div>
                <FormTextarea
                  v-model="form.description"
                  rows="2"
                  label="Description"
                  :error="form.errors.description"
                />
              </div>

              <div>
                <FormTextInput
                  v-model="form.access_code"
                  label="Access Code"
                  :error="form.errors.access_code"
                />
              </div>

              <Stack space="medium">
                <FormSelect
                  v-model="form.votable_host_1_id"
                  label="Votable Host 1"
                  :options="props.hosts"
                  optionLabel="name"
                  optionValue="id"
                  :error="form.errors.votable_host_1_id"
                />

                <FormSelect
                  v-model="form.votable_host_2_id"
                  label="Votable Host 2"
                  :options="props.hosts"
                  optionLabel="name"
                  optionValue="id"
                  :error="form.errors.votable_host_2_id"
                />
              </Stack>

              <div class="flex justify-end space-x-4">
                <Button
                  type="submit"
                  :loading="form.processing"
                  :isDisabled="form.processing || !form.isDirty"
                >
                  Save Changes
                </Button>
              </div>
            </Stack>
          </form>

          <div class="bg-white w-2/5 p-3">
            <img :src="game.data.qr_code_url" alt="QR Code" />
          </div>
        </Stack>
      </div>

      <div class="w-full">
        <Stack space="medium">
          <div class="flex justify-between items-center">
            <Typography variant="h2"> Questions </Typography>

            <Button icon="add" theme="primary-outline" @click="isSlideoverOpen = true">
              Add Question
            </Button>
          </div>

          <template v-if="game.data.questions.length > 0">
            <draggable
              v-model="questionOrder.order"
              :list="questionOrder.order"
              item-key="id"
              handle=".drag-handle"
              @change="handleQuestionReorder"
              class="space-y-4"
            >
              <template #item="{ element: question }">
                <Card as="li" padding="xsmall" class="flex items-center justify-between w-full">
                  <div class="flex items-center gap-2">
                    <Icon icon="drag" class="drag-handle cursor-move text-gray-400" />
                    <Stack space="xxsmall">
                      <Typography variant="body-lg">
                        {{ question.question }}
                      </Typography>
                      <Typography variant="body-small" class="text-gray-500">
                        {{ question.host.user.name }}
                      </Typography>
                    </Stack>
                  </div>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center space-x-3">
                      <Typography variant="body-small" class="text-gray-500">
                        Play Live
                      </Typography>
                      <Switch
                        v-model="question.is_active"
                        @update:model-value="toggleQuestionActive(question.id, question.is_active)"
                      />
                    </div>
                    <button
                      @click="openDeleteQuestionModal(question)"
                      class="text-red-500 opacity-60 hover:opacity-100"
                    >
                      <Icon icon="delete" size="xsmall" />
                    </button>
                  </div>
                </Card>
              </template>
            </draggable>
          </template>

          <template v-else>
            <div class="border-2 border-dashed border-gray-200 p-4 rounded-md">
              <Typography> You haven't created any questions yet. </Typography>
            </div>
          </template>
        </Stack>
      </div>
    </div>

    <teleport to="body">
      <Modal
        title="Delete Qestion"
        :isOpen="showDeleteQuestionModal"
        @close="closeDeleteQuestionModal"
      >
        <div class="flex space-x-6 w-full">
          <Icon icon="danger" class="text-red-500" size="large" />
          <Stack class="w-full">
            <Stack space="small">
              <Typography variant="h1">
                Are you sure you want to delete:
                <span class="font-semibold">{{ questionToDelete.question }} </span>?
              </Typography>
              <Typography> This action cannot be undone. </Typography>
            </Stack>
            <div class="flex justify-end space-x-3">
              <Button theme="subdued" @click="closeDeleteQuestionModal"> Cancel </Button>
              <Button theme="danger" @click="deleteQuestion"> Delete </Button>
            </div>
          </Stack>
        </div>
      </Modal>
    </teleport>

    <teleport to="body">
      <Modal title="Delete Assessment" :isOpen="showDeleteModal" @close="showDeleteModal = false">
        <div class="flex space-x-6 w-full">
          <Icon icon="danger" class="text-red-500" size="large" />
          <Stack class="w-full">
            <Stack space="small">
              <Typography variant="h1">
                Are you sure you want to delete
                {{ game.data.title }}?
              </Typography>
              <Typography> This action cannot be undone. </Typography>
            </Stack>
            <div class="flex justify-end space-x-3">
              <Button theme="subdued" @click="showDeleteModal = false"> Cancel </Button>
              <Button theme="danger" @click="deleteGame"> Delete </Button>
            </div>
          </Stack>
        </div>
      </Modal>
    </teleport>

    <teleport to="body">
      <SlideOut title="Add Question" :isOpen="isSlideoverOpen" @close="isSlideoverOpen = false">
        <form @submit.prevent="createQuestion">
          <Stack>
            <FormTextInput
              id="question"
              name="question"
              label="Question"
              v-model="formQuestion.question"
              required
              :error="formQuestion.errors.question"
            />

            <div class="flex justify-end space-x-4">
              <Button
                type="submit"
                :loading="formQuestion.processing"
                :isDisabled="formQuestion.processing"
              >
                Create
              </Button>
            </div>
          </Stack>
        </form>
      </SlideOut>
    </teleport>
  </AuthenticatedLayout>
</template>
