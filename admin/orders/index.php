<?php if ($_settings->chk_flashdata('success')) : ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.order-logo {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}
</style>
<div class="card card-outline rounded-0 card-indigo">
	<div class="card-header">
		<h3 class="card-title">Órdenes</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Fecha Creación</th>
						<th>Número de Transacción</th>
						<th>Cola</th>
						<th>Monto Total</th>
						<th>Estado</th>
						<th>Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$uwhere = "";
					if ($_settings->userdata('type') !=  '1')
						$uwhere = " where user_id = '{$_settings->userdata('id')}' ";

					$qry = $conn->query("SELECT * FROM `order_list` {$uwhere} order by abs(unix_timestamp(date_created)) desc ");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
							<td class=""><?= $row['code'] ?></td>
							<td class=""><?= $row['queue'] ?></td>
							<td class="text-right"><?= format_num($row['total_amount'], 2) ?></td>
							<td class="text-center">
								<?php
								switch ($row['status']) {
									case 0:
										echo '<span class="badge badge-primary border-gradient-primary px-3 border">Espera</span>';
										break;
									case 1:
										echo '<span class="badge badge-success border-gradient-success px-3 border">Servido</span>';
										break;
									default:
										echo '<span class="badge badge-light border-gradient-light border px-3 border">N/A</span>';
										break;
								}
								?>
							</td>
							<td class="text-center">
								<div class="btn-group btn-group-sm">
									<a class="btn btn-flat btn-sm btn-light bg-gradient-light border view_receipt" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="Print Receipt"><small><span class="fa fa-receipt"></span></small></a>
									<a class="btn btn-flat btn-sm btn-danger bg-gradient-danger delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="Delete Order"><small><span class="fa fa-trash"></span></small></a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.delete_data').click(function() {
			_conf("¿Deseas eliminar este pedido de forma permanente?", "delete_order", [$(this).attr('data-id')])
		})
		$('.view_receipt').click(function() {
			var nw = window.open(_base_url_ + "admin/sales/receipt.php?id=" + $(this).attr('data-id'), '_blank', "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
			setTimeout(() => {
				nw.print()
				setTimeout(() => {
					nw.close()
					location.reload()
				}, 300);
			}, 200);
		})
		$('.table').dataTable({
			columnDefs: [{
				orderable: false,
				targets: [5]
			}],
			order: [0, 'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})

	function delete_order($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_order",
			method: "POST",
			data: {
				id: $id
			},
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("Ocurrió un error", 'error');
				end_loader();
			},
			success: function(resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("Ocurrió un error", 'error');
					end_loader();
				}
			}
		})
	}
</script>