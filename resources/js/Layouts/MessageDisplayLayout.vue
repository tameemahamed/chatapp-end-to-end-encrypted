<script setup>
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, nextTick, onUnmounted } from 'vue';

import { marked } from 'marked';
import DOMPurify from 'dompurify';
import NodeRSA from 'node-rsa';

const messages = ref([])
const newMessage = ref('') 
const chatContainer = ref(null)
const partner_public_key = ref('')

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


const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
}

function receiverEncrypt(messageContent) {
    const key = partner_public_key.value
    const key_public = new NodeRSA(key)

    const en_msg = key_public.encrypt(messageContent, 'base64')
    return en_msg
}

function senderEncrypt(messageContent) {
    const key = page.props.auth.user.public_key
    const key_public = new NodeRSA(key)
    
    const en_msg = key_public.encrypt(messageContent, 'base64')
    return en_msg
}

function userDecrypt(en_msg) {
    const key = localStorage.getItem('private_key')
    const key_private = new NodeRSA(key)

    const messageContent = key_private.decrypt(en_msg, 'utf8')
    return messageContent
}

const sendMessage = () => {
    if (newMessage.value.trim() === '') return;
    const messageContent = newMessage.value.trim();
    
    // Clear input
    const plaintextMessage = messageContent;
    newMessage.value = ''; 

    const tempMessage = {
        message: messageContent,
        decrypted_message: plaintextMessage, // Store plaintext for immediate display
        type: 'sent',
        created_at: new Date().toISOString()
    };
    messages.value.push(tempMessage);

    scrollToBottom();

    // Send encrypted messages to the API
    axios.post(
      '/api/add-message', {
      receiver_id: props.partner_id,
      receiver_en_msg: receiverEncrypt(plaintextMessage),
      sender_en_msg: senderEncrypt(plaintextMessage)
      }, {
        headers
      }
    )
}

const fetchMessages = () => {
    axios.get('/api/individual-messages', {
        params: { partner_id: props.partner_id },
        headers
    })
    .then(response => {
        messages.value = response.data.map(msg => {
            let decrypted = 'Decryption Error';
            try {
                decrypted = userDecrypt(msg.message);
            } catch (e) {
                console.error("Failed to decrypt message:", e, msg.message);
            }
            return {
                ...msg,
                decrypted_message: decrypted
            };
        });
        
        scrollToBottom();
    })
    .catch(error => {
        console.error("Error fetching messages: ", error);
    });

    axios.get('/api/user-public-key', {
        params: { user_id: props.partner_id },
        headers
    })
    .then(response => {
        partner_public_key.value = response.data.public_key
        return partner_public_key.value
    })
}

fetchMessages()

const parseMarkdown = (text) => {
  if (text === null || text === undefined) return '';
  try {
    const raw = marked.parse(String(text));
    return DOMPurify.sanitize(raw);
  } catch (e) {
    // fallback to escaped plain text on error
    const escaped = String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
    return escaped;
  }
}
</script>

<template>
    <div class="flex-1 h-screen flex flex-col bg-gray-50 dark:bg-gray-900">       
      <!-- Messages list -->
      <main ref="chatContainer" class="flex-1 overflow-y-auto p-6 space-y-4">
        <div v-if="messages.length === 0" class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
          No messages yet, say hi 👋
        </div>
  
        <div
          v-for="(msg, idx) in messages"
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
              v-html="parseMarkdown(msg.decrypted_message)"
            >
            </div>
            <div
              class="mt-1 text-xs"
              :class="msg.type === 'sent' ? 'text-right text-gray-100/70' : 'text-left text-gray-500 dark:text-gray-400'"
            >
              <!-- simple timestamp -->
              {{ new Date(msg.created_at).toLocaleTimeString('en-GB', { day: '2-digit', month:'2-digit', year:'2-digit', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Dhaka' }) }}
            </div>
          </div>
        </div>
      </main>
      
      <!-- Message Input Footer -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
        <div class="flex items-center space-x-3">
          <!-- Text Input Box -->
          <input
            type="text"
            v-model="newMessage"
            @keyup.enter="sendMessage"
            placeholder="Type a message..."
            class="flex-1 border border-gray-300 dark:border-gray-600 rounded-full py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 transition duration-150 ease-in-out text-base"
          />
          
          <!-- Send Button -->
          <button
            @click="sendMessage"
            :disabled="!newMessage.trim()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full transition duration-150 ease-in-out shadow-lg disabled:bg-blue-400 dark:disabled:bg-blue-800 disabled:cursor-not-allowed"
          >
            Send
          </button>
        </div>
      </div>
    </div>
</template>
