<?php

declare(strict_types=1);

namespace Entity;

use Database\MyPdo;
use Entity\Collection\SeasonCollection;
use Entity\Exception\EntityNotFoundException;
use PDO;

class Tvshow
{
    private ?int $id;
    private string $name;
    private string $originalName;
    private string $homepage;
    private string $overview;
    private ?int $posterId ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getHomepage(): string
    {
        return $this->homepage;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function getPosterId(): ?int
    {
        return $this->posterId;
    }
    private function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setOriginalName(string $originalName): void
    {
        $this->originalName = $originalName;
    }
    public function setHomepage(string $homepage): void
    {
        $this->homepage = $homepage;
    }
    public function setOverview(string $overview): void
    {
        $this->overview = $overview;
    }



    public static function findById(int $id): Tvshow
    {
        $stmt = MyPdo::getInstance()->prepare(<<<SQL
        SELECT *
        FROM tvshow
        WHERE id = :tvShowId
SQL);
        $stmt->execute([':tvShowId' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Tvshow::class);
        if (($tvShow = $stmt->fetch()) === false) {
            throw new EntityNotFoundException();
        } else {
            return $tvShow;
        }
    }

    public function getPoster(): string
    {
        return "http://localhost:8000/poster.php?posterId={$this->posterId}";
    }

    public function getSeasons(): array
    {
        return SeasonCollection::findByTvShowId($this->id);
    }

    public function delete(): Tvshow
    {
        $del = MyPdo::getInstance()->prepare(<<<SQL
    DELETE FROM tvshow
    WHERE id = :showId

SQL);
        $del->execute([':showId' => $this->id]);
        $this->setId(null);
        return $this;
    }

    protected function update(): Tvshow
    {
        $update = MyPdo::getInstance()->prepare(<<<SQL
    UPDATE tvshow
    SET name = :showName,
        originalName = :showOGName,
        homepage = :showHomepage,
        overview = :showOverview
    WHERE id = :showId

SQL);
        $update->execute([':showName' => $this->name,
            ':showOGName' => $this->originalName,
            ':showHomepage' => $this->homepage,
            ':showOverview' => $this->overview,
            ':showId' => $this->id]);
        return $this;
    }

    public static function create(string $name, string $ogName, string $homepage, string $overview, ?int $id = null): Tvshow
    {
        $show = new Tvshow();
        $show->setName($name);
        $show->setOriginalName($ogName);
        $show->setHomepage($homepage);
        $show->setOverview($overview);
        $show->setId($id);
        $show->posterId = null;
        return $show;

    }

    private function __construct()
    {

    }

    protected function insert(): Tvshow
    {
        $insert = MyPdo::getInstance()->prepare(<<<SQL
        INSERT INTO tvshow (name,originalName,homepage,overview)
        VALUES (:name,:ogName,:homepage,:overview)
SQL);
        $insert->execute(['name' => $this->name,
            'ogName' => $this->originalName,
            'homepage' => $this->homepage,
            'overview' => $this->overview]);
        $this->setId((int) MyPdo::getInstance()->lastInsertId());
        return $this;
    }

    public function save(): Tvshow
    {
        if ($this->id === null) {
            $this->insert();
        } else {
            $this->update();
        }
        return $this;
    }


}
