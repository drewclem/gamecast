// resources/js/Pages/Games/Session.vue
<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useQuestionState } from '@/Composables/useQuestionState'
import { useGameUpdates } from '@/Composables/useGameUpdates'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Button from '@/Stencil/Button.vue'
import Icon from '@/Stencil/Icon.vue'
import Timer from '@/Stencil/Timer.vue'
const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  watcher: {
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

// Set up question state management with real-time broadcasting
const {
  currentQuestion,
  votingStatus,
  voteCounts,
  votingOpen,
  votingClosed,
  resultsRevealed,
  timerRunning,
  timeRemaining,
  loading,
  startTimer,
  stopTimer,
  formatTime,
} = useQuestionState(props.game.data.slug, props.game.data.id)

// Local state
const hasVoted = computed(() =>
  props.watcher.votes.some((vote) => vote.question_id === currentQuestion.value.id)
)
const isVoting = ref(false)
const voteCast = ref(null)
const showVoteAnimation = ref(false)
const isRevealAnimating = ref(false)

// Watch for reveal event
watch(resultsRevealed, (newValue, oldValue) => {
  // If transitioning from not revealed to revealed, trigger animation
  if (newValue && !oldValue) {
    triggerRevealAnimation()
  }
})

// Sync question from game updates
watch(
  () => data.activeQuestion,
  (newQuestion) => {
    if (newQuestion && currentQuestion.value?.id !== newQuestion.id) {
      console.log('Question updated from broadcast:', newQuestion)
      // Reset vote-related state when question changes
      hasVoted.value = false
      voteCast.value = null
    }
  }
)

// Trigger results reveal animation
function triggerRevealAnimation() {
  // Start the reveal animation
  isRevealAnimating.value = true

  // Store the actual vote counts
  const currentVotes = voteCounts?.byHost || {}
  const tempVotes = { ...currentVotes }

  // Short delay for animation effect
  setTimeout(() => {
    // Show a "3, 2, 1" countdown effect
    let count = 3
    const countInterval = setInterval(() => {
      if (count <= 0) {
        clearInterval(countInterval)

        // Animate the results back in
        setTimeout(() => {
          // Restore actual vote counts
          voteCounts.byHost = tempVotes
          voteCounts.total = Object.values(tempVotes).reduce((a, b) => a + b, 0)

          // End animation state after results are shown
          setTimeout(() => {
            isRevealAnimating.value = false
          }, 1500)
        }, 500)
      } else {
        count--
      }
    }, 800)
  }, 500)
}

function submitVote(hostId) {
  return router.post(
    route('games.questions.vote', {
      game: props.game.data.slug,
      question: currentQuestion.value.id,
    }),
    {
      host_id: hostId,
    }
  )
}

// Cast a vote function
async function castVote(hostNumber) {
  if (!votingOpen.value || hasVoted.value || isVoting.value) return

  isVoting.value = true
  voteCast.value = hostNumber

  // Submit vote using the composable method
  const success = await submitVote(hostNumber)

  if (success) {
    hasVoted.value = true
  }

  isVoting.value = false
}

// Activity tracking - regularly ping to show we're still active
// let activityInterval

// function updateActivity() {
//   router.post(
//     route('games.update-activity', props.game.data.slug),
//     {},
//     {
//       preserveState: true,
//     }
//   )
// }

// onMounted(() => {
//   // Set up activity tracking - this is still needed for server-side presence
//   updateActivity()
//   activityInterval = setInterval(updateActivity, 30000) // Every 30 seconds
// })

// onBeforeUnmount(() => {
//   if (activityInterval) {
//     clearInterval(activityInterval)
//   }
// })

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
</script>

<template>
  <Head :title="`${game.data.title} - Live Game`" />

  <div class="min-h-screen bg-yellow-100 py-6 px-4 sm:px-6">
    <div class="max-w-xl mx-auto">
      <!-- Game Header -->
      <Card padding="medium" class="mb-6">
        <Stack space="small">
          <Typography variant="h1">{{ game.data.title }}</Typography>
          <div class="flex items-center justify-between">
            <Typography variant="body-small" class="text-gray-600">
              {{ data.activeWatchers }} active watcher(s)
            </Typography>
            <Typography variant="body-small" class="text-gray-600">
              Joined as: {{ watcher.gamertag }}
            </Typography>
          </div>
        </Stack>
      </Card>

      <!-- Waiting for Question -->
      <Card v-if="!currentQuestion" padding="large" class="text-center">
        <Stack space="medium">
          <Icon icon="loading" size="large" class="text-gray-400 animate-pulse" />
          <Stack space="small">
            <Typography variant="h2">Waiting for the first question...</Typography>
            <Typography variant="body-small" class="text-gray-600">
              The host will start the game soon
            </Typography>
          </Stack>
        </Stack>
      </Card>

      <!-- Question Card -->
      <Card v-else padding="medium" class="mb-6">
        <Stack space="medium">
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-8">
            <Icon icon="loading" size="large" class="text-gray-400 animate-spin" />
            <Typography variant="body" class="text-gray-600 mt-2">Loading...</Typography>
          </div>

          <!-- Question Content -->
          <template v-else>
            <!-- Status Badge -->
            <div class="text-center">
              <Typography
                variant="body-small"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full"
                :class="{
                  'bg-green-100 text-green-800': votingOpen,
                  'bg-yellow-100 text-yellow-800': votingClosed && !resultsRevealed,
                  'bg-blue-100 text-blue-800': resultsRevealed,
                }"
              >
                {{
                  votingOpen
                    ? 'Voting Open'
                    : resultsRevealed
                    ? 'Results Revealed'
                    : 'Voting Closed'
                }}
              </Typography>
            </div>

            <Typography variant="billboard" class="text-center">
              {{ currentQuestion.question }}
            </Typography>

            <!-- Voting Buttons -->
            <div v-if="votingOpen" class="grid grid-cols-2 gap-4">
              <button
                v-for="host in hosts"
                :key="host.id"
                :style="{ backgroundColor: host.color }"
                class="w-full flex items-center justify-center p-3 rounded-md group shadow-md"
                :class="{ 'pointer-events-none opacity-50': hasVoted }"
                @click="castVote(host.id)"
                :disabled="hasVoted || isVoting"
              >
                <img
                  :src="`/storage/${host.avatar}`"
                  :alt="host.name"
                  class="w-3/4 group-hover:scale-110 transition-all duration-300"
                />
                <span class="sr-only">Vote for {{ host.name }}</span>
              </button>
            </div>

            <!-- Vote Counter during open voting -->
            <div v-if="votingOpen" class="text-center">
              <Typography variant="body-small" class="text-gray-600">
                {{ voteCounts.total }}
                {{ voteCounts.total === 1 ? 'vote' : 'votes' }} cast so far
              </Typography>
            </div>

            <!-- Voting Closed Message -->
            <div v-else-if="votingClosed && !resultsRevealed" class="text-center">
              <Typography variant="body-lg" class="text-gray-600">
                Voting is now closed. Results will be revealed soon!
              </Typography>
              <Typography variant="body-small" class="text-gray-500 mt-2">
                {{ voteCounts?.total ?? 0 }}
                {{ voteCounts?.total === 1 ? 'vote' : 'votes' }} were cast
              </Typography>
            </div>

            <!-- Results Display -->
            <div v-else-if="resultsRevealed" class="py-4">
              <!-- Results Reveal Animation -->
              <div v-if="isRevealAnimating" class="text-center py-8">
                <Typography variant="h1" class="text-4xl font-bold animate-bounce">
                  Revealing Results...
                </Typography>
              </div>

              <!-- Results Display -->
              <div v-else>
                <Stack v-if="isTie" space="medium" class="text-center">
                  <Typography variant="h1">It's a Tie!</Typography>
                  <ul class="flex justify-center items-center gap-4">
                    <li
                      v-for="winner in currentQuestion.winners"
                      :key="winner.id"
                      :style="{ backgroundColor: winner.color }"
                      class="rounded-full p-1 h-20 w-20 flex items-center justify-center"
                    >
                      <img :src="`/storage/${winner.avatar}`" :alt="winner.name" class="w-16" />
                    </li>
                  </ul>
                </Stack>
                <div v-else>
                  <div class="relative rounded-lg mb-4 overflow-hidden">
                    <!-- Vote Bars -->
                    <!-- Winner Announcement -->
                    <Stack v-if="currentQuestion.winners" space="medium" class="text-center">
                      <Stack space="small">
                        <div
                          class="flex items-center justify-center p-12 rounded-full max-w-[75%] aspect-square mx-auto"
                          :style="{ backgroundColor: currentQuestion.winners[0].color }"
                        >
                          <img
                            :src="`/storage/${currentQuestion.winners[0].avatar}`"
                            :alt="currentQuestion.winners[0].name"
                            class="group-hover:scale-110 transition-all duration-300"
                          />
                        </div>
                        <Typography variant="billboard"
                          >{{ currentQuestion.winners[0].name }} wins!</Typography
                        >
                      </Stack>
                      <div
                        v-for="host in hosts"
                        :key="host.id"
                        class="relative h-16 mb-2 last:mb-0"
                      >
                        <div
                          class="absolute inset-0 transition-all duration-1000 rounded-md"
                          :style="{
                            width: `${getVotePercentage(host.id)}%`,
                            backgroundColor: host.color,
                            opacity: isRevealAnimating ? 0 : 1,
                          }"
                        >
                          <div class="absolute inset-0 flex items-center px-4">
                            <Typography variant="body" class="font-bold">
                              {{ host.name }}: {{ getVotesForHost(host.id) }}
                            </Typography>
                          </div>
                        </div>
                      </div>
                    </Stack>

                    <Typography variant="body-small" class="text-center text-gray-600 mt-4">
                      {{ voteCounts?.total ?? 0 }} total
                      {{ voteCounts?.total === 1 ? 'vote' : 'votes' }}
                    </Typography>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vote Confirmation -->
            <div v-if="hasVoted && votingOpen" class="text-center">
              <Typography variant="body-small" class="text-green-600"> You've voted! </Typography>
            </div>
          </template>
        </Stack>
      </Card>
    </div>
  </div>
</template>

<style scoped>
.host-vote-container {
  margin: 10px 0;
  background: #f0f0f0;
  border-radius: 4px;
  overflow: hidden;
}

.host-vote-bar {
  padding: 10px;
  background: #4a90e2;
  color: white;
  transition: width 0.3s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.host-vote-bar.winner {
  background: #2ecc71;
  font-weight: bold;
}

.host-name {
  font-weight: 500;
}

.vote-count {
  font-size: 0.9em;
}
</style>
