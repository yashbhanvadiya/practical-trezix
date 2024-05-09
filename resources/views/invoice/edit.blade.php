@extends('layouts.layout')
@section('css')
@endsection

@section('content')
    <x-app-layout>
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-8">
                    <form id="editInvoiceForm" method="POST" action="{{ route('invoice.updateInvoice', $invoice->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="invoice_no">Invoice No:</label>
                            <input type="text" class="form-control @error('invoice_no') is-invalid @enderror"
                                name="invoice_no" id="invoice_no" value="{{ old('invoice_no', $invoice->invoice_no) }}"
                                required>
                            @error('invoice_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="invoice_logo">Invoice Logo:</label>
                            <input type="file" class="form-control @error('invoice_logo') is-invalid @enderror"
                                name="invoice_logo" id="invoice_logo">
                            @error('invoice_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($invoice->invoice_logo)
                                <img src="{{ asset('images/invoices/' . $invoice->invoice_logo) }}" alt="Invoice Logo"
                                    style="max-width: 200px; margin-top: 10px;">
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="date">Date:</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" name="date"
                                id="date" value="{{ old('date', $invoice->date) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="customer">Customer:</label>
                            <select name="customer" id="customer"
                                class="form-control @error('customer') is-invalid @enderror">
                                <option value="">Select customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="invoiceItems">
                            <h2>Invoice Items: </h2>
                            <div class="invoice-item">
                                <table class="table table-bordered" id="editInvoice">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoiceItems as $index => $item)
                                            <tr>
                                                <td>
                                                    <select class="form-control product"
                                                        name="addItem[{{ $index }}][product]" required>
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $product->id == $item->product_id ? 'selected' : '' }}>
                                                                {{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="addItem[{{ $index }}][qty]"
                                                        class="form-control qty"
                                                        value="{{ old('addItem.' . $index . '.qty', $item->qty) }}"
                                                        required></td>
                                                <td><input type="number" name="addItem[{{ $index }}][price]"
                                                        class="form-control price"
                                                        value="{{ old('addItem.' . $index . '.price', $item->price) }}"
                                                        required></td>
                                                <td><input type="number" name="addItem[{{ $index }}][total]"
                                                        class="form-control total_price"
                                                        value="{{ old('addItem.' . $index . '.total', $item->total_price) }}"
                                                        readonly></td>
                                                <td>
                                                    @if ($index == 0)
                                                        <button type="button" name="add" id="addItems"
                                                            class="btn btn-success"><i class="fa fa-plus"
                                                                aria-hidden="true"></i></button>
                                                    @endif
                                                    <button type="button" class="btn btn-danger remove-tr"><i
                                                            class="fa fa-trash" aria-hidden="true"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="subtotal">Subtotal:</label>
                            <input type="number" class="form-control" name="subtotal" id="subtotal" required readonly
                                value="{{ old('subtotal', $invoice->subtotal) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            function initSelect2(element) {
                $(element).select2({
                    ajax: {
                        url: "{{ URL('/products/search') }}",
                        dataType: 'json',
                        delay: 500,
                        processResults: function(data) {
                            console.log('data123', data);
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            initSelect2('.product');

            // Calculate total price for each item
            $(document).on('input', '.qty, .price', function() {
                var tr = $(this).closest('tr');
                var qty = tr.find('.qty').val();
                var price = tr.find('.price').val();
                var totalPrice = qty * price;
                tr.find('.total_price').val(totalPrice);
                calculateSubtotal();
            });

            // Calculate subtotal
            function calculateSubtotal() {
                var subtotal = 0;
                $('.total_price').each(function() {
                    subtotal += parseFloat($(this).val());
                });
                $('#subtotal').val(subtotal);
            }

            var i = $("#editInvoice tr").length;
            $('#addItems').click(function() {
                var newRow = $('<tr>' +
                    '<td><select class="form-control product" name="addItem[' + i +
                    '][product]" required></select></td>' +
                    '<td><input type="number" name="addItem[' + i +
                    '][qty]" class="form-control qty" required></td>' +
                    '<td><input type="number" name="addItem[' + i +
                    '][price]" class="form-control price" required></td>' +
                    '<td><input type="number" name="addItem[' + i +
                    '][total]" class="form-control total_price" readonly></td>' +
                    '<td><button type="button" class="btn btn-danger remove-tr"><i class="fa fa-trash" aria-hidden="true"></i></button></td>' +
                    '</tr>');
                $('#editInvoice').append(newRow);

                initSelect2(newRow.find('.product'));

                i++;
            });

            $(document).on('click', '.remove-tr', function() {
                $(this).closest('tr').remove();
                calculateSubtotal();
            });
        });
    </script>
@endsection
