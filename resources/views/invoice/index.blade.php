@extends('layouts.layout')
@section('css')
    <style>
    </style>
@endsection

@section('content')
    <x-app-layout>
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('invoice.viewInvoice') }}" class="btn btn-primary">View Invoice</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <form id="createInvoiceForm" method="POST" action="{{ route('invoice.addInvoice') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="invoice_no">Invoice No:</label>
                            <input type="text" class="form-control" name="invoice_no" id="invoice_no" required>
                            @error('invoice_no')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="invoice_logo">Invoice Logo:</label>
                            <input type="file" class="form-control" name="invoice_logo" id="invoice_logo">
                            @error('invoice_logo')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="date">Date:</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                            @error('date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="customer">Customer:</label>
                            <select name="customer" id="customer" class="form-control">
                                <option value="">Select customer</option>
                                @foreach ($customers as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                            @error('customer')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="invoiceItems">
                            <h2>Invoice Items: </h2>
                            <div class="invoice-item">
                                <table class="table table-bordered" id="addInvoice">
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total Price</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="form-control product product-select" name="addItem[0][product]"
                                                required>
                                                <option value="">Select Product</option>
                                            </select>
                                        </td>
                                        <td><input type="number" name="addItem[0][qty]" class="form-control qty" required>
                                        </td>
                                        <td><input type="number" name="addItem[0][price]" class="form-control price"
                                                required></td>
                                        <td><input type="number" name="addItem[0][total]" class="form-control total_price"
                                                readonly></td>
                                        <td><button type="button" name="add" id="addItems" class="btn btn-success"><i
                                                    class="fa fa-plus" aria-hidden="true"></i></button></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="subtotal">Subtotal:</label>
                            <input type="number" class="form-control" name="subtotal" id="subtotal" required readonly>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Invoice</button>
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
                        url: "products/search",
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

            var i = 1;
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
                $('#addInvoice').append(newRow);

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
