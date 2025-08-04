<?php

namespace julio101290\boilerplateproducts\Models;

use CodeIgniter\Model;

class ProductsModel extends Model {

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id'
        , 'idEmpresa'
        , 'code'
        , 'idCategory'
        , 'description'
        , 'stock'
        , 'validateStock'
        , 'inventarioRiguroso'
        , 'buyPrice'
        , 'salePrice'
        , 'porcentSale'
        , 'porcentTax'
        , 'porcentIVARetenido'
        , 'porcentISRRetenido'
        , 'routeImage'
        , 'created_at'
        , 'deleted_at'
        , 'updated_at'
        , 'unidadSAT'
        , 'claveProductoSAT'
        , 'unidad'
        , 'nombreUnidadSAT'
        , 'nombreClaveProducto'
        , 'barcode'
        , 'inmuebleOcupado'
        , 'tasaExcenta'
        , 'predial'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [
        'barcode' => 'permit_empty|numeric',
        'buyPrice' => 'numeric',
        'salePrice' => 'numeric',
        'stock' => 'numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function mdlProductos($empresas) {


        $resultado = $this->db->table('products a')
                ->select(
                        'a.id AS id,
                        a.code AS code,
                        b.nombre AS nombre,
                        a.idCategory AS idCategory,
                        a.validateStock AS validateStock,
                        a.inventarioRiguroso AS inventarioRiguroso,
                        a.description AS description,
                        a.stock AS stock,
                        a.buyPrice AS buyPrice,
                        a.salePrice AS salePrice,
                        a.porcentSale AS porcentSale,
                        a.porcentTax AS porcentTax,
                        a.routeImage AS routeImage,
                        a.created_at AS created_at,
                        a.deleted_at AS deleted_at,
                        a.updated_at AS updated_at,
                        a.barcode AS barcode,
                        a.unidad AS unidad,
                        a.porcentIVARetenido AS porcentIVARetenido,
                        a.porcentISRRetenido AS porcentISRRetenido,
                        a.nombreUnidadSAT AS nombreUnidadSAT,
                        a.nombreClaveProducto AS nombreClaveProducto,
                        a.unidadSAT AS unidadSAT,
                        a.claveProductoSAT AS claveProductoSAT,
                        a.inmuebleOcupado AS inmuebleOcupado,
                        a.tasaExcenta AS tasaExcenta,
                        a.predial AS predial'
                )
                ->join('empresas b', 'a.idEmpresa = b.id')
                ->whereIn('a.idEmpresa', $empresas)
                ->where('a.deleted_at', null);

        return $resultado;
    }

