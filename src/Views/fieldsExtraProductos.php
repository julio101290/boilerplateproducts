<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>
<?= $this->section('content') ?>
<?= $this->include('julio101290\boilerplateproducts\Views\modulesFieldsExtraProductos/modalCaptureFieldsExtraProductos') ?>
<div class="card card-default">
    <div class="card-header">
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

    $(function () {
        $("#modalAddFieldsExtraProductos").draggable();
    });
</script>
<?= $this->endSection() ?>