<?php

class HomeView
{
    public static function render()
    {
        echo <<<HTML
            <h1>Iskolai nyilvántartó rendszer</h1>
            <p>Üdvözöljük! Kérjük, válasszon a fenti menüből.</p>
        HTML;
    }
}
