<?php

namespace App\Repository;

use App\Entity\Note;
use App\Utils\GeneralUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function searchQuery(?array $search)
    {
        $qb = $this->createQueryBuilder("note")
            ->leftJoin("note.tags", "tag")
            ->addSelect("tag");

        foreach (["title", "category", "sequence", "content"] as $field) {
            if (!GeneralUtils::emptyKeyValue($field, $search)) {
                $value = '%' . $search[$field] . '%';
                $qb->andWhere("note.$field LIKE :$field")
                    ->setParameter($field, $value);
            }
        }

        if (!GeneralUtils::emptyKeyValue("tag", $search)) {
            $qb->andWhere("tag.name = :tag")
                ->setParameter("tag", $search["tag"]);
        }

        return $qb;
    }
}
