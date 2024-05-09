<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Auth;
use Validator;
use App\Models\InvoiceItem;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use App\Mail\InvoiceMail;
use Mail;
use PDF;

class InvoiceController extends Controller
{
    public function index(Request $request) {
        try{
            $customers = $this->customer::orderBy('id','DESC')->get();
            $products = $this->product::orderBy('id','DESC')->get();    

            return view('invoice.index', compact('customers','products'));
        } catch(Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function addInvoice(Request $request) {
        try{
            $validate = $request->validate([
                "invoice_no" => "required|unique:invoices,invoice_no",
                'date' => 'required|date',
                'customer' => 'required',
                'invoice_logo' => 'required|mimes:jpeg,png,jpg'
            ]);

            $invoice = $this->invoice;
            $invoice->invoice_no = $request->invoice_no;
            $invoice->date = $request->date;
            $invoice->customer_id = $request->customer;
            $invoice->subtotal = $request->subtotal;
            $invoice->created_by = Auth::user()->id;

            if($request->hasfile('invoice_logo')){
                $file = $request->file('invoice_logo');
                $image_name = rand(10000,99999).".".$file->getClientOriginalExtension();
                $file->move(public_path('images/invoices/'),$image_name);
                $invoice->invoice_logo = $image_name;
            }
            
            $invoice->save();

            // Attach invoice items
            foreach ($request->addItem as $item) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->product_id = $item['product'];
                $invoiceItem->qty = $item['qty'];
                $invoiceItem->price = $item['price'];
                $invoiceItem->total_price = !empty($item['total']) ? $item['total'] : 0;
                $invoiceItem->save();
            }

            // send pdf on mail
            $pdf = new Dompdf();
            $html = view('invoice.invoice_template', ['invoice' => $invoice])->render();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            $pdfContent = $pdf->output();
        
            // Send email with PDF attachment
            Mail::send([], [], function ($message) use ($pdfContent, $invoice) {
                $message->to($invoice->customer->email)->subject('Invoice');
                $message->attachData($pdfContent, 'invoice.pdf', ['mime' => 'application/pdf']);
            });

            return redirect('/invoice/viewInvoice')->with('success', 'Invoice created successfully');

        } catch(\Exception $e){
            return $this->sendError('something went wrong', 500);
        }

    }

    public function updateInvoice(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                "invoice_no" => "required|unique:invoices,invoice_no,".$id,
                'date' => 'required|date',
                'customer' => 'required',
                'invoice_logo' => 'nullable|mimes:jpeg,png,jpg'
            ]);
    
            $invoice = $this->invoice::findOrFail($id);
            $invoice->invoice_no = $request->invoice_no;
            $invoice->date = $request->date;
            $invoice->customer_id = $request->customer;
            $invoice->subtotal = $request->subtotal;
    
            if ($request->hasFile('invoice_logo')) {
                $file = $request->file('invoice_logo');
                $image_name = rand(10000, 99999) . "." . $file->getClientOriginalExtension();
                $file->move(public_path('images/invoices/'), $image_name);
                $invoice->invoice_logo = $image_name;
            }
    
            $invoice->save();
    
            // Update invoice items
            if ($request->has('addItem')) {
                $deleteInvoice = $this->invoiceItem::where('invoice_id', $invoice->id)->delete();

                foreach ($request->addItem as $item) {
                    $invoiceItem = new InvoiceItem();
                    $invoiceItem->invoice_id = $invoice->id;
                    $invoiceItem->product_id = $item['product'];
                    $invoiceItem->qty = $item['qty'];
                    $invoiceItem->price = $item['price'];
                    $invoiceItem->total_price = !empty($item['total']) ? $item['total'] : 0;
                    $invoiceItem->save();
                }
            }

            $pdf = new Dompdf();
            $html = view('invoice.invoice_template', ['invoice' => $invoice])->render();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            $pdfContent = $pdf->output();
        
            // Send email with PDF attachment
            Mail::send([], [], function ($message) use ($pdfContent, $invoice) {
                $message->to($invoice->customer->email)->subject('Invoice');
                $message->attachData($pdfContent, 'invoice.pdf', ['mime' => 'application/pdf']);
            });

            return redirect('/invoice/viewInvoice')->with('success', 'Invoice updated successfully');
    
        } catch(\Exception $e) {
            return $this->sendError('Something went wrong', 500);
        }
    }    

    public function editInvoice(Request $request, $id) {
        try {
            $invoice = $this->invoice::findOrFail($id);
            $customers = $this->customer::all();
            $products = $this->product::orderBy('id','DESC')->get();
            $invoiceItems = $invoice->invoiceItems;
    
            return view('invoice.edit', compact('invoice', 'customers', 'products', 'invoiceItems'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function viewInvoice(Request $request) {
        try {
            $invoices = $this->invoice::where('created_by', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
    
            return view('invoice.view',compact('invoices'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function productsSearch(Request $request)
    {
        try{
            $searchData = $request->input('search');
            $products = $this->product::where('name', 'like', "%$searchData%")->get();

            return response()->json($products);

        } catch(Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function deleteInvoice($id)
    {
        $data = [
            'status' => 2
        ];

        $invoice = $this->invoice::find($id);
        if(!empty($invoice)){
            $this->invoiceItem::where('invoice_id',$invoice->id)->delete();
            $invoice->delete();
            $data = [
                'status' => 1
            ];
        }
        return $data;
    }

    private function generatePdf($invoice) {
        $html = view('invoice.invoice_template', compact('invoice'))->render();
    
        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
    
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        return $dompdf;
    }
}
