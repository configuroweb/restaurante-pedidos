<?php
require_once('./../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT m.*, c.name as `category` from `menu_list` m inner join category_list c on m.category_id = c.id where m.id = '{$_GET['id']}' and m.delete_flag = 0 ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	} else {
		echo '<script>alert("ID menú no es válido"); location.replace("./?page=menus")</script>';
	}
} else {
	echo '<script>alert("ID menú es requerido"); location.replace("./?page=menus")</script>';
}
?>
<style>
	#uni_modal .modal-footer {
		display: none;
	}
</style>
<div class="container-fluid">
	<dl>
		<dt class="text-muted">Categoría</dt>
		<dd class="pl-4"><?= isset($category) ? $category : "" ?></dd>
		<dt class="text-muted">Código</dt>
		<dd class="pl-4"><?= isset($code) ? $code : "" ?></dd>
		<dt class="text-muted">Nombre</dt>
		<dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
		<dt class="text-muted">Precio</dt>
		<dd class="pl-4"><?= isset($price) ? format_num($price, 2) : "" ?></dd>
		<dt class="text-muted">Descripción</dt>
		<dd class="pl-4"><?= isset($description) ? str_replace(["\n\r", "\n", "\r"], "<br>", htmlspecialchars_decode($description)) : '' ?></dd>
		<dt class="text-muted">Estado</dt>
		<dd class="pl-4">
			<?php if ($status == 1) : ?>
				<span class="badge badge-success px-3 rounded-pill">Disponible</span>
			<?php else : ?>
				<span class="badge badge-danger px-3 rounded-pill">Indisponible</span>
			<?php endif; ?>
		</dd>
	</dl>
</div>
<hr class="mx-n3">
<div class="text-right pt-1">
	<button class="btn btn-sm btn-flat btn-light bg-gradient-light border" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
</div>