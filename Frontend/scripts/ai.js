// AI CATCH UP FOR START_CHAT.HTML
async function getAICatchUp() {
    const email = document.getElementById("otherEmail").value.trim();
    if (!email) return notify("Enter an email first.","error");

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/messages/ai-catchup",
            { email },
            { headers: { "X-Auth-Token": localStorage.getItem("token") } }
        );

        const data = resp.data.data;

        if (!data.show_summary) {
            notify(data.summary,"error");
            return;
        }

        showAIPopup(data.summary);

    } catch (error) {
        console.log("AI error:", error);
        notify("AI Error","error");
    }
}
//-----------------------for POPUP--------------------------
function showAIPopup(text) {
    document.getElementById("aiPopupContent").innerText = text;
    document.getElementById("aiPopup").classList.remove("hidden");
    document.getElementById("aiPopupOverlay").classList.remove("hidden");
}

function closeAIPopup() {
    document.getElementById("aiPopup").classList.add("hidden");
    document.getElementById("aiPopupOverlay").classList.add("hidden");
}
//---------------------------------------------------------
// USED ONLY IN chat.html
function closeAISummary() {
    const panel = document.getElementById("ai-panel");
    if (panel) panel.style.display = "none";
}
