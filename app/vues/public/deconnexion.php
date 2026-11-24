<?php

// Déconnexion : on vide puis on détruit la session, avant de rediriger.
session_start();

$_SESSION = [];
session_unset();
session_destroy();

header('Location: connexion.php');
exit;
