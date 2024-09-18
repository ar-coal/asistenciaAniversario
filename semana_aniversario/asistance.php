<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="utf-8">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="./style.css">
</head>
<main>
  <nav style="font-family: 'Roboto', sans-serif;" class="nav">
    <img src="https://www.tecvalles.mx/wp/wp-content/uploads/2023/07/1reglamento.png" alt="tecnm">
    <img src="https://www.tecvalles.mx/web/images/logos/logotec.png" alt="tecvalles">
    <a class="main-page" href="./index.php">Instituto Tecnológico de México campus Ciudad Valles</a>
    <!-- <button type="button">Ingresar</button> -->
  </nav>
  <div style="font-family: 'Roboto', sans-serif;" class="search">
    <select id="tipo-busqueda">
      <option value="*">---Todos---</option>
      <option value="4">II</option>
      <option value="26">II(Mixta)</option>
      <option value="6">IIA</option>
      <option value="9">ISC</option>
      <option value="10">IGE</option>
      <option value="14">IGE(Mixta)</option>
      <option value="20">IA</option>
      <option value="25">IEA</option>
    </select>

    <input type="text" id="search-input" placeholder="Buscar...">
  </div>
  <div style="font-family: 'Roboto', sans-serif;" class="body">
    <div style="margin: 15% auto;" id="loading" class="loading-bar"></div>
    <table style="display: none;" id="student-table">
      <thead>
        <tr>
          <th>Numero de control</th>
          <th>Nombre</th>
          <th>Asistencia</th>
        </tr>
      </thead>
      <tbody id="data-body"></tbody>
    </table>
  </div>

</main>
<script>
  var jsonData = []; // Variable para almacenar los datos JSON
  var filteredData; // Datos filtrados
  var Campo = "*";
  var banned = [];
  var diccionario;

  var mySelect = document.getElementById('tipo-busqueda');
  mySelect.onchange = (event) => {
    var value = event.target.value;
    Campo = value;
    showNextData();
  }

  $.ajax({
    url: 'StudentsData.json',
    dataType: 'json',
    success: function (data) {
      //jsonData = data;
      for (let i = 0; i < data.length; i++) {
        var item = data[i];
        var nombre_completo = item.nombre + " " + item.apellido_paterno + " " + item.apellido_materno;
        var ob = Object();
        ob['numero_control'] = item.numero_control;
        ob['nombre'] = nombre_completo;
        //diccionario.push(ob);
        jsonData.push(ob);
      }
      //jsonData = diccionario;
      //console.log(diccionario);
      showNextData();
      setTimeout(() => {
        document.getElementById("loading").style.display="none"
        document.getElementById("student-table").style.removeProperty("display")
      }, 2000); // Mostrar los primeros 10 datos al cargar la página
    },
    error: function () {
      alert('Error al obtener los datos.');
    }
  });


  // Búsqueda mediante Ajax
  $('#search-input').on('input', function () {
    var searchText = $(this).val().toLowerCase();

    filteredData = jsonData.filter(function (item) {
      var found = false;

      $.each(item, function (key, value) {
        var fieldValue = value.toString().toLowerCase();
        if (fieldValue.includes(searchText)) {
          found = true;
          return false; // Salir del bucle cuando se encuentra una coincidencia
        }
      });

      return found;
    });

    showNextData(); // Mostrar los datos filtrados o todos los datos
  });

  function showNextData() {

    var tableBody = $('#data-body');
    tableBody.empty();

    var data = filteredData || jsonData; // Usar los datos filtrados o todos los datos
    if (data.length == 0) {
      // No se encontraron datos
      var row = $('<tr>');
      row.append('<td colspan="3" class="error">El alumno no se encuentra registrado en el sistema </td>');
      tableBody.append(row);
      return;
    }

    // Recorrer los datos y construir las filas de la tabla
    for (var i = 0; i < data.length; i++) {
      var item = data[i];
      //var style = '';
      var button_style = '';
      //var nombre_completo = item.nombre + " " + item.apellido_paterno + " " + item.apellido_materno;
      var nombre_completo = item.nombre;
      if (banned.includes(item.numero_control)) {
        button_style = 'style="display:none;"'
      }
      if (Campo == '*') {
        var row = $('<tr>');
        row.append('<td>' + item.numero_control + '</td>');
        row.append('<td> ' + nombre_completo.toUpperCase() + ' </td>');
        row.append('<td><button ' + button_style + ' id="' + item.numero_control + '" class="table-button" onclick="registrar(\'' + item.numero_control + '\');" >Registrar asistencia</button></td>');
        tableBody.append(row);
      } else if (item.cve_carrera.trim() == Campo) {
        var row = $('<tr >');
        row.append('<td >' + item.numero_control + '</td>');
        row.append('<td> ' + nombre_completo.toUpperCase() + ' </td>');
        row.append('<td><button ' + button_style + ' id="' + item.numero_control + '" class="table-button" onclick="registrar(\'' + item.numero_control + '\');" >Registrar asistencia</button></td>');
        tableBody.append(row);
      }

    }

  }

  function registrar(no_control) {
    $(':button').prop('disabled', true);
    const data = {
      comando: 'registrar',
      numero_control: no_control,
      curso: "<?php echo ($_POST['curso']) ?>",
      horas: "<?php echo ($_POST['horas']) ?>"
    };
    var respuesta = ""
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'controller.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
      if (xhr.status === 200) {
        respuesta = xhr.response;
      } else {
        // La autenticación falló
        console.log('Error de autenticación');
      }
    };
    xhr.send(JSON.stringify(data));
    setTimeout(function () {
      if (respuesta == 100) {
        banned.push(no_control);
        document.getElementById(no_control).style.display = "none";
        alert("Alumno registrado con exito");
      } else {
        alert(respuesta);
      }
      $(':button').prop('disabled', false);
    }, 1000);

  }
</script>

</html>