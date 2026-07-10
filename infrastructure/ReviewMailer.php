<?php

namespace Pscsession\infrastructure;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class ReviewMailer
{
    public function send(int $rating, string $description): void
    {
        // PHPMailer
        $mail = new PHPMailer(true);

        // SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Port = (int) $_ENV['MAIL_PORT'];
        $mail->SMTPAuth = (bool) $_ENV['MAIL_SMTP_AUTH'];
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        // Expéditeur
        $mail->setFrom($_ENV['MAIL_USERNAME'], 'PSC review');

        // Destinataire
        $mail->addAddress($_ENV['MAIL_TO']);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'New PSC Session review';

        $mail->Body = sprintf(
            '
                <h2>New review</h2>
                <p><strong>Rating:</strong> %d / 5</p>
                <p><strong>Comment:</strong></p>
                <p>%s</p>
                ',
            $rating,
            nl2br(htmlspecialchars($description)),
        );

        $mail->send();
    }
}
