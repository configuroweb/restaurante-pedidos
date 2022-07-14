<style>
  #system-cover {
    width: 100%;
    height: 45em;
    object-fit: cover;
    object-position: center center;
  }
</style>
<h1 class="">Hola de Nuevo, <?php echo $_settings->userdata('firstname') . " " . $_settings->userdata('lastname') ?>!</h1>
<hr>
<div class="row">
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-light elevation-1"><i class="fas fa-th-list"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Categorías</span>
        <span class="info-box-number text-right h5">
          <?php
          $category = $conn->query("SELECT * FROM category_list where delete_flag = 0 and `status` = 1")->num_rows;
          echo format_num($category);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-4 col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-indigo elevation-1"><i class="fas fa-hamburger"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Menú</span>
        <span class="info-box-number text-right h5">
          <?php
          $menus = $conn->query("SELECT id FROM menu_list where delete_flag = 0 and `status` = 1")->num_rows;
          echo format_num($menus);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <?php if ($_settings->userdata('type') != 2) : ?>
    <div class="col-12 col-sm-4 col-md-4">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-table"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Órdenes en Cola</span>
          <span class="info-box-number text-right h5">
            <?php
            $orders = $conn->query("SELECT id FROM order_list where `status` = 0")->num_rows;
            echo format_num($orders);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  <?php endif; ?>

  <?php if ($_settings->userdata('type') == 1) : ?>
    <div class="col-12 col-sm-4 col-md-4">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-indigo elevation-1"><i class="fas fa-th-list"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total de Ventas de Hoy</span>
          <span class="info-box-number text-right h5">
            <?php
            $orders = $conn->query("SELECT coalesce(SUM(total_amount),0) FROM order_list where date(`date_created`) = '" . (date('Y-m-d')) . "'")->fetch_array()[0];
            $orders = $orders > 0 ? $orders : 0;
            echo format_num($orders, 2);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  <?php endif; ?>

  <?php if ($_settings->userdata('type') == 2) : ?>
    <div class="col-12 col-sm-4 col-md-4">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-indigo elevation-1"><i class="fas fa-th-list"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total de Ventas Hoy</span>
          <span class="info-box-number text-right h5">
            <?php
            $orders = $conn->query("SELECT coalesce(SUM(total_amount),0) FROM order_list where date(`date_created`) = '" . (date('Y-m-d')) . "' and user_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
            $orders = $orders > 0 ? $orders : 0;
            echo format_num($orders, 2);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  <?php endif; ?>

</div>
<div class="container-fluid text-center">
  <img src="<?= validate_image($_settings->info('cover')) ?>" alt="system-cover" id="system-cover" class="img-fluid">
</div>