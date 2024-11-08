<?php

namespace App\Repositories;

use App\Models\Faq;

class FaqRepository
{
    public function getAllFaqs()
    {
        return Faq::select('id', 'nome')->with('regiao')->get();
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
            'regiao' => $data['regiao'],
        ]);
    
        return $faq;
    }
    

    public function updateFaq(int $id, array $data)
    {        
       
        $faq = Faq::findOrFail($id);       
        $faq->update([
            'texto' => $data['texto'],
            'nome' => $data['nome'],
            'regiao' => $data['regiao'],
        ]);
    
        return $faq;
    }
}
