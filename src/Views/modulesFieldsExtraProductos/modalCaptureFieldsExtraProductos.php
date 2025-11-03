<!-- Modal FieldsExtraProductos -->
<div class="modal fade" id="modalAddFieldsExtraProductos" tabindex="-1" role="dialog" aria-labelledby="modalAddFieldsExtraProductos" aria-hidden="true">
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

                    <div class="form-group row">
                        <label for="emitidoRecibido" class="col-sm-2 col-form-label">Empresa</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>

                                <select class="form-control idEmpresa" name="idEmpresa" id="idEmpresa" style = "width:80%;">
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
                        <label for="idCategory" class="col-sm-2 col-form-label"><?= lang('fieldsExtraProductos.fields.idCategory') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>

                                <select name="idCategory" id="idCategory" style="width: 90%;" class="form-control idCategory form-controlProducts">
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


                                <select name="idSubCategory" id="idSubCategory" style="width: 90%;" class="form-control idSubCategory form-controlProducts">
                                    <option value="0" selected>
                                        <?= lang('products.fields.idSelectSubCategory') ?>
                                    </option>
                                </select>


                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-sm-2 col-form-label"><?= lang('fieldsExtraProductos.fields.type') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>

                                <select name="type" id="type" style="width: 90%;" class="form-control type form-controlProducts">
                                    <option value="1" selected>
                                        Text
                                    </option>

                                    <option value="2" selected>
                                        ComboBox
                                    </option>
                                </select>   
                            </div>
                        </div>
                    </div>

                    <div class="form-group row classOptions" style="display:none;">
                        <label for="type" class="col-sm-2 col-form-label"><?= lang('fieldsExtraProductos.fields.options') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>

                                <select name="options" id="options" multiple="multiple" style="width: 90%;" class="form-control options form-controlProducts">

                                </select>   
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label"><?= lang('fieldsExtraProductos.fields.description') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="description" id="description" class="form-control <?= session('error.description') ? 'is-invalid' : '' ?>" value="<?= old('description') ?>" placeholder="<?= lang('fieldsExtraProductos.fields.description') ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveFieldsExtraProductos"><?= lang('boilerplate.global.save') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>


<script>

    $(document).on('click', '.btnAddFieldsExtraProductos', function (e) {


        $(".form-control").val("");

        $("#idFieldsExtraProductos").val("0");

        $("#btnSaveFieldsExtraProductos").removeAttr("disabled");

    });

    /* 
     * AL hacer click al editar
     */



    $(document).on('click', '.btnEditFieldsExtraProductos', function (e) {


        var idFieldsExtraProductos = $(this).attr("idFieldsExtraProductos");

        //LIMPIAMOS CONTROLES
        $(".form-control").val("");

        $("#idFieldsExtraProductos").val(idFieldsExtraProductos);
        $("#btnGuardarFieldsExtraProductos").removeAttr("disabled");

    });


    /**
     * Categorias por empresa
     */

    $(".idCategory").select2({
        ajax: {
            url: "<?= base_url('admin/categorias/getCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresa').val(); // CSRF hash

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

    $(".idSubCategory").select2({
        ajax: {
            url: "<?= base_url('admin/subCategorias/getSubCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresa').val(); // CSRF hash
                var idCategoria = $('.idCategory').val(); // CSRF hash

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

    $("#idEmpresa").select2();
    
    $(".type").on("change",function(){
    
        var value = $(this).val();
        console.log(value);
        
        if(value == 2){
            
            $(".classOptions").show();     // lo muestra
        
        }else{
            
            $(".classOptions").hide();     // lo oculta
        
        }
    
    });
    
      $(".options").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });

</script>


<?= $this->endSection() ?>
        