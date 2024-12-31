    <style>

        .login-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
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
        <h1>Out</h1>
        {{errors}}
        <form method="POST" action="">
            <input type="text" name="eleve" placeholder="eleve codebarre" codebarre>
            {{msgeleve}}
            <input type="text" name="pc" placeholder="pc codebarre">
            {{msgpc}}
            <div>
                <button type="submit">Check</button>
                <button type="submit">Valider</button
            </div>
        </form>
    </div>