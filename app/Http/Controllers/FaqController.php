<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\FaqRequest;
use App\Repositories\FaqRepository;

class FaqController extends Controller
{
    protected $faqRepository;

    public function __construct(FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    // Exibir a lista de FAQs com Inertia
    public function index()
    {
        $faqs = $this->faqRepository->getAllFaqs(); // Retorna a lista de FAQs
        return Inertia::render('Faq', [
            'faqList' => $faqs // Passa para o componente Vue
        ]);
    }
   
    public function show($id)
    {
        $faq = $this->faqRepository->getFaqById($id); 
    
            return response()->json($faq);     
    
        
    }public function store(FaqRequest $request)
    {
     
    
        $created = $this->faqRepository->store($request->only('nome', 'texto'));
    
        if ($created) {
            return back()->with('success', 'Nova FAQ criada com sucesso!');
        }
    
        return back()->withErrors(['message' => 'Erro ao Criar o FAQ.']);
    }    

    public function update(FaqRequest $request, $id)
    {
        
     
       
        $updated = $this->faqRepository->updateFaq($id, $request->only('nome', 'texto'));

        if ($updated) {
            return 'ok';
        }
        return 'fail';
    }
}
