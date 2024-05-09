<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class APIInvoiceAPIController extends Controller
{
    public function createMaterial(Request $request)
    {
        try {
            $validator= Validator::make($request->all(),[
                'name' => 'required',
                'description' => 'required',
                'date' => 'required|date_format:"Y-m-d"'
            ]);

            if($validator->fails()){
                return $this->sendError(200, $validator->errors()->first());
            }

            $material = $this->material;
            $material->name = $request->name;
            $material->description = $request->description;
            $material->date = $request->date;
            $material->save();

            return $this->sendResponse($material, 'Material created successfully');
        } catch (Exception $e) {
            return $this->sendError(500, 'Something Went Wrong');
        }
    }

    public function createCustomer(Request $request)
    {
        try {
            $validator= Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|unique:customers,email',
                'phone_number' => [
                    'required','digits:10',
                ]
            ]);
            if($validator->fails()){
                return $this->sendError(200,$validator->errors()->first());
            }

            $customer = $this->customer;
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone_number = $request->phone_number;
            $customer->save();

            return $this->sendResponse($customer, 'Customer created successfully');
        } catch (Exception $e) {
            return $this->sendError(500, 'Something Went Wrong');
        }
    }

    public function fetchInvoice(Request $request)
    {
        try {
            $invoices = $this->invoice::with(['customer','invoiceItems.product'])->latest()->paginate(10);

            return $this->sendResponse($invoices, 'Invoices get successfully');
        } catch (Exception $e) {
            return $this->sendError(500, 'Something Went Wrong');
        }
    }
}
