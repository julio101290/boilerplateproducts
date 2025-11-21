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
use julio101290\boilerplateproducts\Models\FieldsExtraProductosModel;
use julio101290\boilerplateproducts\Models\DataExtraFieldsProductsModel;
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
    protected $fieldsExtra;
    protected $fieldsExtraValues;

    public function __construct() {
        $this->products = new ProductsModel();
        $this->log = new LogModel();
        $this->categorias = new CategoriasModel();
        $this->subCategorias = new SubcategoriasModel();
        $this->empresa = new EmpresasModel();
        $this->sellsDetails = new SellsDetailsModel();
        $this->quoteDetails = new QuotesDetailsModel();
        $this->tiposMovimientoInventario = new Tipos_movimientos_inventarioModel();
        $this->fieldsExtra = new FieldsExtraProductosModel();
        $this->fieldsExtraValues = new DataExtraFieldsProductsModel();

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

    public function getAllProductsInventoryAjaxSelect2($empresa) {


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        $productsInventory = $this->products->mdlProductosEmpresaInventarioSalida($empresasID, $empresa);

        $data = array();

        foreach ($productsInventory as $products => $value) {

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

        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }

        if (!isset($postData['searchTerm'])) {

            $postData['searchTerm'] = "";
        }


        // Fetch record

        $listProducts = $products->mdlProductosEmpresSelectAjax($empresasID,$idEmpresa,0,25,$postData['searchTerm']);

        $data = array();
        /*
        $data[] = array(
            "id" => 0,
            "text" => "0 Todos Los Productos",
        );
         * 
         */
        foreach ($listProducts as $product) {
            $data[] = array(
                "id" => $product['id'],
                "text" =>  $product['id'] . ' ' . $product['lote'] . ' ' . $product['description'],
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


            $productos = $this->products->select("id,barcode")->whereIn("idEmpresa", $empresasID)->findAll();

            foreach ($productos as $key => $value) {

                $pdf->AddPage('L', array(101, 50));

                //$pdf->Cell(0, 0, 'BAR CODE', 0, 1);
                $pdf->write1DBarcode($value["barcode"], 'C39', '', '', '', 18, 0.4, $style, 'N');
            }

            ob_end_clean();
            $this->response->setHeader("Content-Type", "application/pdf");
            $pdf->Output('etiqueta.pdf', 'I');

            return;
        }


        $productos = $this->products->select("barcode")
                        ->whereIn("idEmpresa", $empresasID)
                        ->where("id", $idProducto)->findAll();

        $pdf->AddPage('L', array(101, 50));

        $pdf->write1DBarcode($productos[0]["barcode"], 'C39', '', '', '', 18, 0.4, $style, 'N');

        ob_end_clean();
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output('etiqueta.pdf', 'I');
    }

    /**
     * Read Products
     */
    public function getProductsFieldsExtra() {


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }
        $idProducts = $this->request->getPost("idProduct");

        $datosProducts = $this->products->mdlGetProductoEmpresa($empresasID, $idProducts);

        //GET FIELD EXTRA
        $fieldExtra = $this->fieldsExtra->select("*")
                ->where("idCategory", $datosProducts->idCategory)
                ->where("idSubCategory", $datosProducts->idSubCategoria)
                ->findAll();

        $html = '';

        // 🔹 Obtenemos los campos extra configurados
        $fieldExtra = $this->fieldsExtra
                ->select('*')
                ->where('idCategory', $datosProducts->idCategory)
                ->where('idSubCategory', $datosProducts->idSubCategoria)
                ->findAll();

// 🔹 Siempre agregar este campo oculto con el valor de $idProducts
        $html = '<input type="hidden" id="idProductExtraFields" name="idProductExtraFields" value="' . $idProducts . '">';

// 🔹 Si hay campos configurados
        if (!empty($fieldExtra)) {

            // 🔹 Obtener valores existentes para este producto (si ya hay guardados)
            $savedValues = $this->fieldsExtraValues
                    ->select('idField, value')
                    ->where('idProduct', $idProducts)
                    ->findAll();

            // Convertir a arreglo [idField => value] para acceso rápido
            $savedMap = [];
            foreach ($savedValues as $sv) {
                $savedMap[$sv['idField']] = $sv['value'];
            }

            foreach ($fieldExtra as $field) {
                $fieldId = (int) $field['id']; // ID único del campo
                $name = "extraField_{$fieldId}";
                $id = "extraField_{$fieldId}";
                $label = ucwords(str_replace('_', ' ', $field['description']));

                // 🔹 Si ya hay valor guardado, úsalo
                $value = old($name) ?? ($savedMap[$fieldId] ?? '');
                $errorClass = "<?= session('error.{$name}') ? 'is-invalid' : '' ?>";

                if ($field['type'] == 1) {
                    // 🔹 Campo tipo TEXT
                    $html .= <<<EOF
        <div class="form-group row">
            <label for="{$id}" class="col-sm-2 col-form-label">{$label}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                    </div>
                    <input type="text" name="{$name}" id="{$id}" 
                        class="form-control {$errorClass}" 
                        value="{$value}" placeholder="{$label}" autocomplete="on">
                </div>
            </div>
        </div>
        EOF;
                } elseif ($field['type'] == 2) {
                    // 🔹 Campo tipo SELECT
                    $optionsHtml = '';
                    $options = explode(',', $field['options']);
                    foreach ($options as $opt) {
                        $opt = trim($opt);
                        $selected = ($opt == $value) ? 'selected' : '';
                        $optionsHtml .= "<option value=\"{$opt}\" {$selected}>{$opt}</option>";
                    }

                    $html .= <<<EOF
        <div class="form-group row">
            <label for="{$id}" class="col-sm-2 col-form-label">{$label}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                    </div>
                    <select class="form-control" name="{$name}" id="{$id}" style="width:80%;">
                        {$optionsHtml}
                    </select>
                </div>
            </div>
        </div>
        EOF;
                }
            }
        }

        echo $html;
    }

    /**
     * Save or update Products
     */
    public function saveExtraFields() {
        helper('auth');
        $userName = user()->username ?? 'system';
        $idUser = user()->id ?? 0;

        // Recoger datos enviados
        $datos = $this->request->getPost();

        // Validación: debe venir el idProduct
        if (empty($datos['idProduct']) || $datos['idProduct'] == 0) {
            return $this->response->setStatusCode(400)
                            ->setJSON(['status' => 'error', 'message' => 'Falta el ID del producto']);
        }

        $idProduct = (int) $datos['idProduct'];

        // Cargar modelo de data extra
        $dataExtraModel = new \julio101290\boilerplateproducts\Models\DataExtraFieldsProductsModel();

        try {
            // Eliminar registros previos del producto (para evitar duplicados)
            $dataExtraModel->where('idProduct', $idProduct)->delete();

            // Recorrer los campos y guardar uno por uno
            foreach ($datos as $key => $value) {

                // Saltar campos no relevantes
                if ($key === 'idProductExtraFields' || $key === 'csrf_test_name') {
                    continue;
                }

                // 🔹 Extraer idField desde el nombre del campo, ej: "extraField_5" → 5
                if (preg_match('/^extraField_(\d+)$/', $key, $matches)) {
                    $idField = (int) $matches[1];

                    // Guardar en la base de datos
                    $dataExtraModel->insert([
                        'idProduct' => $idProduct,
                        'idField' => $idField,
                        'value' => trim($value),
                    ]);
                }
            }

            // Registrar log
            $dateLog = [
                "description" => "Campos extra guardados para producto #{$idProduct}: " . json_encode($datos),
                "user" => $userName,
            ];
            $this->log->save($dateLog);

            return $this->response->setJSON([
                        'status' => 'ok',
                        'message' => 'Campos extra guardados correctamente',
            ]);
        } catch (\Exception $ex) {
            return $this->response->setStatusCode(500)->setJSON([
                        'status' => 'error',
                        'message' => 'Error al guardar campos extra: ' . $ex->getMessage(),
            ]);
        }
    }
}
