<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Pscsession\infrastructure\ReviewMailer;

class PscsessionReviewModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $rating = (int) ($payload['rating'] ?? 0);
            $description = trim($payload['description'] ?? '');

            $reviewMailer = $this->module->container()->get(ReviewMailer::class);

            $reviewMailer->send($rating, $description);

            $this->ajaxRender(
                json_encode([
                    'success' => true,
                ]),
            );

            exit();
        } catch (Exception $e) {
            http_response_code(500);

            $this->ajaxRender(
                json_encode([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]),
            );
        }
    }
}
