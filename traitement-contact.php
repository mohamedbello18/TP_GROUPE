<?php
header('Content-Type: application/json');

// Connexion base de données (adaptez les infos)
$host = 'localhost';
$dbname = 'tp-groupe';
$username = 'root'; // ou votre user
$password = ''; // ou votre mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Si la base n'existe pas, on la crée
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
}

// Créer la table si elle n'existe pas
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

// Récupérer les données du formulaire
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if(empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

if(strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Le nom doit contenir au moins 2 caractères']);
    exit;
}

// Insérer dans la base
$sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $email, $subject, $message]);

echo json_encode(['success' => true, 'message' => 'Message enregistré avec succès']);

?>
