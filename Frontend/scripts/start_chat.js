const token = localStorage.getItem("token");
if (!token) {
    window.location.href = "login.html";
}

async function startChat() {
    const email = document.getElementById("otherEmail").value.trim();
    if (!email) return alert("Enter email!");

    try {
        const resp = await axios.post(
            "../backend/public/start_conversation.php",
            { email: email },
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status === 200) {
            const conversationId = resp.data.conversation_id;

            // Save for use inside chat page
            localStorage.setItem("conversation_id", conversationId);

            // Redirect to chat
            window.location.href = "chat.html";
        } else {
            alert(resp.data.message);
        }

    } catch (e) {
        console.log(e);
        alert("Server error");
    }
}