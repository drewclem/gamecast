<script setup>
import { Head } from '@inertiajs/vue3'
import Typography from '@/Stencil/Typography.vue'
import Avatar from '@/Stencil/Avatar.vue'

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
  <div class="min-h-screen bg-yellow-100 flex flex-col p-6 md:p-8 lg:p-12">
    <div class="mb-6">
      <Typography variant="billboard" class="text-center text-gray-900">
        {{ game.data.title }}
      </Typography>
    </div>

    <div>
      <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <li
          v-for="question in game.data.questions"
          :key="question.id"
          class="bg-white shadow-lg p-4 rounded-md"
        >
          <Typography variant="h2" class="text-gray-800 mb-6">
            {{ question.question }}
          </Typography>
          <ul class="flex flex-col gap-4">
            <li
              v-for="host in votableHosts"
              :key="host.id"
              class="px-4 py-2 rounded-md"
              :class="{ 'bg-green-100 border border-green-600': isWinningHost(question, host.id) }"
            >
              <div class="flex items-center gap-2 relative">
                <Avatar :src="asset(host.avatar)" />
                <div>
                  <Typography v-if="isWinningHost(question, host.id)" variant="body-small">
                    Winner
                  </Typography>
                  <Typography>
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
