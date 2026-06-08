<?php
require_once "Auth.php";

class LayoutView
{
    public static function head($title = "Iskolai nyilvántartó rendszer")
    {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="hu">
        <head>
            <meta charset="UTF-8">
            <title>{$title}</title>
        </head>
        <body>
        HTML;
    }

    public static function menu()
    {
        Auth::startSession();

        
        if (!Auth::isLoggedIn()) {
            echo <<<HTML
            <nav>
                <a href="index.php?view=login">Bejelentkezés</a> |
                <a href="index.php?view=register">Regisztráció</a>
            </nav>
            <hr>
            HTML;
            return;
        }

        $name = htmlspecialchars(Auth::currentName() ?? Auth::currentEmail());
        $role = Auth::role();
        $roleLabel = match($role) {
            'admin'  => 'Rendszergazda',
            'editor' => 'Szerkesztő',
            'user'   => 'Felhasználó',
            default  => $role,
        };

        
        $nav = '<a href="index.php?view=home">Kezdőlap</a>';

     
        $nav .= ' | <a href="index.php?view=subjects">Tantárgyak</a>';
        $nav .= ' | <a href="index.php?view=classes">Osztályok</a>';
        $nav .= ' | <a href="index.php?view=students">Tanulók</a>';
        $nav .= ' | <a href="index.php?view=marks">Jegyek</a>';
        $nav .= ' | <a href="index.php?view=lists">Listák</a>';

        
        if (Auth::isAdmin()) {
            $nav .= ' | <a href="index.php?view=maintenance">Karbantartás</a>';
            $nav .= ' | <a href="index.php?view=users">Felhasználók</a>';
        }

  
        $nav .= <<<HTML
         | <span style="position:relative;display:inline-block">
            <a href="index.php?view=profile" id="user-menu-toggle" title="Profilom és kilépés">
                <strong>$name</strong> ($roleLabel)
            </a>
            <span id="user-dropdown" style="display:none;position:absolute;right:0;top:100%;background:#fff;border:1px solid #ccc;padding:6px 10px;white-space:nowrap;z-index:100;min-width:150px">
                <a href="index.php?view=profile" style="display:block;padding:3px 0">Profil szerkesztése</a>
                <a href="index.php?view=logout" style="display:block;padding:3px 0;color:red">Kijelentkezés</a>
            </span>
        </span>
        HTML;

        echo <<<HTML
        <nav>$nav</nav>
        <script>
        (function(){
            var toggle = document.getElementById('user-menu-toggle');
            var dropdown = document.getElementById('user-dropdown');
            if (!toggle || !dropdown) return;
            toggle.addEventListener('click', function(e){
                e.preventDefault();
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', function(e){
                if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        })();
        </script>
        <hr>
        HTML;
    }

    public static function footer()
    {
        echo <<<HTML
        <hr>
        <footer>
            <p>Ács Vanda - Péter Botond</p>
        </footer>
        </body>
        </html>
        HTML;
    }
}
