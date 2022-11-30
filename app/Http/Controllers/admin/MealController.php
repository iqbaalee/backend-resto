<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Validations\Meal\MealValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MealController extends Controller
{
    public function getList()
    {
        try {
            $result = Product::where('type', 'meal')->paginate(5);

            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $result =  Product::where('id', $id)->first();

            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function addMeal(Request $request)
    {
        try {

            $data = MealValidator::validate($request->all());
            $data['type'] = 'meal';

            $base64Image = $data['photo'];
            $extension = explode('/', explode(':', substr($base64Image, 0, strpos($base64Image, ';')))[1])[1];
            $replace =  substr($base64Image, 0, strpos($base64Image, ',') + 1);
            $image = str_replace($replace, '', $base64Image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::slug($data['name'], '-') . time() . '.' . $extension;

            Storage::disk('public')->put('meals/' . $imageName, base64_decode($image));


            $data['photo'] = asset('storage/meals') . '/' . $imageName;

            $result = Product::create($data);

            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updateMeal(Request $request, $id)
    {
        try {

            $data = MealValidator::validate($request->all());


            $product = Product::where('id', $id)->first();

            if (empty($product)) {
                return response()->error(404, "Not Found!");
            }

            if ($data['photo'] != "null") {

                $base64Image = $data['photo'];
                $extension = explode('/', explode(':', substr($base64Image, 0, strpos($base64Image, ';')))[1])[1];
                $replace =  substr($base64Image, 0, strpos($base64Image, ',') + 1);
                $image = str_replace($replace, '', $base64Image);
                $image = str_replace(' ', '+', $image);
                $imageName = Str::slug($data['name'], '-') . time() . '.' . $extension;

                Storage::disk('public')->put('meals/' . $imageName, base64_decode($image));
                if ($product['photo'] != "") {
                    Storage::disk('public')->delete('meals/' . $product->photo);
                }

                $data['photo'] = asset('storage/meals') . '/' . $imageName;
            } else {

                $imageName = $product->photo ?? '';
                $data['photo'] = $imageName;
            }



            Product::where('id', $id)->update($data);
            $data['id'] = (int)$id;
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteMeal($id)
    {
        try {
            $check = Order::with(['order_detail' => function ($query) use ($id) {
                $query->where('product_id', $id);
            }])->whereNot('status', 'paid')->first();

            if (!empty($check)) {
                return response()->error(400, "Can't delete this meal!, because this meal is already ordered");
            }

            $product = Product::where('id', $id)->first();
            if (empty($product)) {
                return response()->error(404, "Not Found!");
            }
            Product::where('id', $id)->delete();
            return response()->success(['id' => (int)$id], 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
