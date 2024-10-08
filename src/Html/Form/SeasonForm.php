<?php

namespace Html\Form;

use Entity\Season;
use Entity\Tvshow;
use Exception\ParameterException;
use Html\StringEscaper;

class SeasonForm
{
    use StringEscaper;
    private ?Season $season;

    private ?Tvshow $tvshow;

    /**
     * @param Tvshow|null $tvshow
     * @param Season|null $season
     */
    public function __construct(?Tvshow $tvshow = null, ?Season $season = null)
    {
        $this->tvshow = $tvshow;
        $this->season = $season;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function getHtmlForm(string $action): string
    {
        return <<<HTML
<form method="post" action="$action">
    <input name="id" type="hidden" value="{$this->season?->getId()}">
    <label> Nom
        <input type="text" name="name" required value="{$this->escapeString($this->season?->getName())}">
    </label>
        <input type="hidden" name="tvShowId" required value="{$this->tvshow->getId()}"> 
    <label> Numero de saison 
        <input type="number" name="seasonNumber"  pattern="[0-9]" required value="{$this->season?->getSeasonNumber()}">
    </label> 
    <button type="submit">Enregister</button>
</form>
HTML;
    }

    /**
     * @throws ParameterException
     */
    public function setEntityFromQueryString(): void
    {
        $name = '';
        if(!empty($_POST['name'])) {
            $name = $this->stripTagsAndTrim($this->escapeString($_POST['name']));
        } else {
            throw new ParameterException();
        }
        $tvShowId = null;
        if (!empty($_POST['tvShowId']) && ctype_digit($_POST['tvShowId'])) {
            $tvShowId = (int) $_POST['tvShowId'];
        } else {
            throw new ParameterException();
        }
        $seasonNumber = null;
        if (!empty($_POST['seasonNumber']) && ctype_digit($_POST['seasonNumber'])) {
            $seasonNumber = (int) $_POST['seasonNumber'];
        } else {
            throw new ParameterException();
        }
        $id = null;
        if (!empty($_POST['id']) && ctype_digit($_POST['id'])) {
            $id = (int) $_POST['id'];
        }
        $this->season = Season::create($name, $tvShowId, $seasonNumber, $id);
    }
}
