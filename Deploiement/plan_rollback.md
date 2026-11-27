# Plan de retour arrière (rollback)

Avant de déployer : sauvegarder le dossier `app/` actuel et exporter la base de données actuelle.

En cas de problème après la mise en ligne :

1. Arrêter la nouvelle version.
2. Remettre l'ancienne version du dossier `app/`.
3. Restaurer la base de données si elle a été modifiée.
4. Vérifier que le site fonctionne à nouveau.
