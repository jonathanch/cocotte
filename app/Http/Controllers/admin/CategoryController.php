<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Image;


class CategoryController extends Controller
{

    public function index(Request $request) {
        $categories = Category::latest();

        if(!empty($request->get('keyword'))) {
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
    }
            $categories = $categories->paginate(10);


        return view ('admin.category.list',compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->input('name');
            $category->slug = $request->input('slug');
            $category->status = $request->input('status');
            $category->save();

            //stocker l'image
            if (!empty($request->image_id)) {

                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                // Générer image thumbail
            /*  $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = \Intervention\Image\Facades\Image::make($sPath);
                $img->resize(450, 600);
                $img->save($dPath); */

                $category->image = $newImageName;
                $category->save();


            }

            Session::flash('success', 'La catégorie a été ajoutée');

            return response()->json([
                'status' => true,
                'message' => 'La catégorie a été ajoutée',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($categoryId, Request $request) {
        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId, Request $request) {

        $category = Category::find($categoryId);

        if(empty($category)) {
            Session::flash('error','Category non trouvée');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'La catégorie n a pas été trouvée',
            ]);
        }


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if ($validator->passes()) {

            $category->name = $request->input('name');
            $category->slug = $request->input('slug');
            $category->status = $request->input('status');
            $category->save();

            $oldImage = $category->image;

            //stocker l'image
            if (!empty($request->image_id)) {

                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                // Générer image thumbail
              $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = \Intervention\Image\Facades\Image::make($sPath);
                // $img->resize(450, 600);
                // add callback functionality to retain maximal original image size
                $img->fit(450, 600, function ($constraint) {
                $constraint->upsize();
                });
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();

                //Supprimer Old Images
                File::delete(public_path().'/uploads/category/thumbs/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);



            }

            Session::flash('success', 'La catégorie a été modifiée');

            return response()->json([
                'status' => true,
                'message' => 'La catégorie a été modifiée',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    public function destroy($categoryId, Request $request) {
        $category = Category::find($categoryId);
        if (empty($category)) {
            Session::flash('error','Category non trouvée');
            return response()->json([
                'status' => true,
                'message' => 'Category non trouvée'
            ]);
         //   return redirect()->route('categories.index');
        }

        File::delete(public_path().'/uploads/category/thumbs/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();

        Session::flash('success','Category delete');

        return response()->json([
            'status' => true,
            'message' => 'Category supprimée'


        ]);

}

}
