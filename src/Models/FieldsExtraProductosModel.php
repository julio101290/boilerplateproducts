<?php

namespace julio101290\boilerplateproducts\Models;

use CodeIgniter\Model;

class FieldsExtraProductosModel extends Model {

    protected $table = 'fieldsExtraProductos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id'
        , 'idEmpresa'
        , 'idCategory'
        , 'idSubCategory'
        , 'type'
        , 'description'
        , 'options'
        , 'created_at'
        , 'updated_at'
        , 'deleted_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function mdlGetFieldsExtraProductos(array $idEmpresas) {
        return $this->db->table('fieldsExtraProductos a')
                        ->join('empresas b', 'a.idEmpresa = b.id')
                        ->join('categorias c', 'a.idCategory = c.id')
                        ->join('subcategorias d', 'a.idSubCategory = d.id')
                        ->select("a.id"
                                . ", a.idEmpresa"
                                . ", a.idCategory"
                                . ", a.idSubCategory"
                                . ", a.type"
                                . ", a.options"
                                . ", a.description"
                                . ", c.descripcion as descripcionCategoria"
                                . ", d.descripcion descripcionSubCategoria"
                                . ", a.created_at"
                                . ", a.updated_at"
                                . ", a.deleted_at"
                                . ", b.nombre AS nombreEmpresa")
                        ->whereIn('a.idEmpresa', $idEmpresas);
    }
}
