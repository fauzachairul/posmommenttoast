<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Carbon\Carbon;

class TransactionHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /* ===============================
     | STATE
     =============================== */
    public $dateFilter = null;
    public $selectedOrder = null;
    public $isShowModal = false;

    /* ===============================
     | MOUNT
     =============================== */
    public function mount()
    {
        $this->dateFilter = null;
    }

    /* ===============================
     | RESET PAGINATION WHEN FILTER CHANGES
     =============================== */
    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    /* ===============================
     | SHOW ORDER DETAIL (MODAL)
     =============================== */
    public function showDetail($orderId)
    {
        $this->selectedOrder = Order::with([
            'items.product:id,name,price,image'
        ])
            ->where('status', 'completed')
            ->findOrFail($orderId);

        $this->isShowModal = true;
    }

    public function closeModal()
    {
        $this->reset(['selectedOrder', 'isShowModal']);
    }

    /* ===============================
     | RENDER
     =============================== */
    public function render()
    {
        /**
         * ---------------------------------------------
         * BASE QUERY
         * ---------------------------------------------
         */
        $baseQuery = Order::query()
            ->where('status', 'completed');

        if ($this->dateFilter) {
            $baseQuery->whereDate('transaction_date', $this->dateFilter);
        }

        /**
         * ---------------------------------------------
         * LIST DATA (LIGHTWEIGHT)
         * ---------------------------------------------
         */
        $orders = (clone $baseQuery)
            ->select('id', 'transaction_code', 'customer_name', 'transaction_date', 'total_amount', 'status')
            ->latest()
            ->paginate(5);

        /**
         * ---------------------------------------------
         * TOTAL REVENUE (SAME FILTER)
         * ---------------------------------------------
         */
        $dailyTotal = (clone $baseQuery)->sum('total_amount');

        return view('livewire.transaction-history', [
            'orders'     => $orders,
            'dailyTotal' => $dailyTotal,
        ]);
    }
}
