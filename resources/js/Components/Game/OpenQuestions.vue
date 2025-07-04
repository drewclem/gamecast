<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useQuestionState } from '@/Composables/useQuestionState'

import Stack from '@/Stencil/Stack.vue'
import Card from '@/Stencil/Card.vue'
import Typography from '@/Stencil/Typography.vue'
import Carousel from '@/Stencil/Carousel/Carousel.vue'
import CarouselSlide from '@/Stencil/Carousel/CarouselSlide.vue'
import AccordionGroup from '@/Stencil/AccordionGroup.vue'
import AccordionItem from '@/Stencil/AccordionItem.vue'

const props = defineProps({
  gameSlug: {
    type: String,
    required: true,
  },
  hosts: {
    type: Array,
    required: true,
  },
})

const { openQuestions, watcher } = useQuestionState(props.gameSlug, props.gameId)

const isVoting = ref(false)
const votingQuestionId = ref(null)
const showVoteAnimation = ref(false)
const animatingVoteHostId = ref(null)

const localVotes = ref([])

const hasVotedOnQuestion = (questionId) => {
  return (
    watcher.value?.votes?.some((vote) => vote.question_id === questionId) ||
    localVotes.value.some((vote) => vote.questionId === questionId) ||
    false
  )
}

const getVotedHost = (questionId) => {
  if (watcher.value?.votes) {
    const vote = watcher.value.votes.find((vote) => vote.question_id === questionId)
    if (vote) {
      return props.hosts.find((host) => host.id === vote.host_id) || null
    }
  }

  const localVote = localVotes.value.find((vote) => vote.questionId === questionId)
  if (localVote) {
    return props.hosts.find((host) => host.id === localVote.hostId) || null
  }

  return null
}

async function castVote(questionId, hostId) {
  if (isVoting.value || hasVotedOnQuestion(questionId)) return

  isVoting.value = true
  votingQuestionId.value = questionId

  // Start vote animation
  animatingVoteHostId.value = hostId
  showVoteAnimation.value = true

  try {
    localVotes.value.push({
      questionId: questionId,
      hostId: hostId,
    })

    await router.post(
      route('games.questions.vote', {
        game: props.gameSlug,
        question: questionId,
      }),
      {
        host_id: hostId,
      },
      {
        preserveState: true,
        onSuccess: () => {
          console.log('Vote successful for question', questionId, 'host', hostId)
        },
        onError: () => {
          localVotes.value = localVotes.value.filter(
            (vote) => !(vote.questionId === questionId && vote.hostId === hostId)
          )
        },
      }
    )
  } catch (error) {
    console.error('Error voting on open question:', error)
    localVotes.value = localVotes.value.filter(
      (vote) => !(vote.questionId === questionId && vote.hostId === hostId)
    )
  } finally {
    setTimeout(() => {
      showVoteAnimation.value = false
      animatingVoteHostId.value = null
      isVoting.value = false
      votingQuestionId.value = null
    }, 1000)
  }
}
</script>

<template>
  <Stack space="medium">
    <Typography variant="h2">Open Questions</Typography>

    <div>
      <!-- <Carousel hasPagination loop>
        <CarouselSlide v-for="question in openQuestions" :key="question.id">
          <Card padding="large">
            <Stack space="medium">
              <Typography class="text-center" variant="h1" as="p">{{
                question.question
              }}</Typography>

              <div class="grid grid-cols-2 gap-4">
                <div class="p-3" v-for="host in hosts" :key="host.id">
                  <button
                    :style="{ backgroundColor: host.color }"
                    class="w-3/4 mx-auto flex items-center justify-center p-3 rounded-full group shadow-md aspect-square transition-all duration-300"
                    :class="{
                      'pointer-events-none opacity-50': hasVotedOnQuestion(question.id),
                      'scale-110 animate-pulse-fast':
                        showVoteAnimation &&
                        votingQuestionId === question.id &&
                        animatingVoteHostId === host.id,
                    }"
                    @click="castVote(question.id, host.id)"
                    :disabled="
                      hasVotedOnQuestion(question.id) ||
                      (isVoting && votingQuestionId === question.id)
                    "
                  >
                    <img
                      :src="`/storage/${host.avatar}`"
                      :alt="host.name"
                      class="w-3/4 group-hover:scale-110 transition-all duration-300"
                    />
                    <span class="sr-only">Vote for {{ host.name }}</span>
                  </button>
                </div>
              </div>

              <div v-if="hasVotedOnQuestion(question.id)" class="text-center">
                <div class="flex items-center justify-center gap-2 my-2">
                  <div
                    v-if="getVotedHost(question.id)"
                    class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0"
                    :style="{ backgroundColor: getVotedHost(question.id)?.color }"
                  >
                    <img
                      :src="`/storage/${getVotedHost(question.id)?.avatar}`"
                      :alt="getVotedHost(question.id)?.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <Typography variant="body-small" class="text-green-600">
                    You voted for {{ getVotedHost(question.id)?.name || 'a host' }}!
                  </Typography>
                </div>
              </div>
            </Stack>
          </Card>
        </CarouselSlide>
      </Carousel> -->
    </div>

    <div>
      <ul>
        <li>
          <AccordionGroup>
            <AccordionItem
              :title="question.question"
              v-for="question in openQuestions"
              :key="question.id"
              :value="question.id"
            >
              <Stack space="medium">
                <div class="grid grid-cols-2 gap-4">
                  <div class="p-3" v-for="host in hosts" :key="host.id">
                    <button
                      :style="{ backgroundColor: host.color }"
                      class="w-3/4 mx-auto flex items-center justify-center p-3 rounded-full group shadow-md aspect-square transition-all duration-300"
                      :class="{
                        'pointer-events-none opacity-50': hasVotedOnQuestion(question.id),
                        'scale-110 animate-pulse-fast':
                          showVoteAnimation &&
                          votingQuestionId === question.id &&
                          animatingVoteHostId === host.id,
                      }"
                      @click="castVote(question.id, host.id)"
                      :disabled="
                        hasVotedOnQuestion(question.id) ||
                        (isVoting && votingQuestionId === question.id)
                      "
                    >
                      <img
                        :src="`/storage/${host.avatar}`"
                        :alt="host.name"
                        class="w-3/4 group-hover:scale-110 transition-all duration-300"
                      />
                      <span class="sr-only">Vote for {{ host.name }}</span>
                    </button>
                  </div>
                </div>

                <div v-if="hasVotedOnQuestion(question.id)" class="text-center">
                  <div class="flex items-center justify-center gap-2 my-2">
                    <div
                      v-if="getVotedHost(question.id)"
                      class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0"
                      :style="{ backgroundColor: getVotedHost(question.id)?.color }"
                    >
                      <img
                        :src="`/storage/${getVotedHost(question.id)?.avatar}`"
                        :alt="getVotedHost(question.id)?.name"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <Typography variant="body-small" class="text-green-600">
                      You voted for {{ getVotedHost(question.id)?.name || 'a host' }}!
                    </Typography>
                  </div>
                </div>
              </Stack>
            </AccordionItem>
          </AccordionGroup>
        </li>
      </ul>
    </div>
  </Stack>
</template>

<style scoped>
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
