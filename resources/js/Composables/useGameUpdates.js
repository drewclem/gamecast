// resources/js/Composables/useGameUpdates.js
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useGameUpdates(gameId) {
  // Get initial props from Inertia page
  const page = usePage()
  const initialProps = computed(() => page.props)

  const activeWatchers = ref(initialProps.value.activeWatchers || 0)
  const voteCounts = ref(
    initialProps.value.voteCounts || {
      byHost: {},
      total: 0,
    }
  )
  const lastUpdate = ref(Date.now())

  let echoChannel = null

  function setupListeners() {
    console.log(`Setting up listeners for game.${gameId}`)

    echoChannel = window.Echo.channel(`game.${gameId}`)

    echoChannel.listen('WatcherCountUpdated', (e) => {
      console.log('WatcherCountUpdated event received:', e)
      activeWatchers.value = e.watcher_count
      lastUpdate.value = Date.now()
    })

    echoChannel.listen('QuestionVotesUpdated', (e) => {
      console.log('QuestionVotesUpdated event received:', e)
      if (voteCounts.value[e.question_id]) {
        voteCounts.value[e.question_id] = {
          byHost: e.votes_by_host,
          total: e.total_votes,
        }
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
    lastUpdate,
  }
}
