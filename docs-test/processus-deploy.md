# Processus de déploiement

## Préparation

Avant tout déploiement, le responsable vérifie que la branche principale est verte dans GitLab CI. Les tests unitaires et les tests de non-régression doivent passer. Aucune modification directe n'est autorisée sur le serveur de production.

## Déploiement en production

Le déploiement se lance manuellement depuis le pipeline GitLab, sur la branche `main`. Le job `deploy-prod` exécute les migrations Doctrine, puis redémarre les conteneurs Docker. Une fenêtre de maintenance est annoncée aux clients si la migration dure plus de cinq minutes.

## Retour arrière

En cas d'erreur, le pipeline restaure l'image Docker précédente. Les migrations ne sont pas annulées automatiquement : il faut lancer manuellement la commande `doctrine:migrations:migrate prev` après validation de l'équipe.