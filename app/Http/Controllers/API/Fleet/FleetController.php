<?php

namespace App\Http\Controllers\API\Fleet;

use App\Http\Controllers\Controller;
use App\Models\Fleet;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderDispatchedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class FleetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            $vehicles = Fleet::paginate();
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully retrieved fleets',
                    'data'      =>$vehicles
                ], 200);
        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try{
            $validator=Validator::make($request->all(),[
                'registration_number'   => 'required',
                'status'                =>'required'
            ]);
            if ($validator->fails()) {
                return response()
                    ->json([
                        'success' => false,
                        'message' =>$validator->errors()->first()
                    ]);
            }
            $vehicle=Fleet::create([
                'registration_number'   =>$request->input('registration_number'),
                'status'                =>$request->input('status')
            ]);
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully added a new vehicle',
                    'data'      =>$vehicle
                ], 200);
        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try{
            $vehicle = Fleet::where('id',$id)
                ->first();
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully retrieved a vehicle',
                    'data'      =>$vehicle
                ], 200);
        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try{
           Fleet::where('id',$id)
                ->update(array_filter($request->all()));
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully updated a vehicle',
                ], 200);
        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try{
            Fleet::where('id',$id)
                ->delete();
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully removed a vehicle',
                ], 200);
        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()], $exception->getCode());
        }
    }
    public function loading(Request $request)
    {
        try{
            $validator=Validator::make($request->all(),[
                'order_number' =>'required',
                'fleet_id' =>'required',
                'status' =>'required',
            ]);
            if ($validator->fails()){
                return response()
                    ->json([
                        'success' =>false,
                        'message' =>$validator->errors()->first()
                    ]);
            }
            Order::where('order_number',$request->input('order_number'))
                ->update([
                    'status' =>$request->input("status"),
                    'fleet_id' =>$request->input("fleet_id"),
                ]);
            Fleet::where('fleet_id',$request->input('fleet_id'))
                ->update([
                    'status'    => 'Loading'
                ]);

        }catch (Exception $e) {

        }
    }
    public function dispatch(Request $request)
    {
        try{
            $validator=Validator::make($request->all(),[
                'order_number' =>'required',
                'customer_id' =>'required',
                'fleet_id' =>'required',
                'description' =>'required',
                'status' =>'required',
            ]);
            if ($validator->fails()){
                return response()
                    ->json([
                        'success' =>false,
                        'message' =>$validator->errors()->first()
                    ]);
            }
            Order::where('order_number',$request->input('order_number'))
                ->update([
                    'status'    => 'Dispatched'
                ]);
            Fleet::where('fleet_id',$request->input('order_number'))
                ->update([
                    'status'    => 'On Transit'
                ]);
            $user=User::where('customer_id',$request->input('customer_id'))->first();
            OrderDispatchedNotification($user);
        }catch (Exception $e) {

        }
    }
}
