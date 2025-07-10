<template>
  <div class="min-h-screen bg-gradient-to-br from-yellow-50 to-amber-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center">
              <FullLogo class="h-10" />
            </div>
          </div>
          <div class="hidden md:block">
            <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
              <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
              >
                Dashboard
              </Link>

              <template v-else>
                <Link
                  :href="route('login')"
                  class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                  Log in
                </Link>

                <Link
                  v-if="canRegister"
                  :href="route('register')"
                  class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                  Register
                </Link>
              </template>
            </nav>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight">
            Turn Any Room Into a
            <span
              class="bg-gradient-to-r from-yellow-500 to-amber-500 bg-clip-text text-transparent block"
            >
              Game Show Arena
            </span>
          </h1>
          <p class="mt-6 text-xl text-gray-600 max-w-3xl mx-auto">
            Create interactive voting experiences that get everyone involved. Players join instantly
            with QR codes, vote on their phones, and watch dramatic results unfold on the big
            screen.
          </p>
          <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            <a
              class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-yellow-600 hover:to-amber-600 transition-all duration-200 flex items-center justify-center"
              :href="route('register')"
            >
              <Play class="w-5 h-5 mr-2" />
              Start Playing Now
            </a>
            <!-- <button
              class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-full text-lg font-semibold hover:border-gray-400 transition-all duration-200 flex items-center justify-center"
            >
              Watch Demo
              <ChevronRight class="w-5 h-5 ml-2" />
            </button> -->
          </div>
        </div>
      </div>

      <!-- Floating elements -->
      <div
        class="absolute top-20 left-10 w-20 h-20 bg-yellow-200 rounded-full opacity-20 animate-pulse"
      ></div>
      <div
        class="absolute top-40 right-20 w-16 h-16 bg-amber-200 rounded-full opacity-20 animate-pulse delay-1000"
      ></div>
      <div
        class="absolute bottom-20 left-20 w-24 h-24 bg-yellow-300 rounded-full opacity-20 animate-pulse delay-2000"
      ></div>
    </section>

    <!-- Features Section -->
    <section
      id="features"
      class="py-20 bg-white transition-all duration-1000"
      :class="{
        'translate-y-0 opacity-100': isVisible.features,
        'translate-y-10 opacity-0': !isVisible.features,
      }"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Why Choose Hot Take Arena?
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Built for modern audiences who want instant engagement and dramatic entertainment
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="(feature, index) in features"
            :key="index"
            class="p-6 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 hover:from-yellow-50 hover:to-amber-50 transition-all duration-300"
            :class="{ 'ring-2 ring-yellow-200 scale-105': currentFeature === index }"
          >
            <div
              class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-amber-500 rounded-lg flex items-center justify-center text-white mb-4"
            >
              <component :is="feature.icon" class="w-6 h-6" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
            <p class="text-gray-600">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section
      id="how-it-works"
      class="py-20 bg-gradient-to-br from-yellow-50 to-amber-50 transition-all duration-1000"
      :class="{
        'translate-y-0 opacity-100': isVisible.howItWorks,
        'translate-y-10 opacity-0': !isVisible.howItWorks,
      }"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Get Started in 3 Simple Steps
          </h2>
          <p class="text-xl text-gray-600">From setup to showtime in minutes</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="(step, index) in steps" :key="index" class="text-center relative">
            <div
              class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-amber-500 rounded-full flex items-center justify-center text-white text-xl font-bold mb-6 mx-auto"
            >
              {{ step.number }}
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ step.title }}</h3>
            <p class="text-gray-600">{{ step.description }}</p>
            <div
              v-if="index < steps.length - 1"
              class="hidden md:block absolute top-8 left-full w-8 h-8 transform translate-x-4"
            >
              <ArrowRight class="w-8 h-8 text-gray-300" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Demo Preview -->
    <section
      id="demo"
      class="py-20 bg-white transition-all duration-1000"
      :class="{
        'translate-y-0 opacity-100': isVisible.demo,
        'translate-y-10 opacity-0': !isVisible.demo,
      }"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">See It In Action</h2>
          <p class="text-xl text-gray-600">
            Watch how Hot Take Arena transforms any gathering into an interactive experience
          </p>
        </div>

        <div class="relative max-w-4xl mx-auto">
          <div class="bg-gradient-to-r from-yellow-500 to-amber-500 rounded-3xl p-8 text-white">
            <div class="grid md:grid-cols-2 gap-8 items-center">
              <div>
                <h3 class="text-2xl font-bold mb-4">Game Show Magic</h3>
                <ul class="space-y-3">
                  <li class="flex items-center">
                    <Star class="w-5 h-5 mr-3 text-yellow-200" />
                    <span>Instant player engagement</span>
                  </li>
                  <li class="flex items-center">
                    <Star class="w-5 h-5 mr-3 text-yellow-200" />
                    <span>Dramatic countdown reveals</span>
                  </li>
                  <li class="flex items-center">
                    <Star class="w-5 h-5 mr-3 text-yellow-200" />
                    <span>Real-time vote tracking</span>
                  </li>
                  <li class="flex items-center">
                    <Star class="w-5 h-5 mr-3 text-yellow-200" />
                    <span>Professional presentation</span>
                  </li>
                </ul>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center">
                <div
                  class="w-32 h-32 bg-white/20 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                >
                  <div class="w-20 h-20 bg-white/30 rounded-xl flex items-center justify-center">
                    <Play class="w-8 h-8 text-white" />
                  </div>
                </div>
                <p class="text-white/80">Click to watch demo</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-yellow-500 to-amber-500">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
          Ready to Create Your First Game?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
          Join thousands of event organizers, teachers, and party hosts who are already using Hot
          Take Arena
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            class="bg-white text-yellow-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-gray-100 transition-all duration-200"
            :href="route('register')"
          >
            Start Playing Now
          </a>
          <!-- <button
            class="border-2 border-white text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-yellow-600 transition-all duration-200"
          >
            Book a Demo
          </button> -->
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <div class="flex items-center mb-4">
              <ApplicationLogo class="h-12" />
            </div>
            <p class="text-gray-400">
              Transform any gathering into an interactive game show experience.
            </p>
          </div>
          <!-- <div>
            <h4 class="font-semibold mb-4">Product</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">Features</a></li>
              <li><a href="#" class="hover:text-white">Pricing</a></li>
              <li><a href="#" class="hover:text-white">Demo</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Support</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">Help Center</a></li>
              <li><a href="#" class="hover:text-white">Contact</a></li>
              <li><a href="#" class="hover:text-white">Community</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Company</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">About</a></li>
              <li><a href="#" class="hover:text-white">Blog</a></li>
              <li><a href="#" class="hover:text-white">Careers</a></li>
            </ul>
          </div> -->
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; {{ new Date().getFullYear() }} Hot Take Arena. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import FullLogo from '@/Components/global/FullLogo.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import {
  ChevronRight,
  Smartphone,
  Users,
  Trophy,
  Zap,
  Play,
  Star,
  ArrowRight,
} from 'lucide-vue-next'

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
})

