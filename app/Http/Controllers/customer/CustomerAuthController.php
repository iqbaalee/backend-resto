<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Validations\Auth\LoginValidator;
use App\Validations\Auth\RegisterValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = RegisterValidator::validateCustomer($request->all());
            $data['password'] = Hash::make($data['password']);
            $result = Customer::create($data);
            return response()->success($result->only('id', 'name', 'email', 'phone', 'address', 'created_at'), 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function login(Request $request)
    {
        $data = LoginValidator::validate($request->all());

        $user = Customer::where('email', $data['email'])->first();
        if (empty($user)) {
            return response()->error(404, 'User with email ' . $data['email'] . ' not found!');
        }
        if (!Hash::check($data['password'], $user->password)) {
            return response()->error(400, 'Wrong password!');
        }
        try {
            if (!$token = auth()->guard('customer-api')->attempt($data)) {
                return response()->error(400, 'Login credentials are invalid!');
            }
        } catch (JWTException $e) {
            return response()->error(400, 'Failed to create token!');
        }

        return response()->success([
            'token' => $token,
        ], 200, "Success");
    }

    public function logout()
    {
        try {
            auth()->guard('customer-api')->logout();
            return response()->success(null, 200, "Successfully logged out");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = RegisterValidator::validateUpdateCustomer($request->all());
            $user = Customer::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            $result = Customer::where('id', $id)->update($data);
            $data['id'] = $result;
            return response()->success($data, 200, "Success update admin user");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $user = Customer::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, "User not found!");
            }
            return response()->success($user, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $result = Customer::get();
            return response()->success($result, 200, "Success get users");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getProfile()
    {
        try {
            $id = auth()->guard('customer-api')->user()->id;
            $user = Customer::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, "User not found!");
            }
            return response()->success($user, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $id = auth()->guard('customer-api')->user()->id;
            $req = $request->all();
            $req['id'] = $id;
            $data = RegisterValidator::validateUpdateCustomer($req);
            $user = Customer::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            $result = Customer::where('id', $id)->update($data);
            $data['id'] = $result;
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $data = LoginValidator::validateUpdatePassword($request->all());
            $id = auth()->guard('customer-api')->user()->id;

            $user = Customer::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            if (!Hash::check($data['old_password'], $user->password)) {
                return response()->error(400, 'Wrong password!');
            }
            $body = [
                'password' => Hash::make($data['new_password'])
            ];
            $result = Customer::where('id', $id)->update($body);
            return response()->success(null, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
