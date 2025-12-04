<?php
// controllers/start_controller.php

session_start();

// Si ya hay usuario logueado, mandamos directo a home
if (isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=home");
    exit;
}

// Si NO hay sesión, mostramos la start view
require __DIR__ . '/../views/start_view.phtml';