const currentFeature = ref(0)
const isVisible = ref({
  features: false,
  howItWorks: false,
  demo: false,
})

const features = ref([
  {
    icon: Smartphone,
    title: 'Instant QR Code Joining',
    description:
      'Players scan a QR code to join instantly - no apps to download or accounts to create',
  },
  {
    icon: Users,
    title: 'Real-Time Voting',
    description: 'See votes pour in live as players make their choices in real-time',
  },
  {
    icon: Trophy,
    title: 'Dramatic Results',
    description: 'Game-show style reveals with animations and winner celebrations',
  },
  {
    icon: Zap,
    title: 'Lightning Fast',
    description: 'Built for speed - voting opens and closes in seconds, results appear instantly',
  },
])

const steps = ref([
  {
    number: '01',
    title: 'Create Your Game',
    description: 'Set up your hosts, write your questions, and get ready to play',
  },
  {
    number: '02',
    title: 'Share the QR Code',
    description: 'Display the QR code on screen - players scan to join instantly',
  },
  {
    number: '03',
    title: 'Vote & Reveal',
    description: 'Players vote on their phones, you control the dramatic reveal',
  },
])

onMounted(() => {
  // Feature rotation
  setInterval(() => {
    currentFeature.value = (currentFeature.value + 1) % features.value.length
  }, 3000)

  // Intersection observer for animations
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const id = entry.target.id
          if (id === 'features') {
            isVisible.value.features = true
          } else if (id === 'how-it-works') {
            isVisible.value.howItWorks = true
          } else if (id === 'demo') {
            isVisible.value.demo = true
          }
        }
      })
    },
    { threshold: 0.1 }
  )

  // Observe sections
  document.querySelectorAll('#features, #how-it-works, #demo').forEach((el) => {
    observer.observe(el)
  })
})
</script>

<style scoped>
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

.text-transparent {
  color: transparent;
}

.backdrop-blur-md {
  backdrop-filter: blur(12px);
}

.transition-all {
  transition: all 0.3s ease;
}

.duration-1000 {
  transition-duration: 1000ms;
}

.duration-300 {
  transition-duration: 300ms;
}

.duration-200 {
  transition-duration: 200ms;
}

.translate-y-10 {
  transform: translateY(2.5rem);
}

.opacity-0 {
  opacity: 0;
}

.opacity-100 {
  opacity: 1;
}

.scale-105 {
  transform: scale(1.05);
}

.ring-2 {
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 0 2px var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.ring-yellow-200 {
  --tw-ring-color: rgb(254 240 138);
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.delay-1000 {
  animation-delay: 1s;
}

.delay-2000 {
  animation-delay: 2s;
}

.animate-bounce {
  animation: bounce 1s infinite;
}

@keyframes bounce {
  0%,
  100% {
    transform: translateY(-25%);
    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
  }
  50% {
    transform: translateY(0);
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
  }
}
</style>
