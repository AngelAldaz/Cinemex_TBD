<?php
include "./src/db.php";

//$sentencia = $conn->prepare("SELECT * FROM peliculas;");
$sentencia = $conn->prepare("SELECT peliculas.*, 
    clasificaciones.nombre_clasificacion AS clasificacion, 
    generos.nombre_genero AS genero
    FROM peliculas
    LEFT JOIN clasificaciones ON clasificaciones.id_clasificacion = peliculas.id_clasificacion
    LEFT JOIN generos ON generos.id_genero = peliculas.id_genero");

$sentencia->execute();
$allPeliculas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

//print_r($allPeliculas);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos enviados desde JavaScript
  $datos = json_decode(file_get_contents('php://input'), true);

  // Acceder a los datos
  $nombre = $datos['nombre'];
  $email = $datos['email'];
  $comentario = $datos['comentario'];
  $rango = $datos['rango'];
  $id_pelicula = $datos['id_pelicula'];


  // Verificar si el correo ya existe en la base de datos
  $consulta = $conn->prepare("SELECT id_userinfo FROM userinfo WHERE correo = :correo");
  $consulta->bindParam(':correo', $email);
  $consulta->execute();
  if ($consulta->rowCount() > 0) {
    // El correo ya existe, recuperar el ID
    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    $id_userinfo = $resultado['id_userinfo'];
  } else {
    // El correo no existe, insertar un nuevo registro
    $sentencia = $conn->prepare("INSERT INTO userinfo (correo, nombre) VALUES (:correo, :nombre)");
    $sentencia->bindParam(':correo', $email);
    $sentencia->bindParam(':nombre', $nombre);
    $sentencia->execute();

    // Recuperar el ID del nuevo registro insertado
    $id_userinfo = $conn->lastInsertId();
  }

  $sentencia = $conn->prepare("INSERT INTO comentarios (id_userinfo, calificacion, comentario, id_pelicula) VALUES (:id_userinfo, :calificacion, :comentario, :id_pelicula)");
  $sentencia->bindParam(":id_userinfo", $id_userinfo);
  $sentencia->bindParam(":calificacion", $rango);
  $sentencia->bindParam(":comentario", $comentario);
  $sentencia->bindParam(":id_pelicula", $id_pelicula);
  $sentencia->execute();
}


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
    <div class="bg-gray-300/25 rounded px-2">
      <img src="./Logo_de_Cinemex.svg" alt="logo">
    </div>
    <h1 class="text-4xl font-bold">CARTELERA</h1>
  </header>

  <div class="container mx-auto px-4 py-8">
    <div class="movie-list grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php foreach ($allPeliculas as $pelicula) { ?>
        <div class="movie-card border border-gray-300 bg-white p-4 rounded-lg shadow-md flex flex-col ">
          <h2 class="text-2xl font-semibold mb-2 text-center line-clamp-1 "><?php echo $pelicula['titulo'] ?></h2>
          <img src="<?php echo $pelicula['imagen'] ?>" alt="Cartel de la película" class="w-full  object-cover rounded-md mb-4">
          <p class="line-clamp-4"><span class="font-bold text-lg ">Descripción: </span><?php echo $pelicula['sinopsis'] ?></p>
          <button id="popup" class="mt-4 py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600" onclick='showMovieAlert(<?php echo json_encode($pelicula) ?>)'>Ver detalles</button>
        </div>
      <?php } ?>

    </div>
  </div>
  <!-- 
  <div class="bg-white p-4 w-2/4 mx-auto">
    <div class=" flex space-x-5">
      <img src="./public/img/no.jpg" alt="Cartel de la película" class="object-cover">
      <div class="space-y-2">
        <h1 class="text-2xl font-semibold mb-2 text-center">El planeta de los simios</h1>
        <p class="text-wrap"><span class="font-bold text-lg">Descripción: </span>El director Wes Ball le da nueva vida a la épica franquicia global situada varias generaciones en el futuro tras el reinado de César, en el que los simios son la especie dominante que vive en armonía y los humanos han quedado reducidos a vivir en las sombras. Mientras un nuevo y tiránico líder simio construye su imperio, un joven simio emprende un viaje desgarrador que le llevará a cuestionarse todo lo que sabe sobre el pasado y a tomar decisiones que definirán el futuro de simios y humanos por igual.</p>
        <p class="text-wrap"><span class="font-bold text-lg">Director: </span>Wes Ball.</p>
        <p class="text-wrap"><span class="font-bold text-lg">Clasificación: </span>S/C.</p>
        <p class="text-wrap"><span class="font-bold text-lg">Genero: </span>Acción.</p>
        <p class="text-wrap"><span class="font-bold text-lg">País: </span>Estados Unidos.</p>
        <p class="text-wrap"><span class="font-bold text-lg">Duración: </span>2h 25m.</p>
        <p class="text-wrap"><span class="font-bold text-lg">Distribuidor: </span>20th Century Studios.</p>
        <p class="text-wrap"><span class="font-bold text-lg">Funciones: </span></p>
        <div class="relative overflow-x-auto">
          <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-6 py-3">
                  Cine
                </th>
                <th scope="col" class="px-6 py-3">
                  Idioma
                </th>
                <th scope="col" class="px-6 py-3">
                  Formato
                </th>
                <th scope="col" class="px-6 py-3">
                  Hora
                </th>
                <th scope="col" class="px-6 py-3">
                  Día
                </th>
              </tr>
            </thead>
            <tbody>
              <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                  Canek
                </th>
                <td class="px-6 py-4">
                  Español
                </td>
                <td class="px-6 py-4">
                  3D
                </td>
                <td class="px-6 py-4">
                  2:43
                </td>
                <td class="px-6 py-4">
                  Hoy
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex justify-around items-center top-auto">
          <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="showCommentAlert()">Realizar reseña</button>
          <button class=" text-gray-400/75 font-semibold text-base flex items-center">
            Ver reseñas
            <svg class="h-5 ml-2 text-gray-400/75" viewBox="0 0 448 512">
              <path d="M384 32c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96C0 60.7 28.7 32 64 32H384zM320 313.4V176c0-8.8-7.2-16-16-16H166.6c-12.5 0-22.6 10.1-22.6 22.6c0 6 2.4 11.8 6.6 16L184 232l-66.3 66.3C114 302 112 306.9 112 312s2 10 5.7 13.7l36.7 36.7c3.6 3.6 8.5 5.7 13.7 5.7s10-2 13.7-5.7L248 296l33.4 33.4c4.2 4.2 10 6.6 16 6.6c12.5 0 22.6-10.1 22.6-22.6z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div> -->

  <script>
      <?php
