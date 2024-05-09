@extends('layouts.layout')
@section('css')
    <style>
    </style>
@endsection

@section('content')
    <x-app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('invoice') }}" class="btn btn-primary">Add Invoice</a>
                        </div>
                    </div>
                    <div class="p-6 text-gray-900">
                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('success') }}
                            </div>
                        @endif

                        <table class="table table-bordered mt-2">
                            <tr>
                                <th>No</th>
                                <th>Invoice Number</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Subtotal</th>
                                <th width="280px">Action</th>
                            </tr>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration . '.' }}
                                    <td>{{ $invoice->invoice_no }}</td>
                                    <td>{{ $invoice->date }}</td>
                                    <td>{{ $invoice->customer->name }}</td>
                                    <td>{{ $invoice->subtotal }}</td>
                                    <td>
                                        <a class="btn btn-primary"
                                            href="{{ route('invoice.editInvoice', $invoice->id) }}">Edit</a>
                                        <a href="javascript:void(0)" data-id="{{ $invoice->id }}"
                                            class="btn btn-danger delete-invoice">Delete</a>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        {!! $invoices->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endsection
@section('js')
    <script>
        $(document).on('click', '.delete-invoice', function() {
            var invoiceId = $(this).data('id');
            if (confirm("Are you sure want delete this invoice?")) {
                var url = '{{ route('invoice.deleteInvoice', ':id') }}';
                url = url.replace(':id', invoiceId);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "DELETE",
                    url: url,
                    dataType: 'json',
                }).done(function(data) {
                    if (data.status == 1) {
                        alert("Deleted");
                        window.location.reload();
                    } else {
                        alert("Someting went wrong!");
                    }
                }).fail(function() {});
            }
        })
    </script>
@endsection
