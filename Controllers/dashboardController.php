<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
include("../Controllers/bd.php");
$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

switch ($accion) {
    case 1:
        $DtosTbl           = array();
        $DtosTblCategorias = array();
        $DtosCategorias    = [];

        try {
            // Actualizamos los nombres para que coincidan con el frontend
            $fechaInicio = isset($_POST['startDate']) ? $_POST['startDate'] : null;
            $fechaFin    = isset($_POST['endDate']) ? $_POST['endDate'] : null;
            $tipo = isset($_POST['tipo']) && $_POST['tipo'] !== '' ? (int)$_POST['tipo'] : null;

            if($tipo != null){
                $tipo = (int)$tipo;
            }


            $queryTbl = "
                WITH Horas AS (
                    SELECT '08:00' AS Hora, 8 AS Hr UNION ALL
                    SELECT '09:00', 9 UNION ALL 
                    SELECT '10:00', 10 UNION ALL 
                    SELECT '11:00', 11 UNION ALL 
                    SELECT '12:00', 12 UNION ALL 
                    SELECT '13:00', 13 UNION ALL 
                    SELECT '14:00', 14 UNION ALL 
                    SELECT '15:00', 15 UNION ALL 
                    SELECT '16:00', 16 UNION ALL 
                    SELECT '17:00', 17 UNION ALL 
                    SELECT '18:00', 18 UNION ALL 
                    SELECT '19:00', 19 UNION ALL 
                    SELECT '20:00', 20
                ),
                Data AS (
                    SELECT
                        H.Hora,
                        ISNULL(SUM(CASE WHEN C.tipo_cyc = 1 THEN 1 ELSE 0 END), 0) AS Contingencia,
                        ISNULL(SUM(CASE WHEN C.tipo_cyc = 2 THEN 1 ELSE 0 END), 0) AS Crisis
                    FROM Horas H
                    LEFT JOIN cyc C
                        ON DATEPART(HOUR, C.fecha_registro_cyc) = H.Hr
                    WHERE 
                        -- Filtrar por fechas si se proporcionan
                        " . ($fechaInicio && $fechaFin ? "C.fecha_registro_cyc BETWEEN :fechaInicio AND :fechaFin" : "C.fecha_registro_cyc = CONVERT(DATETIME, GETDATE())") . "
                        
                        -- Filtro por tipo si se proporciona
                        " . ($tipo ? "AND C.tipo_cyc = :tipo" : "") . "
                        
                        -- Filtro por horas de 8 a 20
                        AND DATEPART(HOUR, C.fecha_registro_cyc) BETWEEN 8 AND 20
                        AND C.status_cyc IN (1,2)
                        
                    GROUP BY H.Hora
                )
                SELECT 
                    D.Hora, 
                    D.Contingencia, 
                    D.Crisis,
                    (SELECT SUM(Contingencia) FROM Data) AS Total_Contingencia,
                    (SELECT SUM(Crisis) FROM Data) AS Total_Crisis
                FROM Data D
                ORDER BY D.Hora;


            ";

            $queryTblCategorias = "
              WITH Horas AS (
        SELECT '08:00' AS Hora, 8 AS Hr UNION ALL
        SELECT '09:00', 9 UNION ALL 
        SELECT '10:00', 10 UNION ALL 
        SELECT '11:00', 11 UNION ALL 
        SELECT '12:00', 12 UNION ALL 
        SELECT '13:00', 13 UNION ALL 
        SELECT '14:00', 14 UNION ALL 
        SELECT '15:00', 15 UNION ALL 
        SELECT '16:00', 16 UNION ALL 
        SELECT '17:00', 17 UNION ALL 
        SELECT '18:00', 18 UNION ALL 
        SELECT '19:00', 19 UNION ALL 
        SELECT '20:00', 20
    ),
    Categorias AS (
        SELECT id, nombre_crisis 
        FROM [contingencias].[dbo].[cat_crisis]
    ),
    CycCalc AS (
        SELECT 
    cyc.*,
    CASE 
        WHEN cyc.fecha_programacion IS NULL THEN 
            ROUND(DATEDIFF(MINUTE, cyc.fecha_registro_cyc, GETDATE()) / 60.0, 1)
        WHEN cyc.fecha_programacion > GETDATE() THEN 0
        ELSE 
            CASE 
                WHEN EXISTS(
                    SELECT 1
                    FROM logs l
                    WHERE l.description LIKE '%Desactivo la grabación con número de ticket: ' + cyc.no_ticket + '%'
                      AND NOT EXISTS (
                            SELECT 1
                            FROM logs l2
                            WHERE l2.description LIKE '%Activó la grabación con número de ticket: ' + cyc.no_ticket + '%'
                              AND l2.fecha > l.fecha
                        )
                ) THEN 0
                ELSE 
                    ROUND(
                        (DATEDIFF(MINUTE, cyc.fecha_programacion, GETDATE()) - ISNULL(ina.inactive_minutes, 0)) / 60.0, 1
                    )
            END
    END AS HorasTranscurridas
FROM [contingencias].[dbo].[cyc] cyc
OUTER APPLY (
    SELECT ISNULL(SUM(DATEDIFF(MINUTE, l_desact.fecha, l_react.fecha)), 0) AS inactive_minutes
    FROM logs l_desact
    JOIN logs l_react
       ON l_react.description LIKE '%Activó la grabación con número de ticket: ' + cyc.no_ticket + '%'
      AND l_react.fecha > l_desact.fecha
    WHERE l_desact.description LIKE '%Desactivo la grabación con número de ticket: ' + cyc.no_ticket + '%'
) ina
    )
    SELECT
        H.Hora,
        C.nombre_crisis,
        CASE 
             WHEN ISNULL(cc.tipo_cyc, 0) = 1 THEN 'Contingencia'
             WHEN cc.tipo_cyc = 2 THEN 'Crisis'
             ELSE 'OTRO'
        END AS tipo_categoria,
        SUM(ISNULL(cc.HorasTranscurridas, 0)) AS Horas_Transcurridas
    FROM Horas H
    CROSS JOIN Categorias C
    LEFT JOIN CycCalc cc 
       ON DATEPART(HOUR, cc.fecha_registro_cyc) = H.Hr
       AND cc.categoria_cyc = C.id
       AND DATEPART(HOUR, cc.fecha_registro_cyc) BETWEEN 8 AND 20
       AND cc.fecha_registro_cyc BETWEEN :fechaInicio AND :fechaFin
        " . ($tipo ? "AND cc.tipo_cyc = :tipo" : "") . "
        AND cc.status_cyc IN (1,2)
    GROUP BY 
        H.Hora,
        C.nombre_crisis,
        CASE 
             WHEN ISNULL(cc.tipo_cyc, 0) = 1 THEN 'Contingencia'
             WHEN cc.tipo_cyc = 2 THEN 'Crisis'
             ELSE 'OTRO'
        END
    ORDER BY 
        H.Hora,
        C.nombre_crisis,
        CASE 
             WHEN ISNULL(cc.tipo_cyc, 0) = 1 THEN 'Contingencia'
             WHEN cc.tipo_cyc = 2 THEN 'Crisis'
             ELSE 'OTRO'
        END;
            ";

            $query_totalesCategoria = "
               SELECT
                    ISNULL(
                        SUM(
                            CASE 
                                WHEN cyc.fecha_programacion IS NOT NULL 
                                     AND cyc.fecha_registro_cyc IS NOT NULL 
                                     AND cyc.tipo_cyc = 2 
                                THEN CONVERT(DECIMAL(18,1), ROUND(DATEDIFF(MINUTE, cyc.fecha_registro_cyc, cyc.fecha_programacion) / 60.0, 1, 1))
                                ELSE 0
                            END
                        ), 0
                    ) AS horas_totales_crisis,
                    ISNULL(
                        SUM(
                            CASE 
                                WHEN cyc.fecha_programacion IS NOT NULL 
                                     AND cyc.fecha_registro_cyc IS NOT NULL 
                                     AND cyc.tipo_cyc = 1 
                                THEN CONVERT(DECIMAL(18,1), ROUND(DATEDIFF(MINUTE, cyc.fecha_registro_cyc, cyc.fecha_programacion) / 60.0, 1, 1))
                                ELSE 0
                            END
                        ), 0
                    ) AS horas_totales_contingencia
                FROM cyc
                WHERE DATEPART(HOUR, cyc.fecha_registro_cyc) BETWEEN 8 AND 20
                  " . ($fechaInicio && $fechaFin 
                                            ? "AND CONVERT(DATE, cyc.fecha_registro_cyc) BETWEEN :fechaInicio AND :fechaFin" 
                                            : "AND CONVERT(DATE, cyc.fecha_registro_cyc) = CONVERT(DATE, GETDATE())") . "
                                        " . ($tipo ? "AND cyc.tipo_cyc = :tipo" : "") . "
                                        AND cyc.status_cyc IN (1,2);


            ";

            $stmt           = $conn->prepare($queryTbl);
            $stmtCategorias = $conn->prepare($queryTblCategorias);
            $stmtTotales    = $conn->prepare($query_totalesCategoria);

            // Asignar los valores de los parámetros solo si están presentes
            if ($fechaInicio && $fechaFin) {
                $stmt->bindParam(':fechaInicio', $fechaInicio);
                $stmt->bindParam(':fechaFin', $fechaFin);

                $stmtCategorias->bindParam(':fechaInicio', $fechaInicio);
                $stmtCategorias->bindParam(':fechaFin', $fechaFin);

                $stmtTotales->bindParam(':fechaInicio', $fechaInicio);
                $stmtTotales->bindParam(':fechaFin', $fechaFin);

            }

            if ($tipo) {
                $stmt->bindParam(':tipo', $tipo);
                $stmtCategorias->bindParam(':tipo', $tipo, PDO::PARAM_INT);
                $stmtTotales->bindParam(':tipo', $tipo);

            }

            $stmt->execute();
            $stmtCategorias->execute();
            $stmtTotales->execute();

            while ($rowTbl = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $DtosTbl[] = array(
                    'Hora'               => $rowTbl['Hora'],
                    'Contingencia'       => $rowTbl['Contingencia'],
                    'Crisis'             => $rowTbl['Crisis'],
                    'Total_Contingencia' => $rowTbl['Total_Contingencia'],
                    'Total_Crisis'       => $rowTbl['Total_Crisis']
                );
            }

            while ($rowTblCategorias = $stmtCategorias->fetch(PDO::FETCH_ASSOC)) {

                if($rowTblCategorias['Horas_Transcurridas']){
                    $rowTblCategorias['Horas_Transcurridas'] = (float)$rowTblCategorias['Horas_Transcurridas'];
                }

                $DtosTblCategorias[] = array(
                    'Hora'                => $rowTblCategorias['Hora'],
                    'nombre_crisis'       => $rowTblCategorias['nombre_crisis'],
                    'Horas_Transcurridas' => $rowTblCategorias['Horas_Transcurridas'],
                    'tipo'                => $rowTblCategorias['tipo_categoria']
                );
            }

            while ($rowCategoria = $stmtTotales->fetch(PDO::FETCH_ASSOC)) {
                $DtosCategorias[] = array(
                    'horas_totales_crisis'       => $rowCategoria['horas_totales_crisis'],
                    'horas_totales_contingencia' => $rowCategoria['horas_totales_contingencia']
                );
            }

            $respuesta = [
                "datos_tbl"        => $DtosTbl,
                "datos_categorias" => $DtosTblCategorias,
                "totalesCategoria" => $DtosCategorias,
                //"tipo" => $tipo
            ];

            header('Content-Type: application/json');
            echo json_encode($respuesta);

        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo "Acción no reconocida.";
}
