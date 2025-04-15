<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The subscription instance.
     *
     * @var \App\Models\Subscription
     */
    protected $subscription;

    /**
     * The notification type.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Subscription $subscription
     * @param string $type
     */
    public function __construct(Subscription $subscription, string $type)
    {
        $this->subscription = $subscription;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('IndexCom API Subscription Update');

        switch ($this->type) {
            case 'created':
                $message->greeting('Thank you for subscribing!')
                    ->line('Your subscription to the ' . $this->subscription->plan->name . ' plan has been activated.')
                    ->line('You now have access to:')
                    ->line('- ' . $this->subscription->plan->request_limit . ' daily API requests')
                    ->line('- Plan starts: ' . $this->subscription->starts_at->format('F j, Y'))
                    ->action('View API Documentation', url('/api-docs'));

                if ($this->subscription->plan->has_extended_data) {
                    $message->line('- Extended data access is included with your plan');
                }
                break;

            case 'renewed':
                $message->greeting('Your subscription has been renewed!')
                    ->line('Your ' . $this->subscription->plan->name . ' plan has been renewed successfully.')
                    ->line('You will continue to have access to:')
                    ->line('- ' . $this->subscription->plan->request_limit . ' daily API requests')
                    ->action('View API Usage', url('/dashboard/api-usage'));
                break;

            case 'canceled':
                $message->greeting('Your subscription has been canceled')
                    ->line('Your ' . $this->subscription->plan->name . ' plan has been canceled.')
                    ->line('You will have access until: ' . $this->subscription->ends_at->format('F j, Y'))
                    ->line('After that date, your API access will be limited to the free tier.')
                    ->action('Resubscribe', url('/dashboard/subscriptions'));
                break;

            case 'expiring_soon':
                $message->greeting('Your subscription is expiring soon')
                    ->line('Your ' . $this->subscription->plan->name . ' plan will expire on ' . $this->subscription->ends_at->format('F j, Y'))
                    ->line('To maintain uninterrupted access to the API, please renew your subscription.')
                    ->action('Renew Subscription', url('/dashboard/subscriptions'));
                break;

            case 'limit_warning':
                $message->greeting('API Usage Limit Warning')
                    ->line('You have used ' . $this->subscription->api_requests_today . ' of your ' . $this->subscription->plan->request_limit . ' daily API requests.')
                    ->line('You are approaching your daily limit. Consider upgrading your plan for increased access.')
                    ->action('Upgrade Plan', url('/dashboard/subscriptions/upgrade'));
                break;

            default:
                $message->greeting('Subscription Update')
                    ->line('There has been an update to your IndexCom API subscription.')
                    ->action('View Details', url('/dashboard/subscriptions'));
                break;
        }

        return $message->line('Thank you for using IndexCom API!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_id' => $this->subscription->plan_id,
            'plan_name' => $this->subscription->plan->name,
            'type' => $this->type,
            'message' => $this->getNotificationMessage(),
        ];
    }

    /**
     * Get the notification message based on type.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        switch ($this->type) {
            case 'created':
                return 'Your subscription to the ' . $this->subscription->plan->name . ' plan has been activated.';
            case 'renewed':
                return 'Your ' . $this->subscription->plan->name . ' plan has been renewed successfully.';
            case 'canceled':
                return 'Your ' . $this->subscription->plan->name . ' plan has been canceled.';
            case 'expiring_soon':
                return 'Your ' . $this->subscription->plan->name . ' plan will expire on ' . $this->subscription->ends_at->format('F j, Y');
            case 'limit_warning':
                return 'You have used ' . $this->subscription->api_requests_today . ' of your ' . $this->subscription->plan->request_limit . ' daily API requests.';
            default:
                return 'There has been an update to your IndexCom API subscription.';
        }
    }
}
