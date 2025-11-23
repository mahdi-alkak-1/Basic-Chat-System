// AUTH CHECK
const token = localStorage.getItem("token");
if (!token) window.location.href = "login.html";

// Fix optional chaining error
const myEmail = document.getElementById("my-email");
if (myEmail) {
    myEmail.innerText = localStorage.getItem("email");
}

// ------------------------------
// START CHAT
// ------------------------------
async function startChat() {
    const email = document.getElementById("otherEmail").value.trim();
    if (!email) return alert("Enter an email");

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/conversation/start",
            { email },
            { headers: { "X-Auth-Token": token } }
        );

        if (resp.data.status === 200) {
            const cid = resp.data.data.conversation_id;
            localStorage.setItem("conversation_id", cid);
            window.location.href = "chat.html";
        } else {
            alert(resp.data.message);
        }

    } catch (error) {
        console.log(error);
        alert("Server error");
    }
}

// ------------------------------
// LOGOUT
// ------------------------------
function logout() {
    localStorage.clear();
    window.location.href = "login.html";
}
