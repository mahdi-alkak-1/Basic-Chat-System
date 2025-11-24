function notify(msg, type = "info") {
    const box = document.getElementById("notify");
    box.innerText = msg;

    // color based on type
    if (type === "error") box.style.background = "#e74c3c";
    else if (type === "success") box.style.background = "#2ecc71";
    else box.style.background = "#3498db";

    box.classList.remove("hidden");
    box.classList.add("show");

    setTimeout(() => {
        box.classList.remove("show");
    }, 2500);
}