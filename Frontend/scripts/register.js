async function registerUser() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
        return notify("Please fill all fields","error");
    }

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/auth/register",
            { email, password }
        );

        if (resp.data.status === 200) {
            notify("Account created! Login now.","success");
            window.location.href = "login.html";
        } else {
            notify(resp.data.message,"error");
        }

    } catch (error) {
        console.log(error);
        notify("Server error","error");
    }
}
