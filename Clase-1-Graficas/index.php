<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <link rel="stylesheet" href="estilo.css">
    <title>Documento</title>
</head>
<body>
<form action="./index.php" method="post" style="text-align: center;">
    <label for="totales">Total de ventas mayor o igual a:</label>
    <input type="text" name="totales" id="totales" value="<?php echo isset($_POST['totales']) ? $_POST['totales'] : ''; ?>">
    <?php
    include './conexion.php';
    $anios = "SELECT DISTINCT YEAR(fecha) as anio FROM encabezado_fac
    WHERE YEAR(fecha) BETWEEN 2013 AND 2022
    ORDER BY YEAR(fecha) ASC";
    $ejecucion = mysqli_query($conexion, $anios);

    while ($seleccionAnios = mysqli_fetch_array($ejecucion)) {
        $anio = $seleccionAnios['anio'];
        $checked = in_array($anio, (array)$_POST['selected_anios']) ? 'checked' : '';
        echo "<label>$anio</label>";
        echo "<input type='checkbox' name='selected_anios[]' value='$anio' $checked></input>";
    }
    ?>
    <input type="submit" value="Graficar">
</form>
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>
</body>
</html>
<script>
Highcharts.chart('container', {
    title: {
        text: 'Empresa XYZ',
        align: 'center'
    },
    subtitle: {
        text: 'Total de ventas anuales de los últimos 10 años',
        align: 'center'
    },
    yAxis: {
        title: {
            text: 'Ventas en $'
        }
    },
    xAxis: {
        accessibility: {
            rangeDescription: 'Desde 2013 al 2022'
        },
        categories: [<?php
            $selectedAnios = isset($_POST['selected_anios']) ? $_POST['selected_anios'] : [];
            echo implode(', ', $selectedAnios);
        ?>]
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },
    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            }
        }
    },
    series: [{
        name: 'Ventas anuales',
        data: [
            <?php
            include './conexion.php';
            $totales = isset($_POST['totales']) ? $_POST['totales'] : "";

            if ($totales == "") {
                $selectedAniosStr = implode(", ", $selectedAnios);
                $consulta = "SELECT SUM(venta) as venta, YEAR(fecha) as year FROM detalle_fac
                INNER JOIN encabezado_fac ON detalle_fac.codigo = encabezado_fac.codigo
                WHERE YEAR(fecha) IN ($selectedAniosStr)
                GROUP BY year";

                $executar = mysqli_query($conexion, $consulta);

                while ($dato = mysqli_fetch_array($executar)) {
                    $d = number_format($dato['venta'], 2, '.', '');
                    echo $d . ",";
                }
            }
            ?>
        ]
    }],
    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }
});
</script>
