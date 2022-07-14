<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `order_list` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k))
                $$k = htmlspecialchars_decode($v);
        }
    }
    if (isset($user_id)) {
        $user = $conn->query("SELECT username FROM `users` where id= '{$user_id}'");
        if ($user->num_rows > 0) {
            $processed_by = $user->fetch_array()[0];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
include_once('./../inc/header.php');
?>

<body>

    <style>
        html,
        body {
            width: 100% !important;
            min-height: unset !important;
            min-width: unset !important;
        }
    </style>

    <div class="style px-2 py-1" line-height="1em">
        <div class="mb-0 text-center font-weight-bolder"><?= $_settings->info('name') ?></div>
        <div class="mb-0 text-center font-weight-bolder">Recibo</div>
        <hr>
        <div class="d-flex w-100">
            <div class="col-auto">Número de Transacción:</div>
            <div class="col-auto flex-shrink-1 flex-grow-1 pl-2"><?= isset($code) ? $code : '' ?></div>
        </div>
        <div class="d-flex w-100">
            <div class="col-auto">Fecha y Hora:</div>
            <div class="col-auto flex-shrink-1 flex-grow-1 pl-2"><?= isset($date_created) ? date("M, d Y H:i", strtotime($date_created)) : '' ?></div>
        </div>
        <div class="d-flex w-100">
            <div class="col-auto">Generada por:</div>
            <div class="col-auto flex-shrink-1 flex-grow-1 pl-2"><?= isset($processed_by) ? $processed_by : '' ?></div>
        </div>
        <hr>
        <div class="w-100 border-bottom border-dark" style="display:flex">
            <div style="width:15%" class="font-weight-bolder text-center">Cantidad</div>
            <div style="width:55%" class="font-weight-bolder text-center">Pedido</div>
            <div style="width:30%" class="font-weight-bolder text-center">Total</div>
        </div>
        <?php if (isset($id)) : ?>
            <?php
            $items = $conn->query("SELECT oi.*, concat(m.code,' - ', m.name) as `item` FROM `order_items` oi inner join `menu_list` m on oi.menu_id = m.id where oi.order_id = '{$id}'");
            while ($row = $items->fetch_assoc()) :
            ?>
                <div class="w-100" style="display:flex">
                    <div style="width:15%" class="text-center"><?= format_num($row['quantity']) ?></div>
                    <div style="width:55%" class="">
                        <div style="line-height:1em">
                            <div><?= $row['item'] ?></div>
                            <small class="text-muted">x <?= format_num($row['price'], 2) ?></small>
                        </div>
                    </div>
                    <div style="width:30%" class="text-right"><?= format_num($row['price'] * $row['quantity'], 2) ?></div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <div class="border border-dark mb-1"></div>
        <div class="border border-dark"></div>
        <div class="w-100 mb-2" style="display:flex">
            <h5 style="width:70%" class="mb-0 font-weight-bolder">Total</h5>
            <h5 style="width:30%" class="mb-0 font-weight-bolder text-right"><?= isset($total_amount) ? format_num($total_amount, 2) : '0.00' ?></h5>
        </div>
        <div class="w-100 mb-2" style="display:flex">
            <div style="width:70%" class="mb-0 font-weight-bolder">Pago</div>
            <div style="width:30%" class="mb-0 font-weight-bolder text-right"><?= isset($tendered_amount) ? format_num($tendered_amount, 2) : '0.00' ?></div>
        </div>
        <div class="w-100 mb-2" style="display:flex">
            <div style="width:70%" class="mb-0 font-weight-bolder">Cambio</div>
            <div style="width:30%" class="mb-0 font-weight-bolder text-right"><?= isset($total_amount) && isset($tendered_amount) ? format_num($tendered_amount - $total_amount, 2) : '0.00' ?></div>
        </div>
        <div class="border border-dark mb-1"></div>
        <div class="py-3">
            <center>
                <div class="font-weight-bolder">Cola #</div>
            </center>
            <h3 class="text-center foont-weight-bolder mb-0"><?= isset($queue) ? $queue : '' ?></h3>
        </div>
        <div class="border border-dark mb-1"></div>
    </div>
</body>
<script>
    document.querySelector('title').innerHTML = "Recibo - Vista de Impresión"
</script>

</html>