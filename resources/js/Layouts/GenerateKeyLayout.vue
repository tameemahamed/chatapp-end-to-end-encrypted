<script setup>
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import NodeRSA from 'node-rsa';
import { ref } from 'vue';



const page = usePage()
const headers = {};
if (page.props.auth_token) {
    headers['Authorization'] = `Bearer ${page.props.auth_token}`;
}

const private_key = ref('')
const public_key = ref('')
const private_key_incorrect_flag = ref(false)

function updatePublicKey() {
    axios.post('/api/public-key-update', {
        public_key: public_key.value
    }, {
        headers
    })
    .catch(error => {
        console.log('Error updating public key', error)
    });
}

// returns true or false
async function checkPublicKey() {
    const response = await axios.get('/api/public-key-check' , {
        params: { public_key: public_key.value },
        headers
    })
    console.log(response.data); 
    return response.data
}

function storePrivateKey() {
    localStorage.setItem('private_key', private_key.value)
}

const generate_rsa_keypair = () => {
    const key = new NodeRSA({b:4096})

    const gen_private_key = key.exportKey('private')
    const gen_public_key = key.exportKey('public')

    private_key.value = gen_private_key
    public_key.value = gen_public_key

    storePrivateKey()
    updatePublicKey()
} 

const generate_rsa_public_key = async () =>  {
    const gen_private_key = private_key.value
    const key_private = new NodeRSA(gen_private_key)
    const gen_public_key = key_private.exportKey('public')
    public_key.value = gen_public_key

    const isValid = await checkPublicKey()

    if(isValid) {
        storePrivateKey()
        private_key_incorrect_flag.value = false
    }

    else {
        private_key_incorrect_flag.value = true
    }
}

</script>

<template>
    <div class="min-h-screen bg-gray-900 text-white p-8">
        <div class="max-w-2xl mx-auto space-y-6">
            <!-- Private Key Input -->
            <div>
                <label class="block text-sm font-medium mb-2">Private Key</label>
                <textarea v-model="private_key" rows="6"
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your private key or generate a new key pair"></textarea>
            </div>

            <!-- Public Key Display -->
            <div>
                <label class="block text-sm font-medium mb-2">Public Key</label>
                <textarea v-model="public_key" rows="4" readonly
                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-sm opacity-75"
                    placeholder="Public key will appear here"></textarea>
            </div>

            <!-- Error Message -->
            <div v-if="private_key_incorrect_flag" class="bg-red-900/50 border border-red-700 rounded-lg p-4 text-sm">
                Private key doesn't match the public key that is stored in our server.<br>
                Put it correctly or try generating new keypair.
            </div>

            <div class="bg-yellow-900/50 border border-yellow-700 rounded-lg p-4 text-sm">
                <strong>Warning:</strong> Generating a new key pair will permanently delete all existing messages from your end and as well as from the other person's end.
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button @click="generate_rsa_keypair"
                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition-colors">
                    Generate New Key Pair
                </button>
                <button @click="generate_rsa_public_key" :disabled="!private_key"
                    class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 px-4 py-2 rounded-lg transition-colors">
                    Generate Public Key
                </button>
            </div>
        </div>
    </div>
</template>