async function login() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
        return notify("Please fill all fields", "error");
    }

    try {
        const resp = await axios.post(
            "../Backend/public/index.php?route=/auth/login",
            { email,
             password,
            },
            {
                headers: { 'X-Auth-Token': localStorage.getItem('token') },
            }

        );

        if (resp.data.status === 200) {
            const user = resp.data.data;

            localStorage.setItem("token", user.token);
            localStorage.setItem("user_id", user.id);
            localStorage.setItem("email", user.email);

            window.location.href = "start_chat.html";
        } else {
            notify(resp.data.message,"error");
        }

    } catch (error) {
        console.log(error);
        notify("Server error","error");
    }
}
