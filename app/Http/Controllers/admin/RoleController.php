<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Validations\Role\RoleValidator;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getList() 
    {
        try {
            $result = Role::with(['permissions', 'permissions.menu'])->get();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $result = Role::with(['permissions', 'permissions.menu'])->where('id', $id)->first();
            if(empty($result)) {
                return response()->error(404, "Not Found!");
            }
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function addRole(Request $request)
    {
        try {
            $data = RoleValidator::validate($request->all());
            $role_body = [
                'name'=> $data['name']
            ];
            $result = Role::create($role_body);
            $data['id'] = $result->id;

            foreach($data['permissions'] as $value) {
                $body = [
                    'role_id'=> $result->id,
                    'menu_id'=> $value
                ];
                Permission::create($body);
            }

            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $data = RoleValidator::validatePermissions($request->all());
            $role_body = [
                'name'=> $data['name']
            ];
            $role = Role::where('id', $id)->first();
            if(empty($role)) {
                return response()->error(404, "Not Found!");
            }
            Role::where('id', $id)->update($role_body);
            $data['id'] = $id;

            Permission::where('role_id', $id)->delete();

            foreach($data['permissions'] as $value) {
                $body = [
                    'role_id'=> $id,
                    'menu_id'=> $value
                ];
                Permission::create($body);
            }
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteRole($id)
    {
        try {
            $role = Role::where('id', $id)->first();
            if(empty($role)) {
                return response()->error(404, "Not Found!");
            }
            Role::where('id', $id)->delete();
            return response()->success($id, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
