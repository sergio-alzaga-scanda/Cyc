 // Mostrar la tabla según la opción seleccionada
    $('#opciones').change(function() {
        var selectedOption = $(this).val();

        // Ocultar todas las tablas
        $('.table-container').hide();

        // Mostrar la tabla correspondiente si hay una opción seleccionada
        if (selectedOption) {
            $('#tabla-' + selectedOption).show();
        }
    });
    // Cuando la ventana se haya cargado completamente
    window.onload = function() {
        // Oculta el splash de carga después de que todo esté cargado
        document.getElementById('splash').style.display = 'none';
    };