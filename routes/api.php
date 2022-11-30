<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\{
    AuthController,
    CustomerController,
    DiscountController,
    MealController,
    MenuController,
    OrderController,
    TableController,
    RoleController
};

use App\Http\Controllers\customer\{
    CustomerAuthController,
    CustomerDiscountController,
    CustomerMealController,
    CustomerOrderController,
    CustomerTableController
};
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\ReportController;

Route::prefix("v1")->group(function () {
    Route::get("/", function () {
        return response()->success(null, 200, "service-api-footsal");
    });

    Route::post("payment-handler", [
        MidtransController::class,
        "payment_handler",
    ]);

    Route::prefix("admin")->group(function () {
        Route::post("login", [AuthController::class, "login"]);
        Route::post("register", [AuthController::class, "register"]);

        Route::middleware(["jwt.verify"])->group(function () {
            // USER
            Route::post("logout", [AuthController::class, "logout"]);
            Route::put("user/{id}", [AuthController::class, "update"]);
            Route::put("user/{id}", [AuthController::class, "update"]);
            Route::get("user/list", [AuthController::class, "getAll"]);
            Route::get("user/{id}", [AuthController::class, "getDetail"]);
            Route::delete("user/{id}", [AuthController::class, "deleteUSer"]);

            Route::get("profile", [AuthController::class, "getProfile"]);
            Route::put("profile", [AuthController::class, "updateProfile"]);
            Route::put("profile/password", [
                AuthController::class,
                "updatePassword",
            ]);

            // MENU
            Route::get("menu", [MenuController::class, "getList"]);
            Route::get("menu/{id}", [MenuController::class, "getDetail"]);
            Route::post("menu", [MenuController::class, "addOrUpdate"]);
            Route::delete("menu/{id}", [MenuController::class, "deleteMenu"]);

            // CUSTOMER
            Route::get("customer", [CustomerController::class, "getList"]);
            Route::get("customer/{id}", [
                CustomerController::class,
                "getDetail",
            ]);
            Route::post("customer", [CustomerController::class, "addOrUpdate"]);
            Route::delete("customer/{id}", [
                CustomerController::class,
                "deleteCustomer",
            ]);

            // ROLE
            Route::get("role", [RoleController::class, "getList"]);
            Route::get("role/{id}", [RoleController::class, "getDetail"]);
            Route::post("role", [RoleController::class, "addRole"]);
            Route::put("role/{id}", [RoleController::class, "updateRole"]);
            Route::delete("role/{id}", [RoleController::class, "deleteRole"]);

            // TABLES
            Route::get("table", [TableController::class, "getList"]);
            Route::get("table/filter", [TableController::class, "filterList"]);
            Route::get("table/{id}", [TableController::class, "getDetail"]);
            Route::post("table", [TableController::class, "addTable"]);
            Route::put("table/{id}", [TableController::class, "updateTable"]);
            Route::delete("table/{id}", [
                TableController::class,
                "deleteTable",
            ]);

            // MEALS
            Route::get("meal", [MealController::class, "getList"]);
            Route::get("meal/{id}", [MealController::class, "getDetail"]);
            Route::post("meal", [MealController::class, "addMeal"]);
            Route::put("meal/{id}", [MealController::class, "updateMeal"]);
            Route::delete("meal/{id}", [MealController::class, "deleteMeal"]);

            // ORDERS
            Route::get("order", [OrderController::class, "getList"]);

            Route::get("order/count", [OrderController::class, "getCount"]);
            Route::get("order/{number}", [OrderController::class, "getDetail"]);
            Route::post("order", [OrderController::class, "createOrder"]);
            Route::post("order/cancel", [
                OrderController::class,
                "cancelOrder",
            ]);
            Route::put("order/{number}", [
                OrderController::class,
                "updateOrder",
            ]);

            // DISCOUNT
            Route::get("discount", [DiscountController::class, "getList"]);
            Route::get("discount/{id}", [
                DiscountController::class,
                "getDetail",
            ]);
            Route::post("discount", [DiscountController::class, "addOrUpdate"]);
            Route::delete("discount/{id}", [
                DiscountController::class,
                "deleteDiscount",
            ]);

            Route::prefix("report")->group(function () {
                Route::get("order", [ReportController::class, "chartOrder"]);
                Route::get("income", [ReportController::class, "chartIncome"]);
                Route::get("customer", [
                    ReportController::class,
                    "chartCustomer",
                ]);
                Route::get("most_booking", [
                    ReportController::class,
                    "mostBooking",
                ]);
            });
        });
    });

    Route::prefix("customer")->group(function () {
        Route::post("login", [CustomerAuthController::class, "login"]);
        Route::post("register", [CustomerAuthController::class, "register"]);

        Route::middleware(["jwt.verify"])->group(function () {
            Route::post("logout", [CustomerAuthController::class, "logout"]);
            Route::put("user/{id}", [CustomerAuthController::class, "update"]);
            Route::put("user/{id}", [CustomerAuthController::class, "update"]);
            Route::get("user/list", [CustomerAuthController::class, "getAll"]);
            Route::get("user/{id}", [
                CustomerAuthController::class,
                "getDetail",
            ]);

            Route::get("profile", [
                CustomerAuthController::class,
                "getProfile",
            ]);
            Route::put("profile", [
                CustomerAuthController::class,
                "updateProfile",
            ]);
            Route::put("profile/password", [
                CustomerAuthController::class,
                "updatePassword",
            ]);

            Route::prefix("order")->group(function () {
                Route::get("/", [CustomerOrderController::class, "getList"]);

                Route::get("/history", [
                    CustomerOrderController::class,
                    "getHistory",
                ]);
                Route::get("/total", [
                    CustomerOrderController::class,
                    "getTotalOrder",
                ]);
                Route::get("/active", [
                    CustomerOrderController::class,
                    "getCurrentOrder",
                ]);
                Route::get("/{number}", [
                    CustomerOrderController::class,
                    "getDetail",
                ]);
                Route::post("/", [
                    CustomerOrderController::class,
                    "createOrder",
                ]);

                Route::put("/{number}", [
                    CustomerOrderController::class,
                    "updateOrder",
                ]);
            });

            Route::get("table", [CustomerTableController::class, "getList"]);
            Route::get("table/{id}", [
                CustomerTableController::class,
                "getDetail",
            ]);

            Route::get("discount", [
                CustomerDiscountController::class,
                "getList",
            ]);
            Route::get("discount/{id}", [
                CustomerDiscountController::class,
                "getDetail",
            ]);

            Route::get("meal", [CustomerMealController::class, "getList"]);
            Route::get("meal/{id}", [
                CustomerMealController::class,
                "getDetail",
            ]);
        });
    });
});
