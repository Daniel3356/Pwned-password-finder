<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hashing and Pwned Check</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            color: #555;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #4a90e2;
        }
        button {
            padding: 12px;
            font-size: 16px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #357ab7;
        }
        .result {
            text-align: center;
            margin-top: 20px;
        }
        h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .alert {
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .alert.pwned {
            background-color: #ff4d4f;
            color: white;
        }
        .alert.safe {
            background-color: #4caf50;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Password Pwned Check</h2>

    <form method="post" action="">
        <label for="password">Enter Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter your password...">
        <button type="submit">Check Password</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Fetch the password input
        $password = $_POST['password'];

        // Encrypt password using SHA-1
        $hashed_password = sha1($password);

        // Split the hashed password into first 5 chars and the rest
        $first_5_chars = strtoupper(substr($hashed_password, 0, 5)); // API requires uppercase
        $rest_of_hash = strtoupper(substr($hashed_password, 5));

        // Pwned Passwords API URL
        $api_url = "https://api.pwnedpasswords.com/range/$first_5_chars";

        // Make a GET request to the API
        $response = file_get_contents($api_url);

        // Check if the rest of the hash exists in the response
        $lines = explode("\n", $response);
        $found_count = 0; // Initialize as not found

        foreach ($lines as $line) {
            list($hash_suffix, $count) = explode(':', trim($line));
            if ($hash_suffix === $rest_of_hash) {
                $found_count = intval($count); // Password found
                break;
            }
        }

        // Display results
        echo "<div class='result'>";

        if ($found_count > 0) {
            // Format the found count with commas
            $formatted_count = number_format($found_count);
            echo "<div class='alert pwned'>This password has been pwned $formatted_count times!</div>";
        } else {
            echo "<div class='alert safe'>Good news! This password was not found in the Pwned Passwords database.</div>";
        }
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
