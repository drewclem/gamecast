import { usePoll } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

export function useGameUpdates(gameId, options = {}) {
  const { pollInterval = 1000 } = options // Poll every second for more responsiveness
  const lastUpdate = ref(Date.now())

  // Set up polling for game state
  const { data } = usePoll(pollInterval, {
    onStart: () => {
      console.log('Polling for game updates started')
    },
    onFinish: () => {
      console.log('Polling for game updates finished')
    },
    preserveState: true,
    preserveScroll: true,
    only: ['activeWatchers', 'voteCounts', 'activeQuestion'],
  })

  // Watch for changes in the data
  watch(
    data,
    (newData) => {
      if (newData) {
        lastUpdate.value = Date.now()
      }
    },
    { deep: true }
  )

  return {
    data: data || ref(null), // Ensure we always return a ref
    lastUpdate,
  }
}
