<?php

namespace App\Data;

use Illuminate\Support\Carbon;
use ReflectionClass;
use ReflectionException;
use Spatie\EventSourcing\Attributes\EventSerializer as EventSerializerAttribute;
use Spatie\EventSourcing\EventSerializers\EventSerializer;
use Spatie\EventSourcing\StoredEvents\Exceptions\InvalidStoredEvent;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Spatie\EventSourcing\StoredEvents\StoredEvent as BaseStoredEvent;

class V2StoredEvent extends BaseStoredEvent
{
    public function event(): ShouldBeStored
    {
        try {
            $reflectionClass = new ReflectionClass($this->event_class);
        } catch (ReflectionException $exception) {
            throw new InvalidStoredEvent($exception->getMessage());
        }

        if ($serializerAttribute = $reflectionClass->getAttributes(EventSerializerAttribute::class)[0] ?? null) {
            $serializerClass = ($serializerAttribute->newInstance())->serializerClass;
        } else {
            $serializerClass = EventSerializer::class;
        }

        $event = app($serializerClass)->deserialize(
            $this->event_class,
            $this->event_properties,
            $this->meta_data['event_version'] ?? 1
        );

        // Here we inject the new timestamp added at the v2
        if (property_exists($event, 'timestamp') && is_null($event->timestamp)) {
            $event->timestamp = Carbon::createFromTimestamp($this->created_at);
        }

        return $event;
    }
}
