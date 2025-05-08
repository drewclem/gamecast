// resources/js/Pages/Games/Session.vue
<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { usePoll } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useQuestionState } from '@/Composables/useQuestionState'
import { useGameUpdates } from '@/Composables/useGameUpdates'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Button from '@/Stencil/Button.vue'
import Icon from '@/Stencil/Icon.vue'

const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  watcher: {
    type: Object,
    required: true,
  },
  activeQuestion: {
    type: Object,
    required: true,
  },
  activeWatchers: {
    type: Number,
    required: true,
  },
})

// Set up question state management
const {
  currentQuestion,
  votingStatus,
  totalVotes,
  host1Votes,
  host2Votes,
  votingOpen,
  votingClosed,
  resultsRevealed,
} = useQuestionState(props.game.data.slug, props.activeQuestion)

// Local state
const hasVoted = ref(props.watcher.hasVoted)
const isVoting = ref(false)
const voteCast = ref(null)
const showVoteAnimation = ref(false)

// Watch for changes in the active question
// watch(
//   () => data.activeQuestion,
//   (newQuestion) => {
//     if (newQuestion) {
//       currentQuestion.value = newQuestion
//       votingStatus.value = newQuestion.status
//       // Reset vote counts when question changes
//       host1Votes.value = 0
//       host2Votes.value = 0
//       totalVotes.value = 0
//     }
//   },
//   { immediate: true }
// )

// // Watch for changes in vote counts
// watch(
//   () => data.value?.voteCounts,
//   (newVotes) => {
//     if (newVotes && currentQuestion.value) {
//       const questionVotes = newVotes[currentQuestion.value.id]
//       if (questionVotes) {
//         host1Votes.value = questionVotes.host1 || 0
//         host2Votes.value = questionVotes.host2 || 0
//         totalVotes.value = host1Votes.value + host2Votes.value
//       }
//     }
//   },
//   { immediate: true }
// )

// // Watch for changes in active watchers
// watch(
//   () => data.value?.activeWatchers,
//   (newCount) => {
//     if (typeof newCount === 'number') {
//       props.activeWatchers = newCount
//     }
//   },
//   { immediate: true }
// )

// Cast a vote function
function castVote(hostNumber) {
  if (!votingOpen.value || hasVoted.value || isVoting.value) return

  isVoting.value = true
  voteCast.value = hostNumber
  showVoteAnimation.value = true

  // Hide animation after a delay
  setTimeout(() => {
    showVoteAnimation.value = false
  }, 1500)

  // Submit vote to server
  router.post(
    route('games.vote', {
      game: props.game.data.slug,
      question: currentQuestion.value.id,
    }),
    { choice: hostNumber },
    {
      preserveState: true,
      onSuccess: () => {
        hasVoted.value = true
        isVoting.value = false
      },
      onError: () => {
        isVoting.value = false
        showVoteAnimation.value = false
      },
    }
  )
}

// Activity tracking - regularly ping to show we're still active
let activityInterval

function updateActivity() {
  router.post(
    route('games.update-activity', props.game.data.slug),
    {},
    {
      preserveState: true,
    }
  )
}

onMounted(() => {
  // Set up activity tracking
  updateActivity()
  activityInterval = setInterval(updateActivity, 30000) // Every 30 seconds
})

onBeforeUnmount(() => {
  if (activityInterval) {
    clearInterval(activityInterval)
  }
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
              {{ activeWatchers }} active {{ activeWatchers === 1 ? 'watcher' : 'watchers' }}
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
                votingOpen ? 'Voting Open' : resultsRevealed ? 'Results Revealed' : 'Voting Closed'
              }}
            </Typography>
          </div>

          <!-- Question Content -->
          <Typography variant="h2" class="text-center">
            {{ currentQuestion.question }}
          </Typography>

          <!-- Voting Buttons -->
          <div v-if="votingOpen" class="grid grid-cols-2 gap-4">
            <Button
              theme="primary"
              :isDisabled="hasVoted || isVoting"
              @click="castVote(1)"
              class="w-full"
            >
              Vote for Host 1
            </Button>
            <Button
              theme="danger"
              :isDisabled="hasVoted || isVoting"
              @click="castVote(2)"
              class="w-full"
            >
              Vote for Host 2
            </Button>
          </div>

          <!-- Voting Closed Message -->
          <div v-else-if="votingClosed && !resultsRevealed" class="text-center">
            <Typography variant="body-lg" class="text-gray-600">
              Voting is now closed. Results will be revealed soon!
            </Typography>
          </div>

          <!-- Results Display -->
          <div v-else-if="resultsRevealed" class="py-4">
            <div class="relative h-16 bg-gray-100 rounded-lg mb-4 overflow-hidden">
              <!-- Result Bars -->
              <div
                class="absolute top-0 left-0 h-full bg-blue-500 transition-all duration-1000 ease-out"
                :style="{ width: `${(host1Votes / (host1Votes + host2Votes || 1)) * 100}%` }"
              ></div>
              <div
                class="absolute top-0 right-0 h-full bg-red-500 transition-all duration-1000 ease-out"
                :style="{ width: `${(host2Votes / (host1Votes + host2Votes || 1)) * 100}%` }"
              ></div>

              <!-- Labels -->
              <div class="absolute top-0 left-0 w-full h-full flex text-sm">
                <div class="flex-1 flex items-center justify-center text-white font-bold">
                  Host 1: {{ host1Votes }}
                </div>
                <div class="flex-1 flex items-center justify-center text-white font-bold">
                  Host 2: {{ host2Votes }}
                </div>
              </div>
            </div>

            <Typography variant="body-small" class="text-center text-gray-600">
              {{ totalVotes }} total {{ totalVotes === 1 ? 'vote' : 'votes' }}
            </Typography>
          </div>

          <!-- Vote Confirmation -->
          <div v-if="hasVoted && votingOpen" class="text-center">
            <Typography variant="body-small" class="text-green-600">
              Your vote has been recorded! <span v-if="voteCast">(Host {{ voteCast }})</span>
            </Typography>
          </div>
        </Stack>
      </Card>

      <!-- Vote Animation -->
      <div
        v-if="showVoteAnimation"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
      >
        <Card padding="medium" class="animate-bounce">
          <Stack space="small" class="items-center">
            <Icon icon="check" size="large" class="text-green-500" />
            <Typography variant="h2">Vote Cast!</Typography>
          </Stack>
        </Card>
      </div>
    </div>
  </div>
</template>
