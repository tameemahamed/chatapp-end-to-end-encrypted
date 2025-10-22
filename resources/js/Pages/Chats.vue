<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import NodeRSA from 'node-rsa';

// all keys
const private_key = ref('')
const public_key = ref('')

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

function encrypt_msg_reciever () {
    
}

function encrypt_msg_sender () {
    
}

function decrypt_msg () {
    // use sender_private_key variable for decrypt
}

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