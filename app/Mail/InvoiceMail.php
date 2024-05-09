<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, $pdfContent)
    {
        $this->invoice = $invoice;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->view('invoice.invoice_template')
                    ->subject('Invoice Generated')
                    ->attachData($this->pdfContent, 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
