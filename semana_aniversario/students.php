<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="table2excel.js"></script>
</head>

<body style="font-family: 'Roboto', sans-serif;">
    <nav class="nav">
        <img src="https://www.tecvalles.mx/wp/wp-content/uploads/2023/07/1reglamento.png" alt="tecnm">
        <img src="https://www.tecvalles.mx/web/images/logos/logotec.png" alt="tecvalles">
        <a class="main-page" href="./index.php">Instituto Tecnológico de México campus Ciudad Valles</a>
        <!-- <button type="button">Ingresar</button> -->
    </nav>
    <div class="students-body">
        <div class="tabla-alumnos">
            <div class="student-search">
                <span>Alumno: </span><input type="text" name="no_ctrl" id="no_ctrl"><button
                    onclick="getStudent('')">Buscar
                    alumno</button>
            </div>
            <div class="query">
                <span>Operacion </span><select name="operador" id="operador">
                    <option value=">=" selected> >= </option>
                    <option value="<=">
                        <= </option>
                    <option value="="> == </option>
                    <option value=">"> > </option>
                    <option value="<">
                        < </option>
                </select>
                <span>Horas </span><input type="number" name="limite" id="limite" min="1" max="100" value="1">
                <button onclick="fetchsito()">Consulta</button>
                <button class="descarga-excel" onclick="exportReportToExcel()">Descargar tabla</button>
            </div>

            <table class="lista-alumnos" id="table">
                <tr>
                    <th>No.control</th>
                    <th>Alumno</th>
                    <th>Total de horas</th>
                    <th></th>
                </tr>
                <tbody id="textote">

                </tbody>

            </table>
        </div>
        <div class="alumno" id="alumno-card">
            <span id="nombre-alumno"></span><br>
            <span id="control-alumno"></span><br>
            <span id="carrera-alumno"></span>
            <ul id="lista-cursos">
            </ul>
            <span id="total-horas"></span>
        </div>


    </div>

</body>
<script>

    var cursos;
    var estudiantes;

    $.ajax({
        url: 'CursosData.json',
        dataType: 'json',
        success: function (data) {
            cursos = data;
        },
        error: function () {
            alert('Error al obtener los datos.');
        }
    });

    $.ajax({
        url: 'StudentsData.json',
        dataType: 'json',
        success: function (data) {
            estudiantes = data;
        },
        error: function () {
            alert('Error al obtener los datos.');
        }
    });


    function fetchsito() {
        let op = document.getElementById("operador").value;
        let num = document.getElementById("limite").value;
        const data = {
            comando: 'consultar_horas',
            operador: op,
            limite: num
        };
        var respuesta = ""
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'controller.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                respuesta = JSON.parse(xhr.response);
                createTable(respuesta)
            } else {
                // La autenticación falló
                console.log('Error de autenticación');
            }
        };
        xhr.send(JSON.stringify(data));
        setTimeout(function () {

        }, 1000);

    }

    function getStudent(numero_control) {
        if (numero_control == "") {
            var numero_control = document.getElementById("no_ctrl").value
            numero_control = numero_control.trim();
        }
        const data = {
            comando: 'consultar_estudiante',
            numero_control: numero_control
        };
        var respuesta = ""
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'controller.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                respuesta = JSON.parse(xhr.response);
                createCard(respuesta)
            } else {
                // La autenticación falló
                console.log('Error de autenticación');
            }
        };
        xhr.send(JSON.stringify(data));
        setTimeout(function () {

        }, 1000);

    }

    function createTable(data) {
        var tableBody = $('#textote');
        tableBody.empty();
        for (var i = 0; i < data.length; i++) {
            var result = estudiantes.find(({ numero_control }) => numero_control.trim() === data[i]['numero_control'].trim());
            var row = $('<tr><td>' + data[i].numero_control.trim() + '</td><td>' + result.nombre.toUpperCase() + " " + result.apellido_paterno.toUpperCase() + " " + result.apellido_materno.toUpperCase() + '</td><td>' + data[i].total + '</td><td><button href="#" onclick="getStudent(\'' + data[i].numero_control + '\')"> ver registros </button></td></tr>');
            tableBody.append(row)
        }
    }

    function createCard(data) {
        var result = estudiantes.find(({ numero_control }) => numero_control.trim() === data[0]['numero_control'].trim());
        document.getElementById('nombre-alumno').innerHTML = result.nombre.toUpperCase() + " " + result.apellido_paterno.toUpperCase() + " " + result.apellido_materno.toUpperCase();
        document.getElementById('control-alumno').innerHTML = data[0]['numero_control'];
        document.getElementById('carrera-alumno').innerHTML = result.carrera.split('/')[0] + "<br>Semestre:" + result.ciclo;
        var listilla = $('#lista-cursos');
        listilla.empty();
        var total = 0;
        for (var i = 0; i < data.length; i++) {
            if (data[i]['curso'] != "" || data[i]['horas'] != 0) {
                var curso = cursos.find(({ numero_actividad }) => numero_actividad === data[i]['curso']);
                listilla.append('<li>Curso:' + curso['titulo'] + '-----total: ' + curso['horas_totales'] + ' horas<br>Horas: ' + data[i]['horas'] + '<br>Tipo:' + curso.tipo_actividad + '</li>');
                total += parseInt(data[i]['horas']);
            }

        }
        document.getElementById('total-horas').innerHTML = "Horas totales: " + total.toString();

    }

    function exportReportToExcel() {
        var table2excel = new Table2Excel();
        table2excel.export(document.querySelectorAll("table"));
    }
</script>

</html>