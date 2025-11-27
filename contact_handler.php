<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Get form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Le nom est requis.";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    
    if (empty($subject)) {
        $errors[] = "Le sujet est requis.";
    }
    
    if (empty($message)) {
        $errors[] = "Le message est requis.";
    }
    
    // If no errors, send email
    if (empty($errors)) {

        $to = "mohamedbello717@gmail.com"; 
        $email_subject = "Nouveau message de contact: $subject";
        
        $email_body = "
        Nouveau message de contact depuis votre site web:
        
        Nom: $name
        Email: $email
        Sujet: $subject
        
        Message:
        $message
        
        ---
        Cet email a été envoyé depuis le formulaire de contact de votre site.
        ";
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Debug information
        $debug_info = [
            'to' => $to,
            'subject' => $email_subject,
            'headers' => $headers,
            'server' => $_SERVER
        ];
        
        // Try to send email
        if (mail($to, $email_subject, $email_body, $headers)) {
            $response = [
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès!'
            ];
        } else {
            // Log error for debugging
            error_log("Email sending failed. Debug: " . print_r($debug_info, true));
            
            $response = [
                'success' => false,
                'message' => 'Erreur: Le serveur ne peut pas envoyer d\'email. Veuillez nous contacter directement.',
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Veuillez corriger les erreurs suivantes:',
            'errors' => $errors
        ];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // If not POST request, redirect to contact page
    header('Location: contact.html');
    exit;
}
?>
