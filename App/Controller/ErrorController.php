<?php

namespace App\Controller;

use App\View\Error\NotFoundView;

class ErrorController {
    /**
     * Handle 404 not found errors
     * 
     * @return string HTML content for 404 page
     */
    public function notFound() {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Set HTTP status code to 404
        http_response_code(404);
        
        // Render the 404 view
        return NotFoundView::render();
    }
}
