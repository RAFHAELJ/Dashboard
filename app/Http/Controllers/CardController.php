<?php

namespace App\Http\Controllers;

use App\Repositories\CardRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardController extends Controller
{
    protected $cardRepo;

    public function __construct(CardRepository $cardRepo)
    {
        $this->cardRepo = $cardRepo;
    }

    public function index()
    {
        $cards = $this->cardRepo->all();
        return Inertia::render('Home', [
            'cards' => $cards
        ]);
    }

    public function show($id)
    {
        $card = $this->cardRepo->find($id);
        if (!$card) {
            return redirect()->route('cards.index')->with('error', 'Card not found');
        }
        return Inertia::render('Cards/Show', [
            'card' => $card
        ]);
    }

    public function create()
    {
        return Inertia::render('Cards/Create');
    }

    public function store(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'title' => 'required|string',
            'content' => 'nullable',
            'url' => 'nullable|string',
            'type' => 'required|string',
            'chart_options' => 'nullable|array',
            'page' => 'nullable|string',
        ]);

        $this->cardRepo->create($request);

        return redirect()->route('cards.index')->with('success', 'Card created successfully');
    }

    public function edit($id)
    {
        $card = $this->cardRepo->find($id);
        if (!$card) {
            return redirect()->route('cards.index')->with('error', 'Card not found');
        }
        return Inertia::render('Cards/Edit', [
            'card' => $card
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'nullable|string',
            'url' => 'nullable|string',
            'type' => 'required|string',
            'chartOptions' => 'nullable|array',
            'page' => 'required|string',
        ]);

        $this->cardRepo->update($id, $request->all());

        return redirect()->route('cards.index')->with('success', 'Card updated successfully');
    }

    public function destroy($id)
    {
        $this->cardRepo->delete($id);

        return redirect()->route('cards.index')->with('success', 'Card deleted successfully');
    }
}
