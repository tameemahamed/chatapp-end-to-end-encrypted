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

const selectedPartnerId = ref(null);

const selectPartner = (id) => {
    selectedPartnerId.value = id;
    emit('select-partner', id);
};

const searchTerm = ref('');
const users = ref([]);

const searchUsers = () => {
    axios.get('/api/users-list', {
        params: { name: searchTerm.value },
        headers
    })
    .then(response => {
        users.value = response.data;
    })
    .catch(error => {
        console.error('Error searching users:', error);
        users.value = [];
    });
};

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

            <div class="flex items-center space-x-2 mb-3">
                <input
                    v-model="searchTerm"
                    @input="searchUsers"
                    type="text"
                    placeholder="Search users..."
                    class="w-full px-3 py-2 border rounded-md text-sm
                           bg-white dark:bg-gray-700
                           border-gray-300 dark:border-gray-600
                           text-gray-900 dark:text-gray-100
                           placeholder-gray-500 dark:placeholder-gray-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                />
            </div>

            <div v-if="users.length" class="mb-3">
                <ul class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden">
                    <li
                        v-for="user in users"
                        :key="user.id"
                        class="text-sm px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer
                               text-gray-800 dark:text-gray-100"
                    >
                        {{ user.name }}
                    </li>
                </ul>
            </div>
        </div>
        <ul>
            <li
                v-for="partner in partners"
                :key="partner.partner_id"
                :class="[
                  'flex items-center p-4 border-b border-gray-200 dark:border-gray-800 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer',
                  selectedPartnerId === partner.partner_id ? 'bg-gray-100 dark:bg-gray-800' : ''
                ]"
                @click="selectPartner(partner.partner_id)"
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