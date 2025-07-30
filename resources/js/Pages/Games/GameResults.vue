<script setup>
import { Head } from '@inertiajs/vue3'
import Typography from '@/Stencil/Typography.vue'
import Avatar from '@/Stencil/Avatar.vue'
import Stack from '@/Stencil/Stack.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  votableHosts: {
    type: Array,
    required: true,
  },
})

const asset = (path) => {
  return path?.startsWith('http') ? path : `/storage/${path}`
}

const isWinningHost = (question, hostId) => {
  const hostVotes = question.votesByHost[hostId] || 0
  const otherHostId = props.votableHosts.find((h) => h.id !== hostId)?.id
  const otherHostVotes = question.votesByHost[otherHostId] || 0
  return hostVotes > otherHostVotes
}
</script>

<template>
  <Head :title="`Analytics - ${game.data.title}`" />
  <div class="min-h-screen flex flex-col space-y-6 mb-24">
    <header class="border-b border-gray-200">
      <div class="container mx-auto flex items-center justify-between px-6 md:px-0 py-3">
        <ApplicationLogo class="block w-10 fill-current text-gray-800" />
      </div>
    </header>
    <div class="container mx-auto flex flex-col md:flex-row gap-6 px-6 md:px-0">
      <div class="w-full md:w-1/4">
        <div class="p-3 rounded-md shadow-md bg-white border border-gray-200 w-full">
          <Stack space="small">
            <Typography variant="h2" class="text-gray-800">
              {{ game.data.title }}
            </Typography>
            <table>
              <tbody>
                <tr>
                  <td>
                    <Typography variant="body-lg" class="text-gray-800"> Total votes:</Typography>
                  </td>
                  <td class="text-right font-semibold">
                    {{ game.data.votes.length }}
                  </td>
                </tr>
              </tbody>
            </table>
            <ul class="flex flex-col gap-4">
              <li v-for="host in votableHosts" :key="host.id" class="relative">
                <div
                  class="absolute bg-red-500 h-full rounded-full"
                  :style="{
                    width: `${(host.votes.length / game.data.votes.length) * 100}%`,
                    backgroundColor: `${host.color}`,
                  }"
                />
                <div class="relative flex items-center justify-between w-full">
                  <div class="flex items-center gap-2">
                    <img :src="asset(host.avatar)" class="h-16 w-auto" />
                    <Typography class="text-white font-bold" variant="body-lg"
                      >{{
                        Math.round((host.votes.length / game.data.votes.length) * 100)
                      }}%</Typography
                    >
                  </div>
                  <Typography variant="body-lg" class="text-gray-800">
                    {{ host.votes.length }}
                  </Typography>
                </div>
              </li>
            </ul>
          </Stack>
        </div>
      </div>
      <ul class="w-full md:w-3/4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <li
          v-for="question in game.data.questions"
          :key="question.id"
          class="bg-white border border-gray-200 shadow-lg p-4 rounded-md"
        >
          <Typography variant="h2" class="text-gray-800 mb-6">
            {{ question.question }}
          </Typography>
          <ul class="flex flex-col gap-4">
            <li
              v-for="host in votableHosts"
              :key="host.id"
              class="px-4 py-2 rounded-md"
              :class="{
                'bg-green-100 border border-green-600 order-first': isWinningHost(
                  question,
                  host.id
                ),
              }"
            >
              <div class="flex items-center gap-2 relative">
                <Avatar :src="asset(host.avatar)" />
                <div>
                  <Typography
                    v-if="isWinningHost(question, host.id)"
                    variant="body-small"
                    class="text-green-500"
                  >
                    Winner
                  </Typography>
                  <Typography class="font-semibold">
                    {{ host.name }}
                  </Typography>
                  <Typography>
                    {{ question.votesByHost[host.id] }}
                  </Typography>
                </div>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</template>
