<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;

class AuthController extends BaseController
{
    public function __construct(Request $request)
    {
        $uri = $request->path();
        Log::channel('api')->info($uri);

        $this->middleware('admin', ['only' => [
            'store',
            'destroy',
            'index'
        ]]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('authToken')->accessToken;
        $success['user'] =  $user;

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('authToken')->accessToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised!', 200);
        }
    }

    public function index()
    {
        $list = UserResource::collection(User::all());

        return $this->sendResponse($list);
    }

    public function update($id, Request $request)
    {
        $model = User::where('id', $id)->first();
        if (empty($model)) {
            return $this->sendError('Not Found!');
        }
        if (Gate::allows('update-user', $model)) {
            $data = $request->only(['name', 'email']);
            $model->update($data);

            return $this->sendResponse($model, $this->successMsg);
        }
        return $this->sendError($this->errorAccess);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();
        if (empty($user)) {
            return $this->sendError('Not Found!');
        }
        $data = new UserResource($user);

        return $this->sendResponse($data, $this->successMsg);
    }

    public function destroy($id, Request $request)
    {
        $user = User::where('id', $id)->first();
        if (empty($user)) {
            return $this->sendError('Not Found!');
        }

        $user->delete();

        return $this->sendResponse($user, 'Data successfully deleted.');
    }
}
