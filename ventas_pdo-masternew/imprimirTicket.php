<?php
if (!isset($_GET["id"])) {
    exit("No hay id");
}
$id = $_GET["id"];
include_once "base_de_datos.php";
$sentencia = $base_de_datos->prepare("SELECT id, fecha, preciototal FROM ventas WHERE id = ?");
$sentencia->execute([$id]);
$ventas = $sentencia->fetchObject();
if (!$ventas) {
    exit("No existe venta con el id proporcionado");
}

$sentenciaVehiculo = $base_de_datos->prepare("SELECT p.vin, p.modelo,p.costo, pv.cantidad
FROM vehiculo p
INNER JOIN 
vehiculos_vendidos pv
ON p.id = pv.id_vehiculo
WHERE pv.id_venta = ?");
$sentenciaVehiculo->execute([$id]);
$vehiculo = $sentenciaVehiculo->fetchAll();
if (!$vehiculo) {
    exit("No hay productos");
}

?>
<style>
    * {
        font-size: 12px;
        font-family: 'Times New Roman';
    }

    td,
    th,
    tr,
    table {
        border-top: 1px solid black;
        border-collapse: collapse;
    }

    td.producto,
    th.producto {
        width: 75px;
        max-width: 75px;
    }

    td.cantidad,
    th.cantidad {
        width: 50px;
        max-width: 50px;
        word-break: break-all;
    }

    td.precio,
    th.precio {
        width: 50px;
        max-width: 50px;
        word-break: break-all;
        text-align: right;
    }

    .centrado {
        text-align: center;
        align-content: center;
    }

    .ticket {
        width: 175px;
        max-width: 175px;
    }

    img {
        max-width: inherit;
        width: inherit;
    }

    @media print {

        .oculto-impresion,
        .oculto-impresion * {
            display: none !important;
        }
    }
</style>

<body>
    <div class="ticket">
        <img src="./logo.png" alt="Logotipo">
        <p class="centrado">TICKET DE VENTA
            <br><?php echo $ventas->fecha; ?>
        </p>
        <table>
            <thead>
                <tr>
                    <th class="cantidad">CANT</th>
                    <th class="vehiculo">VEHICULO</th>
                    <th class="precio">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $preciototal = 0;
                foreach ($vehiculo as $vehiculo) {
                    $subtotal = $vehiculo->costo * $vehiculo->cantidad;
                    $preciototal += $subtotal;
                ?>
                    <tr>
                        <td class="cantidad"><?php echo $vehiculo->cantidad;  ?></td>
                        <td class="vehiculo"><?php echo $vehiculo->descripcion;  ?> <strong>$<?php echo number_format($vehiculo->costo, 2) ?></strong></td>
                        <td class="precio">$<?php echo number_format($subtotal, 2)  ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" style="text-align: right;">TOTAL</td>
                    <td class="precio">
                        <strong>$<?php echo number_format($preciototal, 2) ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="centrado">¡GRACIAS POR SU COMPRA!
        </p>
    </div>
</body>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        window.print();
        setTimeout(() => {
            window.location.href = "./ventas.php";
        }, 1000);
    });
</script>