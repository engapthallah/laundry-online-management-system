<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Models\Review;
use App\Models\SupportMessage;
use App\Models\DeliveryAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get start and end dates based on period string.
     */
    public static function getDateRange(string $period, ?string $from = null, ?string $to = null): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;

            case 'last7days':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;

            case 'last30days':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;

            case 'lastmonth':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'thisyear':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;

            case 'custom':
                if ($from && $to) {
                    try {
                        $start = Carbon::parse($from)->startOfDay();
                        $end = Carbon::parse($to)->endOfDay();
                    } catch (\Exception $e) {
                        // fallback if parsing fails
                        $start = $now->copy()->startOfMonth();
                        $end = $now->copy()->endOfMonth();
                    }
                } else {
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                }
                break;

            case 'thismonth':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Get KPI summary values for the period and compare them to the previous period.
     */
    public static function getKpiCards(Carbon $start, Carbon $end): array
    {
        // Current values
        $total_revenue = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        $total_orders = Order::whereBetween('created_at', [$start, $end])->count();

        $new_customers = User::where('role', 'customer')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $delivered_orders = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Secondary metrics
        $confirmed_orders = Order::where('status', 'pending_pickup')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $avg_order_value = Order::whereBetween('created_at', [$start, $end])
            ->avg('total_price') ?? 0.0;

        $avg_rating = Review::whereBetween('created_at', [$start, $end])
            ->avg('rating') ?? 0.0;

        $pending_support = SupportMessage::where('status', 'pending')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Calculate previous range
        $days = $start->diffInDays($end) + 1;
        $prevStart = $start->copy()->subDays($days);
        $prevEnd = $end->copy()->subDays($days);

        // Previous values
        $prev_revenue = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$prevStart, $prevEnd])
            ->sum('amount');

        $prev_orders = Order::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $prev_customers = User::where('role', 'customer')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prev_delivered = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        return [
            'total_revenue' => (float)$total_revenue,
            'total_orders' => $total_orders,
            'new_customers' => $new_customers,
            'delivered_orders' => $delivered_orders,
            
            'confirmed_orders' => $confirmed_orders,
            'avg_order_value' => (float)$avg_order_value,
            'avg_rating' => (float)$avg_rating,
            'pending_support' => $pending_support,

            'trends' => [
                'total_revenue' => self::calculateTrend($total_revenue, $prev_revenue),
                'total_orders' => self::calculateTrend($total_orders, $prev_orders),
                'new_customers' => self::calculateTrend($new_customers, $prev_customers),
                'delivered_orders' => self::calculateTrend($delivered_orders, $prev_delivered),
            ]
        ];
    }

    /**
     * Group completed payment amounts by day.
     */
    public static function getRevenueByDay(Carbon $start, Carbon $end): array
    {
        $payments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->get()
            ->groupBy(function($payment) {
                return $payment->paid_at->format('Y-m-d');
            })
            ->map(function($group) {
                return $group->sum('amount');
            })
            ->toArray();

        $labels = [];
        $data = [];
        
        $tempDate = $start->copy();
        while ($tempDate->lte($end)) {
            $dateStr = $tempDate->format('Y-m-d');
            $labels[] = $tempDate->format('M d');
            $data[] = (float)($payments[$dateStr] ?? 0.0);
            $tempDate->addDay();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get 12 months of revenue data for the year.
     */
    public static function getRevenueByMonth(int $year): array
    {
        $payments = Payment::where('status', 'completed')
            ->whereYear('paid_at', $year)
            ->get()
            ->groupBy(function($payment) {
                return (int) $payment->paid_at->format('m');
            })
            ->map(function($group) {
                return $group->sum('amount');
            })
            ->toArray();

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = (float)($payments[$m] ?? 0.0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get count of orders per status for the period.
     */
    public static function getOrdersByStatus(Carbon $start, Carbon $end): array
    {
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = [
            'pending_pickup',
            'picked_up_from_customer',
            'delivered_to_laundry',
            'processing',
            'ready_for_delivery',
            'picked_up_from_laundry',
            'on_the_way',
            'delivered',
            'cancelled'
        ];
        $result = [];
        foreach ($statuses as $status) {
            $result[$status] = $orders[$status] ?? 0;
        }

        return $result;
    }

    /**
     * Get counts and completed revenue per payment method.
     */
    public static function getOrdersByPaymentMethod(Carbon $start, Carbon $end): array
    {
        $counts = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('payment_method, COUNT(*) as count')
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        $revenue = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $methods = ['cash' => 'Cash on Delivery', 'zaad' => 'Zaad', 'edahab' => 'Edahab'];
        $resultCounts = [];
        $resultRevenue = [];

        foreach ($methods as $key => $label) {
            $resultCounts[$label] = $counts[$key] ?? 0;
            $resultRevenue[$label] = (float)($revenue[$key] ?? 0.0);
        }

        return [
            'counts' => $resultCounts,
            'revenue' => $resultRevenue
        ];
    }

    /**
     * Get top services by order items count and revenue.
     */
    public static function getTopServices(Carbon $start, Carbon $end, int $limit = 5): array
    {
        $items = OrderItem::whereHas('order', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->with('service')
            ->selectRaw('service_id, COUNT(*) as total_orders, SUM(quantity) as total_quantity, SUM(price * quantity) as total_revenue, AVG(price) as avg_price')
            ->groupBy('service_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        $totalRevenueAll = OrderItem::whereHas('order', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->selectRaw('SUM(price * quantity) as total')
            ->value('total') ?? 0.0;

        $result = [];
        foreach ($items as $item) {
            $total_revenue = (float) $item->total_revenue;
            $share = $totalRevenueAll > 0 ? ($total_revenue / $totalRevenueAll * 100) : 0;

            $result[] = [
                'name' => $item->service ? $item->service->name : 'Unknown Service',
                'total_orders' => $item->total_orders,
                'total_quantity' => (int) $item->total_quantity,
                'total_revenue' => $total_revenue,
                'avg_price' => (float) $item->avg_price,
                'share_percentage' => $share,
            ];
        }

        return $result;
    }

    /**
     * Get performance metrics per staff member.
     */
    public static function getStaffPerformance(Carbon $start, Carbon $end): array
    {
        $staffMembers = User::where('role', 'staff')->get();
        $result = [];

        foreach ($staffMembers as $staff) {
            $ordersQuery = Order::where('staff_id', $staff->id)
                ->whereBetween('created_at', [$start, $end]);

            $totalAssigned = $ordersQuery->count();
            $ordersCompleted = (clone $ordersQuery)
                ->whereIn('status', ['ready_for_delivery', 'delivered'])
                ->count();

            $completionRate = $totalAssigned > 0 ? ($ordersCompleted / $totalAssigned * 100) : 0;

            // Average processing time in hours (from confirmed status/created_at to ready_for_delivery/delivered status/updated_at)
            if (DB::getDriverName() === 'sqlite') {
                $avgProcessingTime = (clone $ordersQuery)
                    ->whereIn('status', ['ready_for_delivery', 'delivered'])
                    ->selectRaw('AVG((strftime("%s", updated_at) - strftime("%s", created_at)) / 3600) as avg_time')
                    ->value('avg_time');
            } else {
                $avgProcessingTime = (clone $ordersQuery)
                    ->whereIn('status', ['ready_for_delivery', 'delivered'])
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time')
                    ->value('avg_time');
            }

            $result[] = [
                'name' => $staff->name,
                'total_orders_handled' => $totalAssigned,
                'orders_completed' => $ordersCompleted,
                'completion_rate' => $completionRate,
                'avg_processing_time' => $avgProcessingTime !== null ? (float)$avgProcessingTime : 0.0,
            ];
        }

        return $result;
    }

    /**
     * Get performance metrics per delivery agent.
     */
    public static function getDeliveryPerformance(Carbon $start, Carbon $end): array
    {
        $agents = User::where('role', 'delivery')->get();
        $result = [];

        foreach ($agents as $agent) {
            $assignmentsQuery = DeliveryAssignment::where('delivery_agent_id', $agent->id)
                ->whereBetween('delivery_assignments.created_at', [$start, $end]);

            $totalAssigned = $assignmentsQuery->count();
            $totalDelivered = (clone $assignmentsQuery)
                ->where('status', 'delivered')
                ->count();

            $deliveryRate = $totalAssigned > 0 ? ($totalDelivered / $totalAssigned * 100) : 0;

            if (DB::getDriverName() === 'sqlite') {
                $avgDeliveryTime = (clone $assignmentsQuery)
                    ->where('status', 'delivered')
                    ->whereNotNull('assigned_at')
                    ->whereNotNull('delivered_at')
                    ->selectRaw('AVG((strftime("%s", delivered_at) - strftime("%s", assigned_at)) / 3600) as avg_time')
                    ->value('avg_time');
            } else {
                $avgDeliveryTime = (clone $assignmentsQuery)
                    ->where('status', 'delivered')
                    ->whereNotNull('assigned_at')
                    ->whereNotNull('delivered_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assigned_at, delivered_at)) as avg_time')
                    ->value('avg_time');
            }

            // On-Time Delivery criteria: delivered_at <= orders.delivery_time (or within 24 hours if null)
            $onTimeDelivered = (clone $assignmentsQuery)
                ->where('delivery_assignments.status', 'delivered')
                ->join('orders', 'delivery_assignments.order_id', '=', 'orders.id')
                ->where(function($query) {
                    $query->whereRaw('delivery_assignments.delivered_at <= orders.delivery_time')
                          ->orWhereNull('orders.delivery_time');
                })
                ->count();

            $onTimeRate = $totalDelivered > 0 ? ($onTimeDelivered / $totalDelivered * 100) : 0;

            $result[] = [
                'name' => $agent->name,
                'total_assigned' => $totalAssigned,
                'total_delivered' => $totalDelivered,
                'delivery_rate' => $deliveryRate,
                'avg_delivery_time' => $avgDeliveryTime !== null ? (float)$avgDeliveryTime : 0.0,
                'on_time_rate' => $onTimeRate,
            ];
        }

        return $result;
    }

    /**
     * Get customer growth and cumulative metrics.
     */
    public static function getCustomerGrowth(int $year): array
    {
        $customers = User::where('role', 'customer')
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(function($user) {
                return (int) $user->created_at->format('m');
            })
            ->map(function($group) {
                return $group->count();
            })
            ->toArray();

        $totalBeforeYear = User::where('role', 'customer')
            ->where('created_at', '<', Carbon::create($year, 1, 1))
            ->count();

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $newCustomers = [];
        $cumulativeCustomers = [];

        $runningTotal = $totalBeforeYear;
        for ($m = 1; $m <= 12; $m++) {
            $count = $customers[$m] ?? 0;
            $newCustomers[] = $count;
            $runningTotal += $count;
            $cumulativeCustomers[] = $runningTotal;
        }

        return [
            'labels' => $labels,
            'new_customers' => $newCustomers,
            'cumulative_customers' => $cumulativeCustomers
        ];
    }

    /**
     * Get average ratings and star breakdown.
     */
    public static function getReviewStats(Carbon $start, Carbon $end): array
    {
        $reviews = Review::whereBetween('created_at', [$start, $end])->get();

        $total = $reviews->count();
        $avg = $total > 0 ? $reviews->avg('rating') : 0.0;

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($reviews as $review) {
            $rating = (int) $review->rating;
            if ($rating >= 1 && $rating <= 5) {
                $distribution[$rating]++;
            }
        }

        return [
            'average' => (float) $avg,
            'total' => $total,
            'distribution' => $distribution
        ];
    }

    /**
     * Get support message summary statistics.
     */
    public static function getSupportStats(Carbon $start, Carbon $end): array
    {
        $messages = SupportMessage::whereBetween('created_at', [$start, $end]);

        $total = $messages->count();
        $pending = (clone $messages)->where('status', 'pending')->count();
        $resolved = (clone $messages)->where('status', 'resolved')->count();
        $ignored = (clone $messages)->where('status', 'ignored')->count();

        if (DB::getDriverName() === 'sqlite') {
            $avgResponseHours = (clone $messages)
                ->where('status', 'resolved')
                ->whereNotNull('replied_at')
                ->selectRaw('AVG((strftime("%s", replied_at) - strftime("%s", created_at)) / 3600) as avg_time')
                ->value('avg_time');
        } else {
            $avgResponseHours = (clone $messages)
                ->where('status', 'resolved')
                ->whereNotNull('replied_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, replied_at)) as avg_time')
                ->value('avg_time');
        }

        return [
            'total_messages' => $total,
            'pending' => $pending,
            'resolved' => $resolved,
            'ignored' => $ignored,
            'avg_response_hours' => $avgResponseHours !== null ? (float)$avgResponseHours : 0.0,
        ];
    }

    /**
     * Get order count grouped by day (helper for volume trend).
     */
    public static function getOrdersByDay(Carbon $start, Carbon $end): array
    {
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })
            ->map(function($group) {
                return $group->count();
            })
            ->toArray();

        $labels = [];
        $data = [];
        
        $tempDate = $start->copy();
        while ($tempDate->lte($end)) {
            $dateStr = $tempDate->format('Y-m-d');
            $labels[] = $tempDate->format('M d');
            $data[] = (int)($orders[$dateStr] ?? 0);
            $tempDate->addDay();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get top 10 customers by order count in period.
     */
    public static function getTopCustomers(Carbon $start, Carbon $end, int $limit = 10): array
    {
        $ordersGrouped = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('customer_id, COUNT(*) as orders_count, SUM(total_price) as total_spent, AVG(total_price) as avg_order_value, MAX(created_at) as last_order_date')
            ->groupBy('customer_id')
            ->orderByDesc('orders_count')
            ->limit($limit)
            ->with('customer')
            ->get();

        $result = [];
        foreach ($ordersGrouped as $index => $item) {
            $customer = $item->customer;
            $maskedName = 'Unknown Customer';
            if ($customer) {
                $parts = explode(' ', $customer->name);
                if (count($parts) > 1) {
                    $maskedName = $parts[0] . ' ' . substr($parts[count($parts) - 1], 0, 1) . '.';
                } else {
                    $maskedName = $customer->name;
                }
            }

            $result[] = [
                'rank' => $index + 1,
                'name' => $maskedName,
                'orders_count' => $item->orders_count,
                'total_spent' => (float)$item->total_spent,
                'avg_order_value' => (float)$item->avg_order_value,
                'last_order_date' => $item->last_order_date ? Carbon::parse($item->last_order_date)->format('Y-m-d') : null,
            ];
        }

        return $result;
    }

    /**
     * Calculate relative trend between current and previous values.
     */
    private static function calculateTrend($current, $previous): array
    {
        if ($previous == 0) {
            $pct = $current > 0 ? 100.0 : 0.0;
        } else {
            $pct = (($current - $previous) / $previous) * 100;
        }

        if ($pct > 0) {
            return ['direction' => 'up', 'percentage' => $pct];
        } elseif ($pct < 0) {
            return ['direction' => 'down', 'percentage' => abs($pct)];
        } else {
            return ['direction' => 'same', 'percentage' => 0.0];
        }
    }
}
