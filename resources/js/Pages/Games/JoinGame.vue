<script setup>
import { computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'

import Card from '@/Stencil/Card.vue'
import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
import Button from '@/Stencil/Button.vue'
import FormInput from '@/Stencil/Forms/FormInput.vue'

const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
})

const gameData = computed(() => props.game.data)

const form = useForm({
  gamertag: '',
  access_code: '',
})

function joinGame() {
  form.post(route('games.enter', gameData.value.slug))
}
</script>

<template>
  <Head :title="`Join ${gameData.title}`" />
  <div class="flex flex-col items-center justify-center min-h-screen bg-yellow-100 p-6">
    <Card class="w-full md:max-w-[300px]">
      <Stack>
        <Stack space="xsmall">
          <Typography>{{ gameData.title }}</Typography>
          <Typography variant="billboard">Join the fun!</Typography>
        </Stack>

        <form @submit.prevent="joinGame">
          <Stack space="medium">
            <FormInput
              label="Gamer Tag"
              v-model="form.gamertag"
              :required="true"
              :error="form.errors.gamertag"
            />
            <FormInput
              label="Access Code"
              v-model="form.access_code"
              :required="true"
              :error="form.errors.access_code"
            />

            <Button
              theme="secondary"
              @click="joinGame"
              :isDisabled="form.isSubmitting || !form.isDirty"
            >
              Join Game
            </Button>
          </Stack>
        </form>
      </Stack>
    </Card>
  </div>
</template>
