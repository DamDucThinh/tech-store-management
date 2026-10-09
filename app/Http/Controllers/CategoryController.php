<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create', ['category' => new Category]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return redirect()->route('categories.index')
            ->with('success', "Đã thêm danh mục \"{$category->name}\".");
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', "Đã cập nhật danh mục \"{$category->name}\".");
    }

    /**
     * Không cho xóa danh mục khi còn sản phẩm, vì products.category_id đang
     * tham chiếu tới nó. Người dùng cần chuyển sản phẩm sang danh mục khác trước.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return back()->with('error', "Không thể xóa danh mục \"{$category->name}\" vì đang có {$productCount} sản phẩm. Hãy chuyển các sản phẩm sang danh mục khác trước.");
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Đã xóa danh mục \"{$category->name}\".");
    }
}
