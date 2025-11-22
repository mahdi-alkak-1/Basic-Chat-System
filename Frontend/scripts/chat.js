

const token = localStorage.getItem("token");
if (!token) {
    window.location.href = "login.html";
}


async function sendMessage() {
    const text = document.getElementById("messageInput").value.trim();
    if (text === "") return;
    try {
            const response = await axios.post(
                "../backend/public/send_message.php",
                {
                    conversation_id: 1, // TEMP: we hardcode conversation 1 for now
                    message: text
                },
                {
                    headers: { 'X-Auth-Token': localStorage.getItem('token') }
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

        document.getElementById("messageInput").value = "";
    }


function displayMessage(text, sender) {
    const box = document.getElementById("messages-box");

    const bubble = document.createElement("div");
    bubble.className = sender === "me" ? "bubble me" : "bubble them";
    bubble.innerText = text;

    box.appendChild(bubble);
    box.scrollTop = box.scrollHeight;
}

function logout() {
    localStorage.removeItem("token");
    window.location.href = "login.html";
}
