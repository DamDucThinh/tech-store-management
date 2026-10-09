<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Quản lý thấy mọi đơn hàng, nhân viên bán hàng chỉ thấy đơn mình tạo (scope visibleTo).
     */
    public function index(Request $request): View
    {
        $employee = $request->user();

        $orders = Order::query()
            ->visibleTo($employee)
            ->with('employee.profile')
            ->withCount('items')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('customer_name', 'like', $keyword)->orWhere('customer_phone', 'like', $keyword));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($employee->isManager() && $request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->integer('employee_id')))
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $employees = $employee->isManager()
            ? Employee::with('profile')->orderBy('username')->get()
            : collect();

        return view('orders.index', compact('orders', 'employees'));
    }

    public function create(): View
    {
        $products = Product::selling()->with('category')->orderBy('name')->get();

        return view('orders.create', compact('products'));
    }

    /**
     * Tạo đơn hàng và các dòng hàng trong một transaction: lỗi ở bất kỳ bước nào
     * thì không có gì được lưu. Giá lấy từ sản phẩm tại thời điểm đặt hàng và
     * lưu vào order_items.price, không lấy giá do form gửi lên.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data, $request) {
            $items = collect($data['items']);
            $products = Product::selling()
                ->whereIn('id', $items->pluck('product_id'))
                ->get()
                ->keyBy('id');

            if ($products->count() !== $items->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Có sản phẩm vừa ngừng bán, vui lòng kiểm tra lại đơn hàng.',
                ]);
            }

            $order = $request->user()->orders()->create([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'status' => OrderStatus::Pending,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $product = $products[$item['product_id']];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $total += $product->price * $item['quantity'];
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });

        return redirect()->route('orders.show', $order)
            ->with('success', "Đã tạo đơn hàng #{$order->id}.");
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['employee.profile', 'items.product.category']);

        return view('orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái theo đúng thứ tự cho phép (xem App\Enums\OrderStatus).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('updateStatus', $order);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $newStatus = OrderStatus::from($validated['status']);

        if (! $order->status->canTransitionTo($newStatus)) {
            return back()->with('error', "Không thể chuyển đơn từ \"{$order->status->label()}\" sang \"{$newStatus->label()}\".");
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', "Đơn hàng #{$order->id} đã chuyển sang \"{$newStatus->label()}\".");
    }

    public function destroy(Order $order): RedirectResponse
    {
        Gate::authorize('delete', $order);

        $order->delete(); // order_items bị xóa theo nhờ cascadeOnDelete

        return redirect()->route('orders.index')
            ->with('success', "Đã xóa đơn hàng #{$order->id}.");
    }
}
