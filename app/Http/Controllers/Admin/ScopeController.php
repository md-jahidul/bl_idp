<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreScopeRequest;
use App\Models\ResourceServer;
use App\Models\Scope;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScopeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scopes = Scope::all();
        return view('admin.scope.index', compact('scopes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resourceServers = ResourceServer::all();
        return view('admin.scope.create', compact('resourceServers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreScopeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScopeRequest $request)
    {
        Scope::create($request->all());
        return redirect()->route('admin.scope.index')->withFlashSuccess('Scope created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Scope $scope)
    {
        $resourceServers = ResourceServer::all();
        return view('admin.scope.edit', compact('resourceServers', 'scope'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, StoreScopeRequest $request)
    {
        $scope = Scope::findOrFail($id);
        $scope->update($request->all());
        return redirect()->route('admin.scope.index')->withFlashSuccess('Scope updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Scope $scope
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Scope $scope)
    {
        $scope->delete();
        return redirect()->route('admin.scope.index')->withFlashSuccess('Scope deleted successfully');

    }
}
