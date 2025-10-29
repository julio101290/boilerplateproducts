<?php

namespace julio101290\boilerplateproducts\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateproducts\Models\ProductsModel;
use julio101290\boilerplatelog\Models\LogModel;
use CodeIgniter\API\ResponseTrait;
use \julio101290\boilerplateproducts\Models\{
    CategoriasModel
};
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplatesells\Models\SellsDetailsModel;
use julio101290\boilerplatequotes\Models\QuotesDetailsModel;
use julio101290\boilerplatetypesmovement\Models\Tipos_movimientos_inventarioModel;
use julio101290\boilerplateproducts\Models\SubcategoriasModel;
use Hermawan\DataTables\DataTable;

//use App\Models\SellsDetailsModel;
//use App\Models\Tipos_movimientos_inventarioModel;

class ProductsController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $products;
    protected $empresa;
    protected $categorias;
    protected $subCategorias;
    protected $sellsDetails;
    protected $quoteDetails;
    protected $tiposMovimientoInventario;

    public function __construct() {
        $this->products = new ProductsModel();
        $this->log = new LogModel();
        $this->categorias = new CategoriasModel();
        $this->subCategorias = new SubcategoriasModel();
        $this->empresa = new EmpresasModel();
        $this->sellsDetails = new SellsDetailsModel();
        $this->quoteDetails = new QuotesDetailsModel();
        $this->tiposMovimientoInventario = new Tipos_movimientos_inventarioModel();
        helper('menu');
    }

    public function index() {


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($this->request->isAJAX()) {
            $datos = $this->products->mdlProductos($empresasID);
            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }

        $titulos["categorias"] = $this->categorias->select("*")->where("deleted_at", null)->asArray()->findAll();
        $titulos["title"] = lang('products.title');
        $titulos["subtitle"] = lang('products.subtitle');
        return view('julio101290\boilerplateproducts\Views\products', $titulos);
    }

    public function getAllProducts($empresa) {


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($this->request->isAJAX()) {


            $request = $this->request;
            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $search = $request->getGet('search')['value'] ?? '';
            $empresa = (int) $empresa;

            // Obtiene paginación manual desde el modelo
            $resultados = $this->products
                    ->mdlProductosEmpresa($empresasID, $empresa, $start, $length, $search);

            // Retorna JSON con formato de DataTables
            return $this->response->setJSON([
                        'draw' => $draw,
                        'recordsTotal' => $resultados['recordsTotal'],
                        'recordsFiltered' => $resultados['recordsFiltered'],
                        'data' => $resultados['data'],
            ]);
        }
    }

    public function getAllProductsInventory($empresa, $idStorage, $idTipoMovimiento) {


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        //BUSCAMOS EL TIPO DE MOVIMIENTO SI ES ENTRADA O SALIDA
        $tiposMovimiento = $this->tiposMovimientoInventario->select("*")
                        ->wherein("idEmpresa", $empresasID)
                        ->where("id", $idTipoMovimiento)->first();

        if ($tiposMovimiento == null) {

            $datos = $this->products->mdlProductosEmpresaInventarioEntrada($empresasID, $empresa);
            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }

        if ($tiposMovimiento["tipo"] == "ENT") {

            if ($this->request->isAJAX()) {
                $datos = $this->products->mdlProductosEmpresaInventarioEntrada($empresasID, $empresa);
                return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
            }
        }


        if ($tiposMovimiento["tipo"] == "SAL") {

            if ($this->request->isAJAX()) {
                $datos = $this->products->mdlProductosEmpresaInventarioSalida($empresasID, $empresa);
                return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
            }
        }


        $datos = $this->products->mdlProductosEmpresaInventarioEntrada($empresasID, $empresa);
        return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
    }

    /**
     * Get Unidad SAT via AJax
     */
    public function getUnidadSATAjax() {

        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        // Read new token and assign in $response['token']
        $response['token'] = csrf_hash();

        if (!isset($postData['searchTerm'])) {
            // Fetch record

            $listUnidadesSAT = $this->catalogosSAT->clavesUnidades40()->searchByField("texto", "%$%", 100);
            $listUnidadesSAT2 = $this->catalogosSAT->clavesUnidades40()->searchByField("id", "%$%", 100);
        } else {
            $searchTerm = $postData['searchTerm'];

            // Fetch record

            $listUnidadesSAT = $this->catalogosSAT->clavesUnidades40()->searchByField("texto", "%$searchTerm%", 100);
            $listUnidadesSAT2 = $this->catalogosSAT->clavesUnidades40()->searchByField("id", "%$searchTerm%", 100);
        }

        $data = array();
        foreach ($listUnidadesSAT as $unidadSAT => $value) {

            $data[] = array(
                "id" => $value->id(),
                "text" => $value->id() . ' ' . $value->texto(),
            );
        }


        foreach ($listUnidadesSAT2 as $unidadSAT => $value) {

            $data[] = array(
                "id" => $value->id(),
                "text" => $value->id() . ' ' . $value->texto(),
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }

    /**
     * Get Unidad SAT via AJax
     */
    public function getProductosSATAjax() {

        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        // Read new token and assign in $response['token']
        $response['token'] = csrf_hash();

        if (!isset($postData['searchTerm'])) {
            // Fetch record

            $listProducts1 = $this->catalogosSAT->productosServicios40()->searchByField("texto", "%$searchTerm%", 50);

            $listProducts2 = $this->catalogosSAT->productosServicios40()->searchByField("id", "%$searchTerm%", 50);
        } else {
            $searchTerm = $postData['searchTerm'];

            // Fetch record

            $listProducts1 = $this->catalogosSAT->productosServicios40()->searchByField("texto", "%$searchTerm%", 50);
            $listProducts2 = $this->catalogosSAT->productosServicios40()->searchByField("id", "%$searchTerm%", 50);
        }

        $data = array();
        foreach ($listProducts1 as $productosSAT => $value) {

            $data[] = array(
                "id" => $value->id(),
                "text" => $value->id() . ' ' . $value->texto(),
            );
        }

        foreach ($listProducts2 as $productosSAT => $value) {

            $data[] = array(
                "id" => $value->id(),
                "text" => $value->id() . ' ' . $value->texto(),
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }

    /**
     * Get Products via AJax
     */
    public function getProductsAjaxSelect2() {

        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        // Read new token and assign in $response['token']
        $response['token'] = csrf_hash();
        $products = new ProductsModel();
        $idEmpresa = $postData['idEmpresa'];

        if (!isset($postData['searchTerm'])) {
            // Fetch record

            $listProducts = $products->select('id,code,description')->where("deleted_at", null)
                    ->where('idEmpresa', $idEmpresa)
                    ->orderBy('id')
                    ->orderBy('code')
                    ->orderBy('description')
                    ->findAll(1000);
        } else {
            $searchTerm = $postData['searchTerm'];

            // Fetch record

            $listProducts = $products->select('id,code,description')->where("deleted_at", null)
                    ->where('idEmpresa', $idEmpresa)
                    ->groupStart()
                    ->like('description', $searchTerm)
                    ->orLike('id', $searchTerm)
                    ->orLike('code', $searchTerm)
                    ->groupEnd()
                    ->findAll(1000);
        }

        $data = array();
        $data[] = array(
            "id" => 0,
            "text" => "0 Todos Los Productos",
        );
        foreach ($listProducts as $product) {
            $data[] = array(
                "id" => $product['id'],
                "text" => $product['id'] . ' ' . $product['id'] . ' ' . $product['code'] . ' ' . $product['description'],
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }

    /**
     * Read Products
     */
    public function getProducts() {

        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }
        $idProducts = $this->request->getPost("idProducts");

        $datosProducts = $this->products->mdlGetProductoEmpresa($empresasID, $idProducts);

        //GET SUB CATEGORY

        if ($datosProducts->idSubCategoria == null) {

            $datosProducts->descriptionSubCategory = "Sin SubCategoria";
        } else {

            $subCategory = $this->subCategorias->select("descripcion")
                    ->where("id", $datosProducts->idSubCategoria)
                    ->first();

            $datosProducts->descriptionSubCategory = $subCategory["descripcion"];
        }


        echo json_encode($datosProducts);
    }

    /**
     * Save or update Products
     */
    public function save() {
        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        // Recogemos datos POST (sin archivos)
        $datos = $this->request->getPost();

        // Eliminé el var_dump($datos) (no mostrar en producción)
        $imagenProducto = $this->request->getFile('imagenProducto');

        // Inicializamos routeImage con lo que venga (ruta previa) o vacío
        $datos['routeImage'] = $this->request->getPost('routeImage') ?? '';

        // Validaciones del archivo (si se envió)
        if ($imagenProducto && $imagenProducto->isValid() && !$imagenProducto->hasMoved()) {
            // Extensiones permitidas
            $allowed = ['png', 'jpg', 'jpeg'];

            $ext = strtolower($imagenProducto->getClientExtension() ?: '');
            $maxSize = 5 * 1024 * 1024; // 5 MB

            if (!in_array($ext, $allowed)) {
                // Devuelve JSON con error y código apropiado
                return $this->response
                                ->setStatusCode(415)
                                ->setJSON(['status' => 'error', 'message' => lang('empresas.imageExtensionIncorrect')]);
            }

            if ($imagenProducto->getSize() > $maxSize) {
                return $this->response
                                ->setStatusCode(413)
                                ->setJSON(['status' => 'error', 'message' => lang('empresas.imageTooLarge')]);
            }

            // Generamos nuevo nombre para guardar
            $datos['routeImage'] = $imagenProducto->getRandomName();
        } elseif ($imagenProducto && !$imagenProducto->isValid()) {
            // Error durante el upload
            return $this->response
                            ->setStatusCode(400)
                            ->setJSON(['status' => 'error', 'message' => 'Error al subir imagen: ' . $imagenProducto->getErrorString()]);
        }

        // Insertar
        if (empty($datos['idProducts']) || $datos['idProducts'] == 0) {
            try {
                if ($this->products->save($datos) === false) {
                    $errores = $this->products->errors();
                    $msg = implode(' ', $errores);
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
                }

                // Log
                $dateLog["description"] = lang("products.logDescription") . json_encode($datos);
                $dateLog["user"] = $userName;
                $this->log->save($dateLog);

                // Mover archivo (solo si se subió)
                if ($imagenProducto && $imagenProducto->isValid()) {
                    // Usar FCPATH para la carpeta pública (images/products)
                    $targetPath = FCPATH . 'images/products';
                    // Asegurarse que la carpeta existe
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }
                    $imagenProducto->move($targetPath, $datos['routeImage']);
                }

                return $this->response->setJSON(['status' => 'ok', 'message' => 'Guardado Correctamente']);
            } catch (\Exception $ex) {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al guardar: ' . $ex->getMessage()]);
            }
        }

        // Actualizar
        $dataPrevious = $this->products->find($datos['idProducts']);

        try {
            if ($this->products->update($datos['idProducts'], $datos) == false) {
                $errores = $this->products->errors();
                $msg = implode(' ', $errores);
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
            } else {
                $dateLog["description"] = lang("products.logUpdated") . json_encode($datos);
                $dateLog["user"] = $userName;
                $this->log->save($dateLog);

                if ($imagenProducto && $imagenProducto->isValid()) {
                    $targetPath = FCPATH . 'images/products';
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }

                    // Borrar la imagen anterior si existe y la ruta anterior no está vacía
                    if (!empty($dataPrevious['routeImage']) && file_exists($targetPath . '/' . $dataPrevious['routeImage'])) {
                        @unlink($targetPath . '/' . $dataPrevious['routeImage']);
                    }

                    $imagenProducto->move($targetPath, $datos['routeImage']);
                }

                return $this->response->setJSON(['status' => 'ok', 'message' => 'Actualizado Correctamente']);
            }
        } catch (\Exception $ex) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Error al actualizar: ' . $ex->getMessage()]);
        }
    }

    /**
     * Delete Products
     * @param type $id
     * @return type
     */
    public function delete($id) {



        if ($this->sellsDetails->select("id")->where("idProduct", $id)->countAllResults() > 0) {

            $this->products->db->transRollback();
            return $this->failValidationError("No se puede borrar ya que hay ventas con este producto");
        }

        if ($this->quoteDetails->select("id")->where("idProduct", $id)->countAllResults() > 0) {

            $this->products->db->transRollback();
            return $this->failValidationError("No se puede borrar ya que hay cotizaciones con este producto");
        }

        $infoProducts = $this->products->find($id);
        helper('auth');
        $userName = user()->username;
        if (!$found = $this->products->delete($id)) {

            $this->products->db->transRollback();
            return $this->failNotFound(lang('products.msg.msg_get_fail'));
        }

        if ($infoProducts["routeImage"] != "") {

            if (file_exists("images/products/" . $infoProducts["routeImage"])) {

                unlink("images/products/" . $infoProducts["routeImage"]);
            }
        }


        $this->products->purgeDeleted();
        $logData["description"] = lang("products.logDeleted") . json_encode($infoProducts);
        $logData["user"] = $userName;
        $this->log->save($logData);
        $this->products->db->transCommit();

        return $this->respondDeleted($found, lang('products.msg_delete'));
    }

    /**
     * Get Vehiculos via AJax
     */
    public function getProductsAjax() {

        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        // Read new token and assign in $response['token']
        $response['token'] = csrf_hash();
        $custumers = new VehiculosModel();
        $idEmpresa = $postData['idEmpresa'];

        if (!isset($postData['searchTerm'])) {
            // Fetch record

            $listProducts = $products->select(
                            'id AS id, description AS description'
                    )
                    ->where('deleted_at', null)
                    ->where('idEmpresa', $idEmpresa)
                    ->orderBy('id', 'ASC')
                    ->orderBy('description', 'ASC')
                    ->findAll(1000);
        } else {
            $searchTerm = $postData['searchTerm'];

            // Fetch record
            $listProducts = $products->select('id AS id, description AS description')
                    ->where('deleted_at', null)
                    ->where('idEmpresa', $idEmpresa)
                    ->groupStart()
                    ->like('description', $searchTerm)
                    ->orLike('id', $searchTerm)
                    ->groupEnd()
                    ->findAll(1000);
        }

        $data = array();
        foreach ($listProducts as $product) {
            $data[] = array(
                "id" => $custumers['id'],
                "text" => $custumers['id'] . ' ' . $product['description'],
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }

    /**
     * Reporte Consulta
     */
    public function getBarcodePDF($idProducto, $isMail = 0) {


        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

// define barcode style
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );

        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($idProducto == 0) {


            $productos = $this->products->select("id")->whereIn("idEmpresa", $empresasID)->findAll();

            foreach ($productos as $key => $value) {

                $pdf->AddPage('P', 'A7');

                $pdf->Cell(0, 0, 'BAR CODE', 0, 1);
                $pdf->write1DBarcode($value["barcode"], 'S25', '', '', '', 18, 0.4, $style, 'N');
            }

            ob_end_clean();
            $this->response->setHeader("Content-Type", "application/pdf");
            $pdf->Output('etiqueta.pdf', 'I');

            return;
        }


        $productos = $this->products->select("barcode")
                        ->whereIn("idEmpresa", $empresasID)
                        ->where("id", $idProducto)->findAll();

        $pdf->AddPage('P', 'A7');

        $pdf->Cell(0, 0, 'BAR CODE', 0, 1);
        $pdf->write1DBarcode($productos[0]["barcode"], 'S25', '', '', '', 18, 0.4, $style, 'N');

        ob_end_clean();
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output('etiqueta.pdf', 'I');
    }
}