    public function mdlProductosEmpresa($empresas, $empresa, int $start, int $length, string $search = ''): array {
// Condición para inventario NULL en PostgreSQL vs MySQL
        $driver = $this->db->DBDriver;
        $isNull = $driver === 'Postgre' ? '"a"."inventarioRiguroso" IS NULL' : 'a.inventarioRiguroso IS NULL';
        
        $like = $driver === 'Postgre' ? 'ILIKE' : 'LIKE';

// Subconsulta 1: productos sin inventario riguroso
        $b1 = $this->db->table('products AS a')
                ->join('empresas AS b', 'a.idEmpresa = b.id')
                ->select(
                        'a.id AS id,' .
                        'a.code AS code,' .
                        'a.idCategory AS idCategory,' .
                        'a.validateStock AS validateStock,' .
                        'a.inventarioRiguroso AS inventarioRiguroso,' .
                        'a.description AS description,' .
                        'a.stock AS stock,' .
                        'a.buyPrice AS buyPrice,' .
                        'a.salePrice AS salePrice,' .
                        'a.porcentSale AS porcentSale,' .
                        'a.porcentTax AS porcentTax,' .
                        'a.routeImage AS routeImage,' .
                        'a.created_at AS created_at,' .
                        'a.deleted_at AS deleted_at,' .
                        'a.updated_at AS updated_at,' .
                        'a.barcode AS barcode,' .
                        'a.unidad AS unidad,' .
                        'b.nombre AS nombreEmpresa,' .
                        'a.porcentIVARetenido AS porcentIVARetenido,' .
                        'a.porcentISRRetenido AS porcentISRRetenido,' .
                        'a.nombreUnidadSAT AS nombreUnidadSAT,' .
                        'a.nombreClaveProducto AS nombreClaveProducto,' .
                        'a.unidadSAT AS unidadSAT,' .
                        "'' AS lote," .
                        '0 AS idAlmacen,' .
                        "'' AS almacen," .
                        'a.claveProductoSAT AS claveProductoSAT,' .
                        'a.tasaExcenta AS tasaExcenta,' .
                        'a.predial AS predial,' .
                        'a.inmuebleOcupado AS inmuebleOcupado'
                )
                ->where('a.idEmpresa', $empresa)
                ->groupStart()
                ->where('a.inventarioRiguroso', 'off')
                ->orWhere($isNull, null, false)
                ->groupEnd()
                ->where('a.deleted_at', null)
                ->whereIn('a.idEmpresa', $empresas);

// Subconsulta 2: productos con inventario riguroso y saldos
        $b2 = $this->db->table('products AS a')
                ->join('empresas AS b', 'a.idEmpresa = b.id')
                ->join('saldos AS c', 'c.idProducto = a.id AND a.idEmpresa = c.idEmpresa')
                ->join('storages AS d', 'c.idAlmacen = d.id')
                ->select(
                        'a.id AS id,' .
                        'a.code AS code,' .
                        'a.idCategory AS idCategory,' .
                        'a.validateStock AS validateStock,' .
                        'a.inventarioRiguroso AS inventarioRiguroso,' .
                        'a.description AS description,' .
                        'c.cantidad AS stock,' .
                        'a.buyPrice AS buyPrice,' .
                        'a.salePrice AS salePrice,' .
                        'a.porcentSale AS porcentSale,' .
                        'a.porcentTax AS porcentTax,' .
                        'a.routeImage AS routeImage,' .
                        'a.created_at AS created_at,' .
                        'a.deleted_at AS deleted_at,' .
                        'a.updated_at AS updated_at,' .
                        'a.barcode AS barcode,' .
                        'a.unidad AS unidad,' .
                        'b.nombre AS nombreEmpresa,' .
                        'a.porcentIVARetenido AS porcentIVARetenido,' .
                        'a.porcentISRRetenido AS porcentISRRetenido,' .
                        'a.nombreUnidadSAT AS nombreUnidadSAT,' .
                        'a.nombreClaveProducto AS nombreClaveProducto,' .
                        'a.unidadSAT AS unidadSAT,' .
                        'COALESCE(c.lote, \'\') AS lote,' .
                        'c.idAlmacen AS idAlmacen,' .
                        'd.name AS almacen,' .
                        'a.claveProductoSAT AS claveProductoSAT,' .
                        'a.tasaExcenta AS tasaExcenta,' .
                        'a.predial AS predial,' .
                        'a.inmuebleOcupado AS inmuebleOcupado'
                )
                ->where('c.cantidad >', 0)
                ->where('a.idEmpresa', $empresa)
                ->where('a.deleted_at', null)
                ->where('a.inventarioRiguroso', 'on')
                ->where('a.validateStock', 'on')
                ->whereIn('c.idEmpresa', $empresas);

// Compilar subconsultas
        $q1 = rtrim($b1->getCompiledSelect(false), ';');
        $q2 = rtrim($b2->getCompiledSelect(false), ';');
        $sub = "($q1 UNION $q2) AS tempProducts";

        // Conteo total con SQL crudo
        $totalRow = $this->db->query("SELECT COUNT(*) AS total FROM $sub")->getRow();
        $recordsTotal = $totalRow ? (int) $totalRow->total : 0;

        // Conteo filtrado con SQL crudo
        $filterSql = "SELECT COUNT(*) AS total FROM $sub";
        $filterParams = [];
        if ($search !== '') {
            $filterSql .= " WHERE description $like ? OR code $like ?";
            $filterParams = ["%$search%", "%$search%"];
        }
        $filteredRow = $this->db->query($filterSql, $filterParams)->getRow();
        $recordsFiltered = $filteredRow ? (int) $filteredRow->total : 0;

        // Obtener datos paginados con SQL crudo
        $dataSql = "SELECT * FROM $sub";
        $dataParams = [];
        if ($search !== '') {
            $dataSql .= " WHERE description $like ? OR code $like ?";
            $dataParams = ["%$search%", "%$search%"];
        }
        $dataSql .= " ORDER BY description ASC LIMIT ? OFFSET ?";
        $dataParams = array_merge($dataParams, [$length, $start]);

        $data = $this->db->query($dataSql, $dataParams)->getResultArray();

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * Lista Para inventario Riguroso
     * @param type $empresas
     * @param type $empresa
     * @return type
     */
    public function mdlProductosEmpresaInventarioEntrada($empresas, $empresa) {
        return $this->db->table('products a')
                        ->select('
            a.id,
            a.code,
            a.idCategory,
            a.validateStock,
            a.inventarioRiguroso,
            a.description,
            a.stock,
            a.buyPrice,
            a.salePrice,
            a.porcentSale,
            a.porcentTax,
            a.routeImage,
            a.created_at,
            a.deleted_at,
            a.updated_at,
            a.barcode,
            a.unidad,
            b.nombre as nombreEmpresa,
            a.porcentIVARetenido,
            a.porcentISRRetenido,
            a.nombreUnidadSAT,
            a.nombreClaveProducto,
            a.unidadSAT,
            \'\' AS lote,
            \'\' AS almacen,
            a.inmuebleOcupado,
            a.tasaExcenta,
            a.predial,
            a.claveProductoSAT
        ')
                        ->join('empresas b', 'a.idEmpresa = b.id')
                        ->where('a.idEmpresa', $empresa)
                        ->where('a.deleted_at', null)
                        ->where('a.inventarioRiguroso', 'on')
                        ->where('a.validateStock', 'on')
                        ->whereIn('a.idEmpresa', $empresas);
    }

    public function mdlProductosEmpresaInventarioSalida($empresas, $empresa) {
        return $this->db->table('products a')
                        ->select('
            a.id,
            a.code,
            a.idCategory,
            a.validateStock,
            a.inventarioRiguroso,
            a.description,
            c.cantidad AS stock,
            a.buyPrice,
            a.salePrice,
            a.porcentSale,
            a.porcentTax,
            a.routeImage,
            a.created_at,
            a.deleted_at,
            a.updated_at,
            a.barcode,
            a.unidad,
            b.nombre AS nombreEmpresa,
            a.porcentIVARetenido,
            a.porcentISRRetenido,
            a.nombreUnidadSAT,
            a.nombreClaveProducto,
            a.unidadSAT,
            c.lote AS lote,
            c.idAlmacen,
            d.name AS almacen,
            a.inmuebleOcupado,
            a.tasaExcenta,
            a.predial,
            a.claveProductoSAT
        ')
                        ->join('empresas b', 'a.idEmpresa = b.id')
                        ->join('saldos c', 'c.idProducto = a.id AND a.idEmpresa = c.idEmpresa')
                        ->join('storages d', 'c.idAlmacen = d.id')
                        ->where('a.idEmpresa', $empresa)
                        ->where('a.deleted_at', null)
                        ->where('a.inventarioRiguroso', 'on')
                        ->where('a.validateStock', 'on')
                        ->whereIn('c.idEmpresa', $empresas);
    }

    public function mdlGetProductoEmpresa($empresas, $idProducto) {

        $resultado = $this->db->table('products a')
                ->select(
                        'a.id AS id,
                        a.code AS code,
                        a.idEmpresa AS idEmpresa,
                        a.validateStock AS validateStock,
                        a.inventarioRiguroso AS inventarioRiguroso,
                        a.idCategory AS idCategory,
                        c.clave AS clave,
                        c.descripcion AS descripcionCategoria,
                        a.description AS description,
                        a.stock AS stock,
                        a.buyPrice AS buyPrice,
                        a.salePrice AS salePrice,
                        a.porcentSale AS porcentSale,
                        a.porcentTax AS porcentTax,
                        a.routeImage AS routeImage,
                        a.created_at AS created_at,
                        a.deleted_at AS deleted_at,
                        a.updated_at AS updated_at,
                        a.barcode AS barcode,
                        a.unidad AS unidad,
                        b.nombre AS nombreEmpresa,
                        a.porcentIVARetenido AS porcentIVARetenido,
                        a.porcentISRRetenido AS porcentISRRetenido,
                        a.nombreUnidadSAT AS nombreUnidadSAT,
                        a.nombreClaveProducto AS nombreClaveProducto,
                        a.unidadSAT AS unidadSAT,
                        a.inmuebleOcupado AS inmuebleOcupado,
                        a.tasaExcenta AS tasaExcenta,
                        a.predial AS predial,
                        a.claveProductoSAT AS claveProductoSAT'
                )
                ->join('empresas b', 'a.idEmpresa = b.id')
                ->join('categorias c', 'a.idCategory = c.id')
                ->where('a.id', $idProducto)
                ->where('a.deleted_at', null)
                ->whereIn('a.idEmpresa', $empresas)
                ->get()
                ->getFirstRow();

        return $resultado;
    }
}
