<?php

namespace AmeliaBooking\Application\Services\Booking;

use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class BookingFallbackService
 *
 * Provides fallback HTML pages when redirect URLs are not defined
 *
 * @package AmeliaBooking\Application\Services\Booking
 */
class BookingFallbackService
{
    /**
     * Generates HTML for booking confirmation fallback page
     */
    private static function getFallbackPage(string $statusType): string
    {
        $messages = self::getMessages();

        if (!isset($messages[$statusType])) {
            $statusType = 'failed';
        }

        $message = $messages[$statusType];

        return self::generateHtml(
            $message['title'],
            $message['description'],
            $message['color'],
            $message['icon']
        );
    }

    /**
     * Get all message configurations
     */
    private static function getMessages(): array
    {
        return [
            'approved' => [
                'title' => BackendStrings::get('fallback_booking_confirmed_title'),
                'description' => BackendStrings::get('fallback_booking_confirmed_desc'),
                'color' => '#28a745',
                'icon' => 'check'
            ],
            'processed' => [
                'title' => BackendStrings::get('fallback_booking_processed_title'),
                'description' => BackendStrings::get('fallback_booking_processed_desc'),
                'color' => '#ffc107',
                'icon' => 'warning'
            ],
            'approved_with_issues' => [
                'title' => BackendStrings::get('fallback_booking_approved_issues_title'),
                'description' => BackendStrings::get('fallback_booking_approved_issues_desc'),
                'color' => '#ff9800',
                'icon' => 'warning'
            ],
            'failed' => [
                'title' => BackendStrings::get('fallback_booking_failed_title'),
                'description' => BackendStrings::get('fallback_booking_failed_desc'),
                'color' => '#dc3545',
                'icon' => 'error'
            ],
            'canceled' => [
                'title' => BackendStrings::get('fallback_booking_canceled_title'),
                'description' => BackendStrings::get('fallback_booking_canceled_desc'),
                'color' => '#6c757d',
                'icon' => 'info'
            ],
            'cancel_error' => [
                'title' => BackendStrings::get('fallback_cancellation_failed_title'),
                'description' => BackendStrings::get('fallback_cancellation_failed_desc'),
                'color' => '#dc3545',
                'icon' => 'error'
            ],
            'rejected' => [
                'title' => BackendStrings::get('fallback_booking_rejected_title'),
                'description' => BackendStrings::get('fallback_booking_rejected_desc'),
                'color' => '#6c757d',
                'icon' => 'info'
            ]
        ];
    }

    /**
     * Generate HTML page
     */
    private static function generateHtml(string $title, string $description, string $color, string $iconType): string
    {
        $siteUrl = home_url();
        $siteName = get_bloginfo('name');
        $returnHomeText = BackendStrings::get('fallback_return_to_home');

        // Get the appropriate SVG icon based on type
        $iconSvg = self::getIconSvg($iconType);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - {$siteName}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
        }
        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: {$color};
            opacity: 0.9;
        }
        .icon svg {
            width: 45px;
            height: 45px;
            fill: white;
        }
        h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background-color: {$color};
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 24px;
            }
            p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            {$iconSvg}
        </div>
        <h1>{$title}</h1>
        <p>{$description}</p>
        <a href="{$siteUrl}" class="btn">{$returnHomeText}</a>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get SVG icon based on type
     */
    private static function getIconSvg(string $iconType): string
    {
        $checkIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' .
            '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 ' .
            '1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';

        $errorIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' .
            '<path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 ' .
            '12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>';

        $warningIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' .
            '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>';

        $infoIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' .
            '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>';

        $icons = [
            'check' => $checkIcon,
            'error' => $errorIcon,
            'warning' => $warningIcon,
            'info' => $infoIcon
        ];

        return $icons[$iconType] ?? $icons['info'];
    }

    /**
     * Get fallback page HTML
     */
    public static function getFallbackHtml(string $statusType): string
    {
        return self::getFallbackPage($statusType);
    }
}
