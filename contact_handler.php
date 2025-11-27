<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    $errors = [];
    
    if (empty($name)) $errors[] = "Le nom est requis.";
    if (empty($email)) $errors[] = "L'email est requis.";
    if (empty($subject)) $errors[] = "Le sujet est requis.";
    if (empty($message)) $errors[] = "Le message est requis.";
    
    if (empty($errors)) {
        // Create a more structured data entry
        $timestamp = date('Y-m-d H:i:s');
        $entry = "=== Nouveau Contact ===\n";
        $entry .= "Date: $timestamp\n";
        $entry .= "Nom: $name\n";
        $entry .= "Email: $email\n";
        $entry .= "Sujet: $subject\n";
        $entry .= "Message: $message\n";
        $entry .= "=====================\n\n";
        
        $filename = 'contact_submissions.txt';
        
        // Try to save to file
        if (file_put_contents($filename, $entry, FILE_APPEND | LOCK_EX)) {
            $response = [
                'success' => true,
                'message' => 'Votre message a été enregistré! Nous vous contacterons bientôt.'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Erreur technique. Veuillez réessayer plus tard.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Veuillez corriger les erreurs suivantes:',
            'errors' => $errors
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
