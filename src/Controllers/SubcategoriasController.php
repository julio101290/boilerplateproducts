<?php

namespace julio101290\boilerplateproducts\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateproducts\Models\{
    SubcategoriasModel
};
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplateproducts\Models\CategoriasModel;

class SubcategoriasController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $subcategorias;
    protected $empresa;
    protected $category;

    public function __construct() {
        $this->subcategorias = new SubcategoriasModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        $this->category = new CategoriasModel();
        helper(['menu', 'utilerias']);
    }

    public function index() {
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

            $fields = $this->subcategorias->allowedFields;
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->subcategorias->mdlGetSubcategorias($empresasID);

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

        $titulos["title"] = lang('subcategorias.title');
        $titulos["subtitle"] = lang('subcategorias.subtitle');
        return view('julio101290\boilerplateproducts\Views\subcategorias', $titulos);
    }

    public function getSubcategorias() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $idSubcategorias = $this->request->getPost("idSubcategorias");

        $dato = $this->subcategorias->whereIn('idEmpresa', $empresasID)
                ->where('id', $idSubcategorias)
                ->first();

        //Get Category
        $category = $this->category
                        ->select("descripcion")
                        ->where("id", $dato["idCategoria"])->first();

        $dato["descriptionCategory"] = $category["descripcion"];

        return $this->response->setJSON($dato);
    }

    /**
     * Get Sub Category via AJax
     */
    public function getSubCategoriasAjax() {

        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        // Read new token and assign in $response['token']
        $response['token'] = csrf_hash();
        $categorias = new CategoriasModel();
        $idEmpresa = $postData['idEmpresa'];
        $idCategoria = $postData['idCategoria'];

        if (!isset($postData['searchTerm'])) {
            // Fetch record

            $listSubCategorias = $this->subcategorias->select('id,descripcion')->where("deleted_at", null)
                    ->where('idEmpresa', $idEmpresa)
                    ->where('idCategoria', $idCategoria)
                    ->orderBy('id')
                    ->orderBy('descripcion')
                    ->findAll(25);
        } else {
            $searchTerm = $postData['searchTerm'];

            // Fetch record

            $listSubCategorias = $this->subcategorias->select('id,descripcion')
                    ->where("deleted_at", null)
                    ->where('idEmpresa', $idEmpresa)
                    ->where('idCategoria', $idCategoria)
                    ->groupStart()
                    ->like('id', $searchTerm)
                    ->orLike('descripcion', $searchTerm)
                    ->groupEnd()
                    ->findAll(25);
        }

        $data = array();
        foreach ($listSubCategorias as $subCategoria) {
            $data[] = array(
                "id" => $subCategoria['id'],
                "text" => $subCategoria['id'] ." ". $subCategoria['descripcion'],
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }

    public function save() {
        helper('auth');

        $userName = user()->username;
        $datos = $this->request->getPost();
        $idKey = $datos["idSubcategorias"] ?? 0;

        if ($idKey == 0) {
            try {
                if (!$this->subcategorias->save($datos)) {
                    $errores = implode(" ", $this->subcategorias->errors());
                    return $this->respond(['status' => 400, 'message' => $errores], 400);
                }
                $this->log->save([
                    "description" => lang("subcategorias.logDescription") . json_encode($datos),
                    "user" => $userName
                ]);
                return $this->respond(['status' => 201, 'message' => 'Guardado correctamente'], 201);
            } catch (\Throwable $ex) {
                return $this->respond(['status' => 500, 'message' => 'Error al guardar: ' . $ex->getMessage()], 500);
            }
        } else {
            if (!$this->subcategorias->update($idKey, $datos)) {
                $errores = implode(" ", $this->subcategorias->errors());
                return $this->respond(['status' => 400, 'message' => $errores], 400);
            }
            $this->log->save([
                "description" => lang("subcategorias.logUpdated") . json_encode($datos),
                "user" => $userName
            ]);
            return $this->respond(['status' => 200, 'message' => 'Actualizado correctamente'], 200);
        }
    }

    public function delete($id) {
        helper('auth');

        $userName = user()->username;
        $registro = $this->subcategorias->find($id);

        if (!$this->subcategorias->delete($id)) {
            return $this->respond(['status' => 404, 'message' => lang("subcategorias.msg.msg_get_fail")], 404);
        }

        $this->subcategorias->purgeDeleted();
        $this->log->save([
            "description" => lang("subcategorias.logDeleted") . json_encode($registro),
            "user" => $userName
        ]);

        return $this->respondDeleted($registro, lang("subcategorias.msg_delete"));
    }
}
