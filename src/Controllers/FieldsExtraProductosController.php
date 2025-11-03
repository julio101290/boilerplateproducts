<?php

namespace julio101290\boilerplateproducts\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateproducts\Models\{FieldsExtraProductosModel};
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplateproducts\Models\CategoriasModel;
use julio101290\boilerplateproducts\Models\SubcategoriasModel;

class FieldsExtraProductosController extends BaseController
{
    use ResponseTrait;

    protected $log;
    protected $fieldsExtraProductos;
    protected $empresa;
    protected $category;
    protected $subCategory;

    public function __construct()
    {
        $this->fieldsExtraProductos = new FieldsExtraProductosModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        $this->category = new CategoriasModel();
        $this->subCategory = new SubcategoriasModel();
    
        helper(['menu', 'utilerias']);
    }

    public function index()
    {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        if ($this->request->isAJAX()) {
            $request = service('request');

            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $searchValue = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = (int) $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

            $fields = $this->fieldsExtraProductos->allowedFields;
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->fieldsExtraProductos->mdlGetFieldsExtraProductos($empresasID);

            $total = clone $builder;
            $recordsTotal = $total->countAllResults(false);

            if (!empty($searchValue)) {
                $builder->groupStart();
                foreach ($fields as $field) {
                    $builder->orLike("a." . $field, $searchValue);
                }
                $builder->groupEnd();
            }

            $filteredBuilder = clone $builder;
            $recordsFiltered = $filteredBuilder->countAllResults(false);

            $data = $builder->orderBy("a." . $orderField, $orderDir)
                             ->get($length, $start)
                             ->getResultArray();

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        $titulos["title"] = lang('fieldsExtraProductos.title');
        $titulos["subtitle"] = lang('fieldsExtraProductos.subtitle');
        return view('julio101290\boilerplateproducts\Views\fieldsExtraProductos', $titulos);
    }

    public function getFieldsExtraProductos()
    {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $idFieldsExtraProductos = $this->request->getPost("idFieldsExtraProductos");
        $dato = $this->fieldsExtraProductos->whereIn('idEmpresa', $empresasID)
                                   ->where('id', $idFieldsExtraProductos)
                                   ->first();
        
        //GET DATA CATEGORY
        $dataCategory = $this->category
                             ->select("descripcion")
                             ->where("id",$dato["idCategory"])
                             ->first();
        
        //GET DATA SUB CATEGORY
        
        $dataSubCategory = $this->subCategory
                                ->select("descripcion")
                                ->where("id",$dato["idSubCategory"])    
                                ->first();
        
        $dato["descripcionCategoria"] = $dataCategory["descripcion"];
        $dato["descripcionSubCategoria"] = $dataSubCategory["descripcion"];

        return $this->response->setJSON($dato);
    }

    public function save()
    {
        helper('auth');

        $userName = user()->username;
        $datos = $this->request->getPost();
        $idKey = $datos["idFieldsExtraProductos"] ?? 0;

        if ($idKey == 0) {
            try {
                if (!$this->fieldsExtraProductos->save($datos)) {
                    $errores = implode(" ", $this->fieldsExtraProductos->errors());
                    return $this->respond(['status' => 400, 'message' => $errores], 400);
                }
                $this->log->save([
                    "description" => lang("fieldsExtraProductos.logDescription") . json_encode($datos),
                    "user" => $userName
                ]);
                return $this->respond(['status' => 201, 'message' => 'Guardado correctamente'], 201);
            } catch (\Throwable $ex) {
                return $this->respond(['status' => 500, 'message' => 'Error al guardar: ' . $ex->getMessage()], 500);
            }
        } else {
            if (!$this->fieldsExtraProductos->update($idKey, $datos)) {
                $errores = implode(" ", $this->fieldsExtraProductos->errors());
                return $this->respond(['status' => 400, 'message' => $errores], 400);
            }
            $this->log->save([
                "description" => lang("fieldsExtraProductos.logUpdated") . json_encode($datos),
                "user" => $userName
            ]);
            return $this->respond(['status' => 200, 'message' => 'Actualizado correctamente'], 200);
        }
    }

    public function delete($id)
    {
        helper('auth');

        $userName = user()->username;
        $registro = $this->fieldsExtraProductos->find($id);

        if (!$this->fieldsExtraProductos->delete($id)) {
            return $this->respond(['status' => 404, 'message' => lang("fieldsExtraProductos.msg.msg_get_fail")], 404);
        }

        $this->fieldsExtraProductos->purgeDeleted();
        $this->log->save([
            "description" => lang("fieldsExtraProductos.logDeleted") . json_encode($registro),
            "user" => $userName
        ]);

        return $this->respondDeleted($registro, lang("fieldsExtraProductos.msg_delete"));
    }
}