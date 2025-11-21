<?php
// controllers/noticias_controller.php

require_once "db/db.php";

$conexion = Database::conectar();

$sql = "SELECT 
            id,
            titulo,
            descripcion,
            imagen,
            noticia_revisada,
            imagen_revisada,
            publicado,
            fecha_publicacion,

            -- Map de estados desde la BBDD
            CASE noticia_revisada
                WHEN 0 THEN 'Pendiente'
                WHEN 1 THEN 'Revisada'
                ELSE ''
            END AS noticia_estado,

            CASE imagen_revisada
                WHEN 0 THEN 'Pendiente'
                WHEN 1 THEN 'Aprobada'
                ELSE ''
            END AS imagen_estado,

            CASE publicado
                WHEN 0 THEN 'Borrador'
                WHEN 1 THEN 'Publicado'
                ELSE ''
            END AS publicado_estado

        FROM noticias
        ORDER BY id DESC";

$result = $conexion->query($sql);

// Cargamos la vista
require_once "views/noticias_view.phtml";

$conexion->close();
    //  <!-- HOLA RUBEN -->
