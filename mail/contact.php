<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require '../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (
    empty($_POST['name']) || 
    empty($_POST['subject']) || 
    empty($_POST['message']) || 
    !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
) {
    http_response_code(400);
    exit('Invalid input');
}

$name = strip_tags($_POST['name']);
$email = strip_tags($_POST['email']);
$m_subject = strip_tags($_POST['subject']);
$message = strip_tags($_POST['message']);

$mail = new PHPMailer(true);

try {
    // Paramètres SMTP de Brevo via .env
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USERNAME'];
    $mail->Password   = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Expéditeur et destinataire
    $mail->setFrom($_ENV['SMTP_USERNAME'], $name);
    $mail->addAddress($_ENV['SMTP_RECIPIENT']);

    // Contenu
    $mail->isHTML(false);
    $mail->Subject = "$m_subject - $name";
    $mail->Body    = "Vous avez reçu un nouveau message depuis le formulaire de contact.\n\n"
                   . "Nom: $name\n"
                   . "Email: $email\n"
                   . "Sujet: $m_subject\n"
                   . "Message:\n$message";

    $mail->send();
    http_response_code(200);
    echo 'Message envoyé avec succès';
} catch (Exception $e) {
    http_response_code(500);
    echo "Erreur d\'envoi: {$mail->ErrorInfo}";
}
