$(document).ready(function() {
    let graficaActividad;
    let graficaActividadCategoria;
    const ctx  = document.getElementById('actividadChart').getContext('2d');
    const ctx2 = document.getElementById('actividadChartCategorias').getContext('2d');

    function obtenerDatos() {
        // Asegurarnos de que la hora esté en formato HH:00
        const startDate = $('#startDate').val() + ' ' + ($('#startHour').val() || '08:00'); // Por defecto 08:00 si no hay valor
        const endDate   = $('#endDate').val() + ' ' + ($('#endHour').val() || '20:00'); // Por defecto 20:00 si no hay valor
        const tipo      = $('#tipo').val();

        $.ajax({
            url: '../Controllers/dashboardController.php',
            method: 'POST',
            data: { accion: 1, startDate, endDate, tipo },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                actualizarGrafica(response.datos_tbl);
                actualizarGraficaCategorias(response.datos_categorias, response.totalesCategoria);
            },
            error: function(err) {
                console.error('Error al obtener datos:', err);
            }
        });
    }

    function actualizarGrafica(data) {
        console.log(data);
        if (!Array.isArray(data)) {
            console.error("Se esperaba un array, pero se recibió:", data);
            return; // Salimos si no es un array
        }

        const horas = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
        
        const contingencia = horas.map(h => {
            const horaData = data.find(item => item.Hora === h);
            return horaData ? horaData.Contingencia : 0;
        });

        const crisis = horas.map(h => {
            const horaData = data.find(item => item.Hora === h);
            return horaData ? horaData.Crisis : 0;
        });

        const totalContingencia = contingencia.reduce((a, b) => a + Number(b), 0);
        const totalCrisis = crisis.reduce((a, b) => a + Number(b), 0);


        if (graficaActividad) {
            graficaActividad.destroy();
        }

        graficaActividad = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: horas,
                datasets: [
                    {
                        label: `Contingencia (Total: ${totalContingencia})`,
                        data: contingencia,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: `Crisis (Total: ${totalCrisis})`,
                        data: crisis,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: `Número de Crisis y Contingencias Total CR: ${totalCrisis}  Total CO: ${totalContingencia}`,
                        font: {
                            size: 16 // Tamaño del título
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                animation: {
                    onComplete: function () {
                        const chartInstance = this;
                        const ctx = chartInstance.ctx;
                        ctx.font = 'bold 12px Arial';
                        ctx.fillStyle = '#000';
                        ctx.textAlign = 'center';
                        ctx.fillStyle = '#FFFFFF';
                        ctx.textBaseline = 'middle'; 

                        chartInstance.data.datasets.forEach(function (dataset, i) {
                            const meta = chartInstance.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                const dataValue = dataset.data[index];
                                if (dataValue !== null && dataValue !== 0) {
                                    const yPos = bar.y + (bar.base - bar.y) / 2; // 👈 Cálculo para centro
                                    ctx.fillText(dataValue, bar.x, yPos);
                                }
                            });
                        });
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        display:false,
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        stacked: false
                    }
                }
            }
        });

    }

    function actualizarGraficaCategorias(data, totalesCategoria) {
    console.log(data);
    console.log(totalesCategoria);

    if (!Array.isArray(data)) {
        console.error("Se esperaba un array, pero se recibió:", data);
        return;
    }

    // Variables para sumar las horas transcurridas
    let totalHorasCrisis = 0;
    let totalHorasContingencia = 0;

    // Recorrer los datos y sumar según el tipo
    data.forEach(item => {
        if (item.tipo === "Crisis") {
            totalHorasCrisis += item.Horas_Transcurridas;
        } 
        if (item.tipo === "Contingencia") {
            totalHorasContingencia += item.Horas_Transcurridas;
        }
    });

    // Filtrar datos para Crisis y Contingencia
    const datosCrisis = data.filter(item => item.tipo === "Crisis");
    const datosContingencia = data.filter(item => item.tipo === "Contingencia");

    // Preparar los puntos para Crisis
    const puntosCrisis = datosCrisis.map(item => ({
        x: item.Hora,
        y: item.nombre_crisis,
        r: Math.sqrt(parseFloat(item.Horas_Transcurridas)) * 2,
        Horas_Transcurridas: parseFloat(item.Horas_Transcurridas),
        tipo: item.tipo
    }));

    // Preparar los puntos para Contingencia
    const puntosContingencia = datosContingencia.map(item => ({
        x: item.Hora,
        y: item.nombre_crisis,
        r: Math.sqrt(parseFloat(item.Horas_Transcurridas)) * 2,
        Horas_Transcurridas: parseFloat(item.Horas_Transcurridas),
        tipo: item.tipo
    }));

    // Destruir la gráfica anterior si existe
    if (graficaActividadCategoria) {
        graficaActividadCategoria.destroy();
    }

    // Crear la nueva gráfica
    graficaActividadCategoria = new Chart(ctx2, {
                type: 'scatter',
                data: {
                    datasets: [
                        {
                            label: `Contingencia (Total: ${totalHorasContingencia} hrs)`,
                            data: puntosContingencia,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)', // Color azul para Contingencia
                            pointRadius: 10, // Tamaño del punto
                            pointHoverRadius: 12 // Tamaño al pasar el mouse
                        },
                        {
                            label: `Crisis (Total: ${totalHorasCrisis} hrs)`,
                            data: puntosCrisis,
                            backgroundColor: 'rgba(75, 192, 192, 0.7)', // Color rojo para Crisis
                            pointRadius: 10,
                            pointHoverRadius: 12
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            type: 'category',
                            labels: [...new Set(data.map(item => item.Hora))], // Horas únicas
                            title: {
                                display: true,
                                text: 'Hora'
                            }
                        },
                        y: {
                            type: 'category',
                            labels: [...new Set(data.map(item => item.nombre_crisis))], // Categorías únicas
                            title: {
                                display: true,
                                text: 'Categoría'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Duración de Contingencias y Crisis', // Título de la gráfica
                            font: {
                                size: 16 // Tamaño del título
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const punto = context.raw;
                                    return [
                                        `Hora: ${punto.x}`,
                                        `Categoría: ${punto.y}`,
                                        `Horas Transcurridas: ${punto.Horas_Transcurridas.toFixed(2)}`,
                                        `Tipo: ${punto.tipo}`
                                    ];
                                }
                            }
                        },
                        datalabels: { // Etiquetas al lado de los puntos
                            align: 'bottom', // Alineado abajo del punto
                            anchor: 'start', // Posición en relación con el punto
                            color: 'black',
                            font: {
                                size: 12
                            },
                            formatter: (value, context) => value.Horas_Transcurridas.toFixed(1) + " hrs"
                        }
                    }
                },
                plugins: [ChartDataLabels] // Activa el plugin de etiquetas
            });

}


    $('#aplicarFiltros').on('click', obtenerDatos);

    $('#resetFiltros').on('click', function() {
        const today = new Date().toISOString().split('T')[0]; // Obtener la fecha actual en formato YYYY-MM-DD
        $('#startDate').val(today); // Establecer el valor de la fecha de inicio como el día actual
        $('#startHour').val('08:00'); // Ajustado para solo permitir horas
        $('#endDate').val(today); // Establecer el valor de la fecha de fin como el día actual
        $('#endHour').val('20:00');
        $('#tipo').val(''); // Limpiar el tipo de ciclo
        obtenerDatos(); // Llamar a la función para actualizar los datos
    });

    const today = new Date().toISOString().split('T')[0];
    $('#startDate, #endDate').val(today);
    obtenerDatos();
});

