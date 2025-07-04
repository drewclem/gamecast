// resources/js/Composables/useGameUpdates.js
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useGameUpdates(gameId) {
  // Get initial props from Inertia page
  const page = usePage()
  const initialProps = computed(() => page.props)

  const activeWatchers = ref(initialProps.value.activeWatchers || 0)
  const game = ref({
    ...initialProps.value.game.data,
  })

  const voteCounts = ref(
    initialProps.value.voteCounts || {
      byHost: {},
      total: 0,
    }
  )
  const lastUpdate = ref(Date.now())

  watch(initialProps, (newProps) => {
    activeWatchers.value = newProps.activeWatchers
    voteCounts.value = newProps.voteCounts
    game.value = newProps.game.data
  })

  let echoChannel = null

  function setupListeners() {
    console.log(`Setting up listeners for game.${gameId}`)

    echoChannel = window.Echo.channel(`game.${gameId}`)

    echoChannel.listen('WatcherCountUpdated', (e) => {
      activeWatchers.value = e.watcher_count
      lastUpdate.value = Date.now()
    })

    echoChannel.listen('QuestionVotesUpdated', (e) => {
      if (voteCounts.value[e.question_id]) {
        voteCounts.value[e.question_id] = {
          byHost: e.votes_by_host,
          total: e.total_votes,
        }
      }

      if (e.game) {
        game.value = e.game
      }
      lastUpdate.value = Date.now()
    })
  }

  // Initialize and clean up
  onMounted(() => {
    setupListeners()
  })

  onBeforeUnmount(() => {
    if (echoChannel) {
      echoChannel.stopListening('WatcherCountUpdated')
      echoChannel.stopListening('QuestionVotesUpdated')
    }
  })

  return {
    data: {
      activeWatchers,
      voteCounts,
    },
    game,
    lastUpdate,
  }
}
