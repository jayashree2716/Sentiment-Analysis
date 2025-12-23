    <?php
    session_start();

    $DB_HOST = 'localhost';
    $DB_USER = 'root';
    $DB_PASS = ''; // set your MySQL root password here
    $DB_NAME = 'ecommerce';

    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($mysqli->connect_error) {
        die('DB Connect Error: ' . $mysqli->connect_error);
    }

    function esc($s) {
        global $mysqli;
        return htmlspecialchars($mysqli->real_escape_string($s));
    }

$conn = new mysqli("localhost", "root", "", "ecommerce");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
