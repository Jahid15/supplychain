<?php
// register.php
$host = "localhost"; // Change to your DB host
$user = "root";      // Change to your DB username
$pass = "";          // Change to your DB password
$db = "scm";         // Change to your DB name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Security: Hash the password!
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Ref_ID left NULL here (you can add logic if needed)
$stmt = $conn->prepare("INSERT INTO `USERs` (Username, Password, Email, User_Role) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("ssss", $username, $hash, $email, $role);



    if ($stmt->execute()) {
    echo "<div style='margin:2rem'>Registration successful!</div>";
} else {
    echo "<div style='margin:2rem;color:red'>Error: " . $stmt->error . "</div>";
}

    $stmt->close();
}
$conn->close();
?>
