<?php

namespace App\Http\Controllers;

use App\Repositories\NoteRepository;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    protected $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    public function index()
    {
        $notes = $this->noteRepository->all();
        return response()->json($notes);
    }

    public function show($id)
    {
        $note = $this->noteRepository->find($id);
        return response()->json($note);
    }

    public function store(Request $request)
    {
       
        $validatedData = $request->validate([
            'content' => 'nullable|string',
            'color' => 'nullable|string',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'note_id' => 'nullable|integer',
        ]);

        $note = $this->noteRepository->create($validatedData);
        return response()->json($note, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'content' => 'nullable|string',
            'color' => 'nullable|string',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
        ]);

        $note = $this->noteRepository->update($id, $validatedData);
        return response()->json($note);
    }

    public function destroy($id)
    {
        $this->noteRepository->delete($id);
        return response()->json(['message' => 'Note deleted successfully']);
    }
}
