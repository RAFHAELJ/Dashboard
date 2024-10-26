<?php

namespace App\Repositories;

use App\Models\Form;

class FormRepository
{

    public function getAllForms()
    {
        return Form::all();
    }
    public function createForm(array $data)
    {
       // \dd($data);
        $form = new Form();
        $form->questions = json_encode($data['questions']);
        $form->title = $data['title'];
        $form->save();

        return $form;
    }

    public function getFormById($id)
    {
        return Form::findOrFail($id);
    }

    public function updateForm($id, array $data)
    {
        $form = Form::findOrFail($id);
        $form->questions = json_encode($data['questions']);
        $form->save();

        return $form;
    }

    public function deleteForm($id)
    {
        $form = Form::findOrFail($id);
        $form->delete();
    }
}
