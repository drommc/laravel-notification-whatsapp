<?php

namespace NotificationChannels\WhatsApp\Test\Component;

use NotificationChannels\WhatsApp\Component\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    /** @test */
    public function location_with_basic_coordinates()
    {
        $location = new Location(37.483307, -122.148981);
        
        $expected = [
            'type' => 'location',
            'location' => [
                'latitude' => '37.483307',
                'longitude' => '-122.148981',
            ],
        ];

        $this->assertEquals($expected, $location->toArray());
    }

    /** @test */
    public function location_with_name_and_address()
    {
        $location = new Location(
            37.483307, 
            -122.148981, 
            'Facebook HQ', 
            '1 Hacker Way, Menlo Park, CA 94025'
        );
        
        $expected = [
            'type' => 'location',
            'location' => [
                'latitude' => '37.483307',
                'longitude' => '-122.148981',
                'name' => 'Facebook HQ',
                'address' => '1 Hacker Way, Menlo Park, CA 94025',
            ],
        ];

        $this->assertEquals($expected, $location->toArray());
    }

    /** @test */
    public function location_with_only_name()
    {
        $location = new Location(37.483307, -122.148981, 'Facebook HQ');
        
        $expected = [
            'type' => 'location',
            'location' => [
                'latitude' => '37.483307',
                'longitude' => '-122.148981',
                'name' => 'Facebook HQ',
            ],
        ];

        $this->assertEquals($expected, $location->toArray());
    }

    /** @test */
    public function location_with_parameter_name()
    {
        $location = (new Location(37.483307, -122.148981, 'Delivery Location'))
            ->parameterName('delivery_address');
        
        $expected = [
            'type' => 'location',
            'location' => [
                'latitude' => '37.483307',
                'longitude' => '-122.148981',
                'name' => 'Delivery Location',
            ],
            'parameter_name' => 'delivery_address',
        ];

        $this->assertEquals($expected, $location->toArray());
    }
}
