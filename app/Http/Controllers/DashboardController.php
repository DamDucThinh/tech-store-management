<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Quản lý thấy số liệu toàn cửa hàng, nhân viên bán hàng chỉ thấy số liệu đơn của mình.
     * Doanh thu chỉ tính đơn Hoàn thành.
     */
    public function __invoke(Request $request): View
    {
        $employee = $request->user();
        $orders = fn () => Order::query()->visibleTo($employee);

        $statusCounts = $orders()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'revenue' => $orders()->where('status', OrderStatus::Completed)->sum('total_amount'),
            'revenue_this_month' => $orders()
                ->where('status', OrderStatus::Completed)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('total_amount'),
            'orders' => $statusCounts->sum(),
            'selling_products' => Product::selling()->count(),
        ];

        $recentOrders = $orders()
            ->with('employee.profile')
            ->latest()
            ->latest('id')
            ->take(5)
            ->get();

        $topProducts = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->whereHas('order', fn ($query) => $query->visibleTo($employee)->where('status', OrderStatus::Completed))
            ->with('product.category')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'statusCounts', 'recentOrders', 'topProducts'));
    }
}
