<?php

namespace NotificationChannels\WhatsApp\Component;

class DateTime extends Component
{
    protected \DateTimeImmutable $dateTime;

    protected string $format;

    public function __construct(\DateTimeImmutable $dateTime, string $format = 'Y-m-d H:i:s')
    {
        $this->dateTime = $dateTime;
        $this->format = $format;
    }

    public function toArray(): array
    {
        $baseArray = [
            'type' => 'date_time',
            'date_time' => [
                'fallback_value' => $this->dateTime->format($this->format),
            ],
        ];

        return $this->buildParameterArray($baseArray);
    }
}
