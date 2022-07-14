<?php if ($_settings->chk_flashdata('success')) : ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.enroll-logo {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}
</style>
<div class="card card-outline rounded-0 card-indigo">
	<div class="card-header">
		<h3 class="card-title">Stock Manager</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="30%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Item</th>
						<th>Unit</th>
						<th>Available</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT i.*, c.name as `category`,( COALESCE((SELECT SUM(quantity) FROM `stockin_list` where item_id = i.id),0) - COALESCE((SELECT SUM(quantity) FROM `stockout_list` where item_id = i.id),0) - COALESCE((SELECT SUM(quantity) FROM `waste_list` where item_id = i.id),0) ) as `available` from `item_list` i inner join category_list c on i.category_id = c.id where i.delete_flag = 0 order by i.`name` asc ");
					while ($row = $qry->fetch_assoc()) :
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
							<td class="">
								<div style="line-height:1em">
									<div><?= $row['name'] ?> [<?= $row['unit'] ?>]</div>
									<div><small class="text-muted"><?= $row['category'] ?></small></div>
								</div>
							</td>
							<td class=""><?= $row['unit'] ?></td>
							<td class="text-right"><?= format_num($row['available']) ?></td>
							<td align="center">
								<a class="btn btn-flat btn-sm btn-light bg-gradient-light border" href="./?page=stocks/view_stock&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
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
			_conf("Are you sure to delete this enroll permanently?", "delete_enroll", [$(this).attr('data-id')])
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

	function delete_enroll($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_enroll",
			method: "POST",
			data: {
				id: $id
			},
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function(resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>