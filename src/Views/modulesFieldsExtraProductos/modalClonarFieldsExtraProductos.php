<!-- Modal FieldsExtraProductos -->
<div class="modal fade" id="modalClonarFieldsExtraProductos" tabindex="-1" role="dialog" aria-labelledby="modalClonarFieldsExtraProductos" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('fieldsExtraProductos.createEdit') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-fieldsExtraProductos" class="form-horizontal">
                    <input type="hidden" id="idFieldsExtraProductos" name="idFieldsExtraProductos" value="0">
                    <span class="" style="font-weight: bold;">Origen:</i></span>
                    <br>
                    <div class="form-group designer ctrPrototipo" style="margin-bottom: 10px;">
                        <div class="input-group designer" style=" height: 23.6px; width: 400px;">
                            <span class="designer input-group-addon" style="margin-right:8px;">
                                Empresa:
                            </span>
                            <select class="form-control idEmpresaClonar" name="idEmpresaClonar"  disabled id="idEmpresaClonar" style = "width:80%;">
                                <option value="0">Seleccione empresa</option>
                                <?php
                                foreach ($empresas as $key => $value) {

                                    echo "<option value='$value[id]' selected>$value[id] - $value[nombre] </option>  ";
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="form-group designer ctrPrototipo" style="margin-bottom: 10px;">
                        <div class="input-group designer" style=" height: 23.6px; width: 400px;">
                            <span class="designer input-group-addon" style="margin-right:8px;">
                                Categoria:
                            </span>
                            <select name="idCategoryClonar" id="idCategoryClonar" disabled style="width: 90%;" class="form-control idCategoryClonar form-controlProducts">
                                <option value="0" selected>
                                    <?= lang('products.fields.idSelectCategory') ?>
                                </option>

                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="form-group designer ctrPrototipo"  style="margin-bottom: 8px;">
                        <div class="input-group designer" style=" height: 23.6px; width: 400px;">
                            <span class="designer input-group-addon" style="margin-right:8px;">
                                SubCategoria:
                            </span>
                             <select name="idSubCategoryClonar" id="idSubCategoryClonar"  disabled style="width: 90%;" class="form-control idSubCategoryClonar form-controlProducts">
                                    <option value="0" selected>
                                        <?= lang('products.fields.idSelectSubCategory') ?>
                                    </option>
                                </select>
                        </div>
                    </div>
                    <div>
                        <br>
                    </div>
                    <span class="" style="font-weight: bold;">Destino:</i></span>
                    <br>
                    <div class="form-group row">
                        <label for="emitidoRecibido" class="col-sm-2 col-form-label">Empresa</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>

                                <select class="form-control idEmpresaNew" name="idEmpresaNew" id="idEmpresaNew"  disabled style = "width:80%;">
                                    <option value="0">Seleccione empresa</option>
                                    <?php
                                    foreach ($empresas as $key => $value) {

                                        echo "<option value='$value[id]' selected>$value[id] - $value[nombre] </option>  ";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCategoryNew" class="col-sm-2 col-form-label"><?= lang('fieldsExtraProductos.fields.idCategory') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>

                                <select name="idCategoryNew" id="idCategoryNew" style="width: 90%;" class="form-control idCategoryNew form-controlProducts">
                                    <option value="0" selected>
                                        <?= lang('products.fields.idSelectCategory') ?>
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idSubCategory" class="col-sm-2 col-form-label"><?= lang('fieldsExtraProductos.fields.idSubCategory') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>


                                <select name="idSubCategoryNew" id="idSubCategoryNew" style="width: 90%;" class="form-control idSubCategoryNew form-controlProducts">
                                    <option value="0" selected>
                                        <?= lang('products.fields.idSelectSubCategory') ?>
                                    </option>
                                </select>


                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm" id="btnClonarFieldsExtraProductos"><?= lang('boilerplate.global.save') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>


<script>

    $(document).on('click', '.btnClonarFieldsExtraProductos', function (e) {
        
        $('#idEmpresaClonar').val($('#idEmpresaList').val());
        $('#idEmpresaNew').val($('#idEmpresaList').val());
        //$('#idCategoryClonar').val($('#idCategoryList').val());
        var nameCategory =  $('#idCategoryList').select2('data')[0].text;
        var idCategory =  $('#idCategoryList').val();
        var option = new Option(nameCategory, idCategory, true, true);
        $('#idCategoryClonar').append(option).trigger('change');
        
        var nameSubCategory =  $('#idSubCategoryList').select2('data')[0].text;
        var idSubCategory =  $('#idSubCategoryList').val();
        var optionSub = new Option(nameSubCategory, idSubCategory, true, true);
        $('#idSubCategoryClonar').append(optionSub).trigger('change');
        
        
        $("#btnSaveFieldsExtraProductos").removeAttr("disabled");

    });

    /* 
     * AL hacer click al editar
     */

//
//
//    $(document).on('click', '.btnEditFieldsExtraProductos', function (e) {
//
//
//        var idFieldsExtraProductos = $(this).attr("idFieldsExtraProductos");
//
//        //LIMPIAMOS CONTROLES
//        $(".form-control").val("");
//
//        $("#idFieldsExtraProductos").val(idFieldsExtraProductos);
//        $("#btnGuardarFieldsExtraProductos").removeAttr("disabled");
//
//    });


    /**
     * Categorias por empresa
     */

    $(".idCategoryNew").select2({
        ajax: {
            url: "<?= base_url('admin/categorias/getCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresaClonar').val(); // CSRF hash
                console.log("ddd");
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

    $(".idSubCategoryNew").select2({
        ajax: {
            url: "<?= base_url('admin/subCategorias/getSubCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresaNew').val(); // CSRF hash
                var idCategoria = $('.idCategoryNew').val(); // CSRF hash

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

    $(document).on('click', '#btnClonarFieldsExtraProductos', function (e) {
        var idEmpresaClonar = $("#idEmpresaClonar").val();
        var idCategoryClonar = $("#idCategoryClonar").val();
        var idSubCategoryClonar = $("#idSubCategoryClonar").val();
        var idEmpresaNew = $("#idEmpresaNew").val();
        var idCategoryNew = $("#idCategoryNew").val();
        var idSubCategoryNew = $("#idSubCategoryNew").val();

//        $("#btnClonarFieldsExtraProductos").attr("disabled", true);
        var datos = new FormData();
        datos.append("idEmpresaClonar", idEmpresaClonar);
        datos.append("idCategoryClonar", idCategoryClonar);
        datos.append("idSubCategoryClonar", idSubCategoryClonar);
        datos.append("idEmpresaNew", idEmpresaNew);
        datos.append("idCategoryNew", idCategoryNew);
        datos.append("idSubCategoryNew", idSubCategoryNew);

        $.ajax({
            url: "<?= base_url('admin/fieldsExtraProductos/saveClonar') ?>",
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
                    $("#btnClonarFieldsExtraProductos").removeAttr("disabled");
                    $('#modalClonarFieldsExtraProductos').modal('hide');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: respuesta.message || "Error desconocido"
                    });
                    $("#btnClonarFieldsExtraProductos").removeAttr("disabled");
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
//    $(document).on('click', '.btnAddFieldsExtraProductos', function (e) {
//        
//        $("#idCategory").val("0");
//        $("#idCategory").trigger("change");
//
//        $("#idSubCategory").val("0");
//        $("#idSubCategory").trigger("change");
//
//        $("#idEmpresa").val("0");
//        $("#idEmpresa").trigger("change");
//
//    });

    $("#idEmpresa").select2();

    $(".type").on("change", function () {

        var value = $(this).val();
        console.log(value);

        if (value == 2) {

            $(".classOptions").show();     // lo muestra

        } else {

            $(".classOptions").hide();     // lo oculta

        }

    });

    $(".options").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });
    $(".idEmpresa").change(function () {
        $("#idCategory").val("0").trigger("change.select2");
    });
    $(".idCategory").change(function () {
        $("#idSubCategory").val("0").trigger("change.select2");
    });

</script>


<?= $this->endSection() ?>
        