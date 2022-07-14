<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `menu_list` where id = '{$_GET['id']}' and `delete_flag` = 0 ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	}
}
?>
<div class="container-fluid">
	<form action="" id="menu-form">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="category_id" class="control-label">Categoría</label>
			<select name="category_id" id="category_id" class="form-control form-control-sm rounded-0" required="required">
				<option value="" <?= isset($category_id) ? 'selected' : '' ?>></option>
				<?php
				$categories = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1 ");
				while ($row = $categories->fetch_assoc()) :
				?>
					<option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="code" class="control-label">Código</label>
			<input type="text" name="code" id="code" class="form-control form-control-sm rounded-0" value="<?php echo isset($code) ? $code : ''; ?>" required />
		</div>
		<div class="form-group">
			<label for="name" class="control-label">Nombre</label>
			<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
		</div>
		<div class="form-group">
			<label for="price" class="control-label">Precio</label>
			<input type="number" step="any" name="price" id="price" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($price) ? $price : ''; ?>" required />
		</div>
		<div class="form-group">
			<label for="description" class="control-label">Descripción</label>
			<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? $description : ''; ?></textarea>
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Estado</label>
			<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
				<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Disponible</option>
				<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Indisponible</option>
			</select>
		</div>
	</form>
</div>
<script>
	$(document).ready(function() {
		$('#uni_modal').on('shown.bs.modal', function() {
			$('#category_id').select2({
				placeholder: "Selecciona Categoría aquí",
				width: '100%',
				containerCssClass: 'form-control form-control-sm rounded-0',
				dropdownParent: $('#uni_modal')
			})
		})
		$('#menu-form').submit(function(e) {
			e.preventDefault();
			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_menu",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err)
					alert_toast("Ocurrió un error", 'error');
					end_loader();
				},
				success: function(resp) {
					if (typeof resp == 'object' && resp.status == 'success') {
						// location.reload()
						alert_toast(resp.msg, 'success')
						uni_modal("<i class='fa fa-th-list'></i> Información Menú ", "menus/view_menu.php?id=" + resp.iid)
						$('#uni_modal').on('hide.bs.modal', function() {
							location.reload()
						})
					} else if (resp.status == 'failed' && !!resp.msg) {
						var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$("html, body").scrollTop(0);
						end_loader()
					} else {
						alert_toast("Ocurrió un error", 'error');
						end_loader();
						console.log(resp)
					}
				}
			})
		})

	})
</script>