async function login() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const error = document.getElementById("error");

    error.innerText = "";  // clear errors

    if (!email || !password) {
        error.innerText = "All fields required.";
        return;
    }
    

    try {
        const response = await axios.post("../backend/auth/login.php",
            {   email: email,
                password: password ,
            },
            {
                headers: { 'Content-Type': 'application/json'},
            }
        );

        const userData  = response.data.data;
        if (response.data.status === 200) {
            localStorage.setItem("user_id", userData.id)
            localStorage.setItem("token", userData.token);
            window.location.href = "chat.html";
        } else {
            error.innerText = response.data.message;
        }

    } catch (err) {
        error.innerText = "Server error.";
        console.log(err);
    }
}