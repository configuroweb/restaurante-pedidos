<?php if ($_settings->chk_flashdata('success')) : ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.menu-img {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}
</style>
<div class="card card-outline rounded-0 card-indigo">
	<div class="card-header">
		<h3 class="card-title">Platos Disponibles</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Crear Nuevo</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="30%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Fecha de Creación</th>
						<th>Menú</th>
						<th>Descripción</th>
						<th>Precio</th>
						<th>Estado</th>
						<th>Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT m.*, c.name as `category` from `menu_list` m inner join category_list c on m.category_id = c.id where m.delete_flag = 0 order by m.`code` asc, m.`name` asc ");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
							<td class="">
								<div style="line-height:1em">
									<div><?= $row['code'] ?> - <?= $row['name'] ?></div>
									<div><small class="text-muted"><?= $row['category'] ?></small></div>
								</div>
							</td>
							<td class="">
								<p class="mb-0 truncate-1"><?= strip_tags(htmlspecialchars_decode($row['description'])) ?></p>
							</td>
							<td class="text-center">
								<?php if ($row['status'] == 1) : ?>
									<span class="badge badge-success px-3 rounded-pill">Disponible</span>
								<?php else : ?>
									<span class="badge badge-danger px-3 rounded-pill">Indisponible</span>
								<?php endif; ?>
							</td>
							<td class="text-right"><?= format_num($row['price'], 2) ?></td>
							<td align="center">
								<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Acción
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item view-data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> Ver</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item edit-data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Editar</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Eliminar</a>
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
			_conf("¿Deseas eliminar este menú de forma permanente?", "delete_menu", [$(this).attr('data-id')])
		})
		$('#create_new').click(function() {
			uni_modal("<i class='far fa-plus-square'></i> Agregar nuevo menú ", "menus/manage_menu.php")
		})
		$('.edit-data').click(function() {
			uni_modal("<i class='fa fa-edit'></i> Agregar nuevo menú ", "menus/manage_menu.php?id=" + $(this).attr('data-id'))
		})
		$('.view-data').click(function() {
			uni_modal("<i class='fa fa-th-list'></i> Información Menú ", "menus/view_menu.php?id=" + $(this).attr('data-id'))
		})
		$('.table').dataTable({
			columnDefs: [{
				orderable: false,
				targets: [6]
			}],
			order: [0, 'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})

	function delete_menu($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_menu",
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