<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Validations\Customer\CustomerAdminValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function getList()
    {
        try {
            $result = Customer::all();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
    public function getDetail($id)
    {
        try {
            $result = Customer::where('id', $id)->first();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function addOrUpdate(Request $request)
    {

        if ($request->id == null) {
            $password = '123';
            $request->merge(['password' => bcrypt($password)]);

            $rule = [
                'name' => 'required|string',
                'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')],
                'phone' => ['required', 'max:14', Rule::unique('customers', 'phone')],
                'address' => "required|string|max:255",
                'password' => "max:255",
            ];
        } else {
            $rule = [
                'id' => 'numeric',
                'name' => 'required|string',
                'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($request['id'])],
                'phone' => ['required', 'max:14', Rule::unique('customers', 'phone')->ignore($request['id'])],
                'address' => "required|string|max:255",
            ];
        }
        try {
            $data = CustomerAdminValidator::validate($request->all(), $rule);

            Customer::upsert(
                $data,
                ['id'],
                ['name', 'email', 'phone', 'address', 'password']
            );
            $data = [
                'id' => $request->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ];
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function addCustomerFromOrder(array $payload)
    {
        try {
            CustomerAdminValidator::validate($payload, [
                'name' => 'required|string',
            ]);
            $save = Customer::create($payload);
            return $save;
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetailCustomer($id)
    {
        try {
            return Customer::where('id', $id)->first();
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteCustomer($id)
    {
        try {
            $check = Order::where('customer_id', $id)->first();
            if (!empty($check)) {
                return response()->error(400, "Customer has order");
            }

            $customer = Customer::where('id', $id)->first();
            if (empty($customer)) {
                return response()->error(404, "Not Found!");
            }

            Customer::where('id', $id)->delete();

            return response()->success($customer, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
