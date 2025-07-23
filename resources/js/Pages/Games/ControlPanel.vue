<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useGameUpdates } from '@/Composables/useGameUpdates'
import { useQuestionState } from '@/Composables/useQuestionState'

import Typography from '@/Stencil/Typography.vue'
import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Button from '@/Stencil/Button.vue'
import Icon from '@/Stencil/Icon.vue'
import Timer from '@/Stencil/Timer.vue'
import SlideOut from '@/Stencil/SlideOut.vue'
import FormTextInput from '@/Stencil/Forms/FormInput.vue'

// Define props
const props = defineProps({
  game: {
    type: Object,
    required: true,
  },
  hosts: {
    type: Array,
    default: () => [],
  },
})

// Set up question state management
const {
  currentQuestion,
  votingStatus,
  voteCounts,
  votingOpen,
  votingClosed,
  resultsRevealed,
  startTimer,
  stopTimer,
  formatTime,
  timerRunning,
  timeRemaining,
  setActiveQuestion,
  openVoting,
  openQuestions,
  closeVoting,
  revealResults,
} = useQuestionState(props.game.data.slug, props.game.data.id)

// Set up game updates
const { data, game } = useGameUpdates(props.game.data.id)

const formQuestion = useForm({
  question: '',
})

const isSlideoverOpen = ref(false)
const showHosts = ref(false)

function createQuestion() {
  formQuestion.post(route('games.questions.store', props.game.data), {
    onSuccess: () => {
      isSlideoverOpen.value = false
    },
    preserveState: false,
    preserveScroll: true,
  })
}

function getVotesForHost(hostId) {
  return voteCounts.value?.byHost?.[hostId] || 0
}

// Add reveal animation function
function triggerRevealAnimation() {
  showHosts.value = false
  setTimeout(() => {
    showHosts.value = true
  }, 5000) // Show hosts after 3 seconds
}

// Watch for reveal event
watch(resultsRevealed, (newValue, oldValue) => {
  if (newValue && !oldValue) {
    triggerRevealAnimation()
  }
})
</script>

