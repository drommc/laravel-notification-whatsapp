<?php

namespace NotificationChannels\WhatsApp\Component;

abstract class Component
{
    protected ?string $parameterName = null;

    public function parameterName(string $name): self
    {
        $this->parameterName = $name;

        return $this;
    }

    protected function buildParameterArray(array $baseArray): array
    {
        if ($this->parameterName !== null) {
            $baseArray['parameter_name'] = $this->parameterName;
        }

        return $baseArray;
    }

    abstract public function toArray(): array;
}
