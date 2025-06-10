<?php

namespace NotificationChannels\WhatsApp\Test\Component;

use NotificationChannels\WhatsApp\Component\Currency;
use NotificationChannels\WhatsApp\Component\DateTime;
use NotificationChannels\WhatsApp\Component\Document;
use NotificationChannels\WhatsApp\Component\Image;
use NotificationChannels\WhatsApp\Component\QuickReplyButton;
use NotificationChannels\WhatsApp\Component\Text;
use NotificationChannels\WhatsApp\Component\Video;
use PHPUnit\Framework\TestCase;

final class ParameterNameTest extends TestCase
{
    /** @test */
    public function text_component_can_have_parameter_name()
    {
        $component = (new Text('Hello World'))->parameterName('greeting');
        
        $expected = [
            'type' => 'text',
            'text' => 'Hello World',
            'parameter_name' => 'greeting',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function currency_component_can_have_parameter_name()
    {
        $component = (new Currency(10.25, 'USD'))->parameterName('amount');
        
        $expected = [
            'type' => 'currency',
            'currency' => [
                'code' => 'USD',
                'amount_1000' => 10250,
            ],
            'parameter_name' => 'amount',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function date_time_component_can_have_parameter_name()
    {
        $dateTime = new \DateTimeImmutable('2023-12-25 15:30:00');
        $component = (new DateTime($dateTime))->parameterName('delivery_date');
        
        $expected = [
            'type' => 'date_time',
            'date_time' => [
                'fallback_value' => '2023-12-25 15:30:00',
            ],
            'parameter_name' => 'delivery_date',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function image_component_can_have_parameter_name()
    {
        $component = (new Image('https://example.com/image.jpg'))->parameterName('product_image');
        
        $expected = [
            'type' => 'image',
            'image' => [
                'link' => 'https://example.com/image.jpg',
            ],
            'parameter_name' => 'product_image',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function video_component_can_have_parameter_name()
    {
        $component = (new Video('https://example.com/video.mp4'))->parameterName('tutorial_video');
        
        $expected = [
            'type' => 'video',
            'video' => [
                'link' => 'https://example.com/video.mp4',
            ],
            'parameter_name' => 'tutorial_video',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function document_component_can_have_parameter_name()
    {
        $component = (new Document('https://example.com/doc.pdf'))->parameterName('invoice_doc');
        
        $expected = [
            'type' => 'document',
            'document' => [
                'link' => 'https://example.com/doc.pdf',
                'filename' => 'document',
            ],
            'parameter_name' => 'invoice_doc',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function quick_reply_button_can_have_parameter_name()
    {
        $component = (new QuickReplyButton(['Yes', 'No']))->parameterName('confirmation');
        
        $expected = [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => 0,
            'parameters' => [
                [
                    'type' => 'payload',
                    'payload' => 'Yes',
                ],
                [
                    'type' => 'payload',
                    'payload' => 'No',
                ],
            ],
            'parameter_name' => 'confirmation',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function components_without_parameter_name_work_as_before()
    {
        $component = new Text('Hello World');
        
        $expected = [
            'type' => 'text',
            'text' => 'Hello World',
        ];

        $this->assertEquals($expected, $component->toArray());
    }

    /** @test */
    public function parameter_name_method_returns_component_for_chaining()
    {
        $component = new Text('Hello World');
        $result = $component->parameterName('greeting');

        $this->assertSame($component, $result);
    }
}
