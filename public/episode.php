<?php

use Entity\Exception\EntityNotFoundException;
use Entity\Season;
use Entity\Tvshow;
use Exception\ParameterException;
use Html\AppWebPage;

try {
    if (!empty($_GET['seasonId']) && ctype_digit($_GET['seasonId'])) {
        $seasonId = $_GET['seasonId'];
    } else {
        throw new ParameterException();
    }
    $html = new AppWebPage();
    $season = Season::findById($seasonId);
    $seriesName = $html->escapeString(Tvshow::findById($season->getTvShowId())->getName());
    $html = new AppWebPage("Série Tv : $seriesName \n {$html->escapeString($season->getName())}");
    $html->appendButtonToMenu("admin/seasonForm.php?tvShowId={$season->getTvShowId()}&seasonId={$season->getId()}", "Modifier");
    $html->appendButtonToMenu("admin/season-delete.php?seasonId={$season->getId()}", "Supprimer");
    $html->appendCss(
        <<<CSS
    .Season,.Episode{
    display: flex;
    flex-flow: row wrap;
    border : 2px solid #456969;
    margin: 4px;
    background-color: #5e9393;  
    }
    .Episode{
    border-radius : 20px;
    padding : 4px;
    }
    .Season{
    padding : 5px;
    justify-content: space-between;   
    flex-wrap:nowrap;
    }
    .Info{
    display: flex;
    flex-direction: column;
    font-size: 20px;
    }
    @media (max-width:400px){
        .Season{
        flex-flow: column nowrap;
        }
    }
    a:link{text-decoration:none;color:black}a:visited{text-decoration:none;color:black}
    CSS
    );
    $html->appendContent(
        <<<HTML
    <div class = "Season">
       <img src = "{$season->getPoster()}" alt="Poster de la saison {$season->getName()}du show Télévisée de $seriesName">
       <div class = "Info">
       <a href = "tvshow.php?tvshowId={$season->getTvShowId()}">{$html->escapeString($season->getName())} </a>
       <article>$seriesName</article>
       </div>
    </div>
    HTML
    );
    foreach($season->getEpisodes() as $episode) {
        $html->appendContent(
            <<<HTML
        <div class="Episode">
            <p> {$html->escapeString($episode->getEpisodeNumber())} - {$html->escapeString($episode->getName())}</p>
        HTML
        );
        if (!empty($episode->getOverview())) {
            $html->appendContent("<p>{$html->escapeString($episode->getOverview())}</p>");
        }
        $html->appendContent("</div>");

    }
    echo $html->toHtml();
} catch(EntityNotFoundException $e) {
    http_response_code(404);
} catch(ParameterException $e) {
    http_response_code(400);
} catch(Exception $e) {
    http_response_code(500);
}
