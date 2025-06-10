<?php

namespace NotificationChannels\WhatsApp\Test;

use NotificationChannels\WhatsApp\Component\Currency;
use NotificationChannels\WhatsApp\Component\DateTime;
use NotificationChannels\WhatsApp\Component\Image;
use NotificationChannels\WhatsApp\Component\QuickReplyButton;
use NotificationChannels\WhatsApp\Component\Text;
use NotificationChannels\WhatsApp\WhatsAppTemplate;
use PHPUnit\Framework\TestCase;

final class ParameterNameIntegrationTest extends TestCase
{
    /** @test */
    public function template_with_named_parameters_works_correctly()
    {
        $deliveryDate = new \DateTimeImmutable('2023-12-25 15:30:00');
        
        $template = WhatsAppTemplate::create('1234567890', 'order_confirmation', 'en_US')
            ->header(
                (new Image('https://example.com/product.jpg'))->parameterName('product_image')
            )
            ->body(
                (new Text('John Doe'))->parameterName('customer_name')
            )
            ->body(
                (new Currency(99.99, 'USD'))->parameterName('order_total')
            )
            ->body(
                (new DateTime($deliveryDate))->parameterName('delivery_date')
            )
            ->buttons(
                (new QuickReplyButton(['Track Order', 'Contact Support']))->parameterName('action_buttons')
            );

        // Verify the components are structured correctly
        $components = $template->components();
        
        // Check header with parameter name
        $headerComponents = $components->header();
        $this->assertCount(1, $headerComponents);
        $this->assertEquals('product_image', $headerComponents[0]['parameter_name']);
        $this->assertEquals('image', $headerComponents[0]['type']);

        // Check body components with parameter names
        $bodyComponents = $components->body();
        $this->assertCount(3, $bodyComponents);
        
        $this->assertEquals('customer_name', $bodyComponents[0]['parameter_name']);
        $this->assertEquals('text', $bodyComponents[0]['type']);
        $this->assertEquals('John Doe', $bodyComponents[0]['text']);
        
        $this->assertEquals('order_total', $bodyComponents[1]['parameter_name']);
        $this->assertEquals('currency', $bodyComponents[1]['type']);
        $this->assertEquals('USD', $bodyComponents[1]['currency']['code']);
        $this->assertEquals(99990, $bodyComponents[1]['currency']['amount_1000']);
        
        $this->assertEquals('delivery_date', $bodyComponents[2]['parameter_name']);
        $this->assertEquals('date_time', $bodyComponents[2]['type']);
        $this->assertEquals('2023-12-25 15:30:00', $bodyComponents[2]['date_time']['fallback_value']);

        // Check buttons with parameter name
        $buttonComponents = $components->buttons();
        $this->assertCount(1, $buttonComponents);
        $this->assertEquals('action_buttons', $buttonComponents[0]['parameter_name']);
        $this->assertEquals('button', $buttonComponents[0]['type']);
        $this->assertEquals('quick_reply', $buttonComponents[0]['sub_type']);
    }

    /** @test */
    public function template_mixing_named_and_positional_parameters()
    {
        $template = WhatsAppTemplate::create('1234567890', 'mixed_template', 'en_US')
            ->body(new Text('Welcome'))  // No parameter name (positional)
            ->body((new Text('John Doe'))->parameterName('customer_name'))  // Named parameter
            ->body(new Currency(50.00, 'EUR'));  // No parameter name (positional)

        $bodyComponents = $template->components()->body();
        $this->assertCount(3, $bodyComponents);
        
        // First component - positional (no parameter_name)
        $this->assertArrayNotHasKey('parameter_name', $bodyComponents[0]);
        $this->assertEquals('Welcome', $bodyComponents[0]['text']);
        
        // Second component - named
        $this->assertEquals('customer_name', $bodyComponents[1]['parameter_name']);
        $this->assertEquals('John Doe', $bodyComponents[1]['text']);
        
        // Third component - positional (no parameter_name)
        $this->assertArrayNotHasKey('parameter_name', $bodyComponents[2]);
        $this->assertEquals('EUR', $bodyComponents[2]['currency']['code']);
    }
}
