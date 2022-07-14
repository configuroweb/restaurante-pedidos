<?php
require_once('../config.php');
class Master extends DBConnection
{
	private $settings;
	public function __construct()
	{
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	function capture_err()
	{
		if (!$this->conn->error)
			return false;
		else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img()
	{
		extract($_POST);
		if (is_file($path)) {
			if (unlink($path)) {
				$resp['status'] = 'success';
			} else {
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete ' . $path;
			}
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown ' . $path . ' path';
		}
		return json_encode($resp);
	}
	function save_category()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "La categoría ya existe.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `category_list` set {$data} ";
		} else {
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "Nueva categoría guardada con éxito.";
			else
				$resp['msg'] = " Categoría actualizada con éxito.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_category()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Categoría eliminada con éxito.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_menu()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id'))) {
				if (!empty($data)) $data .= ",";
				$v = htmlspecialchars($this->conn->real_escape_string($v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `menu_list` where `code` = '{$code}' and delete_flag = 0 " . (!empty($id) ? " and id != {$id} " : "") . " ")->num_rows;
		if ($this->capture_err())
			return $this->capture_err();
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "El código de menú ya existe.";
			return json_encode($resp);
			exit;
		}
		if (empty($id)) {
			$sql = "INSERT INTO `menu_list` set {$data} ";
		} else {
			$sql = "UPDATE `menu_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if ($save) {
			$iid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['iid'] = $iid;
			$resp['status'] = 'success';
			if (empty($id))
				$resp['msg'] = "Menú creado con éxito.";
			else
				$resp['msg'] = " Menú actualizado con éxito.";
		} else {
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error . "[{$sql}]";
		}
		// if($resp['status'] == 'success')
		// 	$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_menu()
	{
		extract($_POST);
		$del = $this->conn->query("UPDATE `menu_list` set `delete_flag` = 1 where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " Menú eliminado con éxito.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function place_order()
	{
		$prefix = date("Ymd");
		$code = sprintf("%'.05d", 1);
		while (true) {
			$check = $this->conn->query("SELECT * FROM `order_list` where code = '{$prefix}{$code}'")->num_rows;
			if ($check > 0) {
				$code = sprintf("%'.05d", abs($code) + 1);
			} else {
				$_POST['code'] = $prefix . $code;
				$_POST['queue'] = $code;
				break;
			}
		}
		$_POST['user_id'] = $this->settings->userdata('id');
		extract($_POST);
		$order_fields = ['code', 'queue', 'total_amount', 'tendered_amount', 'user_id'];
		$data = "";
		foreach ($_POST as $k => $v) {
			if (in_array($k, $order_fields) && !is_array($_POST[$k])) {
				$v = addslashes(htmlspecialchars($this->conn->real_escape_string($v)));
				if (!empty($data)) $data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}
		$sql = "INSERT INTO `order_list` set {$data}";
		$save = $this->conn->query($sql);
		if ($save) {
			$oid = $this->conn->insert_id;
			$resp['oid'] = $oid;
			$data = '';
			foreach ($menu_id as $k => $v) {
				if (!empty($data)) $data .= ", ";
				$data .= "('{$oid}', '{$menu_id[$k]}', '{$price[$k]}', '{$quantity[$k]}')";
			}
			$sql2 = "INSERT INTO `order_items` (`order_id`, `menu_id`, `price`, `quantity`) VALUES {$data}";
			$save2 = $this->conn->query($sql2);
			if ($save2) {
				$resp['status'] = 'success';
				$resp['msg'] = ' Se ha realizado el pedido.';
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "El pedido no se ha podido guardar por un fallo desconocido. ";
				$resp['err'] = $this->conn->error;
				$resp['sql'] = $sql2;
				$this->conn->query("DELETE FROM `order_list` where id = '{$oid}'");
			}
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Orden no se ha guardado por un fallo desconocido";
			$resp['err'] = $this->conn->error;
			$resp['sql'] = $sql;
		}
		return json_encode($resp);
	}
	function delete_order()
	{
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `order_list` where id = '{$id}'");
		if ($del) {
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', " El pedido se ha eliminado correctamente.");
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function get_order()
	{
		extract($_POST);
		$swhere = "";
		if (isset($listed) && count($listed) > 0) {
			$swhere = " and id not in (" . implode(",", $listed) . ")";
		}
		$orders = $this->conn->query("SELECT id, `queue` FROM `order_list` where `status` = 0 {$swhere} order by abs(unix_timestamp(date_created)) asc limit 10");
		$data = [];
		while ($row = $orders->fetch_assoc()) {
			$items = $this->conn->query("SELECT oi.*, concat(m.code, m.name) as `item` FROM `order_items` oi inner join menu_list m on oi.menu_id = m.id where order_id = '{$row['id']}'");
			$item_arr = [];
			while ($irow = $items->fetch_assoc()) {
				$item_arr[] = $irow;
			}
			$row['item_arr'] = $item_arr;
			$data[] = $row;
		}
		$resp['status'] = 'success';
		$resp['data'] = $data;
		return json_encode($resp);
	}
	function serve_order()
	{
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = 1 where id = '{$id}'");
		if ($update) {
			$resp['status'] = 'success';
		} else {
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
		break;
	case 'save_category':
		echo $Master->save_category();
		break;
	case 'delete_category':
		echo $Master->delete_category();
		break;
	case 'save_menu':
		echo $Master->save_menu();
		break;
	case 'delete_menu':
		echo $Master->delete_menu();
		break;
	case 'place_order':
		echo $Master->place_order();
		break;
	case 'delete_order':
		echo $Master->delete_order();
		break;
	case 'get_order':
		echo $Master->get_order();
		break;
	case 'serve_order':
		echo $Master->serve_order();
		break;
	default:
		// echo $sysset->index();
		break;
}
