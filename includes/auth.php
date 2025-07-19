<?php
// Call session_start() at the very top of your entry script!

function require_role($role) {
    if (!isset($_SESSION['UserID']) || $_SESSION['User_Role'] !== $role) {
        header("Location: /common/login.php");
        exit;
    }
}

function require_login() {
    if (!isset($_SESSION['UserID'])) {
        header("Location: /common/login.php");
        exit;
    }
}
?>



// How to use these includes?
//At the top of any page, add (adjust the path if needed):
<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/auth.php");
require_once("../includes/functions.php");
?>