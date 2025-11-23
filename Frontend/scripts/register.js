async function registerUser() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
        return alert("Please fill all fields");
    }

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/auth/register",
            { email, password }
        );

        if (resp.data.status === 200) {
            alert("Account created! Login now.");
            window.location.href = "login.html";
        } else {
            alert(resp.data.message);
        }

    } catch (error) {
        console.log(error);
        alert("Server error");
    }
}
