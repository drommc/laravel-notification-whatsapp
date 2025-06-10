<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WhatsApp\Component\Currency;
use NotificationChannels\WhatsApp\Component\DateTime;
use NotificationChannels\WhatsApp\Component\Image;
use NotificationChannels\WhatsApp\Component\QuickReplyButton;
use NotificationChannels\WhatsApp\Component\Text;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;

/**
 * Example notification demonstrating the use of parameter_name functionality
 * for WhatsApp template messages according to Meta WhatsApp Cloud API requirements.
 * 
 * @see https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-message-templates
 */
class OrderConfirmationNotification extends Notification
{
    private $order;
    private $customer;
    private $deliveryDate;

    public function __construct($order, $customer, \DateTimeImmutable $deliveryDate)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    /**
     * Build the WhatsApp template message with named parameters.
     * 
     * This example shows how to use parameter_name for better template maintenance
     * and compliance with Meta WhatsApp API requirements.
     */
    public function toWhatsapp($notifiable): WhatsAppTemplate
    {
        return WhatsAppTemplate::create()
            ->name('order_confirmation') // Your configured template name
            ->language('en_US')
            
            // Header with named parameter for the product image
            ->header(
                (new Image($this->order['product_image']))
                    ->parameterName('product_image')
            )
            
            // Body components with named parameters
            ->body(
                (new Text($this->customer['name']))
                    ->parameterName('customer_name')
            )
            ->body(
                (new Text($this->order['product_name']))
                    ->parameterName('product_name')
            )
            ->body(
                (new Currency($this->order['total'], $this->order['currency']))
                    ->parameterName('order_total')
            )
            ->body(
                (new DateTime($this->deliveryDate, 'F j, Y'))
                    ->parameterName('delivery_date')
            )
            
            // Buttons with named parameters
            ->buttons(
                (new QuickReplyButton(['Track Order', 'Contact Support']))
                    ->parameterName('action_buttons')
            )
            
            ->to($notifiable->phone_number);
    }
}

/**
 * Example of mixed usage - combining named and positional parameters
 */
class WelcomeNotification extends Notification
{
    private $customerName;
    private $welcomeBonus;

    public function __construct(string $customerName, float $welcomeBonus)
    {
        $this->customerName = $customerName;
        $this->welcomeBonus = $welcomeBonus;
    }

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsapp($notifiable): WhatsAppTemplate
    {
        return WhatsAppTemplate::create()
            ->name('welcome_template')
            ->language('en_US')
            
            // Mix of named and positional parameters
            ->body(new Text('Welcome to our service!')) // Positional
            ->body(
                (new Text($this->customerName))
                    ->parameterName('customer_name') // Named
            )
            ->body(new Text('You have received a bonus:')) // Positional
            ->body(
                (new Currency($this->welcomeBonus, 'USD'))
                    ->parameterName('bonus_amount') // Named
            )
            
            ->to($notifiable->phone_number);
    }
}
