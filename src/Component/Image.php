<?php

namespace NotificationChannels\WhatsApp\Component;

class Image extends Component
{
    /**
     * Link to the image; e.g. https://URL.
     */
    protected string $link;

    public function __construct(string $link)
    {
        $this->link = $link;
    }

    public function toArray(): array
    {
        $baseArray = [
            'type' => 'image',
            'image' => [
                'link' => $this->link,
            ],
        ];

        return $this->buildParameterArray($baseArray);
    }
}
