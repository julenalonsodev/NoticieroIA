<?php
session_start();

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Cargamos la vista home
require "views/home_view.phtml";
