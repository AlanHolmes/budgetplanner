<?php

namespace App\Http\Controllers;

use App\Budget;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{

    /**
     * Load the create transaction form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Alan Holmes
     */
    public function create()
    {
        return view('transactions.create', [
            'budgets' => Auth::user()->budgets()->get(),
        ]);
    }

    /**
     * Create a new transaction
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author Alan Holmes
     */
    public function store()
    {
        $this->validate(request(), [
            'description' => ['required'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        $budget = Auth::user()->budgets()->findOrFail(request('budget_id'));

        $budget->transactions()->create([
            'description' => request('description'),
            'amount' => request('amount'),
            'date' => request('date'),
        ]);

        return redirect("/budgets/{$budget->id}");
    }
}
