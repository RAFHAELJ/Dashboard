<?php

namespace App\Repositories;

use App\Models\Faq;

class FaqRepository
{
    public function getAllFaqs()
    {
        return Faq::select('id', 'nome')->get();
    }

    public function getFaqById($id)
    {
        return Faq::findOrFail($id); 
    }

    public function store(array $data)
    {
        $faq = Faq::create([
            'nome' => $data['nome'],
            'texto' => $data['texto'],
        ]);
    
        return $faq;
    }
    

    public function updateFaq(int $id, array $data)
    {        
        $faq = Faq::findOrFail($id);       
        $faq->update([
            'texto' => $data['texto'],
            'nome' => $data['nome'],
        ]);
    
        return $faq;
    }
}
