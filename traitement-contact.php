<?php
header('Content-Type: application/json');

// Récupérer les infos de connexion depuis les variables d'environnement définies dans docker-compose.yml
$host = getenv('DB_HOST') ?: 'db';           // 'db' est le nom du service base dans docker-compose
$dbname = getenv('DB_NAME') ?: 'tp-groupe';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'rootpassword';

try {
    // Connexion PDO à la base MySQL en utilisant les variables d'env
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // En cas d’erreur, essayer de créer la base
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
}

// Création de la table contacts si elle n'existe pas
$sqlCreateTable = "
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($sqlCreateTable);

// Récupération des données POST
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation simple
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

if (strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Le nom doit contenir au moins 2 caractères']);
    exit;
}

// Insertion dans la table contacts
$sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $email, $subject, $message]);

echo json_encode(['success' => true, 'message' => 'Message enregistré avec succès']);
?>
