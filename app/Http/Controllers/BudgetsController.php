<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetsController extends Controller
{
    /**
     * Load the create budget form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Alan Holmes
     */
    public function create()
    {
        return view('budgets.create');
    }

    /**
     * Create a new budget
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author Alan Holmes
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => ['required'],
            'budget' => ['required', 'numeric', 'min:5'],
            'frequency' => ['required', 'in:monthly,weekly'],
            'start_on' => ['required', 'integer', 'min:0'],
        ]);

        Auth::user()->budgets()->create([
            'name' => request('name'),
            'description' => request('description'),
            'budget' => request('budget') * 100,
            'frequency' => request('frequency'),
            'start_on' => request('start_on'),
        ]);

        return redirect('/budgets');
    }

    /**
     * Load the form to edit the given budget
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Alan Holmes
     */
    public function edit($id)
    {
        $budget = Auth::user()->budgets()->findOrFail($id);

        return view('budgets.edit', compact('budget'));
    }

    /**
     * Update the given budget
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @author Alan Holmes
     */
    public function update($id)
    {
        $budget = Auth::user()->budgets()->findOrFail($id);

        $this->validate(request(), [
            'name' => ['required'],
            'budget' => ['required', 'numeric', 'min:5'],
            'frequency' => ['required', 'in:monthly,weekly'],
            'start_on' => ['required', 'integer', 'min:0'],
        ]);


        $budget->update([
            'name' => request('name'),
            'description' => request('description'),
            'budget' => request('budget') * 100,
            'frequency' => request('frequency'),
            'start_on' => request('start_on'),
        ]);

        return redirect('/budgets');
    }
}
