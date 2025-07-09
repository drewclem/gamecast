<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  invitation: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post(route('accept-host-invite', props.invitation), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Accept Invite" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <div class="flex flex-col space-y-4">
      <div class="flex flex-col space-y-1">
        <span class="text-xs text-gray-500"
          >You've been invited to co-host: {{ invitation.host.user.email }}</span
        >
        <h1 class="text-amber-500 text-2xl font-bold">
          Welcome to the show, {{ invitation.host.name }}!
        </h1>
        <p class="text-xs text-gray-500">Set your password to get started</p>
      </div>

      <form @submit.prevent="submit">
        <div>
          <InputLabel for="password" value="Password" />

          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autofocus
          />

          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirm Password" />

          <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
            required
            autocomplete="current-password"
          />

          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4 flex items-center justify-end">
          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
          >
            Forgot your password?
          </Link>

          <PrimaryButton
            class="ms-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing || !form.isDirty"
          >
            Log in
          </PrimaryButton>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>