$sentencia = $conn->prepare("SELECT horarios.*, 
    idiomas.nombre_idioma AS idioma, 
    cines.nombre_cine AS cine,
    formatos.nombre_formato AS formato
    FROM horarios
    LEFT JOIN formatos ON formatos.id_formato = horarios.id_formato
    LEFT JOIN cines ON cines.id_cine = horarios.id_cine
    LEFT JOIN idiomas ON idiomas.id_idioma = horarios.id_idioma");

$sentencia->execute();
$moiveInfos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        ?>
    function showMovieAlert(pelicula) {
      Swal.fire({
        toast: true,
        width: '70%',
        showCloseButton: true,
        showConfirmButton: false,
        html: `
        <div class=" flex space-x-5">
        <img src="${pelicula['imagen']}" alt="Cartel de la película" class="object-cover">
        <div class="space-y-2">
          <h1 class="text-3xl font-semibold mb-2 text-center">${pelicula['titulo']}</h1>
          <p class="text-wrap"><span class="font-bold text-lg">Descripción: </span>${pelicula['sinopsis']}</p>
          <p class="text-wrap"><span class="font-bold text-lg">Director: </span>${pelicula['director_name']}</p>
          <p class="text-wrap"><span class="font-bold text-lg">Clasificación: </span>${pelicula['clasificacion']}</p>
          <p class="text-wrap"><span class="font-bold text-lg">Genero: </span>${pelicula['genero']}</p>
          <p class="text-wrap"><span class="font-bold text-lg">País: </span>${pelicula['pais']}</p>
          <p class="text-wrap"><span class="font-bold text-lg">Duración: </span>${pelicula['duracion']}</p>
          <p class="text-wrap"><span class="font-bold text-lg">Distribuidor: </span>${pelicula['distribuidor']}</p>
          <div class="relative overflow-x-auto">
          <table class="w-full text-sm text-left rtl:text-right text-gray-500 mb-5 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
              <th scope="col" class="px-6 py-3">
                  Cine
                </th>
                <th scope="col" class="px-6 py-3">
                  Idioma
                </th>
                <th scope="col" class="px-6 py-3">
                  Formato
                </th>
                <th scope="col" class="px-6 py-3">
                  Hora
                </th>
                <th scope="col" class="px-6 py-3">
                  Día
                </th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($moiveInfos as $movieInfo) { ?>
              ${pelicula['id_pelicula'] === <?php echo $movieInfo['id_pelicula'] ?> ? `<tr class="bg-white border-b ">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                <?php echo $movieInfo['cine'] ?>
                </th>
                <td class="px-6 py-4">
                <?php echo $movieInfo['idioma'] ?>
                </td>
                <td class="px-6 py-4">
                <?php echo $movieInfo['formato'] ?>
                </td>
                <td class="px-6 py-4">
                <?php echo $movieInfo['hora_de_inicio'] ?>
                </td>
                <td class="px-6 py-4">
                <?php echo $movieInfo['dia'] ?>
                </td>
              </tr>` : ``}
            <?php } ?>
            </tbody>
          </table>
        </div>
          <div class="flex justify-around items-center">
            <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="showCommentAlert(${pelicula['id_pelicula']})">Realizar reseña</button>
            <a class=" text-gray-400/75 font-semibold text-base flex items-center cursor-pointer" href="resenias.php?movieID=${pelicula['id_pelicula']}">
              Ver reseñas
            <svg class="h-5 ml-2 text-gray-400/75" viewBox="0 0 448 512">
              <path d="M384 32c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96C0 60.7 28.7 32 64 32H384zM320 313.4V176c0-8.8-7.2-16-16-16H166.6c-12.5 0-22.6 10.1-22.6 22.6c0 6 2.4 11.8 6.6 16L184 232l-66.3 66.3C114 302 112 306.9 112 312s2 10 5.7 13.7l36.7 36.7c3.6 3.6 8.5 5.7 13.7 5.7s10-2 13.7-5.7L248 296l33.4 33.4c4.2 4.2 10 6.6 16 6.6c12.5 0 22.6-10.1 22.6-22.6z" />
            </svg>
          </a>
        </div>
        </div>
      </div>
      `,
      })
    }

    async function showCommentAlert(pelicula) {
      const {
        value: nombre
      } = await Swal.fire({
        title: "Ingresa tu nombre",
        input: "text",
        showCancelButton: true,
        inputValidator: (value) => {
          if (!value) {
            return "Escribe bien!";
          }
        }
      });
      if (nombre) {
        const {
          value: email
        } = await Swal.fire({
          title: "Ingresa tu correo",
          input: "email",
          inputLabel: `${nombre}`,
          showCancelButton: true,
          inputPlaceholder: "Tu correo"
        });
        if (email) {
          const {
            value: comentario
          } = await Swal.fire({
            title: "Comentario",
            input: "textarea",
            inputLabel: `${nombre}`,
            inputPlaceholder: "Escribe aqui tu comentario...",
            showCancelButton: true
          });
          if (comentario) {
            const {
              value: rango
            } = await Swal.fire({
              title: "Que te parecio la pelicula?",
              icon: "question",
              input: "range",
              inputAttributes: {
                min: "0",
                max: "10",
                step: "1"
              },
              inputValue: 5
            });
            if (rango) {
              await Swal.fire({
                title: "Comentario subido con exito!",
                text: `Gracias por el comentario,${nombre}`,
                icon: "success",
                confirmButtonText: "De nada!"
              });
              var datos = {
                nombre: nombre,
                email: email,
                comentario: comentario,
                rango: rango,
                id_pelicula: pelicula
              };

              // Hacer una solicitud AJAX para enviar los datos a un archivo PHP
              var xhr = new XMLHttpRequest();
              xhr.open("POST", ".", true);
              xhr.setRequestHeader("Content-Type", "application/json");
              xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                  console.log(xhr.responseText); // Esto mostrará la respuesta del archivo PHP
                }
              };
              xhr.send(JSON.stringify(datos));

              window.location.reload();
            }
          }
        }
      }
    }
  </script>

</body>

</html>
