<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\CustomerRepository;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Customer Repository
     *
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * Customer Controller Constructor
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Customers list view.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.customer.index');
    }

    /**
     * Customers Datatables Data
     * 
     * @return \Yajra\Datatables\Facades\Datatables
    */
    public function getCustomersData()
    {
        return Datatables::of($this->customerRepository->getAll())
                ->editColumn('status', function ($customer) {
                    return $customer->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($customer) {
                    return sprintf(
                        '<a href="%s" class="btn btn-xs btn-primary edit">%s</a>',
                        route('admin.customer.edit', $customer->id),
                        'Edit'
                    );
                })        
                ->make(true);
    }
    /**
    * Customer create form.
    *
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
    public function create()
    {
        return view('admin.customer.create');
    }

    /**
     * Store customer info.
     *
     * @param  StoreUserRequest $request
     * 
     * @return mixed
     */
    protected function store(StoreUserRequest $request)
    {
        $request['user_type'] = 'CUSTOMER';
        $customer = $this->customerRepository->save($request);
        $customer->assignRole([3]); // Giving idp-customer role
        return redirect()->route('admin.customer.index')->withFlashSuccess(trans('Customer created successfully.'));
    }

    /**
     * Update customer form.
     *
     * @param  App\Models\User $customer
     * 
     * @return mixed
     */
    protected function edit(User $customer)
    {
        return view('admin.customer.edit', compact('customer'));
    }

    /**
     * Update customer.
     *
     * @param  App\Models\User $customer
     * 
     * @param UpdateCustomerRequest $request
     * 
     * @return mixed
     */
    protected function update($id, Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:20|min:3',
                'email' => 'required|string|email|max:191|unique:users,email,' . $id,
                'mobile' => 'required|string|max:11|min:11|regex:/(01)[0-9]{9}/|unique:users,mobile,' . $id,
            ]
        ); 

        $customer = User::find($id);
        $customer = $this->customerRepository->update($customer, $request);

        return redirect()->route('admin.customer.index')->withFlashSuccess(trans('Customer updated successfully.'));
    }
}
