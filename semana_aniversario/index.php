<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Semana de Aniversario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body style="font-family: 'Roboto', sans-serif;">
    <nav class="nav">
        <img src="https://www.tecvalles.mx/wp/wp-content/uploads/2023/07/1reglamento.png" alt="tecnm">
        <img src="https://www.tecvalles.mx/web/images/logos/logotec.png" alt="tecvalles">
        <a class="main-page" href="./index.php">Instituto Tecnológico de México campus Ciudad Valles</a>
        <!-- <button type="button">Ingresar</button> -->
    </nav>
    <div class="titulo">
        <h1>Semana de Aniversario 2023</h1>
    </div>
    <div class="cursos" id="cursos">
    </div>
</body>

<script>

    var jsonData;
    var cursos = $('#cursos');
    cursos.empty();
    const months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];


    $.ajax({
        url: 'CursosData.json',
        dataType: 'json',
        success: function (data) {
            jsonData = data;
            showData(); // Mostrar los primeros 10 datos al cargar la página
        },
        error: function () {
            alert('Error al obtener los datos.');
        }
    });


    $("button").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "./asistance.php",
            data: {
                id: $(this).val(), // < note use of 'this' here
            },
            success: function (result) {
                window.open(url)
            },
            error: function (result) {
                alert('error');
            }
        });
    });

    function showData() {
        var data = jsonData; // Usar los datos filtrados o todos los datos

        if (data.length === 0) {
            // No se encontraron datos
            var row = $('<h1>No se encuentran los datos </h1>');
            cursos.append(row);
            return;
        }

        // Recorrer los datos y construir las filas de la tabla
        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            var img = "";
            switch (item.area_conocimiento) {
                case "Ciencias Agronómicas":
                    img = "./assets/ciencias_agronomicas.jpg";
                    break;
                case "Alimentos":
                    img = "./assets/ciencias_alimentarias.jpg";
                    break;
                case "Ciencias Computacionales":
                    img = "./assets/ciencias_computacionales.jpg";
                    break;
                case "Ciencias Agropecuarias":
                case "Ciencias Pecuarias":
                    img = "./assets/ciencias_pecuarias.png";
                    break;
                case "Competencias digitales":
                    img = "./assets/DD.png";
                    enlace = 'https://www.facebook.com/profile.php?id=100086381133586'
                    break;
                case "Económico Administrativo":
                    img = "./assets/economico_administrativo.jpg";
                    break;
                case "Ciencias Ambientales":
                    img = "./assets/ingenieria_ambiental.jpg";
                    break;
                case "Ingeniería Industrial":
                    img = "./assets/ingenieria_industrial.jpg";
                    break;
                default:
                    img = "./assets/ingenieria.jpg";
                    break;

            }
            const fa = new Date();
            //fecha de inicio de la actividad
            var fi = item.fecha_inicio.split("-");
            //eliminamos los formatos de las horas se puede optimizar escribiendolas por separado
            var horarios = item.horario_actividad.split("-")
            var hora_inicio = horarios[0].split(":");
            var hora_cierre = horarios[1].split(":");
            //variables para trabajar con las fechas
            var asistencia = "asistencia_in";
            var registrarse;
            var cierre_actividad;
            var inicio_actividad;
            var metodo_registrarse;
            var metodo_asistencia;
            var disabled = '';

            var tiempo_extra = 30 * 60000 //30 minutos extras

            if (item.fecha_fin) {
                //obtenemos fecha fin
                var ff = item.fecha_fin.split("-");
                //cerramos el registro el primer dia de la actividad
                cierre_actividad = new Date(fi[0], fi[1] - 1, fi[2], hora_cierre[0], hora_cierre[1]);
                //escribimos leyenda en los div
                fecha = "Desde el dia " + fi[2] + " hasta el " + ff[2] + " de " + months[cierre_actividad.getMonth()] + " del " + ff[0];
                //comprobamos que aun no se haya cerrado el registro
                if (fa.getTime() <= cierre_actividad.getTime()) {
                    registrarse = "registrarse_in";
                    metodo_registrarse = "alert('curso no disponible por el momento')"

                } 
                //comprobamos que aun estemos dentro de la fecha limite desde la fecha de inicio hasta la fecha final
                cierre_actividad = new Date(ff[0], ff[1] - 1, ff[2], 23, 59);
                inicio_actividad = new Date(fi[0], fi[1] - 1, fi[2], 0, 0)

                if (inicio_actividad.getTime() <= fa.getTime() && fa.getTime() <= cierre_actividad.getTime()) {
                    //las variables ahora cambian para simular el día
                    cierre_actividad = new Date(ff[0], ff[1] - 1, fa.getDate(), hora_cierre[0], hora_cierre[1]);
                    inicio_actividad = new Date(ff[0], ff[1] - 1, fa.getDate(), hora_inicio[0], hora_inicio[1]);
                    //si el usuario se encuentra a la hora en el dia el boton de asistencia deberá de iluminarse
                    if (inicio_actividad.getTime() - tiempo_extra <= fa.getTime() && fa.getTime() <= cierre_actividad.getTime() + tiempo_extra) {
                        asistencia = "asistencia";
                        disabled = '';
                    }
                }

            }
            else {
                //comprobamos que estemos en la fecha y hora
                cierre_actividad = new Date(fi[0], fi[1] - 1, fi[2], hora_cierre[0], hora_cierre[1]);
                inicio_actividad = new Date(fi[0], fi[1] - 1, fi[2], hora_inicio[0], hora_inicio[1]);
                //escribimos la leyenda en el div 
                fecha = "El dia " + fi[2] + " de " + months[cierre_actividad.getMonth()] + " del " + fi[0];
                //si los datos corresponden se iluminan los botones necesarios
                if (fa.getTime() <= cierre_actividad.getTime()) {
                    registrarse = "registrarse_in";
                    metodo_registrarse = "alert('curso no disponible por el momento')"
                } else {
                    registrarse = "registrarse_in"
                    metodo_registrarse = "alert('curso no disponible por el momento')"
                }

                if (inicio_actividad.getTime() - tiempo_extra <= fa.getTime() && fa.getTime() <= cierre_actividad.getTime() + tiempo_extra) {
                    asistencia = "asistencia";
                    disabled = '';
                }
            }

            var curso = $('<div class="curso">');
            curso.append('<div class="image"><img src="' + img + '" width="330px" height="150px"></div>');
            curso.append('<div><span class="titulo">' + item.titulo + '</span><p class="conferencistas">' + item.conferencista + '</p><p class="texto">' + item.objetivo + '</p>')
            curso.append('<p class="horario">' + fecha + '</p><p class="horario">Horario: ' + item.horario_actividad + '</p><p class="horario">Orientado a: ' + item.area_conocimiento + '-' + item.publico_objetivo + '</p><p class="horario">Horas a acreditar: ' + item.horas + '</p><p class="horario">Tipo: ' + item.tipo_actividad + '</p><p class="horario">Capacidad: ' + item.capacidad + '</p><p class="lugar">Ubicacion: ' + item.lugar + '</p></div>')
            curso.append('<form method="post" action="./asistance.php" target="_self" ><input type="hidden" name="curso" value="' + item.numero_actividad + '"><input type="hidden" name="horas" value="' + item.horas_diarias + '"><input class ="' + asistencia + '" type="submit" value="Asistencia" '+disabled+'> </form>')
            curso.append('</div>')
            cursos.append(curso);

        }

    }


</script>

</html>



</div>