<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>
<?= $this->section('content') ?>
<?= $this->include('julio101290\boilerplateproducts\Views\modulesFieldsExtraProductos/modalCaptureFieldsExtraProductos') ?>
<?= $this->include('julio101290\boilerplateproducts\Views\modulesFieldsExtraProductos/modalClonarFieldsExtraProductos') ?>
<div class="card card-default">
    <div class="card-header">
        <div class="float-left">

            <div class="btn-group">

                <div class="form-group">
                    <label for="idEmpresaList">Empresa </label>
                    <select class="form-control idEmpresaList" name="idEmpresaList" id="idEmpresaList" style = "width:80%;">
                        <option value="0">Seleccione empresa</option>
                        <?php
                        foreach ($empresas as $key => $value) {

                            echo "<option value='$value[id]' selected>$value[id] - $value[nombre] </option>  ";
                        }
                        ?>

                    </select>
                </div>

            </div>


            <div class="btn-group">

                <div class="form-group">
                    <label for="idCategoryList"><?= lang('fieldsExtraProductos.fields.idCategory') ?></label>
                    <select name="idCategoryList" id="idCategoryList" style="width: 90%;" class="form-control idCategoryList form-controlProducts">
                        <option value="0" selected>
                            <?= lang('products.fields.idSelectCategory') ?>
                        </option>

                    </select>
                </div>

            </div>

            <div class="btn-group">
                <div class="form-group">
                    <label for="idSubCategoryList"><?= lang('fieldsExtraProductos.fields.idSubCategory') ?></label>

                    <select name="idSubCategoryList" id="idSubCategoryList" style="width: 90%;" class="form-control idSubCategoryList form-controlProducts">
                        <option value="0" selected>
                            <?= lang('products.fields.idSelectSubCategory') ?>
                        </option>
                    </select>
                </div>
            </div>

            <div class="btn-group" style="margin-right:10px;">
                <button type="button" class="btn btn-success btnAceptar" id="btnAceptar" name="btnAceptar"><i class="fa fa-check"></i></button>
            </div>
            <div class="btn-group">
 
                 <button type="button" class="btn btn-primary btnClonarFieldsExtraProductos" data-toggle="modal" data-target="#modalClonarFieldsExtraProductos"><i class="fa fa-copy "></i>  Clonar</button>
            </div>

        </div>
        <div class="float-right">
            <div class="btn-group">
                <button class="btn btn-primary btnAddFieldsExtraProductos" data-toggle="modal" data-target="#modalAddFieldsExtraProductos">
                    <i class="fa fa-plus"></i> <?= lang('fieldsExtraProductos.add') ?>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tableFieldsExtraProductos" class="table table-striped table-hover va-middle tableFieldsExtraProductos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= lang('fieldsExtraProductos.fields.idEmpresa') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.idCategory') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.idSubCategory') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.type') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.description') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.created_at') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.updated_at') ?></th>
                                <th><?= lang('fieldsExtraProductos.fields.deleted_at') ?></th>

                                <th><?= lang('fieldsExtraProductos.fields.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script>
    $(".btnAceptar").on("click", function () {
        //RESETEAR EL COLAPSO DE LA TABLA
        collapsedGroups = {};
        ttop = '';
        fncAceptar();
        
    })
    function fncAceptar() {
        var idEmpresa = $('#idEmpresaList').val();
        var idCategoria = $('#idCategoryList').val();
        var idSubCategoria = $('#idSubCategoryList').val();
        console.log("dneajfn");
      
        
        tableFieldsExtraProductos.ajax.url(`<?= base_url('admin/fieldsExtraProductos') ?>/` + idEmpresa + '/' + idCategoria + '/' + idSubCategoria).load();
   }
    var tableFieldsExtraProductos = $('#tableFieldsExtraProductos').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: '<?= base_url('admin/fieldsExtraProductos') ?>',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [9],
                searchable: false,
                targets: [9]
            }],
        columns: [{'data': 'id'},
            {'data': 'nombreEmpresa'},
            {'data': 'descripcionCategoria'},
            {'data': 'descripcionSubCategoria'},
            {'data': 'type'},
            {'data': 'description'},
            {'data': 'created_at'},
            {'data': 'updated_at'},
            {'data': 'deleted_at'},

            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                         <div class="btn-group btn-group-sm">
                             <button class="btn btn-warning btnEditFieldsExtraProductos" data-toggle="modal" idFieldsExtraProductos="${data.id}" data-target="#modalAddFieldsExtraProductos">  <i class=" fa fa-edit"></i></button>
                             <button class="btn btn-danger btn-delete" data-id="${data.id}"><i class="fas fa-trash"></i></button>
                         </div>
                         </td>`
                }
            }
        ]
    });

    $(document).on('click', '#btnSaveFieldsExtraProductos', function (e) {
        var idFieldsExtraProductos = $("#idFieldsExtraProductos").val();
        var idEmpresa = $("#idEmpresa").val();
        var idCategory = $("#idCategory").val();
        var idSubCategory = $("#idSubCategory").val();
        var type = $("#type").val();
        var options = $("#options").val();
        var description = $("#description").val();

        $("#btnSaveFieldsExtraProductos").attr("disabled", true);
        var datos = new FormData();
        datos.append("idFieldsExtraProductos", idFieldsExtraProductos);
        datos.append("idEmpresa", idEmpresa);
        datos.append("idCategory", idCategory);
        datos.append("idSubCategory", idSubCategory);
        datos.append("type", type);
        datos.append("options", options);
        datos.append("description", description);

        $.ajax({
            url: "<?= base_url('admin/fieldsExtraProductos/save') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                if (respuesta?.message?.includes("Guardado") || respuesta?.message?.includes("Actualizado")) {
                    Toast.fire({
                        icon: 'success',
                        title: respuesta.message
                    });
                    tableFieldsExtraProductos.ajax.reload();
                    $("#btnSaveFieldsExtraProductos").removeAttr("disabled");
                    $('#modalAddFieldsExtraProductos').modal('hide');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: respuesta.message || "Error desconocido"
                    });
                    $("#btnSaveFieldsExtraProductos").removeAttr("disabled");
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: jqXHR.responseText
            });
            $("#btnSaveFieldsExtraProductos").removeAttr("disabled");
        });
    });

    
    
    $(".tableFieldsExtraProductos").on("click", ".btnEditFieldsExtraProductos", function () {
        var idFieldsExtraProductos = $(this).attr("idFieldsExtraProductos");
        var datos = new FormData();
        datos.append("idFieldsExtraProductos", idFieldsExtraProductos);
        $.ajax({
            url: "<?= base_url('admin/fieldsExtraProductos/getFieldsExtraProductos') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                $("#idFieldsExtraProductos").val(respuesta["id"]);
                $("#idEmpresa").val(respuesta["idEmpresa"]).trigger("change");

                var newOptionCategory = new Option(respuesta["descripcionCategoria"], respuesta["idCategory"], true, true);
                $('#idCategory').append(newOptionCategory).trigger('change');
                $("#idCategory").val(respuesta["idCategory"]);

                var newOptionSubCategory = new Option(respuesta["descripcionSubCategoria"], respuesta["idSubCategory"], true, true);
                $('#idSubCategory').append(newOptionSubCategory).trigger('change');
                $("#idSubCategory").val(respuesta["idSubCategory"]);


                $("#type").val(respuesta["type"]);



                var options = respuesta["options"];


                $('#options').empty();

                var arr = options.split(',');

                $.each(arr, function (index, value) {

                    var newOption = new Option(value, value);


                    $('#options').append(newOption).trigger('change');

                });

                $('#options option').prop('selected', true);


                $("#description").val(respuesta["description"]);


                var value = respuesta["type"];


                if (value == 2) {

                    $(".classOptions").show();     // lo muestra

                } else {

                    $(".classOptions").hide();     // lo oculta

                }

            }
        });
    });

    $(".tableFieldsExtraProductos").on("click", ".btn-delete", function () {
        var idFieldsExtraProductos = $(this).attr("data-id");
        Swal.fire({
            title: '<?= lang('boilerplate.global.sweet.title') ?>',
            text: "<?= lang('boilerplate.global.sweet.text') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: `<?= base_url('admin/fieldsExtraProductos') ?>/` + idFieldsExtraProductos,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: jqXHR.statusText,
                    });
                    tableFieldsExtraProductos.ajax.reload();
                }).fail((error) => {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON.messages.error,
                    });
                });
            }
        });
    });

    /**
     * Categorias por empresa
     */

    $(".idCategoryList").select2({
        ajax: {
            url: "<?= base_url('admin/categorias/getCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresaList').val(); // CSRF hash

                return {
                    searchTerm: params.term, // search term
                    [csrfName]: csrfHash, // CSRF Token
                    idEmpresa: idEmpresa // search term
                };
            },
            processResults: function (response) {

                // Update CSRF Token
                $('.txt_csrfname').val(response.token);

                return {
                    results: response.data
                };
            },
            cache: true
        }
    });

    $(".idSubCategoryList").select2({
        ajax: {
            url: "<?= base_url('admin/subCategorias/getSubCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresaList').val(); // CSRF hash
                var idCategoria = $('.idCategoryList').val(); // CSRF hash

                return {
                    searchTerm: params.term, // search term
                    [csrfName]: csrfHash, // CSRF Token
                    idEmpresa: idEmpresa, // search term
                    idCategoria: idCategoria // category
                };
            },
            processResults: function (response) {

                // Update CSRF Token
                $('.txt_csrfname').val(response.token);

                return {
                    results: response.data
                };
            },
            cache: true
        }
    });

    $("#idEmpresaList").select2();
    $(".type").on("change", function () {

        var value = $(this).val();
        console.log(value);

        if (value == 2) {

            $(".classOptions").show();     // lo muestra

        } else {

            $(".classOptions").hide();     // lo oculta

        }

    });
    $(function () {
        $("#modalAddFieldsExtraProductos").draggable();
    });
    
    $(".idEmpresaList").change(function () {
        $("#idCategoryList").val("0").trigger("change.select2");
    });
     $(".idCategoryList").change(function () {
        $("#idSubCategoryList").val("0").trigger("change.select2");
    });
    
    $(".btnClonarFieldsExtraProductos").on("click", function () {
        
        console.log("aaa");
//        var tipo = $('#rTipoObraTMP').val();
//
//        if (tipo == 'rArea') {
        $('#inputEmpresa').val($('#idEmpresaList').val());
//            $('.ctrArea #input2').val($('#area').val());
//            console.log(tipo);
//        } else {
//            $('#input1').val($('#prototipo').val());
//            $('#input2').val($('#version').val());
//        }
//        $('#input3').val($('#etapa').val());
//        $('#input4').val($('#concepto').val());
    })
</script>
<?= $this->endSection() ?>