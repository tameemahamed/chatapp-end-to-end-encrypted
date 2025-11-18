<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChatlistLayout from '@/Layouts/ChatlistLayout.vue';
import MessageDisplayLayout from '@/Layouts/MessageDisplayLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import GenerateKeyLayout from '@/Layouts/GenerateKeyLayout.vue';

// all keys
const private_key = ref('')

// selected partner id (null when none selected)
const selectedPartner = ref(null)

onMounted(() => {
    if(localStorage.getItem('private_key')){
        private_key.value = localStorage.getItem('private_key').trim()
    }
})

</script>

<template>
    <Head title="Chats" />
    <AuthenticatedLayout>
        <div v-if="private_key" class="flex h-screen">
            <!-- on the left sidebar -->
            <ChatlistLayout @select-partner="selectedPartner = $event"/>
        
            <!-- message panel: only show when a partner is selected -->
            <!-- will be to the right side of the chatlist -->
            <MessageDisplayLayout
              v-if="selectedPartner"
              :partner_id="selectedPartner"
              :key="selectedPartner"   
            />
        </div>
        <div v-else>
            <GenerateKeyLayout />
        </div>
    </AuthenticatedLayout>
</template>