# AssetsTracker

    Gestion d'entrées et de sorties materiel avec codebarres.

![Mon Image](./bdd_sheme.png "Shema de la base de données relationelle.")

### httpd.conf
    VirtualHost config (serveur side conf)

        <VirtualHost *:80>
            DocumentRoot "D:/yourRoot/yourRep/public"
            ServerName yourdomaine.local
            <Directory "D:/yourRoot/yourRep/public">
                Order allow,deny
                Allow from all
                Require all granted
            </Directory>
        </VirtualHost>

    Need activation rewrite_module (serveur side conf)

        LoadModule rewrite_module modules/mod_rewrite.so

    replace (serveur side conf)
        
        AllowOverride none

    by

        AllowOverride all



liste à mettre à jour

- [x] la branche est fonctionnelle ?
- [x] pages in et out fonctionnelles ?
- [x] sauvegardes opérationnelles des entrés sorties dans table 'timeline'.
- [x] console des entrées, sorties, et bug en place et opérationnelle.
- [x] insertion d'un champ 'lastpc_id' dans la table 'eleve' pour tracer le dernier pc utilisé. (FK avec 'id' dans la table 'pc')
- [x] insertion d'un champ 'position' dans la table 'pc' pour tracer la derniere opération.
