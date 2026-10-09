<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm: tìm theo tên, lọc theo danh mục và trạng thái.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Flow thêm sản phẩm: form submit POST /products -> route products.store
     * -> ProductRequest validate -> lưu ảnh vào storage -> lưu qua Product Model
     * -> redirect về danh sách kèm thông báo.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return redirect()->route('products.index')
            ->with('success', "Đã thêm sản phẩm \"{$product->name}\".");
    }

    public function show(Product $product): View
    {
        $product->load('category');

        // Số lượng đã bán: chỉ tính các đơn Hoàn thành.
        $soldQuantity = $product->orderItems()
            ->whereHas('order', fn ($query) => $query->where('status', OrderStatus::Completed))
            ->sum('quantity');

        return view('products.show', compact('product', 'soldQuantity'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);
        $oldImage = $product->image;

        // Có ảnh mới thì thay ảnh cũ; tick "Xóa ảnh" thì bỏ ảnh.
        $imageChanged = $request->hasFile('image') || $request->boolean('remove_image');

        if ($imageChanged) {
            $data['image'] = $request->hasFile('image')
                ? $request->file('image')->store('products', 'public')
                : null;
        }

        $product->update($data);

        // Xóa file cũ sau khi đã lưu database thành công.
        if ($imageChanged) {
            $this->deleteImage($oldImage);
        }

        return redirect()->route('products.show', $product)
            ->with('success', "Đã cập nhật sản phẩm \"{$product->name}\".");
    }

    /**
     * Sản phẩm đã nằm trong order_items thì không xóa, để giữ lịch sử đơn hàng.
     * Thay vào đó chuyển trạng thái sang "Ngừng bán".
     */
    public function destroy(Product $product): RedirectResponse
    {
        $orderCount = $product->orderItems()->count();

        if ($orderCount > 0) {
            return back()->with('error', "Không thể xóa \"{$product->name}\" vì sản phẩm đã có trong {$orderCount} đơn hàng. Hãy chuyển trạng thái sang \"Ngừng bán\" để giữ lịch sử đơn hàng.");
        }

        $product->delete();
        $this->deleteImage($product->image);

        return redirect()->route('products.index')
            ->with('success', "Đã xóa sản phẩm \"{$product->name}\".");
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
