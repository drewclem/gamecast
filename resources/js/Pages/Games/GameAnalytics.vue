<script setup>
import { computed, watch } from 'vue'
import { Head, usePoll } from '@inertiajs/vue3'
import { useGameUpdates } from '@/Composables/useGameUpdates'

import Typography from '@/Stencil/Typography.vue'
import Avatar from '@/Stencil/Avatar.vue'
import Card from '@/Stencil/Card.vue'
import Stack from '@/Stencil/Stack.vue'

const props = defineProps({
  game: { type: Object, required: true },
  hosts: { type: Array, required: true },
  voteCounts: { type: Object, required: true },
})

const { data } = useGameUpdates(props.game.data.id)

const asset = (path) => {
  return path?.startsWith('http') ? path : `/storage/${path}`
}

usePoll(
  15000,
  {},
  {
    keepAlive: true,
  }
)

const joinUrl = computed(() => {
  return route('games.join', props.game.data.slug)
})
</script>

<template>
  <Head :title="`Analytics - ${game.data.title}`" />
  <div class="min-h-screen bg-yellow-100 flex flex-col p-6 md:p-8 lg:p-12">
    <div class="mb-6">
      <Typography variant="billboard" class="text-center text-gray-900">
        {{ game.data.title }}
      </Typography>
      <div class="flex justify-center items-center">
        <div class="bg-gray-800 text-white px-4 py-1 rounded-full">
          <Typography>
            {{ data.activeWatchers || 0 }}
            {{ data.activeWatchers === 1 ? 'player' : 'players' }}
          </Typography>
        </div>
      </div>
    </div>

    <div class="flex-1">
      <div class="grid grid-cols-12 gap-6 h-full">
        <div class="col-span-12 md:col-span-9">
          <div class="flex flex-col gap-6">
            <!-- <pre>
              {{ data.gameData }}
            </pre> -->
            <Card v-for="question in data.gameData.value.questions" :key="question.id">
              <div class="flex flex-row items-center gap-6">
                <Stack space="small" class="w-1/3">
                  <Typography variant="h2" class="text-gray-800">
                    {{ question.question }}
                  </Typography>
                  <div class="flex items-center gap-2">
                    <Avatar
                      v-if="question?.host?.avatar"
                      size="xsmall"
                      :src="asset(question.host.avatar)"
                    />
                    <Typography variant="body" class="text-gray-800">
                      {{ question?.host?.name }}
                    </Typography>
                  </div>
                </Stack>

                <div class="w-2/3">
                  <div class="relative flex items-center">
                    <!-- Left avatar (first host) -->
                    <div class="absolute -left-2 top-1/2 -translate-y-1/2 z-10">
                      <!-- <Avatar v-if="hosts[0]?.avatar" :src="asset(hosts[0].avatar)" /> -->
                      <img
                        v-if="hosts[0]?.avatar"
                        :src="asset(hosts[0].avatar)"
                        alt="Host avatar"
                        class="h-16 object-cover"
                      />
                    </div>

                    <!-- Right avatar (second host) -->
                    <div class="absolute -right-2 top-1/2 -translate-y-1/2 z-10">
                      <!-- <Avatar v-if="hosts[1]?.avatar" :src="asset(hosts[1].avatar)" /> -->
                      <img
                        v-if="hosts[1]?.avatar"
                        :src="asset(hosts[1].avatar)"
                        alt="Host avatar"
                        class="h-16 object-cover"
                      />
                    </div>

                    <!-- Vote bars -->
                    <div class="w-full h-6 relative flex items-center">
                      <div
                        class="absolute left-0 top-0 h-full transition-all duration-1000 ease-out rounded-l-full"
                        :style="{
                          width: `${Math.max(
                            1,
                            ((question?.votesByHost?.[hosts[0].id] || 0) /
                              (question?.votes.length || 1)) *
                              100
                          )}%`,
                          backgroundColor: hosts[0].color,
                          zIndex: 1,
                        }"
                      ></div>
                      <div
                        class="absolute right-0 top-0 h-full transition-all duration-1000 ease-out rounded-r-full"
                        :style="{
                          width: `${Math.max(
                            1,
                            ((question?.votesByHost?.[hosts[1].id] || 0) /
                              (question?.votes.length || 1)) *
                              100
                          )}%`,
                          backgroundColor: hosts[1].color,
                          zIndex: 1,
                        }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </Card>
          </div>
        </div>

        <!-- Right column - QR code -->
        <div class="col-span-12 md:col-span-3 flex flex-col">
          <Card>
            <Stack
              space="small"
              class="h-full flex flex-col justify-center items-center text-center p-4"
            >
              <Stack space="xxsmall">
                <Typography variant="h2" class="text-gray-800">Join the game!</Typography>
                <Typography class="text-gray-700">Scan to play</Typography>
              </Stack>

              <div class="w-full aspect-square">
                <img
                  :src="game.data.qr_code_url"
                  alt="QR Code to join game"
                  class="w-full h-full object-contain"
                />
              </div>

              <div class="text-center">
                <Typography variant="body-large"
                  >Password: <span class="font-bold">{{ game.data.access_code }}</span></Typography
                >
                <Typography class="text-gray-700">{{ joinUrl }}</Typography>
              </div>

              <div class="mt-auto pt-4">
                <div class="bg-gray-100 p-3 rounded-lg">
                  <Typography variant="body-lg" class="text-gray-800 font-bold">
                    How to play:
                  </Typography>
                  <Stack space="small" class="mt-1">
                    <div class="flex items-center gap-2">
                      <Typography variant="body" class="text-gray-700 font-bold">1.</Typography>
                      <Typography variant="body" class="text-gray-700">Scan the QR code</Typography>
                    </div>
                    <div class="flex items-center gap-2">
                      <Typography variant="body" class="text-gray-700 font-bold">2.</Typography>
                      <Typography variant="body" class="text-gray-700">Enter a nickname</Typography>
                    </div>
                    <div class="flex items-center gap-2">
                      <Typography variant="body" class="text-gray-700 font-bold">3.</Typography>
                      <Typography variant="body" class="text-gray-700"
                        >Vote for your favorite host!</Typography
                      >
                    </div>
                  </Stack>
                </div>
              </div>
            </Stack>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>
