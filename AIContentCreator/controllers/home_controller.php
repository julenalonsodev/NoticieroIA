<?php
session_start();

// Si no hay usuario logueado, volvemos a start
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=start");
    exit;
}

$usuario = $_SESSION['usuario'];

// Mostramos el home del usuario
require "views/home_view.phtml";

// ------------------------------------------------------------------------
