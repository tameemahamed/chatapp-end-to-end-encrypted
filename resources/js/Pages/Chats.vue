<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChatlistLayout from '@/Layouts/ChatlistLayout.vue';
import MessageDisplayLayout from '@/Layouts/MessageDisplayLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import NodeRSA from 'node-rsa';

// all keys
const private_key = ref('')
const public_key = ref('')

// selected partner id (null when none selected)
const selectedPartner = ref(null)

onMounted(() => {
    if(localStorage.getItem('private_key')){
        private_key.value = localStorage.getItem('private_key').trim()
    }
})

// store or update public key
function storePublicKey() {
    localStorage.setItem('public_key', public_key.value)
}

// store or update private key
function storePrivateKey() {
    localStorage.setItem('private_key', private_key.value)
    storePublicKey()
}


// generate both public and private keys
function generate_rsa_keypair() {
    const key = new NodeRSA({b:4096})

    // these are those which starts with a ----Begin PUBLIC KEY---- flags
    const gen_private_key = key.exportKey('private')
    const gen_public_key = key.exportKey('public')

    private_key.value = gen_private_key
    public_key.value = gen_public_key

    storePrivateKey()
}


// generate public key using private key
function generate_rsa_public_key() {
    const gen_private_key = localStorage.getItem('private_key')
    const key_private = new NodeRSA(gen_private_key)
    const gen_public_key = key_private.exportKey('public')
    public_key.value = gen_public_key

    storePublicKey()
}

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
            <p>not found</p>
        </div>
    </AuthenticatedLayout>
</template>