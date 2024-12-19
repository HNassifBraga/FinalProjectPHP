<?php
session_start(); 


$tempoLimite = 1800; 


if (isset($_SESSION['ultima_atividade'])) //ends session after 30 min of inactivity
{
    $tempoInativo = time() - $_SESSION['ultima_atividade'];


    if ($tempoInativo > $tempoLimite) {
        session_unset();
        session_destroy(); 
        header("Location: login.html");
        exit();
    }
}


$_SESSION['ultima_atividade'] = time();


if (!isset($_SESSION['username'])) //if the 'username' isn't set, redirect to login page
{
    header("Location: login.html");
    exit();
}

if (isset($_SESSION['username']) && !isset($_SESSION['session_regenerated']))//end session id as soon as user logout
{
    session_regenerate_id(true); 
    $_SESSION['session_regenerated'] = true; 
}
?>
