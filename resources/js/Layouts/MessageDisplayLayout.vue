<script setup>
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const messages = ref([])

const props = defineProps({
    partner_id: {
        type: Number,
        required: true
    }
})

const page = usePage()
const headers = {};
if (page.props.auth_token) {
    headers['Authorization'] = `Bearer ${page.props.auth_token}`;
}



onMounted(() => {
    axios.get('/api/individual-messages', {
            params: { partner_id: props.partner_id }, 
            headers
        })
        .then(response => {
            messages.value = response.data;
            // console.log(messages);
        })
        .catch(error => {
            console.error("Error fetching problems : ", error);
        })
})

</script>

<template>
    <div class="flex-1 h-screen flex flex-col bg-gray-50 dark:bg-gray-900">       
      <!-- Messages list -->
      <main class="flex-1 overflow-y-auto p-6 space-y-4">
        <div v-if="messages.length === 0" class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
          No messages yet, say hi 👋
        </div>
  
        <div
          v-for="(msg, idx) in messages.slice().reverse()"
          :key="idx"
          class="flex"
          :class="msg.type === 'sent' ? 'justify-end' : 'justify-start'"
        >
          <div class="max-w-[70%]">
            <div
              :class="[
                'inline-block rounded-2xl px-4 py-2 text-sm break-words shadow',
                msg.type === 'sent'
                  ? 'bg-blue-600 text-white'
                  : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100'
              ]"
            >
              {{ msg.message }}
            </div>
            <div
              class="mt-1 text-xs"
              :class="msg.type === 'sent' ? 'text-right text-gray-100/70' : 'text-left text-gray-500 dark:text-gray-400'"
            >
              <!-- simple human-readable timestamp -->
              {{ new Date(msg.created_at).toLocaleTimeString('en-GB', { day: '2-digit', month:'2-digit', year:'2-digit', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Dhaka' }) }}
            </div>
          </div>
        </div>
      </main>
    </div>
</template>
