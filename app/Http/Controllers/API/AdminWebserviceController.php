<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use App\Models\AndroidAdmin;
use App\Models\Media;
use App\Models\Message;
use App\Models\Post;
use App\Models\Program;
use App\User;
use Illuminate\Http\Request;
use App\Includes\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AdminWebserviceController extends Controller
{

    function login_admin(Request $req)
    {
        // check weather user with input email exists
        $user = User::where([
            ['email', '=', $req->input('email')],
        ])->first();

        if ($user) {
            // check user password
            if (Hash::check($req->input('password'), $user->password)) {
                // finding user related android admin and setting token
                $android_admin = AndroidAdmin::where([
                    ['user_id', '=', $user->id],
                ])->first();

                $android_admin->token = bin2hex(random_bytes(16));
                $android_admin->save();
                return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $android_admin->token);
            } else
                return sprintf('{"result_code": %u}', Constant::$INVALID_PASSWORD);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_EMAIL);
    }

    function check_token(Request $req)
    {
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_posts(Request $req, $type, $chunk_count, $page_count, $search_phrase, $group_id, $field_id)
    {
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();


        if (!$admin)
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);

        $query = [['user_id', '=', $admin->user->id]];
        $table = "posts";

        if ($type == Constant::$TYPE_MEDIA)
            $table = "media";
        elseif ($type == Constant::$TYPE_PROGRAM) {
            $table = "programs";
            if ($group_id != 'null')
                array_push($query, ['group_id', '=', $group_id]);

            if ($field_id != 'null')
                array_push($query, ['field_id', '=', $field_id]);

        } elseif ($type == Constant::$TYPE_MESSAGE) {
            $table = "messages";
            if ($group_id != 'null')
                array_push($query, ['group_id', '=', $group_id]);

            if ($field_id != 'null')
                array_push($query, ['field_id', '=', $field_id]);
        }

        if ($search_phrase != 'null')
            array_push($query, ['title', 'LIKE', '%' . $search_phrase . '%']);

        try {
            $items = DB::table($table)->where($query)->get();
            $last_items = (collect($items)->sortByDesc('id')->chunk($chunk_count))[$page_count];

            //putting the selected chunk in a new array to make it start from index zero
            $temp = [];
            $i = 0;
            foreach ($last_items as $item) {
                $temp[$i] = $item;
                $i++;
            }
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, collect($temp)->toJson());
        } catch (\Exception $e) {
            return sprintf('{"result_code": %u}', Constant::$NO_MORE_POSTS);
        }
    }

    function get_group_list(Request $req)
    {
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $user->groups()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function get_field_list(Request $req)
    {
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            return sprintf('{"result_code": %u, "data": %s}', Constant::$SUCCESS, $user->fields()->get()->toJson());
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    // <editor-fold desc = "POST CRUD">
    //*******************************************POST CRUD PART*********************************************************
    function create_post(Request $req){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();

            //saving program
            $post = Post::create([
                'title' =>  $req->input('title'),
                'preview_content' =>  $req->input('preview_content'),
                'content' =>  $req->input('content'),
            ]);

            $user->posts()->save($post);

            AdminController::notify("اطلاعیه جدید", $post->title, $user->fire_base_server_key,'/topics/all');


            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function update_post(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $post = Post::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($post){
                $post->title = $req->input('title');
                $post->preview_content = $req->input('preview_content');
                $post->content = $req->input('content');
                $post->save();

                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function delete_post(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $post = Post::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($post){
                $post->delete();
                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }
    //*******************************************END POST CRUD PART*****************************************************
    // </editor-fold>

    // <editor-fold desc = "PROGRAM CRUD">
    //*******************************************PROGRAM CRUD PART******************************************************
    function create_program(Request $req){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();

            // checking count limit
            $count = Program::where([
                ['user_id', '=', $user->id],
            ])->count();

            $limit = $user->program_count_limit;
            if ($count >= $limit)
                return sprintf('{"result_code": %u, "data": %s}', Constant::$COUNT_LIMIT, $limit);

            //saving program
            $program = Program::create([
                'title' =>  $req->input('title'),
                'preview_content' =>  $req->input('preview_content'),
                'content' =>  $req->input('content'),
                'group_id' => $req->input('group_id'),
                'field_id' => $req->input('field_id'),
            ]);

            $user->programs()->save($program);

            AdminController::notify("برنامه جدید", $program->title, $user->fire_base_server_key,'/topics/all');

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function update_program(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $program = Program::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($program){
                $program->title = $req->input('title');
                $program->preview_content = $req->input('preview_content');
                $program->content = $req->input('content');
                $program->group_id = $req->input('group_id');
                $program->field_id = $req->input('field_id');
                $program->save();

                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function delete_program(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $program = Program::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($program){
                $program->delete();
                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }
    //*******************************************END PROGRAM CRUD PART**************************************************
    // </editor-fold>

    // <editor-fold desc = "MEDIA CRUD">
    //*******************************************MEDIA CRUD PART********************************************************
    function create_media(Request $req){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();

            // checking count limit
            $count = Media::where([
                ['user_id', '=', $user->id],
            ])->count();

            $limit = $user->media_count_limit;
            if ($count >= $limit)
                return sprintf('{"result_code": %u, "data": %s}', Constant::$COUNT_LIMIT, $limit);

            //checking and saving media
            if (!$req->file('media')->isValid())
                return sprintf('{"result_code": %u}', Constant::$INVALID_FILE);

            $media = Media::create([
                'title' =>  $req->input('title'),
                'description' =>  $req->input('description'),
                'media' => null,
            ]);

            $user->medias()->save($media);
            $this->uploadFileToDisk('create', $req, $media,  'public', 'media');

            AdminController::notify(" رسانه جدید", $media->title, $user->fire_base_server_key,'/topics/all');

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function update_media(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $media = Media::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($media){
                $media->title = $req->input('title');
                $media->description = $req->input('description');
                $media->save();
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

            if ($req->hasFile('media')) {
                if (!$req->file('media')->isValid())
                    return sprintf('{"result_code": %u}', Constant::$INVALID_FILE);

                $this->uploadFileToDisk('update', $req, $media,  'public', 'media');
            }

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function delete_media(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $media = Media::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($media){
                $this->uploadFileToDisk('delete', $req, $media,  'public', 'media');
                $media->delete();
                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function uploadFileToDisk($action, $request, $media, $disk, $destination_path)
    {
        if ($action == 'update' || $action == 'delete'){
            \Storage::disk($disk)->delete($media->media);
            $media->media = null;

            if ($action == 'delete')
                return;
        }

        // 1. Generate a new file name
        $file = $request->file('media');
        $new_file_name = md5($file->getClientOriginalName().time()).'.'.$file->getClientOriginalExtension();

        // 2. Move the new file to the correct path
        $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

        // 3. Save the complete path to the database
        $media->media = $file_path;
        $media->save();

    }
    //*******************************************END MEDIA CRUD PART****************************************************
    // </editor-fold>


    // <editor-fold desc = "MESSAGE CRUD">
    //*******************************************MESSAGE CRUD PART********************************************************
    function create_message(Request $req){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();

            // saving message
            $message = Message::create([
                'title' =>  $req->input('title'),
                'group_id' =>  $req->input('group_id'),
                'field_id' => $req->input('field_id'),
                'gender' => $req->input('gender'),
                'content' =>  $req->input('content'),
            ]);

            $user->messages()->save($message);

            $to = '/topics/group_'.$message->gender.$message->group_id.$message->field_id;
            AdminController::notify("پیام جدید", $message->title, $user->fire_base_server_key, $to);

            return sprintf('{"result_code": %u}', Constant::$SUCCESS);
        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function update_message(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $message = Message::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($message){
                $message->title = $req->input('title');
                $message->group_id = $req->input('group_id');
                $message->field_id = $req->input('field_id');
                $message->gender = $req->input('gender');
                $message->content = $req->input('content');
                $message->save();

                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }

    function delete_message(Request $req, $id){
        $admin = AndroidAdmin::where([
            ['token', '=', $req->input('token')],
        ])->first();

        if ($admin) {
            $user = $admin->user()->first();
            $message = Message::where([
                ['user_id', '=', $user->id],
                ['id', '=', $id],
            ])->first();

            if ($message){
                $message->delete();
                return sprintf('{"result_code": %u}', Constant::$SUCCESS);
            }else
                return sprintf('{"result_code": %u}', Constant::$POST_NOT_EXIST);

        } else
            return sprintf('{"result_code": %u}', Constant::$INVALID_TOKEN);
    }
    //*******************************************END MESSAGE CRUD PART****************************************************
    // </editor-fold>

}
