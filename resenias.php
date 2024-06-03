<?php
include "./src/db.php";

// Verificar si se proporciona un ID de película
if (isset($_GET['movieID'])) {
  $movieID = $_GET['movieID'];

  // Preparar la consulta SQL para obtener los comentarios de la película
  $sentencia = $conn->prepare("SELECT comentarios.*, userinfo.correo AS correo, userinfo.nombre AS nombre FROM comentarios 
    LEFT JOIN userinfo ON userinfo.id_userinfo = comentarios.id_userinfo
    WHERE id_pelicula = :id_pelicula");

  $sentencia->bindParam(":id_pelicula", $movieID);
  $sentencia->execute();

  // Obtener todos los resultados de la consulta
  $registros = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartelera de Cine</title>
    <link rel="stylesheet" href="./src/styles/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="./png-clipart-cinemex-globe-cinema-logos-thumbnail.png" type="image/x-icon">
  </head>

  <body class="font-sans antialiased bg-gray-100 text-gray-900">
    <header class="bg-red-700/85 text-white text-center py-2 px-10 flex justify-around items-center">
      <a class="bg-gray-300/25 rounded px-2" href=".">
        <img src="./Logo_de_Cinemex.svg" alt="logo">
      </a>
      <h1 class="text-4xl font-bold">RESEÑAS</h1>
    </header>

    <?php
    // Verificar si se encontraron comentarios
    if (empty($registros)) {
      // No se encontraron comentarios, mostrar un mensaje
      echo "<h1 class='text-2xl font-bold mt-5 text-center'>No hay comentarios disponibles :(</h1>";
    } else {
      // Se encontraron comentarios, continuar con la visualización
      foreach($registros as $registro) {
        $cantAM = ceil($registro['calificacion'] / 2);
        $cantNE = 5 - $cantAM;
        echo '
      <article class="w-2/4 mx-auto mt-5">
        <div class="font-medium dark:text-white">
          <p style="color:black;">' . htmlspecialchars($registro['nombre']) . '</p>
        </div>
        <div class="flex items-center mb-1 space-x-1 rtl:space-x-reverse">';
        
        for ($i = 0; $i < $cantAM; $i++) {
          echo '<svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z" />
          </svg>';
        }
        
        for ($i = 0; $i < $cantNE; $i++) {
          echo '<svg class="w-4 h-4 text-gray-300 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
            <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z" />
          </svg>';
        }
        
        echo '</div>
        <footer class="mb-5 text-sm text-gray-500 dark:text-gray-400">
          <p>' . htmlspecialchars($registro['correo']) . '</p>
        </footer>
        <p class="mb-2 text-gray-500 dark:text-gray-400">' . htmlspecialchars($registro['comentario']) . '</p>
        <hr style="border-width:2px;">
      </article>';
      }
    }
    ?>
  </body>
  </html>
<?php
}
?>
