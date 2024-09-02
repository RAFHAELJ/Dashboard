<?php

namespace App\Repositories;

use App\Models\Note;

class NoteRepository 
{
    public function all()
    {
        return Note::all();
    }

    public function find($id)
    {
        return Note::findOrFail($id);
    }

    public function create(array $data)
    {
        return Note::create($data);
    }

    public function update($id, array $data)
    {
        $note = $this->find($id);
        $note->update($data);
        return $note;
    }

    public function delete($id)
    {
        $note = $this->find($id);
        $note->delete();
    }
}
