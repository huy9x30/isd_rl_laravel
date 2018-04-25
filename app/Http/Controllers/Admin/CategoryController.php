<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use Carbon\Carbon;
use Validator;

class CategoryController extends Controller
{
    /**
     * Auth guard before calling controller functions.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show() {
    	$categories = Category::paginate(10);

    	return view('admin.categories', compact('categories'));
    }


    public function showCreateForm() {
        return view('admin.categoriesCreate');
    }


    public function showEditForm($categoryId) {
        $category = Category::find($categoryId);

        return view('admin.categoriesEdit', compact('category'));
    }

    public function create(Request $request) {
        if ($request->isMethod('post')) {
            try {
                $rule = [
                    'name' => 'required'
                ];

                $messages = [
                    'name.required' => 'Tên nhóm sản phẩm là bắt buộc'
                ];

                $validator = Validator::make($request->all(), $rule, $messages);
                
                if ($validator->fails()) {
                    return redirect()
                                ->back()
                                ->withErrors($validator)
                                ->withInput();
                }
                if (Category::where('name', strtolower($request->name))->first())
                {
                    return back()->with('error', 'Tên nhóm chính đã tồn tại.');
                }

                $category = new Category;
                $category->name = strtolower($request->name);
                $category->created_at = Carbon::now();
                $category->updated_at = Carbon::now();
                $category->save();

                return back()->with('success', 'Tạo mới thành công');
            } catch (Exception $e){
                return back()->with('error', 'Có lỗi xảy ra trong quá trình tạo mới. Vui lòng thử lại');
            }
        }
    }

    public function edit(Request $request, $categoryId) {
        try {
            $rule = [
                'name' => 'required'
            ];

            $messages = [
                'name.required' => 'Tên nhóm sản phẩm là bắt buộc'
            ];

            $validator = Validator::make($request->all(), $rule, $messages);
            
            if ($validator->fails()) {
                return redirect()
                            ->back()
                            ->withErrors($validator)
                            ->withInput();
                }          

                $category = Category::find($categoryId);

                $category->name = $request->name;
                $category->save();
                
                return back()->with('success', 'Cập nhật thành công.');
        } catch(Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra trong quá trình cập nhật. Vui lòng thử lại');
        }        
    }

    public function delete(Request $request, $categoryId) {
        try {
            if ($request->isMethod('post')) {
                $category = Category::find($categoryId);
                $categoryName = $category->name;
                $category->delete();

                return back()->with('success', 'Xóa "' . $categoryName . '" thành công');
            }
        } catch(Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra trong quá trình cập nhật. Vui lòng thử lại');
        }  
    }
}