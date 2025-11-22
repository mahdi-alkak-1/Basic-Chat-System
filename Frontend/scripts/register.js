async function registerUser() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirm = document.getElementById("confirm").value.trim();
    const error = document.getElementById("error");

    error.innerText = ""; // clear errors

    if (!email || !password || !confirm) {
        error.innerText = "All fields required.";
        return;
    }

    if (password !== confirm) {
        error.innerText = "Passwords do not match.";
        return;
    }

    try {
        const response = await axios.post(
            "../backend/auth/register.php",
            {   email: email,
                password: password },
            {
                headers: { 'Content-Type': 'application/json'},
            }
        );

         console.log(response.data); 
         
        if (response.data.status === 200) {
            alert("Account created! Login now.");
            window.location.href = "login.html";
        } else {
            error.innerText = response.data.message;
        }

    } catch (err) {
        error.innerText = "Server error.";
        console.log(err);
    }
}