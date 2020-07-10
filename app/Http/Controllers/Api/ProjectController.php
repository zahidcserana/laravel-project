<?php

namespace App\Http\Controllers\Api;

use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ProjectResource;

class ProjectController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('admin', ['only' => [
        //     'store'
        // ]]);
    }
    /**
     * Create New
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required|min:10',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $input = $request->all();
        if (empty($input['user_id'])) {
            $input['user_id'] = Auth::user()->id;
        }
        $data = Project::create($input);

        return $this->sendResponse($data, 'Project successfully created.');
    }

    public function index()
    {
        $list = ProjectResource::collection(Project::all());

        return $this->sendResponse($list);
    }

    public function update($id, Request $request)
    {
        $model = Project::where('id', $id)->first();
        if (empty($model)) {
            return $this->sendError('Not Found!');
        }
        if (Gate::allows('update-project', $model)) {
            $data = $request->only(['description', 'name']);
            $model->update($data);

            return $this->sendResponse($model, $this->successMsg);
        }
        return $this->sendError($this->errorAccess);
    }

    public function show($id)
    {
        $model = Project::where('id', $id)->first();
        if (empty($model)) {
            return $this->sendError('Not Found!');
        }
        return $this->sendResponse($model, $this->successMsg);
    }

    public function destroy($id, Request $request)
    {
        $model = Project::where('id', $id)->first();
        if (empty($model)) {
            return $this->sendError('Not Found!');
        }
        if (Gate::allows('destroy-project', $model)) {
            $model->delete();
            return $this->sendResponse($model, $this->deleteMsg);
        }
        return $this->sendError($this->errorAccess);
    }
}
