<?php

namespace App\Http\Controllers;

use auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\CsvExportService;
use App\Http\Requests\RadioRequest;
use App\Repositories\LogRepository;
use App\Repositories\RadioRepository;

class RadioController extends Controller {
    protected $radioRepository;
    protected $csvExportService;
    protected $logRepository;

    public function __construct(RadioRepository $radioRepository, LogRepository $logRepository, CsvExportService $csvExportService)
    {
        $this->radioRepository = $radioRepository;
        $this->logRepository = $logRepository;
        $this->csvExportService = $csvExportService;
    }

    public function export(Request $request)
    {
        $startDate = $request->input('startD');
        $endDate = $request->input('endD');
        $username = $request->input('username');
        $mac = $request->input('mac');
        $region = $request->input('region');

        $data = $this->radioRepository->getAllTrackedUsers($startDate, $endDate, $username, $mac, $region);

        $headers = ['ID', 'Nome', 'Início', 'Fim', 'Duração', 'Entrada', 'Saída', 'MAC', 'Usuário'];
        return $this->csvExportService->downloadCsv('Relatorio de acessos', $headers, $data);
    }
    public function index(Request $request,)
    {
        $radios = $this->radioRepository->all($request,$request->input('per_page'));
    
       
        
        if (request()->wantsJson()) {
            return response()->json($radios);
        }    
      
        return Inertia::render('radios/RadiosListar', [
            'radios' => $radios
        ]);
      
    }
    public function indexSelect(Request $request)
    {
        $radios = $this->radioRepository->indexSelect($request);
    
       
        if (request()->wantsJson()) {
            return response()->json($radios);
        }    
      
        return Inertia::render('radios/RadiosListar', [
            'radios' => $radios
        ]);
    }

    public function radioRelatorio(Request $request)
    {
        
        $radios = $this->radioRepository->radioRelatorio($request);
    
        
        return Inertia::render('radios/RelatoriosRadios', [
            'radios' => $radios,
            'filters' => $request->all(), 
        ]);
    }
    public function rastrearRadiosUso(Request $request)
    {
        
        $radios = $this->radioRepository->rastrearRadiosUso($request);
    
        
        return Inertia::render('radios/RastrearRadiosUso', [
            'radios' => $radios,
            'filters' => $request->all(), 
        ]);
    }
    public function macHistory($id)
{
    $history = $this->radioRepository->getMacHistory($id);

    if (request()->wantsJson()) {
        return response()->json($history);
    }

    return Inertia::render('radios/HistóricoMac', [
        'history' => $history
    ]);
}


    public function store(RadioRequest $request) {     
        
     
       
        try {
            
            $radio = $this->radioRepository->create($request->all());
            $this->logRepository->createLog(auth()->id(), "Adcionado Novo Rádio {$request->radio} ", $request->regiao);
            return redirect()->route('radios.index')
                ->with('success', 'Rádio criado com sucesso!');
        } catch (\Exception $e) {
           
            return redirect()->back()->withErrors(['error' => 'Erro ao criar rádio: ' . $e->getMessage()]);
        }
    }
    

    public function show($id) {
        $radio = $this->radioRepository->find($id);
        return Inertia::render('radios/Show', [
            'radio' => $radio
        ]);
    }

    public function update(RadioRequest $request, $id) {
     
    
        try {            
          
            $radio = $this->radioRepository->update($id, $request->all());
            $this->logRepository->createLog(auth()->id(), "Atualização de Rádio {$request->radio} ", $request->regiao);
    
            return redirect()->route('radios.index')
                ->with('success', 'Rádio atualizado com sucesso!');
        } catch (\Exception $e) {            
            return Inertia::render('Error', [
                'error' => 'Erro ao atualizar rádio: ' . $e->getMessage()
            ]);
        }
    }
    

    public function destroy($id) {
        $this->radioRepository->delete($id);
        $this->logRepository->createLog(auth()->id(), "Apagado Rádio {$id}");
        return redirect()->route('radios.index')
            ->with('success', 'Rádio deletado com sucesso!');
    }

    public function getGeoRadio()
    {
        $data = $this->radioRepository->getGeoRadio();

        return inertia('radios/GetMapaRadios', ['data' => $data]);
    }
    public function baseTrack(Request $request)
    {
        $startDate = $request->input('startD') ?? Carbon::now()->subDays(15)->format('Y-m-d');
        $endDate = $request->input('endD') ?? Carbon::now()->format('Y-m-d');
        $username = $request->input('username');
        
       
        $macData = $this->radioRepository->getTrackedMacAccessData($startDate, $endDate, 10, $username);
      
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dados de Acesso dos MACs',
                'data' => $macData,
            ]);
        }

        return Inertia::render('radios/RastrearRadiosUso', [
            'macData' => $macData
        ]);
    }
    
    public function track(Request $request)
    {        
        $startDate = $request->input('startD');
        $endDate = $request->input('endD');
        $username = $request->input('username');
       
        $usersFinal = $this->radioRepository->getTrackedUsers($startDate, $endDate ,$perPage = 10, $username);

       
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data',
                'data' => $usersFinal,
            ]);
        }
        
        return Inertia::render('radios/RastrearRadios', [
            'users' => $usersFinal
        ]);
    }

    public function radiosInfo()
{
    $info = $this->radioRepository->getRadiosInfo();

        return response()->json(['success' => true, 'message' => 'Logged', 'data' => $info]);
   
}
}
