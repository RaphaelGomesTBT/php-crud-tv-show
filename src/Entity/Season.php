<?php

namespace Entity;

use Database\MyPdo;
use Entity\Collection\EpisodeCollection;
use Entity\Exception\EntityNotFoundException;
use PDO;

class Season
{
    private ?int $id;

    private int $tvShowId;

    private String $name;
    private int $seasonNumber;
    private ?int $posterId;

    public function setId(?int $id): Season
    {
        $this->id = $id;
        return $this;
    }

    public function setTvShowId(int $tvShowId): Season
    {
        $this->tvShowId = $tvShowId;
        return $this;
    }

    public function setName(string $name): Season
    {
        $this->name = $name;
        return $this;
    }

    public function setSeasonNumber(int $seasonNumber): Season
    {
        $this->seasonNumber = $seasonNumber;
        return $this;
    }

    private function __construct()
    {

    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getTvShowId(): int
    {
        return $this->tvShowId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSeasonNumber(): int
    {
        return $this->seasonNumber;
    }

    public function getPosterId(): ?int
    {
        return $this->posterId;
    }

    public static function findById(int $id): Season
    {
        $req = MyPdo::getInstance()->prepare("SELECT * FROM season WHERE id = :id");
        $req->execute(['id' => $id]);
        $req->setFetchMode(PDO::FETCH_CLASS, Season::class);
        if(($res = $req->fetch()) === false) {
            throw new EntityNotFoundException();
        } else {
            return $res;
        }
    }

    public function getPoster(): string
    {
        return "http://localhost:8000/poster.php?posterId={$this->posterId}";
    }

    public function getEpisodes(): array
    {
        return EpisodeCollection::findBySeasonId($this->id);
    }

    public function update(): self
    {
        $req = MyPdo::getInstance()->prepare(<<<SQL
        UPDATE season 
        Set name = :name
        WHERE id = :id
SQL);
        $req->execute([':name' => $this->name, ':id' => $this->id]);
        return $this;
    }

    public static function create(string $name, int $tvShowId, int $seasonNumber, ?int $id = null): self
    {
        $seas = new Season();
        $seas->setName($name);
        $seas->setTvShowId($tvShowId);
        $seas->setSeasonNumber($seasonNumber);
        $seas->setId($id);
        $seas->posterId = null;
        return $seas;
    }

    public function delete(): Season
    {
        $del = MyPdo::getInstance()->prepare(<<<SQL
        DELETE FROM season
        WHERE id = :Id
        SQL);
        $del->execute([':Id' => $this->id]);
        $this->setId(null);
        return $this;
    }

    protected function insert(): Season
    {
        $insert = MyPdo::getInstance()->prepare(<<<SQL
        INSERT INTO season (tvshowid,name,seasonNumber)
        VALUES (:tvshowid,:name,:seasonNumber)
SQL);
        $insert->execute([':name' => $this->name,
            ':tvshowid' => $this->tvShowId,
            ':seasonNumber' => $this->seasonNumber]);
        $this->setId((int) MyPdo::getInstance()->lastInsertId());
        return $this;
    }

    public function save(): Season
    {
        if ($this->id === null) {
            $this->insert();
        } else {
            $this->update();
        }
        return $this;
    }

}