<template>
  <Head :title="`Control Panel - ${props.game.data.title}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <Typography variant="h1">{{ props.game.data.title }} - Control Panel</Typography>
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2 text-green-600">
            <Typography class="font-semibold"> {{ data.activeWatchers ?? 0 }} </Typography>
            <Icon icon="team" size="small" class="text-green-600" />
          </div>
          <Button
            theme="primary-outline"
            :href="route('games.analytics', props.game.data)"
            icon="poll"
          >
            Live Voting View
          </Button>
        </div>
      </div>
    </template>

    <div>
      <Stack space="medium">
        <!-- Share URL Card -->
        <Card>
          <Stack space="small">
            <Typography variant="h2">Share with Watchers</Typography>
            <div class="flex items-center bg-gray-100 p-3 rounded">
              <Typography class="flex-grow truncate">
                {{ route('games.join', props.game.data) }}
              </Typography>
            </div>
            <Typography variant="body-small" class="text-gray-600">
              Password: <span class="font-medium">{{ props.game.data.access_code }}</span>
            </Typography>
          </Stack>
        </Card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Questions List -->
          <div class="lg:col-span-1">
            <Card>
              <Stack space="medium">
                <div class="flex justify-between items-center">
                  <Typography variant="h2">Questions</Typography>

                  <div>
                    <Button
                      icon="add"
                      theme="primary-outline"
                      size="small"
                      @click="isSlideoverOpen = true"
                    >
                      Add Question
                    </Button>
                  </div>
                </div>
                <Stack space="small">
                  <Card
                    v-for="question in game?.questions"
                    :key="question.id"
                    class="cursor-pointer transition-colors"
                    :class="{
                      'bg-green-50 border-green-200':
                        currentQuestion && currentQuestion.id === question.id,
                      'hover:bg-gray-50': !(currentQuestion && currentQuestion.id === question.id),
                    }"
                    @click="setActiveQuestion(question.id)"
                  >
                    <Stack space="small">
                      <div class="flex justify-between">
                        <Stack space="xsmall">
                          <Typography
                            variant="body-small"
                            class="absolute top-0 mt-3 text-gray-500"
                          >
                            {{ question.votes.length }}
                            {{ question.votes.length === 1 ? 'vote' : 'votes' }}
                          </Typography>
                          <Typography variant="body-lg">{{ question.question }}</Typography>
                        </Stack>
                        <div class="shrink-0">
                          <Typography
                            v-if="question.is_active"
                            variant="body-small"
                            class="text-yellow-600 bg-yellow-100 p-1 rounded-full"
                          >
                            Play Live
                          </Typography>
                        </div>
                      </div>
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
          </div>

          <!-- Current Question Control -->
          <div class="lg:col-span-2">
            <Card>
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
                  <div class="flex justify-between items-center">
                    <Typography variant="h2">Current Question</Typography>
                    <Button theme="primary-outline" @click="setActiveQuestion(null)">
                      Clear Question
                    </Button>
                  </div>

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
                          {{ voteCounts.total ?? 0 }}
                          {{ voteCounts.total === 1 ? 'vote' : 'votes' }}
                        </Typography>
                      </Stack>
                    </div>
                  </Card>

                  <!-- Voting Controls -->
                  <div v-if="votingOpen">
                    <Stack space="small" class="flex-wrap">
                      <div class="flex gap-3">
                        <Button
                          v-if="!timerRunning"
                          theme="primary-outline"
                          @click="startTimer(30)"
                        >
                          30s Timer
                        </Button>
                        <Button
                          v-if="!timerRunning"
                          theme="primary-outline"
                          @click="startTimer(60)"
                        >
                          60s Timer
                        </Button>
                        <Button v-if="timerRunning" theme="warning-outline" @click="stopTimer">
                          Stop Timer
                        </Button>
                        <Button theme="danger" @click="closeVoting"> Close Voting </Button>
                      </div>
                    </Stack>
                  </div>

                  <!-- Results Controls -->
                  <div v-if="votingClosed && !resultsRevealed">
                    <Button theme="primary" @click="revealResults">Reveal Results</Button>
                  </div>

                  <!-- Results Display -->
                  <div v-if="resultsRevealed">
                    <template v-if="showHosts">
                      <Stack space="medium">
                        <!-- Winners Display -->
                        <div v-if="currentQuestion.winners?.length" class="text-center">
                          <Typography variant="h2" class="mb-4">
                            {{
                              showHosts
                                ? currentQuestion.winners.length > 1
                                  ? "It's a Tie!"
                                  : 'Winner!'
                                : 'Revealing Results...'
                            }}
                          </Typography>
                          <div class="flex justify-center gap-4">
                            <div
                              v-for="winner in currentQuestion.winners"
                              :key="winner.id"
                              class="flex flex-col items-center"
                            >
                              <div
                                class="w-24 h-24 rounded-full flex items-center justify-center mb-2"
                                :style="{ backgroundColor: winner.color }"
                              >
                                <img
                                  :src="`/storage/${winner.avatar}`"
                                  :alt="winner.name"
                                  class="w-20 h-20 object-contain transition-opacity duration-500"
                                  :class="{ 'opacity-0': !showHosts }"
                                />
                              </div>
                              <Typography variant="h3" :class="{ 'opacity-0': !showHosts }">
                                {{ winner.name }}
                              </Typography>
                            </div>
                          </div>
                        </div>

                        <!-- Animated Results Bar -->
                        <div class="relative h-6 bg-gray-100 rounded-lg overflow-hidden">
                          <div
                            v-for="host in hosts"
                            :key="host.id"
                            class="absolute top-0 h-full transition-all duration-1000 ease-out animate-pulse"
                            :class="{
                              'left-0': host.id === hosts[0].id,
                              'right-0': host.id === hosts[1].id,
                            }"
                            :style="{
                              width: `${Math.max(
                                1,
                                ((voteCounts?.byHost?.[host.id] || 0) / (voteCounts?.total || 1)) *
                                  99
                              )}%`,
                              backgroundColor: host.color,
                              opacity: showHosts ? 1 : 0,
                            }"
                          ></div>

                          <!-- Labels -->
                          <div class="absolute top-0 left-0 w-full h-full flex">
                            <div
                              v-for="host in hosts"
                              :key="host.id"
                              class="flex-1 flex items-center justify-center text-white animate-pulse"
                              :class="{ 'opacity-0': !showHosts }"
                            >
                              <Typography variant="h2">
                                {{ host.name }}: {{ voteCounts?.byHost?.[host.id] || 0 }}
                              </Typography>
                            </div>
                          </div>
                        </div>
                      </Stack>
                    </template>
                    <template v-else>
                      <Stack space="medium">
                        <Typography variant="h2">The winner is...</Typography>
                      </Stack>
                    </template>
                  </div>
                </Stack>
              </div>
            </Card>
          </div>
        </div>
      </Stack>
    </div>

    <teleport to="body">
      <SlideOut title="Add Question" :isOpen="isSlideoverOpen" @close="isSlideoverOpen = false">
        <form @submit.prevent="createQuestion">
          <Stack>
            <FormTextInput
              id="question"
              name="question"
              label="Question"
              v-model="formQuestion.question"
              required
              :error="formQuestion.errors.question"
            />

            <div class="flex justify-end space-x-4">
              <Button
                type="submit"
                :loading="formQuestion.processing"
                :isDisabled="formQuestion.processing"
              >
                Create
              </Button>
            </div>
          </Stack>
        </form>
      </SlideOut>
    </teleport>
  </AuthenticatedLayout>
</template>

<style>
.animate-pulse {
  @apply transition-all duration-500 ease-in-out;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.02);
  }
}
</style>
