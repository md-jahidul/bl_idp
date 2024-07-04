@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Add Scope') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.user.scope.save', $user->id) }}">
                            @csrf

                            <div class="form-group row">
                                <div class="col-md-6">
                                    @foreach($scopes as $scope)
                                        <div class="form-check">
                                            <input type="checkbox" id="scope" class="form-check-input" name="scopes[]"
                                                   {{ in_array($scope->id, $userScopeIds) ? 'checked' : 'dd' }} value="{{$scope->id}}">
                                            <label class="form-check-label" for="scope"> {{$scope->scope}} </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                    <a href="{{route('admin.user.index')}}" class="btn btn-dark">
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
