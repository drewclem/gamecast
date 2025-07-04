<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import { useQuestionState } from '@/Composables/useQuestionState'
import { useGameUpdates } from '@/Composables/useGameUpdates'

import Typography from '@/Stencil/Typography.vue'
import Icon from '@/Stencil/Icon.vue'
import Card from '@/Stencil/Card.vue'
import Stack from '@/Stencil/Stack.vue'
import Spinner from '@/Stencil/Spinner.vue'
const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  hosts: {
    type: Array,
    required: true,
  },
})

// Set up game updates with real-time broadcasting
const { data } = useGameUpdates(props.game.data.id)

// Set up question state management
const {
  currentQuestion,
  votingStatus,
  voteCounts,
  votingOpen,
  votingClosed,
  resultsRevealed,
  loading,
} = useQuestionState(props.game.data.slug, props.game.data.id)

// Animation states
const isRevealAnimating = ref(false)
const countdownNumber = ref(null)
const showQuestion = ref(false)
const showHostsEntrance = ref(false)
const hostEntranceComplete = ref(false)

// Debug logs for question updates - these help diagnose issues
console.log('Initial question state:', currentQuestion.value)

// Play intro sequence on mount
onMounted(() => {
  // Add immediate data check - this will help diagnose if data is missing initially
  console.log('Game data on mount:', props.game.data)

  setTimeout(() => {
    if (currentQuestion.value) {
      showQuestion.value = true
      // After question appears, bring in the hosts
      setTimeout(() => {
        showHostsEntrance.value = true
        // Mark host entrance as complete after animation
        setTimeout(() => {
          hostEntranceComplete.value = true // Fixed - was incorrectly set to RiTruckLine
        }, 1000)
      }, 500)
    }
  }, 1000) // Reduced to 1000ms for faster initial display
})

// Watch for reveal event
watch(resultsRevealed, (newValue, oldValue) => {
  // If transitioning from not revealed to revealed, trigger animation
  if (newValue && !oldValue) {
    triggerRevealAnimation()
  }
})

// Watch for new questions - add debugging
watch(
  () => currentQuestion.value,
  (newQuestion, oldQuestion) => {
    console.log('Question changed:', {
      new: newQuestion?.id,
      old: oldQuestion?.id,
      fullNewQuestion: newQuestion,
    })

    if (newQuestion && (!oldQuestion || newQuestion.id !== oldQuestion.id)) {
      console.log('Animations triggered for new question')
      // Reset animations for new question
      showQuestion.value = false
      showHostsEntrance.value = false
      hostEntranceComplete.value = false

      // Play entrance animation
      setTimeout(() => {
        showQuestion.value = true
        setTimeout(() => {
          showHostsEntrance.value = true
          setTimeout(() => {
            hostEntranceComplete.value = true
          }, 1000)
        }, 500)
      }, 500)
    }
  },
  { deep: true } // Added deep watching to catch internal property changes
)

// Also watch game updates for activeQuestion
watch(
  () => data.activeQuestion,
  (newQuestion) => {
    console.log('Game update detected new question:', newQuestion)
    // This could be a fallback if the Echo events aren't working
    if (newQuestion && (!currentQuestion.value || newQuestion.id !== currentQuestion.value.id)) {
      console.log('Forcing question update from game updates')
      // Force update if needed - this is a backup method
      currentQuestion.value = newQuestion
    }
  },
  { deep: true }
)

// Trigger results reveal animation
function triggerRevealAnimation() {
  isRevealAnimating.value = true

  // Dramatic countdown effect
  countdownNumber.value = 3

  const countdownInterval = setInterval(() => {
    countdownNumber.value--

    if (countdownNumber.value <= 0) {
      clearInterval(countdownInterval)
      countdownNumber.value = null

      // Finish animation after short delay
      setTimeout(() => {
        isRevealAnimating.value = false
      }, 1000)
    }
  }, 1000)
}

function getVotesForHost(hostId) {
  return voteCounts.value?.byHost?.[hostId] || 0
}

