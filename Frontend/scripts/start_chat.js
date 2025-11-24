const token = localStorage.getItem("token");
if (!token) window.location.href = "login.html";

// START CHAT
async function startChat() {
    const email = document.getElementById("otherEmail").value.trim();
    if (!email) return notify("Enter an email","error");
    // alert("Enter an email")
    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/conversation/start",
            { email },
            { headers: { "X-Auth-Token": token } }
        );

        console.log("am in startchat: ",resp);//resp contain conversation id
        if (resp.data.status === 200) {
            const cid = resp.data.data.conversation_id;
            localStorage.setItem("conversation_id", cid);
            window.location.href = "chat.html";
        } else {
            notify(resp.data.message,"error");
        }

    } catch (error) {
        console.log(error);
        notify("Server error", "error");
    }
}


// LOGOUT
function logout() {
    localStorage.clear();
    window.location.href = "login.html";
}
