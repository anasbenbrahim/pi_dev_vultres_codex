<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Enum\EventType; // Added use statement for EventType

use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create sample events
        for ($i = 1; $i <= 5; $i++) {
            $event = new Event();
            $event->setNom('Sample Event ' . $i);
            $event->setDescr('Description for Sample Event ' . $i);
            $event->setDate(new \DateTime('+1 day')); // Sample date
            $event->setType(EventType::SIMPLE); // Using SIMPLE as a valid EventType


            $event->setLatitude(37.7749 + $i * 0.01); // Sample latitude
            $event->setPhoto('default_photo.jpg'); // Setting a default photo

            $event->setLongitude(-122.4194 + $i * 0.01); // Sample longitude
            $manager->persist($event);
        }

        $manager->flush();
    }
}
