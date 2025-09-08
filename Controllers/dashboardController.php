<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php"); // Aquí bd.php debe crear la conexión $conn con MySQLi (objeto)

$id_usuario     = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];
$proyecto       = $_SESSION['proyecto'];

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
            $fechaInicio = $_POST['startDate'] ?? null;
            $fechaFin    = $_POST['endDate'] ?? null;
            $tipo        = (isset($_POST['tipo']) && $_POST['tipo'] !== '') ? (int)$_POST['tipo'] : null;

            // Modificar consultas: cambiar los placeholders :param por ? y ajustar el SQL a MySQL si es necesario

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
                        IFNULL(SUM(CASE WHEN C.tipo_cyc = 1 THEN 1 ELSE 0 END), 0) AS Contingencia,
                        IFNULL(SUM(CASE WHEN C.tipo_cyc = 2 THEN 1 ELSE 0 END), 0) AS Crisis
                    FROM Horas H
                    LEFT JOIN cyc C
                        ON H.Hr = HOUR(C.fecha_registro_cyc)
                        AND C.proyecto = ?
                    WHERE 
                        " . ($fechaInicio && $fechaFin ? "C.fecha_registro_cyc BETWEEN ? AND ?" : "DATE(C.fecha_registro_cyc) = CURDATE()") . "
                        " . ($tipo ? "AND C.tipo_cyc = ?" : "") . "
                        AND HOUR(C.fecha_registro_cyc) BETWEEN 8 AND 20
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
                  FROM cat_crisis
              ),
              CycCalc AS (
                  SELECT 
                      cyc.*,
                      CASE 
                          WHEN cyc.fecha_programacion IS NULL THEN 
                              ROUND(TIMESTAMPDIFF(MINUTE, cyc.fecha_registro_cyc, NOW()) / 60.0, 1)
                          WHEN cyc.fecha_programacion > NOW() THEN 0
                          ELSE 
                              CASE 
                                  WHEN EXISTS(
                                      SELECT 1
                                      FROM logs l
                                      WHERE l.description LIKE CONCAT('%Desactivo la grabación con número de ticket: ', cyc.no_ticket, '%')
                                      AND NOT EXISTS (
                                          SELECT 1
                                          FROM logs l2
                                          WHERE l2.description LIKE CONCAT('%Activó la grabación con número de ticket: ', cyc.no_ticket, '%')
                                          AND l2.fecha > l.fecha
                                      )
                                  ) THEN 0
                                  ELSE 
                                      ROUND(
                                          (TIMESTAMPDIFF(MINUTE, cyc.fecha_programacion, NOW()) - IFNULL(ina.inactive_minutes, 0)) / 60.0, 1
                                      )
                              END
                      END AS HorasTranscurridas
                  FROM cyc
                  LEFT JOIN (
                      SELECT 
                          SUM(TIMESTAMPDIFF(MINUTE, l_desact.fecha, l_react.fecha)) AS inactive_minutes,
                          SUBSTRING_INDEX(SUBSTRING_INDEX(l_desact.description, ': ', -1), ' ', 1) AS ticket_no
                      FROM logs l_desact
                      JOIN logs l_react
                          ON l_react.description LIKE CONCAT('%Activó la grabación con número de ticket: ', SUBSTRING_INDEX(SUBSTRING_INDEX(l_desact.description, ': ', -1), ' ', 1), '%')
                          AND l_react.fecha > l_desact.fecha
                      WHERE l_desact.description LIKE '%Desactivo la grabación con número de ticket: %'
                      GROUP BY ticket_no
                  ) ina ON ina.ticket_no = cyc.no_ticket
                  WHERE cyc.proyecto = ?
              )
              SELECT
                  H.Hora,
                  C.nombre_crisis,
                  CASE 
                      WHEN IFNULL(cc.tipo_cyc, 0) = 1 THEN 'Contingencia'
                      WHEN cc.tipo_cyc = 2 THEN 'Crisis'
                      ELSE 'OTRO'
                  END AS tipo_categoria,
                  SUM(IFNULL(cc.HorasTranscurridas, 0)) AS Horas_Transcurridas
              FROM Horas H
              CROSS JOIN Categorias C
              LEFT JOIN CycCalc cc 
                  ON H.Hr = HOUR(cc.fecha_registro_cyc)
                  AND cc.categoria_cyc = C.id
                  AND HOUR(cc.fecha_registro_cyc) BETWEEN 8 AND 20
                  AND cc.fecha_registro_cyc BETWEEN ? AND ?
                  " . ($tipo ? "AND cc.tipo_cyc = ?" : "") . "
                  AND cc.status_cyc IN (1,2)
              GROUP BY 
                  H.Hora,
                  C.nombre_crisis,
                  tipo_categoria
              ORDER BY 
                  H.Hora,
                  C.nombre_crisis,
                  tipo_categoria;
            ";

            $query_totalesCategoria = "
                SELECT
                    IFNULL(
                        SUM(
                            CASE 
                                WHEN cyc.fecha_programacion IS NOT NULL 
                                    AND cyc.fecha_registro_cyc IS NOT NULL 
                                    AND cyc.tipo_cyc = 2 
                                THEN ROUND(TIMESTAMPDIFF(MINUTE, cyc.fecha_registro_cyc, cyc.fecha_programacion) / 60.0, 1)
                                ELSE 0
                            END
                        ), 0
                    ) AS horas_totales_crisis,
                    IFNULL(
                        SUM(
                            CASE 
                                WHEN cyc.fecha_programacion IS NOT NULL 
                                    AND cyc.fecha_registro_cyc IS NOT NULL 
                                    AND cyc.tipo_cyc = 1 
                                THEN ROUND(TIMESTAMPDIFF(MINUTE, cyc.fecha_registro_cyc, cyc.fecha_programacion) / 60.0, 1)
                                ELSE 0
                            END
                        ), 0
                    ) AS horas_totales_contingencia
                FROM cyc
                WHERE HOUR(cyc.fecha_registro_cyc) BETWEEN 8 AND 20
                AND cyc.proyecto = ?
                " . ($fechaInicio && $fechaFin 
                        ? "AND DATE(cyc.fecha_registro_cyc) BETWEEN ? AND ?" 
                        : "AND DATE(cyc.fecha_registro_cyc) = CURDATE()") . "
                " . ($tipo ? "AND cyc.tipo_cyc = ?" : "") . "
                AND cyc.status_cyc IN (1,2);
            ";

            // Preparamos y ejecutamos las consultas
            // IMPORTANTE: bind_param necesita tipos (s: string, i: int, d: double, b: blob)

            // Preparar consulta 1
            $stmt = $conn->prepare($queryTbl);
            if (!$stmt) throw new Exception("Error en prepare queryTbl: " . $conn->error);

            // Parámetros para queryTbl
            $params = [$proyecto];
            $types = "s";

            if ($fechaInicio && $fechaFin) {
                $params[] = $fechaInicio;
                $params[] = $fechaFin;
                $types .= "ss";
            }

            if ($tipo) {
                $params[] = $tipo;
                $types .= "i";
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $resultTbl = $stmt->get_result();

            // Preparar consulta 2
            $stmtCategorias = $conn->prepare($queryTblCategorias);
            if (!$stmtCategorias) throw new Exception("Error en prepare queryTblCategorias: " . $conn->error);

            // Parámetros para queryTblCategorias
            $paramsCat = [$proyecto, $fechaInicio, $fechaFin]; // Las fechas son obligatorias en esta consulta
            $typesCat = "sss";

            if ($tipo) {
                $paramsCat[] = $tipo;
                $typesCat .= "i";
            }

            $stmtCategorias->bind_param($typesCat, ...$paramsCat);
            $stmtCategorias->execute();
            $resultCategorias = $stmtCategorias->get_result();

            // Preparar consulta 3
            $stmtTotales = $conn->prepare($query_totalesCategoria);
            if (!$stmtTotales) throw new Exception("Error en prepare query_totalesCategoria: " . $conn->error);

            // Parámetros para totales
            $paramsTot = [$proyecto];
            $typesTot = "s";

            if ($fechaInicio && $fechaFin) {
                $paramsTot[] = $fechaInicio;
                $paramsTot[] = $fechaFin;
                $typesTot .= "ss";
            }

            if ($tipo) {
                $paramsTot[] = $tipo;
                $typesTot .= "i";
            }

            $stmtTotales->bind_param($typesTot, ...$paramsTot);
            $stmtTotales->execute();
            $resultTotales = $stmtTotales->get_result();

            // Fetch resultados
            while ($rowTbl = $resultTbl->fetch_assoc()) {
                $DtosTbl[] = [
                    'Hora'               => $rowTbl['Hora'],
                    'Contingencia'       => $rowTbl['Contingencia'],
                    'Crisis'             => $rowTbl['Crisis'],
                    'Total_Contingencia' => $rowTbl['Total_Contingencia'],
                    'Total_Crisis'       => $rowTbl['Total_Crisis']
                ];
            }

            while ($rowTblCategorias = $resultCategorias->fetch_assoc()) {
                if ($rowTblCategorias['Horas_Transcurridas']) {
                    $rowTblCategorias['Horas_Transcurridas'] = (float)$rowTblCategorias['Horas_Transcurridas'];
                }

                $DtosTblCategorias[] = [
                    'Hora'                => $rowTblCategorias['Hora'],
                    'nombre_crisis'       => $rowTblCategorias['nombre_crisis'],
                    'Horas_Transcurridas' => $rowTblCategorias['Horas_Transcurridas'],
                    'tipo'                => $rowTblCategorias['tipo_categoria']
                ];
            }

            while ($rowCategoria = $resultTotales->fetch_assoc()) {
                $DtosCategorias[] = [
                    'horas_totales_crisis'       => $rowCategoria['horas_totales_crisis'],
                    'horas_totales_contingencia' => $rowCategoria['horas_totales_contingencia']
                ];
            }

            $respuesta = [
                "datos_tbl"        => $DtosTbl,
                "datos_categorias" => $DtosTblCategorias,
                "totalesCategoria" => $DtosCategorias,
            ];

            header('Content-Type: application/json');
            echo json_encode($respuesta);

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo "Acción no reconocida.";
}