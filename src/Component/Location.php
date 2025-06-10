<?php

namespace NotificationChannels\WhatsApp\Component;

class Location extends Component
{
    protected float $latitude;
    protected float $longitude;
    protected ?string $name;
    protected ?string $address;

    public function __construct(float $latitude, float $longitude, ?string $name = null, ?string $address = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->name = $name;
        $this->address = $address;
    }

    public function toArray(): array
    {
        $location = [
            'latitude' => (string) $this->latitude,
            'longitude' => (string) $this->longitude,
        ];

        if ($this->name !== null) {
            $location['name'] = $this->name;
        }

        if ($this->address !== null) {
            $location['address'] = $this->address;
        }

        $baseArray = [
            'type' => 'location',
            'location' => $location,
        ];

        return $this->buildParameterArray($baseArray);
    }
}
