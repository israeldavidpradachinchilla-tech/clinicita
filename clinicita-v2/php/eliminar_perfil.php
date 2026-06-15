<?php
require 'conexion.php';
requiereRol('paciente');
$u = usuarioActual();
$conexion->query('DELETE FROM usuarios WHERE id='.(int)$u['id']);
session_destroy();
echo "<script>alert('Perfil eliminado');location.href='../index.html';</script>";
