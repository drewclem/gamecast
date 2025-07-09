// resources/js/Composables/useQuestionState.js
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const QuestionStatus = {
  PENDING: 'pending',
  ACTIVE: 'active',
  CLOSED: 'closed',
  REVEALED: 'revealed',
}

export function useQuestionState(gameSlug, gameId) {
  const page = usePage()

  const pageProps = computed(() => page.props)

  const currentQuestion = ref(pageProps.value.activeQuestion || null)
  const votingStatus = ref(pageProps.value.activeQuestion?.status || QuestionStatus.CLOSED)
  const voteCounts = ref(pageProps.value.voteCounts || { byHost: {}, total: 0 })
  const loading = ref(false)
  const openQuestions = ref(pageProps.value.game.data.openQuestions || [])
  const watcher = ref(pageProps.value.watcher || null)

  // Watch for changes in page props and update our refs
  watch(
    pageProps,
    (newProps) => {
      if (newProps.activeQuestion) {
        loading.value = true
        currentQuestion.value = newProps.activeQuestion
        votingStatus.value = newProps.activeQuestion.status
        loading.value = false
        voteCounts.value = newProps.voteCounts ?? { byHost: {}, total: 0 }
      }
    },
    { deep: true }
  )

  const timerRunning = ref(false)
  const timeRemaining = ref(0)
  let timerInterval = null

  let echoChannel = null

  const votingOpen = computed(() => votingStatus.value === QuestionStatus.ACTIVE)
  const votingClosed = computed(() => votingStatus.value === QuestionStatus.CLOSED)
  const resultsRevealed = computed(() => votingStatus.value === QuestionStatus.REVEALED)

  function startTimer(seconds) {
    if (timerRunning.value) return

    timeRemaining.value = seconds
    timerRunning.value = true

    timerInterval = setInterval(() => {
      timeRemaining.value--

      if (timeRemaining.value <= 0) {
        stopTimer()
        closeVoting()
      }
    }, 1000)
  }

  function stopTimer() {
    if (timerInterval) {
      clearInterval(timerInterval)
      timerRunning.value = false
    }
  }

  function formatTime(seconds) {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  function setupEventListeners(gameId) {
    echoChannel = window.Echo.channel(`game.${gameId}`)

    echoChannel.listen('QuestionVotesUpdated', (e) => {
      if (currentQuestion.value && currentQuestion.value.id === e.question_id) {
        voteCounts.value = {
          byHost: e.votes_by_host,
          total: e.total_votes,
        }
      }
    })

    echoChannel.listen('QuestionStatusChanged', (e) => {
      if (currentQuestion.value && currentQuestion.value.id === e.id) {
        currentQuestion.value.status = e.status
        votingStatus.value = e.status

        if (e.status === QuestionStatus.REVEALED) {
          voteCounts.value = e.vote_counts
        }

        if (e.winners) {
          currentQuestion.value.winners = e.winners
        }
      }
    })

    echoChannel.listen('CurrentQuestionChanged', (e) => {
      currentQuestion.value = e.question

      if (e.question) {
        loading.value = true
        votingStatus.value = e.question.status

        // Only reset vote counts if there are no votes for this question
        if (!e.question.votes_count) {
          voteCounts.value = { byHost: {}, total: 0 }
        } else {
          voteCounts.value = e.question.vote_counts || { byHost: {}, total: 0 }
        }
        loading.value = false
      } else {
        currentQuestion.value = null
      }
    })
  }

  async function updateQuestionState(questionId, status) {
    return router.put(
      route('games.questions.update', { game: gameSlug, question: questionId }),
      {
        status,
      },
      {
        preserveState: true,
      }
    )
  }

  async function setActiveQuestion(questionId) {
    loading.value = true
    stopTimer()

    return router.put(
      route('games.update', { game: gameSlug }),
      {
        current_question_id: questionId,
      },
      {
        onSuccess: async () => {
          await updateQuestionState(questionId, QuestionStatus.ACTIVE)
          loading.value = false
        },
      }
    )
  }

  async function openVoting() {
    if (!currentQuestion.value) return
    loading.value = true
    const result = await updateQuestionState(currentQuestion.value.id, QuestionStatus.ACTIVE)
    loading.value = false
    return result
  }

  async function closeVoting() {
    if (!currentQuestion.value) return
    loading.value = true
    stopTimer()
    const result = await updateQuestionState(currentQuestion.value.id, QuestionStatus.CLOSED)
    loading.value = false
    return result
  }

  async function revealResults() {
    if (!currentQuestion.value) return
    loading.value = true
    const result = await updateQuestionState(currentQuestion.value.id, QuestionStatus.REVEALED)
    loading.value = false
    return result
  }

  onMounted(() => {
    const gameId = pageProps.value.game?.data?.id

    if (gameId) {
      setupEventListeners(gameId)
    } else {
      console.warn('No game ID available for Echo setup')
    }
  })

  onBeforeUnmount(() => {
    stopTimer()

    if (echoChannel) {
      echoChannel.stopListening('QuestionVotesUpdated')
      echoChannel.stopListening('QuestionStatusChanged')
      echoChannel.stopListening('CurrentQuestionChanged')
    }
  })

  return {
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
    setActiveQuestion,
    openVoting,
    closeVoting,
    watcher,
    revealResults,
    openQuestions,
  }
}
