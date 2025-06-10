<?php

namespace NotificationChannels\WhatsApp\Component;

class Video extends Component
{
    /**
     * Link to the video; e.g. https://URL.
     */
    protected string $link;

    public function __construct(string $link)
    {
        $this->link = $link;
    }

    public function toArray(): array
    {
        $baseArray = [
            'type' => 'video',
            'video' => [
                'link' => $this->link,
            ],
        ];

        return $this->buildParameterArray($baseArray);
    }
}
