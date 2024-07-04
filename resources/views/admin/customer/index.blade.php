@extends('layouts.app')

@section('after-styles')
    {{ Html::style("https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css") }}
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="width: 49%; text-align: left; display: inline-block"> Customers </div>
                    
                    <div style="width: 50%; text-align: right; display: inline-block">
                        <a href="{{ route('admin.customer.create') }}"  class="btn btn-success">Create Customer</a>
                    </div>
                </div>

                <div class="card-body">
                    @include('includes.messages')

                    <table id="customers-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('after-scripts')
    {{ Html::script("https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js") }}
    {{ Html::script("https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js") }}

    <script>
        $(function() {
            $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.customers.datatable") }}',
                    type: 'get',
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'mobile', name: 'mobile' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' }
                ]
            }).draw(true);
        });
    </script>

@endsection
