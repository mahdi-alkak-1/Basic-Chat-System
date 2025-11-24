// AI CATCH UP FOR START_CHAT.HTML
async function getAICatchUp() {
    const email = document.getElementById("otherEmail").value.trim();
    if (!email) return alert("Enter an email first.");

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/messages/ai-catchup",
            { email },
            { headers: { "X-Auth-Token": localStorage.getItem("token") } }
        );

        const data = resp.data.data;

        if (!data.show_summary) {
            alert(data.summary);
            return;
        }

        alert("AI Summary:\n\n" + data.summary);

    } catch (error) {
        console.log("AI error:", error);
        alert("AI Error");
    }
}

// USED ONLY IN chat.html
function closeAISummary() {
    const panel = document.getElementById("ai-panel");
    if (panel) panel.style.display = "none";
}
