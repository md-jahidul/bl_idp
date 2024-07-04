@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">

                        @can('client-management')
                            <h1>Under construction - will be filled with charts and graphs</h1>
                            <button class="btn btn-warning btn-lg" type="button">
                                IDP Client <span class="badge">4</span>
                            </button>
                        @endcan

                        @can('customer-management')
                            <button class="btn btn-info btn-lg" type="button">
                                Customer <span class="badge">4</span>
                            </button>
                        @endcan

                        @include('includes.messages')
                        @can('client-self-management')
                            <passport-clients ability="{{ $canCreateToken }}"></passport-clients>
                            <br>
                            <passport-authorized-clients></passport-authorized-clients>
                            <br>
                            <passport-personal-access-tokens></passport-personal-access-tokens>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
