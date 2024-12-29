    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .login-container h1 {
            margin-bottom: 20px;
            color: #555;
        }

        .login-container .error {
            color: red;
            margin-bottom: 15px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .login-container button {
            padding: 10px;
            background-color: #5b9bd5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #4178a9;
        }
    </style>
    <div class="login-container">
        <h1>Connexion</h1>
        {{errors}}
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Pseudo" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>