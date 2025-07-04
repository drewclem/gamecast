<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed, provide } from 'vue'
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

import OpenQuestions from '@/Components/Game/OpenQuestions.vue'

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
  openQuestions,
  timerRunning,
  watcher,
  timeRemaining,
  loading,
  startTimer,
  stopTimer,
  formatTime,
} = useQuestionState(props.game.data.slug, props.game.data.id)

// Local state for tracking votes that haven't been reflected in server state yet
const localVotes = ref([])

// Local state for UI
const isVoting = ref(false)
const voteCast = ref(null)
const showVoteAnimation = ref(false)
const isRevealAnimating = ref(false)
const animatingVote = ref(false)
const animatingHostId = ref(null)
const countdown = ref(3)

// Determine if user has voted on a specific question
const hasVotedOnQuestion = (questionId) => {
  // Check in both server state and local state
  return (
    watcher.value?.votes?.some((vote) => vote.question_id === questionId) ||
    localVotes.value.some((vote) => vote.questionId === questionId) ||
    false
  )
}

// Get the host that the user voted for
const getVotedHost = (questionId) => {
  // First check in watcher.value.votes (server state)
  if (watcher.value?.votes) {
    const vote = watcher.value.votes.find((vote) => vote.question_id === questionId)
    if (vote) {
      return props.hosts.find((host) => host.id === vote.host_id) || null
    }
  }

  // Then check in localVotes (client state)
  const localVote = localVotes.value.find((vote) => vote.questionId === questionId)
  if (localVote) {
    return props.hosts.find((host) => host.id === localVote.hostId) || null
  }

  return null
}

// Computed property to check if user has voted on the active question
const hasVoted = computed(() =>
  currentQuestion.value ? hasVotedOnQuestion(currentQuestion.value.id) : false
)

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
      voteCast.value = null
    }
  }
)

// Trigger results reveal animation
function triggerRevealAnimation() {
  // Start the reveal animation
  isRevealAnimating.value = true
  countdown.value = 3

  // Store the actual vote counts
  const currentVotes = voteCounts?.byHost || {}
  const tempVotes = { ...currentVotes }

  // Short delay for animation effect
  setTimeout(() => {
    // Show a "3, 2, 1" countdown effect
    const countInterval = setInterval(() => {
      if (countdown.value <= 1) {
        clearInterval(countInterval)
        countdown.value = 0

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
        countdown.value--
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
async function castVote(hostId) {
  if (!votingOpen.value || hasVoted.value || isVoting.value) return

  isVoting.value = true
  voteCast.value = hostId

  // Start vote animation
  animatingHostId.value = hostId
  animatingVote.value = true

  // Update local state immediately for responsive UI
  localVotes.value.push({
    questionId: currentQuestion.value.id,
    hostId: hostId,
  })

  try {
    // Submit vote using the composable method
    await submitVote(hostId)
  } catch (error) {
    console.error('Error voting on active question:', error)
    // Reset local state if the request fails
    localVotes.value = localVotes.value.filter(
      (vote) => !(vote.questionId === currentQuestion.value.id && vote.hostId === hostId)
    )
  } finally {
    // End animation after a short delay
    setTimeout(() => {
      animatingVote.value = false
      animatingHostId.value = null
      isVoting.value = false
    }, 1000)
  }
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
</script>

<template>
  <Head :title="`${game.data.title} - Live Game`" />

  <div class="min-h-screen bg-yellow-50 py-6 px-4 sm:px-6">
    <Stack class="max-w-xl mx-auto">
      <div>
        <!-- Game Header -->
        <Card class="mb-6">
          <Stack space="small">
            <Typography variant="h1">{{ game.data.title }}</Typography>
            <div class="flex items-center justify-between">
              <Typography variant="body-small" class="text-gray-600">
                {{ data.activeWatchers }} active player(s)
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
              <Typography variant="h2">No live question...</Typography>
              <Typography variant="body-small" class="text-gray-600">
                Vote on questions while you mingle and chat with others!
              </Typography>
            </Stack>
          </Stack>
        </Card>

        <!-- Question Card -->
        <Card v-else padding="medium">
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
                  class="w-full flex items-center justify-center p-3 rounded-full aspect-square group shadow-md transition-all duration-300"
                  :class="{
                    'pointer-events-none opacity-50': hasVoted,
                    'scale-110 animate-pulse-fast': animatingVote && animatingHostId === host.id,
                  }"
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
                    {{ countdown > 0 ? countdown : 'The winner is...' }}
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
                    <div class="relative rounded-lg mb-4">
                      <!-- Vote Bars -->
                      <!-- Winner Announcement -->
                      <Stack v-if="currentQuestion.winners" space="medium" class="text-center">
                        <Stack space="small">
                          <div
                            class="relative flex items-center justify-center p-8 rounded-full max-w-[75%] aspect-square mx-auto"
                          >
                            <div
                              class="absolute -inset-2 rounded-full opacity-25 animate-pulse"
                              :style="{ backgroundColor: currentQuestion.winners[0].color }"
                            />
                            <div
                              class="relative rounded-full w-48 h-48"
                              :style="{ backgroundColor: currentQuestion.winners[0].color }"
                            >
                              <img
                                :src="`/storage/${currentQuestion.winners[0].avatar}`"
                                :alt="currentQuestion.winners[0].name"
                              />
                            </div>
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
                              <Typography
                                variant="body"
                                class="font-bold"
                                :class="{ 'text-white': getVotePercentage(host.id) > 0 }"
                              >
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
                <div class="flex items-center justify-center gap-2 mt-2">
                  <div
                    v-if="getVotedHost(currentQuestion.id)"
                    class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0"
                    :style="{ backgroundColor: getVotedHost(currentQuestion.id)?.color }"
                  >
                    <img
                      :src="`/storage/${getVotedHost(currentQuestion.id)?.avatar}`"
                      :alt="getVotedHost(currentQuestion.id)?.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <Typography variant="body-small" class="text-green-600">
                    You voted for {{ getVotedHost(currentQuestion.id)?.name || 'a host' }}!
                  </Typography>
                </div>
              </div>
            </template>
          </Stack>
        </Card>
      </div>
      <OpenQuestions :gameSlug="game.data.slug" :hosts="hosts" />
    </Stack>
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

.animate-pulse {
  @apply transition-all duration-500 ease-in-out;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.25;
    transform: scale(0.9);
  }
  50% {
    opacity: 0.5;
    transform: scale(1.01);
    transition: all 0.5s ease-in-out;
  }
}

.animate-pulse-fast {
  animation: pulse-fast 0.5s infinite alternate;
}

@keyframes pulse-fast {
  0% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1.15);
  }
}
</style>
