<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login / Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .box {
            width: 380px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        h3 {
            text-align: center;
            color: #1e3c72;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            background: #1e3c72;
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #16305d;
        }

        .toggle {
            margin-top: 15px;
            text-align: center;
            color: #1e3c72;
            cursor: pointer;
        }

        .hidden {
            display: none;
        }

        .error {
            background: #ffe6e6;
            color: #b00020;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="box">

        <h3 id="title">Login</h3>

        <!-- LOGIN -->
        <form id="loginForm">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- REGISTER -->
        <form id="registerForm" class="hidden">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>

        <div id="errorBox" class="error hidden"></div>

        <div class="toggle" id="toggle">Create account</div>

    </div>

    <script>
        const toggle = document.getElementById('toggle');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const errorBox = document.getElementById('errorBox');
        const title = document.getElementById('title');

        toggle.onclick = () => {
            loginForm.classList.toggle('hidden');
            registerForm.classList.toggle('hidden');
            title.innerText = loginForm.classList.contains('hidden') ? 'Register' : 'Login';
            toggle.innerText = title.innerText === 'Login' ?
                'Create account' :
                'Already have an account?';
        };

        function showError(msg) {
            errorBox.innerText = msg;
            errorBox.classList.remove('hidden');
            setTimeout(() => errorBox.classList.add('hidden'), 4000);
        }

        // LOGIN
        loginForm.onsubmit = async e => {
            e.preventDefault();

            const form = new FormData(loginForm);

            const res = await fetch('/login', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: form
            });

            if (res.ok) {
                window.location.href = '/dashboard';
            } else {
                showError('Invalid email or password');
            }
        };

        // REGISTER
        registerForm.onsubmit = async e => {
            e.preventDefault();

            const form = new FormData(registerForm);

            const res = await fetch('/register', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: form
            });

            if (res.ok) {
                alert('Registered successfully. Please login.');
                toggle.click();
            } else {
                showError('Registration failed. Check inputs.');
            }
        };
    </script>

</body>

</html>
