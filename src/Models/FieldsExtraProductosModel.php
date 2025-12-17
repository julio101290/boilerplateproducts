<?php

namespace julio101290\boilerplateproducts\Models;

use CodeIgniter\Model;

class FieldsExtraProductosModel extends Model {

    protected $table = 'fieldsextraproductos';
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
        return $this->db->table('fieldsextraproductos a')
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

    public function mdlGetFieldsExtraProductosFilters($idEmpresa, $idCategoria, $idSubCategoria) {
        $builder = $this->db->table('fieldsextraproductos a')
                ->join('empresas b', 'a.idEmpresa = b.id')
                ->join('categorias c', 'a.idCategory = c.id')
                ->join('subcategorias d', 'a.idSubCategory = d.id')
                ->select("
                            a.id,
                            a.idEmpresa,
                            a.idCategory,
                            a.idSubCategory,
                            a.type,
                            a.options,
                            a.description,
                            c.descripcion as descripcionCategoria,
                            d.descripcion as descripcionSubCategoria,
                            a.created_at,
                            a.updated_at,
                            a.deleted_at,
                            b.nombre AS nombreEmpresa
                        ")
                ->where('a.idEmpresa', $idEmpresa);

        // Si categoría NO es cero, aplicar el filtro
        if ($idCategoria != 0) {
            $builder->where('a.idCategory', $idCategoria);
        }

        // Si subcategoría NO es cero, aplicar el filtro
        if ($idSubCategoria != 0) {
            $builder->where('a.idSubCategory', $idSubCategoria);
        }

        return $builder;
    }

    public function mdlSaveClonar(array $datos) {
        /*
          $datos = [
          'idEmpresaClonar'        => 9,
          'idCategoryClonar'       => 30,
          'idSubCategoryClonar'    => 2,
          'idCategoryNew'          => 30,
          'idSubCategoryNew'       => 1,
          ];
         */

        // 1️⃣ Builder del SELECT (equivale a temp1 + update)
        $builderSelect = $this->db->table('fieldsextraproductos f')
                ->select([
                    'f.idEmpresa',
                    $this->db->escape($datos['idCategoryNew']) . ' AS idCategory',
                    $this->db->escape($datos['idSubCategoryNew']) . ' AS idSubCategory',
                    'f.type',
                    'f.description',
                    'f.options',
                    'NOW() AS created_at',
                    'NOW() AS updated_at',
                    'f.deleted_at'
                ])
                ->where('f.idEmpresa', $datos['idEmpresaClonar'])
                ->where('f.idCategory', $datos['idCategoryClonar'])
                ->where('f.idSubCategory', $datos['idSubCategoryClonar']);

        // 2️⃣ Compilar el SELECT (NO ejecuta)
        $sqlSelect = $builderSelect->getCompiledSelect();

        // 3️⃣ INSERT INTO ... SELECT
        $sqlInsert = "
        INSERT INTO fieldsextraproductos
        (
            idEmpresa,
            idCategory,
            idSubCategory,
            type,
            description,
            options,
            created_at,
            updated_at,
            deleted_at
        )
        $sqlSelect
    ";

        // 4️⃣ Ejecutar
        return $this->db->query($sqlInsert);
    }
}
