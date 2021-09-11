<?php

namespace App\Service;

use App\Entity\Note;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class TagManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function syncTags(Note $note)
    {
        $oldTags = $this->em->getRepository(Tag::class)->findBy(["note" => $note]);
        foreach ($oldTags as $oldTag) {
            $this->em->remove($oldTag);
        }
        foreach ($note->getTags() as $tag) {
            $note->addTag($tag);
            $this->em->persist($tag);
        }
    }
}