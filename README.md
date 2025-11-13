# End to End Encrypted Chat Application

**A Laravel + Vue (Vite) real-time chat application implementing client-side end-to-end encryption (E2EE).**
This project provides a full-stack implementation where messages are encrypted in the browser using public-key cryptography before being sent to the server. The server stores and forwards ciphertext only, and does not hold users' private keys.

---

## Key features

* User registration & authentication (Laravel)
* One-to-one chat interface built with Vue
* Client-side RSA-based end-to-end encryption:

  * Each user has an RSA key pair (public/private).
  * Private key is stored client-side; public key is shared with server for other clients to encrypt to.
  * Messages are encrypted on sender’s side with recipient’s public key (so only recipient’s private key can decrypt).
* Server stores and forwards ciphertext only (no plaintext).
* Real-time message delivery (Laravel broadcasting or websockets).
* Tailwind CSS for styling, Vite for building frontend assets.

---

## E2EE workflow

1. **Keypair generation (client-side)**

   * When a user registers or first logs in, the frontend generates an RSA key pair (public and private).
   * The **public key** is uploaded to the server and stored with the user's profile (so others can fetch it for encryption).
   * The **private key** remains in the client browser local storage. The server never receives the private key.

2. **Sending a message**

   * Sender fetches recipient's public key from the server.
   * Sender encrypts the plaintext message in the browser using the recipient's public key (for RSA, e.g., via NodeRSA).
   * The ciphertext is sent to the server via an HTTP request or websocket event.
   * Server stores the ciphertext (in DB) and broadcasts it to the recipient (or queues it for delivery).

3. **Receiving a message**

   * Recipient receives ciphertext via websocket or fetch.
   * Recipient decrypts the ciphertext using their client-side private key to reveal the plaintext.

4. **Server role**

   * Stores ciphertext + metadata (sender_id, recipient_id, timestamps).
   * Forwards ciphertext to recipients; cannot decrypt without private keys.

---

<video controls playsinline style="max-width:100%;height:auto;">
  <source src="storage/readme/videos/simplechat.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>
<small>A simple chatting demo</small>

---

## Installation & Setup (typical Laravel + Vite + Vue steps)

1. **Clone**

```bash
git clone https://github.com/tameemahamed/chatapp-end-to-end-encrypted.git
cd chatapp-end-to-end-encrypted
```

2. **Backend dependencies**

```bash
composer install
```

3. **Environment**

```bash
cp .env.example .env
# Edit .env:
#  - Set DB_DATABASE, DB_USERNAME, DB_PASSWORD
#  - Set APP_URL, and any broadcast config (PUSHER_APP_ID, etc.) if used
php artisan key:generate
```

4. **Database**

```bash
php artisan migrate
php artisan db:seed
```

5. **Frontend dependencies**

```bash
npm install
```

6. **Build / Run**

* For development (hot reload):

```bash
npm run dev
php artisan serve
```

* For production build:

```bash
npm run build
php artisan serve   # or deploy to your web server
```

7. **Broadcasting / Websockets**

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=...
PUSHER_APP_KEY=...
PUSHER_APP_SECRET=...
PUSHER_APP_CLUSTER=mt1
```