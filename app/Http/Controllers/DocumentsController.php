<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use File;
use Auth;
use App\Documents;
use App\User;

class DocumentsController extends Controller
{
    public function genNewFolder($id, $root, $counter = 0)
    {
        $new_name = "new folder".$counter;
        if($counter == 0)
        {
            $new_name = "new folder";
        }
        $count = Documents::where('user_id', $id)
                            ->where('root', $root)
                            ->where('name', $new_name)
                            ->count();
        if($count == 0)
        {
            return $new_name;
        }
        else
        {
            return $this->genNewFolder($id, $root, $counter+1);
        }
    }

    public function show(Request $request)
    {
        $documents = User::find($request->id)->documents()->where('root', $request->root)->get();

        return response()->json(compact('documents'));
    }

    public function create(Request $request)
    {
        $new_name = $this->genNewFolder($request->id, $request->root);
        $documents = new Documents;
        $documents->user_id = $request->id;
        $documents->root = $request->root;
        $documents->name = $new_name;
        $documents->type = 'folder';
        $documents->ext = 'folder';
        $documents->save();

        return response()->json(compact('documents'), 200);
    }

    public function update(Request $request, $id)
    {
        $check = Documents::where("id","!=", $id)
                            ->where("user_id", $request->user_id)
                            ->where("root", $request->root)
                            ->where("name", $request->name)->count();
        if($check > 0)
        {
            return response()->json(array("error" => "name already exist"), 501);
        }

        $documents = Documents::findOrFail($id);
        $documents->name = $request->name;
        $documents->save();

        return response()->json(compact('documents'));
    }

    public function delete(Request $request, $id)
    {
        $documents = Documents::find($id);

        if($documents->type == "file")
        {
            File::delete('file_upload/'.$documents->name);
        }

        $documents->delete();

        return 204;
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xls,doc,pdf,zip,rar,docx,xlsx|max:2048'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }

        if($files = $request->file('file'))
        {
            $destinationPath = 'file_upload/'; // upload path
            $files->move($destinationPath, $files->getClientOriginalName());
        
            $documents = new Documents;
            $documents->user_id = $request->user_id;
            $documents->root = $request->root;
            $documents->name = $files->getClientOriginalName();
            $documents->type = 'file';
            $documents->ext = $files->getClientOriginalExtension();
            $documents->save();

            return response()->json(compact('documents'), 200);
        }
    }
}
