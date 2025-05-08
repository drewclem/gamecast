<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useGameUpdates } from '@/Composables/useGameUpdates'
import { useQuestionState } from '@/Composables/useQuestionState'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Button from '@/Stencil/Button.vue'
import Icon from '@/Stencil/Icon.vue'
import Timer from '@/Stencil/Timer.vue'

// Define props
const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  activeQuestion: Object,
  activeWatchers: Number,
  voteCounts: Object,
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
  timerRunning,
  timeRemaining,
  startTimer,
  stopTimer,
  formatTime,
  setActiveQuestion,
  openVoting,
  closeVoting,
  revealResults,
} = useQuestionState(props.game.slug, props.activeQuestion)

// Set up game updates
useGameUpdates(props.game.id)
</script>

<template>
  <Head :title="`Control Panel - ${game.title}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <Typography variant="h1">{{ game.title }} - Control Panel</Typography>
        <div class="flex items-center space-x-4">
          <Icon icon="team" size="small" class="text-green-800" />
          <Typography class="text-green-800"> {{ activeWatchers }} </Typography>
        </div>
      </div>
    </template>

    <div>
      <Stack space="medium">
        <!-- Share URL Card -->
        <Card>
          <Stack space="small">
            <Typography variant="h2">Share with Watchers</Typography>
            <!-- <div class="flex items-center bg-gray-100 p-3 rounded">
                <Typography class="flex-grow truncate">
                  {{ window.location.origin }}/games/{{ game.id }}/join
                </Typography>
                <Button
                  theme="primary"
                  @click="
                    navigator.clipboard.writeText(`${window.location.origin}/games/${game.id}/join`)
                  "
                >
                  Copy
                </Button>
              </div> -->
            <Typography variant="body-small" class="text-gray-600">
              Password: <span class="font-medium">{{ game.access_password }}</span>
            </Typography>
          </Stack>
        </Card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Questions List -->
          <Card class="lg:col-span-1">
            <Stack space="medium">
              <Typography variant="h2">Questions</Typography>
              <Stack space="small">
                <Card
                  v-for="question in game.questions"
                  :key="question.id"
                  class="cursor-pointer transition-colors"
                  :class="{
                    'bg-green-50 border-green-200':
                      currentQuestion && currentQuestion.id === question.id,
                    'hover:bg-gray-50': !(currentQuestion && currentQuestion.id === question.id),
                    'pointer-events-none': question.status === 'closed',
                  }"
                  @click="setActiveQuestion(question.id)"
                >
                  <Stack space="xsmall">
                    <Typography variant="body-lg">{{ question.question }}</Typography>
                    <Typography
                      variant="body-small"
                      :class="{
                        'text-gray-500': question.status === 'pending',
                        'text-green-600 font-medium': question.status === 'active',
                        'text-yellow-600 font-medium': question.status === 'closed',
                        'text-blue-600 font-medium': question.status === 'revealed',
                      }"
                    >
                      {{ question.status.charAt(0).toUpperCase() + question.status.slice(1) }}
                    </Typography>
                  </Stack>
                </Card>
              </Stack>
            </Stack>
          </Card>

          <!-- Current Question Control -->
          <Card class="lg:col-span-2">
            <div v-if="!currentQuestion" class="text-center py-12">
              <Stack space="small">
                <Typography variant="body-lg" class="text-gray-600">
                  No active question
                </Typography>
                <Typography variant="body-small" class="text-gray-500">
                  Select a question from the list to start.
                </Typography>
              </Stack>
            </div>

            <div v-else>
              <Stack space="medium">
                <Typography variant="h2">Current Question</Typography>

                <!-- Question Content -->
                <Card padding="medium" class="bg-gray-50">
                  <Typography variant="h1">{{ currentQuestion.question }}</Typography>
                </Card>

                <!-- Voting Status Box -->
                <Card padding="medium" class="bg-gray-100">
                  <div class="flex items-center justify-between">
                    <Stack space="xsmall">
                      <Typography variant="body-small" class="font-medium">Status:</Typography>
                      <Typography
                        :class="{
                          'text-green-600': votingOpen,
                          'text-yellow-600': votingClosed && !resultsRevealed,
                          'text-blue-600': resultsRevealed,
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
                    </Stack>

                    <Stack space="xsmall" class="items-end">
                      <Timer
                        :seconds="timeRemaining"
                        :isRunning="timerRunning"
                        size="medium"
                        @timeUp="closeVoting"
                      />
                      <Typography variant="body-small" class="font-medium">
                        {{ totalVotes }} votes
                      </Typography>
                    </Stack>
                  </div>
                </Card>

                <!-- Voting Controls -->
                <div v-if="votingOpen">
                  <Stack space="small" class="flex-wrap">
                    <div class="flex gap-3">
                      <Button v-if="!timerRunning" theme="primary-outline" @click="startTimer(30)">
                        30s Timer
                      </Button>
                      <Button v-if="!timerRunning" theme="primary-outline" @click="startTimer(60)">
                        60s Timer
                      </Button>
                      <Button v-if="!timerRunning" theme="primary-outline" @click="startTimer(120)">
                        2m Timer
                      </Button>
                      <Button v-if="timerRunning" theme="warning-outline" @click="stopTimer">
                        Stop Timer
                      </Button>
                      <Button theme="danger-outline" @click="closeVoting"> Close Voting </Button>
                    </div>
                  </Stack>
                </div>

                <!-- Results Controls -->
                <div v-if="votingClosed && !resultsRevealed">
                  <Button theme="primary" @click="revealResults">Reveal Results</Button>
                </div>

                <!-- Results Display -->
                <div v-if="resultsRevealed">
                  <Stack space="medium">
                    <!-- Animated Results Bar -->
                    <div class="relative h-24 bg-gray-100 rounded-lg overflow-hidden">
                      <div
                        class="absolute top-0 left-0 h-full bg-blue-500 transition-all duration-1000 ease-out"
                        :style="{
                          width: `${(host1Votes / (host1Votes + host2Votes || 1)) * 100}%`,
                        }"
                      ></div>
                      <div
                        class="absolute top-0 right-0 h-full bg-red-500 transition-all duration-1000 ease-out"
                        :style="{
                          width: `${(host2Votes / (host1Votes + host2Votes || 1)) * 100}%`,
                        }"
                      ></div>

                      <!-- Labels -->
                      <div class="absolute top-0 left-0 w-full h-full flex">
                        <div
                          class="flex-1 flex items-center justify-center text-white font-bold text-2xl"
                        >
                          Host 1: {{ host1Votes }}
                        </div>
                        <div
                          class="flex-1 flex items-center justify-center text-white font-bold text-2xl"
                        >
                          Host 2: {{ host2Votes }}
                        </div>
                      </div>
                    </div>

                    <!-- Next Question -->
                    <Button theme="success" @click="setActiveQuestion(currentQuestion.id + 1)">
                      Next Question
                    </Button>
                  </Stack>
                </div>
              </Stack>
            </div>
          </Card>
        </div>
      </Stack>
    </div>
  </AuthenticatedLayout>
</template>
