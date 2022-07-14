<?php if ($_settings->chk_flashdata('success')) : ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.user-avatar {
		width: 3rem;
		height: 3rem;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="card card-outline rounded-0 card-indigo">
	<div class="card-header">
		<h3 class="card-title">Usuarios</h3>
		<div class="card-tools">
			<a href="./?page=user/manage_user" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Crear Nuevo</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="25%">
					<col width="15%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Fecha Creación</th>
						<th>Avatar</th>
						<th>Nombre</th>
						<th>Usuario</th>
						<th>Tipo</th>
						<th>Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT *, concat(firstname,' ', coalesce(concat(middlename,' '), '') , lastname) as `name` from `users` where id != '{$_settings->userdata('id')}' order by concat(firstname,' ', lastname) asc ");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i", strtotime($row['date_updated'])) ?></td>
							<td class="text-center">
								<img src="<?= validate_image($row['avatar']) ?>" alt="" class="img-thumbnail rounded-circle user-avatar">
							</td>
							<td><?php echo $row['name'] ?></td>
							<td><?php echo $row['username'] ?></td>
							<td class="text-center">
								<?php if ($row['type'] == 1) : ?>
									Administrador
								<?php elseif ($row['type'] == 2) : ?>
									Mesero
								<?php elseif ($row['type'] == 3) : ?>
									Cocina
								<?php else : ?>
									N/A
								<?php endif; ?>
							</td>
							<td align="center">
								<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Acción
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="./?page=user/manage_user&id=<?= $row['id'] ?>"><span class="fa fa-edit text-dark"></span> Editar</a>
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
			_conf("¿Desear eliminar este usuario de forma permanente?", "delete_user", [$(this).attr('data-id')])
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

	function delete_user($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Users.php?f=delete",
			method: "POST",
			data: {
				id: $id
			},
			error: err => {
				console.log(err)
				alert_toast("Ocurrió un error", 'error');
				end_loader();
			},
			success: function(resp) {
				if (resp == 1) {
					location.reload();
				} else {
					alert_toast("Ocurrió un error", 'error');
					end_loader();
				}
			}
		})
	}
</script>