// ===============================
// AUTH CHECK
// ===============================
const token = localStorage.getItem("token");
if (!token) window.location.href = "login.html";

let conversationId = localStorage.getItem("conversation_id") || null;
let currentUserId = localStorage.getItem("user_id");

// DOM
const messagesBox = document.getElementById("messages-box");
const chatWith = document.getElementById("chat-with");
const conversationList = document.getElementById("conversation-list");


// ===============================
// LOAD USER EMAIL
// ===============================
document.getElementById("my-email").innerText = localStorage.getItem("email") || "";


// ===============================
// LOAD ALL CONVERSATIONS
// ===============================
// ⚠️ Only call /conversation/list IF you add it in routes
async function loadConversations() {
    try {
        const resp = await axios.get(
            "../Backend/public/index.php?route=/conversation/list",
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status !== 200) return;

        conversationList.innerHTML = "";

        resp.data.data.conversations.forEach(conv => {
            const div = document.createElement("div");
            div.className = "conversation-item";
            if (conv.id == conversationId) div.classList.add("active-chat");

            div.innerText = conv.other_email;
            div.onclick = () => selectConversation(conv.id, conv.other_email);

            conversationList.appendChild(div);
        });

    } catch (error) {
        console.log("Conversation list error:", error);
    }
}


// ===============================
// SELECT CONVERSATION
// ===============================
function selectConversation(id, email) {
    conversationId = id;
    localStorage.setItem("conversation_id", id);

    chatWith.innerText = email;

    messagesBox.innerHTML = "";

    loadMessages();
    loadConversations();
}


// ===============================
// LOAD MESSAGES
// ===============================
async function loadMessages() {
    if (!conversationId) return;

    try {
        const resp = await axios.get(
            "../Backend/public/index.php?route=/messages/list&conversation_id=" + conversationId,
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status !== 200) return;

        messagesBox.innerHTML = "";
        console.log(resp.data.data.messages);
        resp.data.data.messages.forEach(msg => {
            const sender = msg.sender_id == currentUserId ? "me" : "them";
            displayMessage(msg.text, sender, msg);
        });

        markDelivered();
        markRead();

    } catch (error) {
        console.log("Load messages error:", error);
    }
}


// ===============================
// SEND MESSAGE
// ===============================
async function sendMessage() {
    const text = document.getElementById("messageInput").value.trim();
    if (!text) return;

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/messages/send",
            { conversation_id: conversationId, message: text },
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status === 200) {
            displayMessage(text, "me");
            document.getElementById("messageInput").value = "";
        }

    } catch (error) {
        console.log("Send message error:", error);
    }
}

function displayMessage(text, sender, msg = null) {
    const bubble = document.createElement("div");
    bubble.className = "bubble " + sender;

    bubble.innerText = text;

    // Add ticks only for messages YOU sent
    if (sender === "me") {
        const tick = document.createElement("div");
        tick.className = "tick";

        if (msg?.read_at) tick.innerHTML = "✔✔";          // read
        else if (msg?.delivered_at) tick.innerHTML = "✔✔"; // delivered
        else tick.innerHTML = "✔";                         // sent

        bubble.appendChild(tick);
    }

    messagesBox.appendChild(bubble);
    messagesBox.scrollTop = messagesBox.scrollHeight;
}

// ===============================
// MARK DELIVERED + READ
// ===============================
async function markDelivered() {
    await axios.post(
        "../Backend/public/index.php?route=/messages/mark-delivered",
        { conversation_id: conversationId },
        { headers: { "X-Auth-Token": token } }
    );
}

async function markRead() {
    await axios.post(
        "../Backend/public/index.php?route=/messages/mark-read",
        { conversation_id: conversationId },
        { headers: { "X-Auth-Token": token } }
    );
}


// ===============================
// BUTTONS
// ===============================
async function refreshChat() {
    messagesBox.innerHTML = "";
    await loadMessages();
}

async function startNewChat() {
    const email = prompt("Enter user email:");
    if (!email) return;

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/conversation/start",
            { email },
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status === 200) {
            conversationId = resp.data.data.conversation_id;
            localStorage.setItem("conversation_id", conversationId);

            chatWith.innerText = email;
            messagesBox.innerHTML = "";

            loadConversations();
            loadMessages();
        }

    } catch (e) {
        console.log(e);
        alert("Error starting chat");
    }
}

function logout() {
    localStorage.clear();
    window.location.href = "login.html";
}


// ===============================
// INITIAL LOAD
// ===============================
loadConversations();
if (conversationId) loadMessages();
