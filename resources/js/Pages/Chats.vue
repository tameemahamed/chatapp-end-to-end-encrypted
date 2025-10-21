<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const private_key = ref('')

onMounted(() => {
    if(localStorage.getItem('private_key')){
        private_key.value = localStorage.getItem('private_key').trim()
    }
})
</script>

<template>
    <Head title="Chats" />
    <AuthenticatedLayout>
        <div v-if="private_key">
            <p>{{ private_key }}</p>
        </div>
        <div v-else>
            <p>not found</p>
        </div>
    </AuthenticatedLayout>
</template>