function getVotePercentage(hostId) {
  const total = voteCounts.value?.total || 0
  if (!total) return 0
  return Math.round(((voteCounts.value?.byHost?.[hostId] || 0) / total) * 100)
}

const isTie = computed(() => {
  return currentQuestion.value?.winners?.length > 1
})

const joinUrl = computed(() => {
  return route('games.join', props.game.data.slug)
})

const activeVotes = computed(() => {
  return voteCounts.value?.total || 0
})
</script>

<template>
  <Head :title="`${game.data.title} - Gameshow View`" />

  <!-- Full screen gameshow layout -->
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

    <!-- Main content grid -->
    <div class="flex-1">
      <div class="grid grid-cols-12 gap-6 h-full">
        <!-- Left column - Game content -->
        <div class="col-span-12 md:col-span-8 flex flex-col">
          <!-- Loading state -->
          <div v-if="loading" class="flex-1 flex items-center justify-center">
            <Stack space="medium" class="text-center">
              <div class="mx-auto">
                <Icon icon="loading" size="large" class="text-gray-800 animate-spin" />
              </div>
              <Typography variant="h2" class="text-gray-800"> Loading... </Typography>
            </Stack>
          </div>

          <!-- Waiting for question state -->
          <Card v-else-if="!currentQuestion">
            <Stack space="medium" class="text-center">
              <Typography variant="h2" class="text-gray-800">
                Waiting for the host to select a question
              </Typography>
              <Typography variant="body-lg" class="text-gray-600">
                The game will begin shortly...
              </Typography>
            </Stack>
          </Card>

          <!-- Game content -->
          <div v-else class="flex-1 flex flex-col">
            <template v-if="showQuestion">
              <Card
                class="mb-6 transform transition-transform duration-500"
                :class="{
                  'scale-100 translate-y-0 opacity-100': showQuestion,
                  'scale-90 -translate-y-10 opacity-0': !showQuestion,
                }"
              >
                <Stack space="medium" class="p-6 text-center">
                  <Typography variant="billboard">
                    {{ currentQuestion.question }}
                  </Typography>

                  <div
                    v-if="currentQuestion.host"
                    class="flex justify-center items-center space-x-3"
                  >
                    <div
                      class="w-10 h-10 rounded-full"
                      :style="{ backgroundColor: currentQuestion.host.color }"
                    >
                      <img
                        :src="`/storage/${currentQuestion.host.avatar}`"
                        :alt="currentQuestion.host.name"
                        class="w-full"
                      />
                    </div>
                    <Typography variant="h3" class="text-gray-900">
                      {{ currentQuestion.host.name }}
                    </Typography>
                  </div>
                </Stack>
              </Card>

              <Card>
                <Stack space="small">
                  <!-- Status indicator -->
                  <div class="text-center">
                    <div
                      class="inline-block px-3 py-1 rounded-full border-2"
                      :class="{
                        'bg-green-100 text-green-600 border-green-500': votingOpen,
                        'bg-yellow-200 text-yellow-900 border-yellow-500':
                          votingClosed && !resultsRevealed,
                        'bg-blue-100 text-blue-900 border-blue-500': resultsRevealed,
                      }"
                    >
                      <Typography>
                        {{
                          votingOpen ? 'VOTE NOW!' : resultsRevealed ? 'RESULTS' : 'VOTING CLOSED'
                        }}
                      </Typography>
                    </div>
                  </div>

                  <!-- Voting phase content -->
                  <div v-if="votingOpen" class="flex-1 flex flex-col">
                    <!-- Host options -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                      <Card
                        v-for="(host, index) in hosts"
                        :key="host.id"
                        class="transform transition-all duration-500 overflow-hidden rounded-xl shadow-lg"
                        :class="{
                          'translate-x-0 opacity-100': showHostsEntrance,
                          '-translate-x-full opacity-0': !showHostsEntrance && index === 0,
                          'translate-x-full opacity-0': !showHostsEntrance && index === 1,
                        }"
                        :style="{ backgroundColor: host.color }"
                      >
                        <div class="p-3 flex flex-col items-center">
                          <img
                            :src="`/storage/${host.avatar}`"
                            :alt="host.name"
                            class="max-w-full h-32 object-contain"
                          />
                          <div
                            class="w-full bg-opacity-60 py-2 px-3 mt-2 rounded"
                            :style="{ backgroundColor: host.color }"
                          >
                            <Typography variant="h3" class="text-white text-center">{{
                              host.name
                            }}</Typography>
                          </div>
                        </div>
                      </Card>
                    </div>

                    <!-- Live vote counter -->
                    <div>
                      <div class="transform transition-all duration-500 text-center">
                        <Typography
                          variant="billboard"
                          class="text-7xl font-black text-gray-800 mb-2"
                        >
                          {{ activeVotes }}
                        </Typography>
                        <Typography variant="h3" class="text-2xl text-gray-600">
                          {{ activeVotes === 1 ? 'VOTE' : 'VOTES' }}
                        </Typography>
                      </div>
                    </div>
                  </div>

                  <!-- Voting closed message -->
                  <div
                    v-else-if="votingClosed && !resultsRevealed"
                    class="flex-1 flex flex-col items-center justify-center"
                  >
                    <Stack space="small" class="text-center">
                      <Typography variant="h2">
                        {{ activeVotes }} {{ activeVotes === 1 ? 'vote' : 'votes' }} cast
                      </Typography>
                      <Typography> Voting has ended! </Typography>
                      <Typography variant="h3" class="text-green-500">
                        Results coming soon...
                      </Typography>
                    </Stack>
                  </div>

                  <!-- Results display -->
                  <div v-else-if="resultsRevealed" class="flex-1">
                    <!-- Results reveal animation -->
                    <div v-if="isRevealAnimating" class="h-full flex items-center justify-center">
                      <div v-if="countdownNumber" class="text-center">
                        <Typography
                          variant="billboard"
                          class="text-8xl font-black text-red-500 animate-pulse text-shadow"
                        >
                          {{ countdownNumber }}
                        </Typography>
                      </div>
                      <Typography
                        v-else
                        variant="h1"
                        class="text-4xl font-bold text-gray-text-red-500 animate-pulse"
                      >
                        The winner is...
                      </Typography>
                    </div>

                    <!-- Tie result -->
                    <div
                      v-else-if="isTie && currentQuestion.winners?.length > 0"
                      class="h-full flex flex-col"
                    >
                      <div class="text-center mb-6">
                        <Typography variant="billboard" class="text-gray-800 mb-4"
                          >IT'S A TIE!</Typography
                        >
                        <div class="flex justify-center items-center gap-4">
                          <div
                            v-for="winner in currentQuestion.winners"
                            :key="winner.id"
                            class="flex flex-col items-center justify-center shadow-xl rounded-full w-32 h-32"
                            :style="{ backgroundColor: winner.color }"
                          >
                            <img
                              :src="`/storage/${winner.avatar}`"
                              :alt="winner.name"
                              class="w-24 h-24 object-contain"
                            />
                          </div>
                        </div>
                      </div>

                      <!-- Vote bars -->
                      <div class="mt-4">
                        <div v-for="host in hosts" :key="host.id" class="mb-4">
                          <div class="flex justify-between items-center mb-1">
                            <Typography variant="body-lg" class="font-bold">{{
                              host.name
                            }}</Typography>
                            <Typography variant="body-lg"
                              >{{ getVotesForHost(host.id) }} votes</Typography
                            >
                          </div>
                          <div class="h-8 bg-gray-200 rounded-full overflow-hidden relative">
                            <div
                              class="absolute inset-y-0 left-0 transition-all duration-1000 rounded-full flex items-center justify-end pr-2"
                              :style="{
                                width: `${getVotePercentage(host.id)}%`,
                                backgroundColor: host.color,
                              }"
                            >
                              <Typography variant="body" class="text-white font-bold">
                                {{ getVotePercentage(host.id) }}%
                              </Typography>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Single winner result -->
                    <div
                      v-else-if="currentQuestion.winners?.length > 0"
                      class="h-full flex flex-col"
                    >
                      <div class="text-center mb-6">
                        <div class="flex flex-col items-center justify-center">
                          <div class="relative mb-4">
                            <div
                              class="absolute -inset-4 rounded-full opacity-25 animate-pulse"
                              :style="{ backgroundColor: currentQuestion.winners[0].color }"
                            ></div>
                            <div
                              class="relative rounded-full p-3 shadow-xl w-40 h-40 flex items-center justify-center"
                              :style="{ backgroundColor: currentQuestion.winners[0].color }"
                            >
                              <img
                                :src="`/storage/${currentQuestion.winners[0].avatar}`"
                                :alt="currentQuestion.winners[0].name"
                                class="w-32 h-32 object-contain"
                              />
                            </div>
                          </div>
                          <Typography variant="h1" class="text-gray-900 mb-2">
                            {{ currentQuestion.winners[0].name }}
                          </Typography>
                          <Typography variant="billboard" class="text-gray-800 font-bold">
                            WINS!
                          </Typography>
                        </div>
                      </div>

                      <!-- Vote bars -->
                      <div class="mt-4">
                        <div v-for="host in hosts" :key="host.id" class="mb-4">
                          <div class="flex justify-between items-center mb-1">
                            <Typography variant="body-lg" class="font-bold">
                              {{ host.name }}
                            </Typography>
                            <Typography variant="body-lg">
                              {{ getVotesForHost(host.id) }} votes
                            </Typography>
                          </div>
                          <div class="h-8 bg-gray-200 rounded-full overflow-hidden relative">
                            <div
                              class="absolute inset-y-0 left-0 transition-all duration-1000 rounded-full flex items-center justify-end pr-2"
                              :style="{
                                width: `${getVotePercentage(host.id)}%`,
                                backgroundColor: host.color,
                              }"
                            >
                              <Typography variant="body" class="text-white font-bold">
                                {{ getVotePercentage(host.id) }}%
                              </Typography>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- No votes case -->
                    <div v-else class="h-full flex items-center justify-center">
                      <div class="text-center">
                        <Typography variant="h2" class="text-gray-800 mb-3">
                          No votes were cast
                        </Typography>
                        <Typography variant="body-lg" class="text-gray-600">
                          Everyone must have been too shy!
                        </Typography>
                      </div>
                    </div>
                  </div>
                </Stack>
              </Card>
            </template>

            <template v-else>
              <div class="flex-1 flex justify-center p-12">
                <Spinner class="h-24 w-24 text-yellow-600" />
              </div>
            </template>
          </div>
        </div>

        <!-- Right column - QR code -->
        <div class="col-span-12 md:col-span-4 flex flex-col">
          <Card class="h-full">
            <Stack
              space="small"
              class="h-full flex flex-col justify-center items-center text-center p-4"
            >
              <Typography variant="h2" class="text-gray-800">Join the game!</Typography>

              <div class="w-full aspect-square">
                <img
                  :src="game.data.qr_code_url"
                  alt="QR Code to join game"
                  class="w-full h-full object-contain"
                />
              </div>

              <div class="text-center">
                <Typography variant="h2" class="text-gray-700">Scan to play</Typography>
                <Typography class="text-gray-700">{{ joinUrl }}</Typography>
              </div>

              <div class="mt-auto pt-4">
                <div class="bg-gray-100 p-3 rounded-lg">
                  <Typography variant="body-lg" class="text-gray-800 font-bold">
                    How to play:
                  </Typography>
                  <Stack space="small" class="mt-2">
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

<style scoped>
.animate-pulse {
  @apply transition-all duration-500 ease-in-out;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.25;
    transform: scale(0.95);
  }
  50% {
    opacity: 0.5;
    transform: scale(1.02);
    transition: all 0.5s ease-in-out;
  }
}
</style>
