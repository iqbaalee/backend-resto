<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Validations\Auth\{
    LoginValidator,
    RegisterValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = RegisterValidator::validate($request->all());
            $data['password'] = Hash::make($data['password']);
            $result = User::create($data);
            return response()->success($result->only('id', 'name', 'email', 'role_id', 'created_at'), 200, "Success create admin user");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function login(Request $request)
    {
        $data = LoginValidator::validate($request->all());

        $user = User::where('email', $data['email'])->first();
        if (empty($user)) {
            return response()->error(404, 'User with email ' . $data['email'] . ' not found!');
        }
        if (!Hash::check($data['password'], $user->password)) {
            return response()->error(400, 'Wrong password!');
        }

        try {
            if (!$token = auth()->guard('admin-api')->attempt($data)) {
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
            auth()->guard('admin-api')->logout();
            return response()->success(null, 200, "Successfully logged out");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = RegisterValidator::validateUpdate($request->all());
            $user = User::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            $result = User::where('id', $id)->update($data);
            $data['id'] = $result;
            return response()->success($data, 200, "Success update admin user");
        } catch (\Exception $e) {
            return response()->error($e->getCode(), $e->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $user = User::with(['role', 'role.permissions', 'role.permissions.menu'])->where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, "User not found!");
            }
            return response()->success($user, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getAll(Request $request)
    {
        try {
            $result = User::with(['role', 'role.permissions', 'role.permissions.menu'])->get();
            return response()->success($result, 200, "Success get users");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getProfile()
    {
        try {
            $id = auth()->guard('admin-api')->user()->id;
            $user = User::with(['role', 'role.permissions', 'role.permissions.menu'])->where('id', $id)->first();
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
            $id = auth()->guard('admin-api')->user()->id;
            $req = $request->all();
            $data = RegisterValidator::validateUpdate($req);
            $user = User::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            $result = User::where('id', $id)->update($data);
            $data['id'] = $result;
            return response()->success($data, 200, "Success update admin user");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $data = LoginValidator::validateUpdatePassword($request->all());
            $id = auth()->guard('admin-api')->user()->id;

            $user = User::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            if (!Hash::check($data['old_password'], $user->password)) {
                return response()->error(400, 'Wrong password!');
            }
            $body = [
                'password' => Hash::make($data['new_password'])
            ];
            $result = User::where('id', $id)->update($body);
            //login after update password
            $reLogin = [
                'email' => $user->email,
                'password' => $data['new_password']
            ];
            try {
                if (!$token = auth()->guard('admin-api')->attempt($reLogin)) {
                    return response()->error(400, 'Login credentials are invalid!');
                }
            } catch (JWTException $e) {
                return response()->error(400, 'Failed to create token!');
            }
            return response()->success([
                'token' => $token,
            ], 200, "Success");
            return response()->success(null, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteUSer($id)
    {
        try {
            $idLogin = auth()->guard('admin-api')->user()->id;
            if ($id === $idLogin) {
                return response()->error(404, "Can't erase yourself");
            }
            $user = User::where('id', $id)->first();
            if (empty($user)) {
                return response()->error(404, 'User not found!');
            }
            $result = User::where('id', $id)->delete();
            return response()->success($id, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
