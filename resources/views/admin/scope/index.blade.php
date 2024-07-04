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
                        <div style="width: 49%; text-align: left; display: inline-block"> Scopes</div>

                        <div style="width: 50%; text-align: right; display: inline-block">
                            <a href="{{ route('admin.scope.create') }}" class="btn btn-success">Create Scope</a>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('includes.messages')

                        <table id="scopes-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Resource Server</th>
                                <th>Scope</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($scopes as $scope)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{$scope->resource_server}}</td>
                                    <td>{{$scope->scope}}</td>
                                    <td>
                                        <a href="{{route('admin.scope.edit', $scope->id)}}"
                                           class="btn btn-sm btn-primary edit">Edit</a>
                                        <form class="form-inline d-inline" method="post" action="{{route('admin.scope.delete', $scope->id)}}">
                                            @method('delete')
                                            @csrf
                                            <input type="submit" value="Delete" class="btn btn-danger btn-sm form-inline">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
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
        $(function () {
            $('#scopes-table').DataTable();
        });
    </script>
@endsection
