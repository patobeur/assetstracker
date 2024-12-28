<?php
namespace app\core;

class FrontConstructor
{
    private $defaultPage;

    public function __construct() {
        $this->defaultPage = file_get_contents('../app/views/front.php');
    }

    public function addContent($stack=[])
    {
        if (count($stack)>0){
            $contents = '';
            for ($i=0; $i < count($stack) ; $i++) {
                $contents.="#CONTENT".$i."#";
            }
            $this->defaultPage = str_replace("#CONTENT#",$contents,$this->defaultPage);

            for ($i=0; $i < count($stack) ; $i++) {
                $this->defaultPage = str_replace("#CONTENT".$i."#",$stack[$i]['CONTENT'],$this->defaultPage);
            }

            $this->defaultPage = str_replace("#TITLE#",$stack[0]['TITLE'],$this->defaultPage);
        }
        else {
            $this->defaultPage = str_replace("#CONTENT#",'vide',$this->defaultPage);
            $this->defaultPage = str_replace("#TITLE#",'default',$this->defaultPage);
        }
    }

    public function displayPage($url,$stack=[])
    {
        $this->addContent($stack);
        $this->addNavigation($url);
        print_r($this->defaultPage);
    }

    public function addNavigation($url)
    {
        // menu vue
        $navigationVue = '<nav><ul>#CONTENTS#</ul></nav>';
        // items
        $items = [
            'accueil' => '<li><a href="/">Accueil</a></li>',
            'login'   => ((!isset($_SESSION['user']) && ($url != 'login' && $url != 'profile')) ? '<li><a href="/login">login</a></li>' : ''),
            'profile' => ((isset($_SESSION['user']) && $url != 'profile') ? '<li><a href="profile.php">Profile</a></li>' : ''),
            'logout'  => ((isset($_SESSION['user'])) ? '<li><a href="/logout">Déconnexion</a></li>' : ''),
            'pseudo'  => (isset($_SESSION['user'])) ? '<li><a>['.$_SESSION['user'].']<a></li>' : ''
        ];
    
        $menuItems = '';
    
        foreach ($items as $key => $value) {
            $menuItems .= $value;
        }
        
        $navigationVue = str_replace("#CONTENTS#",$menuItems,$navigationVue);
        $this->defaultPage = str_replace("#NAVIGATION#",$navigationVue,$this->defaultPage);
    }














    


}
