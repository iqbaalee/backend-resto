<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Validations\Menu\MenuValidator;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getList()
    {
        try {
            $result = Menu::all();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $menu = Menu::where('id', $id)->first();
            if (empty($menu)) {
                return response()->error(404, "Not Found!");
            }

            return response()->success($menu, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }


    public function addOrUpdate(Request $request)
    {
        try {
            $data = MenuValidator::validate($request->all());
            Menu::upsert(
                $data,
                ['id'],
                ['name', 'url']
            );
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteMenu($id)
    {
        try {
            $menu = Menu::where('id', $id)->first();
            if (empty($menu)) {
                return response()->error(404, "Not Found!");
            }

            Menu::where('id', $id)->delete();

            return response()->success($id, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
