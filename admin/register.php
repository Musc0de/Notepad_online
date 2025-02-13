<?php
session_start();
require_once '../database.php';

// Initialize variables
$username = $password = '';
$registration_error = '';

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $registration_error = 'Username already exists';
    } else {
        // Insert new admin
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username, $hashed_password);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $registration_error = 'Registration failed';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Additional custom styles */
        .error-message {
            color: #e53e3e; /* Red color for errors */
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="container mx-auto p-6 max-w-sm bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 text-center">Admin Registration</h2>

        <?php if (!empty($registration_error)): ?>
            <p class="error-message mb-4 text-center"><?php echo htmlspecialchars($registration_error); ?></p>
        <?php endif; ?>

        <form method="post" action="" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700 font-medium mb-1">Username:</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Password:</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="text-center">
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Register</button>
            </div>
        </form>
    </div>
</body>
</html>

