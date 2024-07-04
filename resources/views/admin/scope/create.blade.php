@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Create Scope') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.scope.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="resource_server"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Resource Server') }}</label>

                                <div class="col-md-6">
                                    <select name="resource_server" id="resource_server" class="custom-select" required>
                                        <option>Select Server</option>
                                        @foreach($resourceServers as $resourceServer)
                                            <option value="{{$resourceServer->name}}">{{$resourceServer->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('resource_server')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="scope"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Scope') }}</label>

                                <div class="col-md-6">
                                    <input id="scope" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="scope"
                                           value="{{ old('scope') }}" required autocomplete="scope" autofocus>

                                    @error('scope')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Create') }}
                                    </button>
                                    <a href="{{route('admin.scope.index')}}" class="btn btn-dark">
                                        {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
