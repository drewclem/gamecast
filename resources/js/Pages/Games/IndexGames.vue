<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import Button from '@/Stencil/Button.vue'
import Typography from '@/Stencil/Typography.vue'
import SlideOut from '@/Stencil/SlideOut.vue'
import Stack from '@/Stencil/Stack.vue'
import Icon from '@/Stencil/Icon.vue'
import Card from '@/Stencil/Card.vue'
import FormTextInput from '@/Stencil/Forms/FormInput.vue'
import FormTextarea from '@/Stencil/Forms/FormTextarea.vue'

const props = defineProps({
  games: {
    type: Array,
    required: true,
  },
  show: {
    type: Object,
    required: true,
  },
})

const isSlideoverOpen = ref(false)

const form = useForm({
  title: '',
  access_code: '',
  description: '',
})

function createGame() {
  form.post(route('games.store', { show: props.show.id }))
}

function navigateToGame(slug) {
  router.visit(route('games.edit', { game: slug }))
}
</script>

<template>
  <Head title="Games" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <Typography class="text-xl font-semibold leading-tight text-gray-800"> Games </Typography>

        <Button icon="add" @click="isSlideoverOpen = true"> Create Game </Button>
      </div>
    </template>

    <div>
      <div v-if="games.length === 0" class="text-center py-8">
        <Stack space="medium">
          <Typography variant="body-lg"> You haven't created any games yet. </Typography>
          <div>
            <Button icon="add" @click="isSlideoverOpen = true"> Create Game </Button>
          </div>
        </Stack>
      </div>

      <ul class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card
          as="li"
          v-for="game in games"
          :key="game.id"
          @click="navigateToGame(game.slug)"
          class="cursor-pointer"
        >
          <Stack class="justify-between h-full">
            <div>
              <Typography variant="h3">
                {{ game.title }}
              </Typography>

              <Typography v-if="game.description" class="text-gray-500">
                {{ game.description }}
              </Typography>
            </div>

            <Link :href="route('games.edit', game.slug)">
              <Icon icon="settings" size="xsmall" class="inline -mt-1 text-amber-500" />
              Edit
            </Link>
          </Stack>
        </Card>
      </ul>
    </div>

    <teleport to="body">
      <SlideOut :isOpen="isSlideoverOpen" title="Create Episode" @close="isSlideoverOpen = false">
        <form>
          <Stack>
            <Stack space="medium">
              <FormTextInput
                label="Title"
                name="title"
                v-model="form.title"
                :error="form.errors.title"
                required
              />
              <FormTextInput
                label="Access Code"
                name="access_code"
                helper="This password will be used to protect the episode. It will be required to join."
                v-model="form.access_code"
                :error="form.errors.access_code"
                required
              />
              <FormTextarea
                label="Description"
                name="description"
                v-model="form.description"
                :error="form.errors.description"
              />
            </Stack>

            <div class="flex justify-end">
              <Button theme="primary-outline" @click="createGame"> Create Game </Button>
            </div>
          </Stack>
        </form>
      </SlideOut>
    </teleport>
  </AuthenticatedLayout>
</template>
