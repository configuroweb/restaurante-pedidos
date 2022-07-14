<style>
    #pos-field {
        height: 54em;
        display: flex;
    }

    #menu-list {
        width: 65%;
        height: 100%;
        overflow: auto;
    }

    #order-list {
        width: 35%;
        height: 100%;
        overflow: auto;
    }

    #cat-list {
        height: 4em !important;
        overflow: auto;
        display: flex;
    }

    #item-list {
        height: 40em !important;
    }

    #item-list.empty-data {
        width: 100%;
        align-items: center;
        justify-content: center;
        display: flex;
    }

    #item-list.empty-data:after {
        content: 'La categoría seleccionada aún no tiene elementos disponibles.';
        color: #b7b4b4;
        font-size: 1.7em;
        font-style: italic;
    }

    div#order-items-holder {
        height: 38em !important;
        overflow: auto;
        position: relative;
    }

    div#order-items-header {
        position: sticky !important;
        top: 0;
        z-index: 1;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<div class="content bg-gradient-indigo py-3 px-4">
    <h3 class="font-weight-bolder text-light">Generar Orden</h3>
</div>
<div class="row mt-n4 justify-content-center">
    <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
        <div class="card rounded-0">
            <div class="card-body">
                <form action="" id="sales-form">
                    <input type="hidden" name="total_amount" value="0">
                    <div id="pos-field">
                        <div id="menu-list">
                            <fieldset>
                                <legend>Categorías</legend>
                                <div id="cat-list" class="py-1">
                                    <?php
                                    $cid = '';
                                    $categories = $conn->query("SELECT * FROM `category_list` where  delete_flag = 0 and `status` = 1 order by `name` asc");
                                    while ($row = $categories->fetch_assoc()) :
                                        if (empty($cid)) {
                                            $cid = $row['id'];
                                        }
                                    ?>
                                        <button class="btn btn-default btn-xs rounded-pill px-2 cat_btn mx-3 col-lg-3 col-md-4 col-sm-6 col-xs-10 <?= isset($cid) && $cid == $row['id'] ? "bg-gradient-indigo text-light" : "bg-gradient-light border" ?>" type="button" data-id='<?= $row['id'] ?>'><?= $row['name'] ?></button>
                                    <?php endwhile; ?>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Menú</legend>
                                <div id="item-list" class="py-1 overflow-auto">
                                    <div class="row row-cols-xl-3 row-cols-md-2 row-cols-sm-1 gy-2 gx-2">
                                        <?php
                                        $menus = $conn->query("SELECT * FROM `menu_list` where  delete_flag = 0 and `status` = 1 order by `name` asc");
                                        while ($row = $menus->fetch_assoc()) :

                                        ?>
                                            <div class="col <?= isset($cid) && $cid == $row['category_id'] ? "" : "d-none" ?> menu-item" data-cat-id='<?= $row['category_id'] ?>'>
                                                <button class="btn btn-default btn-block btn-xs rounded-pill px-2 bg-gradient-light border my item-btn" type="button" data-id='<?= $row['id'] ?>' data-price='<?= $row['price'] ?>'>
                                                    <p class="m-0 truncate-1"><?= $row['code'] . " - " . $row['name'] ?></p>
                                                </button>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="text-center py-2">
                                <button class="btn btn-indigo bg-gradient-indigo rounded-pill px-4">Ordenar</button>
                            </div>
                        </div>
                        <div id="order-list" class="bg-gradient-dark p-1">
                            <h4><b>Órdenes</b></h4>
                            <div id="order-items-holder" class="bg-gradient-light mb-3">
                                <div id="order-items-header">
                                    <div class="d-flex w-100 bg-gradient-indigo">
                                        <div class="col-3 text-center font-weight-bolder m-0 border">Cantidad</div>
                                        <div class="col-6 text-center font-weight-bolder m-0 border">Menú</div>
                                        <div class="col-3 text-center font-weight-bolder m-0 border">Total</div>
                                    </div>
                                </div>
                                <div id="order-items-body"></div>
                            </div>
                            <div class="d-flex w-100 mb-2">
                                <h3 class="col-5 mb-0">Total</h3>
                                <h3 class="col-7 mb-0 bg-gradient-light rounded-0 text-right" id="grand_total">0.00</h3>
                            </div>
                            <div class="d-flex w-100 mb-2">
                                <h3 class="col-5 mb-0">Pagado</h3>
                                <h3 class="col-7 mb-0 bg-gradient-light rounded-0 text-right px-0"><input type="number" step="any" name="tendered_amount" class="form-control rounded-0 text-right bg-gradient-light font-weight-bolder" style="font-size:1em" required value="0"></h3>
                            </div>
                            <div class="d-flex w-100 mb-2">
                                <h3 class="col-5 mb-0">Cambio</h3>
                                <h3 class="col-7 mb-0 bg-gradient-light rounded-0 text-right" id="change">0.00</h3>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<noscript id="item-clone">
    <div class="d-flex w-100 bg-gradient-light product-item">
        <div class="col-3 text-center font-weight-bolder m-0 border align-middle">
            <input type="hidden" name="menu_id[]" value="">
            <input type="hidden" name="price[]" value="">
            <div class="input-group input-group-sm">
                <button class="btn btn-indigo btn-xs btn-flat minus-qty" type="button"><i class="fa fa-minus"></i></button>
                <input type="number" min='1' value='1' name="quantity[]" class="form-control form-control-xs rounded-0 text-center" required readonly>
                <button class="btn btn-indigo btn-xs btn-flat plus-qty" type="button"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="col-6 font-weight-bolder m-0 border align-middle">
            <div style="line-height:1em" class="text-sm">
                <div class="w-100 d-flex aling-items-center"><a href="javascript:void(0)" class="text-danger text-decoration-none rem-item mr-1"><i class="fa fa-times"></i></a>
                    <p class="m-0 truncate-1 menu_name">Menu name</p>
                </div>
                <div><small class="text-muted menu_price">x 0.00</small></div>
            </div>
        </div>
        <div class="col-3 font-weight-bolder m-0 border align-middle text-right menu_total">0.00</div>
    </div>
