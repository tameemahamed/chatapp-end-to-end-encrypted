<script setup>
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const partners = ref([]);

const page = usePage();
const headers = {};
if (page.props.auth_token) {
    headers['Authorization'] = `Bearer ${page.props.auth_token}`;
}

// Emit selected partner id to parent
const emit = defineEmits(['select-partner']);

onMounted(() => {
    axios.get('/api/chat-list', { headers })
        .then(response => {
            partners.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching partners:', error);
        });
});
</script>

<template>
    <aside class="w-64 bg-white dark:bg-gray-900 h-screen overflow-y-auto border-r border-gray-200 dark:border-gray-800">
        <div class="p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Chats</h2>
        </div>
        <ul>
            <li
                v-for="partner in partners"
                :key="partner.partner_id"
                class="flex items-center p-4 border-b border-gray-200 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer"
                @click="emit('select-partner', partner.partner_id)"
                :data-partner-id="partner.partner_id"
            >
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-700"><img :src="partner.partner_profile_picture_url" alt=""></div> 
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ partner.partner_name }}</p>
                    <!-- <p class="text-xs text-gray-500 dark:text-gray-400">Last message preview...</p> -->
                </div>
            </li>
        </ul>
    </aside>
</template>