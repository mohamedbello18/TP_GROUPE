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
        // Save to file instead of sending email
        $data = [
            'date' => date('Y-m-d H:i:s'),
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];
        
        file_put_contents('contact_submissions.txt', print_r($data, true) . "\n---\n", FILE_APPEND);
        
        $response = [
            'success' => true,
            'message' => 'Votre message a été enregistré! Nous vous contacterons bientôt.'
        ];
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
