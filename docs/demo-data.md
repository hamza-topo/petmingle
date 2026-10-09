# Données de démonstration

Après avoir récupéré cette version du projet, depuis la racine :

```sh
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
docker compose exec app php artisan db:seed --class=DemoDataSeeder
```

Si le lien de stockage existe déjà, passez à la commande suivante.
Le seeder est disponible uniquement avec `APP_ENV=local` ou `testing`. Il n'est pas lancé par le seeder par défaut.

Sur une base sans données de démonstration, il ajoute 36 comptes avec un animal et une localisation : 12 à Marrakech, 12 à Casablanca et 12 à Agadir. Les coordonnées sont fictives et proches du centre de chaque ville.

| Compte | Mot de passe |
| --- | --- |
| demo.marrakech@petmingle.test | PetmingleDemo!2026 |
| demo.casablanca@petmingle.test | PetmingleDemo!2026 |
| demo.agadir@petmingle.test | PetmingleDemo!2026 |

Chaque compte principal dispose de trois matches, de trois conversations avec messages lus et non lus, de likes reçus et d'un like envoyé. Connectez-vous avec l'un de ces comptes pour tester ces parcours. Un compte existant peut découvrir les profils proches, mais ne reçoit pas de conversations artificielles.

Les chiens réutilisent une photo de démonstration déjà présente dans le projet, copiée dans le stockage public. Les chats n'ont pas de photo et utilisent le rendu de remplacement de l'interface. Aucun service externe n'est nécessaire.

La commande peut être relancée sans doubler les données. Elle conserve les comptes, profils et messages déjà présents, y compris les modifications apportées aux données de démonstration. Les enregistrements supprimés restent supprimés. Elle ne vide aucune table et ne déclenche pas les observateurs qui envoient des emails ou des événements temps réel. N'utilisez pas `migrate:fresh` pour ajouter ces données.