</noscript>
<script>
    function calc_total() {
        var gt = 0;
        $('#order-items-body .product-item').each(function() {
            var total = 0;
            var price = $(this).find('input[name="price[]"]').val()
            price = price > 0 ? price : 0;
            var qty = $(this).find('input[name="quantity[]"]').val()
            qty = qty > 0 ? qty : 0;
            total = parseFloat(price) * parseFloat(qty)
            gt += parseFloat(total)
            $(this).find('.menu_total').text(parseFloat(total).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }))
        })
        $('[name="total_amount"]').val(gt).trigger('change')
        $('#grand_total').text(parseFloat(gt).toLocaleString('en-US', {
            style: 'decimal',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }))

    }
    $(function() {
        $('body').addClass('sidebar-collapse')
        if ($('#item-list>.row>.col:visible').length > 0) {
            if ($('#item-list').hasClass('empty-data') == true) {
                $('#item-list').removeClass('empty-data');
            }
        } else {
            if ($('#item-list').hasClass('empty-data') == false) {
                $('#item-list').addClass('empty-data');
            }
        }
        $('.cat_btn').click(function() {
            $('.cat_btn.bg-gradient-indigo').removeClass('bg-gradient-indigo text-light').addClass('bg-gradient-light border')
            $(this).removeClass('bg-gradient-light border').addClass('bg-gradient-indigo text-light')
            var id = $(this).attr('data-id')
            $('.menu-item').addClass('d-none')
            $('.menu-item[data-cat-id="' + id + '"]').removeClass('d-none')
            if ($('#item-list>.row>.col:visible').length > 0) {
                if ($('#item-list').hasClass('empty-data') == true) {
                    $('#item-list').removeClass('empty-data');
                }
            } else {
                if ($('#item-list').hasClass('empty-data') == false) {
                    $('#item-list').addClass('empty-data');
                }
            }
        })

        $('.item-btn').click(function() {
            var id = $(this).attr('data-id')
            var price = $(this).attr('data-price')
            var name = $(this).text().trim()
            var item = $($('noscript#item-clone').html()).clone()
            if ($('#order-items-body .product-item[data-id="' + id + '"]').length > 0) {
                item = $('#order-items-body .product-item[data-id="' + id + '"]')
                var qty = item.find('input[name="quantity[]"]').val()
                qty = qty > 0 ? qty : 0;
                qty = parseInt(qty) + 1;
                item.find('input[name="quantity[]"]').val(qty)
                calc_total()
                return false;
            }
            item.attr('data-id', id)
            item.find('input[name="menu_id[]"]').val(id)
            item.find('input[name="price[]"]').val(price)
            item.find('.menu_name').text(name)
            item.find('.menu_price').text("x " + (parseFloat(price).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })))
            item.find('.menu_total').text((parseFloat(price).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })))
            $('#order-items-body').append(item)
            calc_total()
            item.find('.minus-qty').click(function() {
                var qty = item.find('input[name="quantity[]"]').val()
                qty = qty > 0 ? qty : 0;
                qty = qty == 1 ? 1 : parseInt(qty) - 1
                item.find('input[name="quantity[]"]').val(qty)
                calc_total()
            })
            item.find('.plus-qty').click(function() {
                var qty = item.find('input[name="quantity[]"]').val()
                qty = qty > 0 ? qty : 0;
                qty = parseInt(qty) + 1
                item.find('input[name="quantity[]"]').val(qty)
                calc_total()
            })
            item.find('.rem-item').click(function() {
                if (confirm("¿Deseas eliminar esta comida?") == true) {
                    item.remove()
                    calc_total()
                }
            })
        })
        $('input[name="tendered_amount"], input[name="total_amount"]').on('input change', function() {
            var total = $('input[name="total_amount"]').val()
            var tendered = $('input[name="tendered_amount"]').val()
            total = total > 0 ? total : 0;
            tendered = tendered > 0 ? tendered : 0;
            var change = parseFloat(tendered) - parseFloat(total)
            $('#change').text(parseFloat(change).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }))
        })
        $('#sales-form').submit(function(e) {
            e.preventDefault()
            if ($('#order-items-body .product-item').length <= 0) {
                alert_toast("Agrega al menos 1 comida", "indigo")
                return false;
            }
            if (parseFloat($('input[name="tendered_amount"]').val()) < parseFloat($('input[name="total_amount"]').val())) {
                alert_toast("Monto Pagado Inválido.", "error")
                return false;
            }
            start_loader()
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=place_order",
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("Ocurrió un error", "error")
                    end_loader()
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        alert_toast(resp.msg, 'success')
                        // location.reload()
                        setTimeout(() => {
                            var nw = window.open(_base_url_ + "admin/sales/receipt.php?id=" + resp.oid, '_blank', "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
                            setTimeout(() => {
                                nw.print()
                                setTimeout(() => {
                                    nw.close()
                                    location.reload()
                                }, 300);
                            }, 200);
                        }, 200);
                    } else if (!!resp.msg) {
                        alert_toast(resp.msg, 'error')
                    } else {
                        alert_toast(resp.msg, 'error')
                    }
                    end_loader()
                }
            })
        })
    })
</script>