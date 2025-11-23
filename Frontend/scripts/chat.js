
const token = localStorage.getItem("token");
if (!token) {
    window.location.href = "login.html";
}


let conversationId = localStorage.getItem("conversation_id");

if (!conversationId) {
    alert("Start a chat first!");
    window.location.href = "start_chat.html";
}




// SEND MESSAGE
async function sendMessage() {
    const text = document.getElementById("messageInput").value.trim();
    if (text === "") return;

    try {
        const response = await axios.post(
            "../backend/public/send_message.php",
            {
                conversation_id: conversationId,
                message: text
            },
            {
                headers: { "X-Auth-Token": token }
            }
        );

        if (response.data.status === 200) {
            displayMessage(text, "me");
            document.getElementById("messageInput").value = "";
        } else {
            console.log("Error:", response.data.message);
        }

    } catch (error) {
        console.log("Server error:", error);
    }
}




// DISPLAY MESSAGE IN UI
function displayMessage(text, sender) {
    const box = document.getElementById("messages-box");

    const bubble = document.createElement("div");
    bubble.className = sender === "me" ? "bubble me" : "bubble them";
    bubble.innerText = text;

    box.appendChild(bubble);
    box.scrollTop = box.scrollHeight;
}




// LOGOUT
function logout() {
    localStorage.removeItem("token");
    localStorage.removeItem("user_id");
    localStorage.removeItem("conversation_id"); // very important
    window.location.href = "login.html";
}




// LOAD MESSAGES
async function loadMessages() {
    try {
        const resp = await axios.get(
            "../backend/public/get_messages.php?conversation_id=" + conversationId,
            {
                headers: { "X-Auth-Token": token }
            }
        );

        if (resp.data.status == 200) {
            const msgs = resp.data.messages;

            msgs.forEach(msg => {
                const sender =
                    msg.sender_id == localStorage.getItem("user_id")
                        ? "me"
                        : "them";

                displayMessage(msg.text, sender);
            });

        } else {
            console.log("Error loading messages");
        }

    } catch (error) {
        console.log("Server error:", error);
    }
}

// Load on first page open
loadMessages();



// REFRESH CHAT MANUALLY
async function refreshChat() {
    const box = document.getElementById("messages-box");
    box.innerHTML = ""; // clear UI

    await loadMessages(); // reload messages
}



// CHANGE CHAT (Switch to another email)

async function changeChat() {
    const email = prompt("Enter the email of the user you want to chat with:");
    if (!email) return;

    try {
        const resp = await axios.post(
            "../backend/public/start_conversation.php",
            { email: email },
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status === 200) {
            const newId = resp.data.conversation_id;

            // Save new conversation
            localStorage.setItem("conversation_id", newId);
            conversationId = newId; // update variable

            document.getElementById("chat-with").innerText = email;

            // Clear UI
            document.getElementById("messages-box").innerHTML = "";

            // Load the new chat
            loadMessages();

        } else {
            alert(resp.data.message);
        }

    } catch (error) {
        console.log(error);
        alert("Error changing chat");
    }
}
