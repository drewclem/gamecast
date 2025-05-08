import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const QuestionStatus = {
  PENDING: 'pending',
  ACTIVE: 'active',
  CLOSED: 'closed',
  REVEALED: 'revealed',
}

export function useQuestionState(gameSlug, initialQuestion = null) {
  // Initialize with null if no initial question
  const currentQuestion = ref(initialQuestion || null)
  const votingStatus = ref(initialQuestion?.status || QuestionStatus.CLOSED)
  const totalVotes = ref(0)
  const host1Votes = ref(0)
  const host2Votes = ref(0)

  // Timer state
  const timerRunning = ref(false)
  const timeRemaining = ref(0)
  let timerInterval = null

  // Computed properties for status checks
  const votingOpen = computed(() => votingStatus.value === QuestionStatus.ACTIVE)
  const votingClosed = computed(() => votingStatus.value === QuestionStatus.CLOSED)
  const resultsRevealed = computed(() => votingStatus.value === QuestionStatus.REVEALED)

  // Timer functions
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

  // Update question state
  async function updateQuestionState(questionId, status) {
    return router.put(
      route('games.questions.update', { game: gameSlug, question: questionId }),
      {
        status,
      },
      {
        preserveState: true,
        onSuccess: (page) => {
          if (page.props.activeQuestion) {
            currentQuestion.value = page.props.activeQuestion
            votingStatus.value = page.props.activeQuestion.status
          }
        },
      }
    )
  }

  // Set active question
  async function setActiveQuestion(questionId) {
    stopTimer()

    return router.put(
      route('games.update', { game: gameSlug }),
      {
        current_question_id: questionId,
      },
      {
        preserveState: true,
        onSuccess: async (page) => {
          currentQuestion.value = page.props.activeQuestion
          votingStatus.value = QuestionStatus.ACTIVE
          totalVotes.value = 0
          host1Votes.value = 0
          host2Votes.value = 0

          // Update the new question's state
          await updateQuestionState(questionId, QuestionStatus.ACTIVE)
        },
      }
    )
  }

  // Open voting
  async function openVoting() {
    if (!currentQuestion.value) return
    return updateQuestionState(currentQuestion.value.id, QuestionStatus.ACTIVE)
  }

  // Close voting
  async function closeVoting() {
    if (!currentQuestion.value) return
    stopTimer()
    return updateQuestionState(currentQuestion.value.id, QuestionStatus.CLOSED)
  }

  // Reveal results
  async function revealResults() {
    if (!currentQuestion.value) return
    return updateQuestionState(currentQuestion.value.id, QuestionStatus.REVEALED)
  }

  return {
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
  }
}
