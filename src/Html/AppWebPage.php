<?php

namespace Html;

use Html\WebPage;

class AppWebPage extends WebPage
{
    private string $menu = "";

    public function appendButtonToMenu(string $url, string $nom): void
    {
        $this->menu .= "<button onclick=\"window.location.href = '$url';\">$nom</button>\n";
    }

    public function __construct(string $title = "")
    {
        parent::__construct($title);
        parent::appendCssUrl("css/style1.css");
    }
    public function toHtml(): string
    {
        return <<<HTML
        <!doctype html>
        <html lang = "fr">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title> {$this->getTitle()} </title>
            {$this->getHead()}
        </head>
        <body>
        <div class="header">
            <h1>{$this->getTitle()}</h1>
        </div>
        <div class = "menu">
            <button onclick="window.location.href = 'http://localhost:8000/index.php';"> Index </button>
            {$this->menu}
        </div>
        <div class = "content" >
            {$this->getBody()}
        </div>
        <div class = "footer">
            <p> {$this->getLastModification()}</p>
        </div>
        </body>
        </html>
        HTML;
    }
}
