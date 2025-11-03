<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>
<?= $this->section('content') ?>
<?= $this->include('julio101290\boilerplateproducts\Views/modulesSubcategorias/modalCaptureSubcategorias') ?>

<div class="card card-default">
    <div class="card-header">
        <div class="float-right">
            <div class="btn-group">
                <button class="btn btn-primary btnAddSubcategorias" data-toggle="modal" data-target="#modalAddSubcategorias">
                    <i class="fa fa-plus"></i> <?= lang('subcategorias.add') ?>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tableSubcategorias" class="table table-striped table-hover va-middle tableSubcategorias">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= lang('subcategorias.fields.idEmpresa') ?></th>
                                <th><?= lang('subcategorias.fields.idCategoria') ?></th>
                                <th><?= lang('subcategorias.fields.descripcion') ?></th>
                                <th><?= lang('subcategorias.fields.created_at') ?></th>
                                <th><?= lang('subcategorias.fields.updated_at') ?></th>
                                <th><?= lang('subcategorias.fields.deleted_at') ?></th>

                                <th><?= lang('subcategorias.fields.actions') ?></th>
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
    var tableSubcategorias = $('#tableSubcategorias').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: '<?= base_url('admin/subcategorias') ?>',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [7],
                searchable: false,
                targets: [7]
            }],
        columns: [{'data': 'id'},
            {'data': 'nombreEmpresa'},
            {'data': 'nombreCategoria'},
            {'data': 'descripcion'},
            {'data': 'created_at'},
            {'data': 'updated_at'},
            {'data': 'deleted_at'},

            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                         <div class="btn-group btn-group-sm">
                             <button class="btn btn-warning btnEditSubcategorias" data-toggle="modal" idSubcategorias="${data.id}" data-target="#modalAddSubcategorias">  <i class=" fa fa-edit"></i></button>
                             <button class="btn btn-danger btn-delete" data-id="${data.id}"><i class="fas fa-trash"></i></button>
                         </div>
                         </td>`
                }
            }
        ]
    });

    $(document).on('click', '#btnSaveSubcategorias', function (e) {
        var idSubcategorias = $("#idSubcategorias").val();
        var idEmpresa = $("#idEmpresa").val();
        var idCategoria = $("#idCategoria").val();
        var descripcion = $("#descripcion").val();

        $("#btnSaveSubcategorias").attr("disabled", true);
        var datos = new FormData();
        datos.append("idSubcategorias", idSubcategorias);
        datos.append("idEmpresa", idEmpresa);
        datos.append("idCategoria", idCategoria);
        datos.append("descripcion", descripcion);

        $.ajax({
            url: "<?= base_url('admin/subcategorias/save') ?>",
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
                    tableSubcategorias.ajax.reload();
                    $("#btnSaveSubcategorias").removeAttr("disabled");
                    $('#modalAddSubcategorias').modal('hide');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: respuesta.message || "Error desconocido"
                    });
                    $("#btnSaveSubcategorias").removeAttr("disabled");
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: jqXHR.responseText
            });
            $("#btnSaveSubcategorias").removeAttr("disabled");
        });
    });

    $(".tableSubcategorias").on("click", ".btnEditSubcategorias", function () {
        var idSubcategorias = $(this).attr("idSubcategorias");
        var datos = new FormData();
        datos.append("idSubcategorias", idSubcategorias);
        $.ajax({
            url: "<?= base_url('admin/subcategorias/getSubcategorias') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                $("#idSubcategorias").val(respuesta["id"]);
                $("#idEmpresa").val(respuesta["idEmpresa"]).trigger("change");
               
        
                $("#idCategoria").val(respuesta["idCategoria"]);
                
                
                var newOptionCategory = new Option(respuesta["descriptionCategory"], respuesta["idCategoria"], true, true);
                $('#idCategoria').append(newOptionCategory).trigger('change');
                $("#idCategoria").val(respuesta["idCategoria"]);
                
        
                $("#descripcion").val(respuesta["descripcion"]);

            }
        });
    });

    $(".tableSubcategorias").on("click", ".btn-delete", function () {
        var idSubcategorias = $(this).attr("data-id");
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
                    url: `<?= base_url('admin/subcategorias') ?>/` + idSubcategorias,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: jqXHR.statusText,
                    });
                    tableSubcategorias.ajax.reload();
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
        $("#modalAddSubcategorias").draggable();
    });
</script>
<?= $this->endSection() ?>