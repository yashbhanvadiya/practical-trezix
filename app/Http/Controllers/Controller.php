<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Material;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var Product
     */
    public $product;

    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * @var InvoiceItem
     */
    public $invoiceItem;

    /**
     * @var Material
     */
    public $material;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->product = new Product();
        $this->customer = new Customer();
        $this->invoice = new Invoice();
        $this->invoiceItem = new InvoiceItem();
        $this->material = new Material();
    }

    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
