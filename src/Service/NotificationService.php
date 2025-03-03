<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;

class NotificationService
{
    private $hub;
    private $logger;
    private $mercureEnabled;

    public function __construct(HubInterface $hub, LoggerInterface $logger = null)
    {
        $this->hub = $hub;
        $this->logger = $logger;
        // Default to enabled, but allow for graceful failure
        $this->mercureEnabled = true;
    }

    /**
     * Publish a notification to Mercure
     * 
     * @param string $type The notification type (e.g., 'new_object', 'new_exchange')
     * @param string $message The notification message
     * @param array $data Additional data for the notification
     * @return bool True if the notification was sent successfully, false otherwise
     */
    public function notify(string $type, string $message, array $data = []): bool
    {
        // If Mercure has been disabled due to previous errors, don't attempt to send
        if (!$this->mercureEnabled) {
            if ($this->logger) {
                $this->logger->info('Notification not sent: Mercure is disabled', [
                    'type' => $type,
                    'message' => $message
                ]);
            }
            return false;
        }

        $content = [
            'type' => $type,
            'message' => $message,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            'data' => $data
        ];

        try {
            // Use a simpler topic
            $update = new Update(
                'https://swapcircle.com/notifications',
                json_encode($content)
            );

            // Publish the notification to Mercure
            $this->hub->publish($update);
            
            return true;
        } catch (TransportException $e) {
            // Handle connection issues
            if ($this->logger) {
                $this->logger->error('Failed to send Mercure notification: ' . $e->getMessage(), [
                    'exception' => $e,
                    'type' => $type,
                    'message' => $message
                ]);
            }
            
            // Disable Mercure for the remainder of the request to prevent further errors
            $this->mercureEnabled = false;
            
            return false;
        } catch (\Exception $e) {
            // Handle any other exceptions
            if ($this->logger) {
                $this->logger->error('Error sending notification: ' . $e->getMessage(), [
                    'exception' => $e,
                    'type' => $type,
                    'message' => $message
                ]);
            }
            
            return false;
        }
    }
}
