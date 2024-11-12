<?php
namespace App\Repositories;


use Carbon\Carbon;
use App\Models\Radio;
use App\Models\RadAcct;
use App\Models\RadCheck;
use Carbon\CarbonPeriod;
use App\Models\RadioDash;
use App\Models\MacHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RadioRepository  {

    public function all(Request $request,int $per_page) {
        if ($request->has('search')) {
            $search = $request->search;
            return RadioDash::when($search, function ($query) use ($search) {
                $query->where('radio', 'like', "%{$search}%")
                      ->orWhere('mac', 'like', "%{$search}%")
                      ->orWhere('endereco', 'like', "%{$search}%")                   
                      ;
            })->paginate($per_page);
    }  
        
        return RadioDash::with(['regiao','controladora'])->paginate($per_page);
    }
    public function indexSelect(Request $request) {

        
        return RadioDash::with(['regiao','controladora'])->paginate(1000);
    }

    public function radioRelatorio(Request $request)
    {
        
        $query = RadioDash::query();
    
        
        if ($request->has('name') && !empty($request->name)) {
            $query->where('nome', 'LIKE', '%' . $request->name . '%');
        }    
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }    
        
        if ($request->has('region') && !empty($request->region)) {
            $query->where('regiao', $request->region);
        }    
        
        $query->orderBy('created_at', 'desc');    
        
        return $query->paginate(10);
    }
    


    public function find($id) {
        // Usar findOrFail para lidar com registros inexistentes
        return RadioDash::findOrFail($id);
    }

    public function create(array $data) {
        //dd($data);
        // Criar e retornar o registro
        return RadioDash::create($data);
    }

    public function update($id, array $data) {
        $radio = RadioDash::findOrFail($id);
    
        // Verifica se o MAC foi alterado
        if ($radio->mac !== $data['mac']) {
            // Cria o histórico de troca de MAC
            MacHistory::create([
                'nome' => $radio->radio,
                'radio_id' => $radio->id,
                'mac_antigo' => $radio->mac,
                'mac_novo' => $data['mac']
            ]);
        }
        
    
        $radio->update($data);
        return $radio;
    }
    public function getMacHistory($radioId) {
    return MacHistory::where('radio_id', $radioId)->orderBy('created_at', 'desc')->get();
}

    public function delete($id) {
        // Verificar se o registro existe antes de tentar deletá-lo
        $radio = RadioDash::findOrFail($id);
        return $radio->delete();
    }

    public function search($term) {
        // Implementar uma busca com paginação
        return RadioDash::where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->paginate();
    }
   
    public function getTrackedMacAccessData($startDate = null, $endDate = null)
{
    // Definindo o período padrão para 15 dias antes da data atual, se não especificado
    $startDate = $startDate ?? Carbon::now()->subDays(15)->format('Y-m-d');
    $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

    // Consulta para obter contagem de acessos, total de upload e download por MAC
    $result = RadAcct::select(
            'calledstationid as mac',
            DB::raw('COUNT(*) as access_count'),
            DB::raw('SUM(acctinputoctets) as total_upload'),    // Total de upload
            DB::raw('SUM(acctoutputoctets) as total_download')  // Total de download
        )
        ->whereBetween(DB::raw('DATE(acctstarttime)'), [$startDate, $endDate]) // Período
        ->whereNotNull('calledstationid')
        ->groupBy('calledstationid')
        ->get();

    return $result->map(function ($data) {
        return [
            'mac' => $data->mac,
            'access_count' => $data->access_count,
            'total_upload' => $this->formatBytes($data->total_upload),
            'total_download' => $this->formatBytes($data->total_download),
        ];
    });
}

// Função para formatar bytes
private function formatBytes($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
   
    
    public function getGeoRadio()
    {
        return Cache::remember('geo_radio_data', 1, function () {
            $radios = RadioDash::all(['id', 'mac', 'geo']);
            
            if ($radios->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Nenhum dado'
                ];
            }
    
            $newData = [];
            $hoje = Carbon::today()->format('Y-m-d');
            $ontem = Carbon::yesterday()->format('Y-m-d');
            $tresDiasAtras = Carbon::today()->subDays(3)->format('Y-m-d');
            $latitudes = [];
            $longitudes = [];
            
            // Obter todos os acessos dos últimos 3 dias
            $macs = $radios->pluck('mac')->toArray();
            $acessos = RadAcct::whereIn('calledstationid', $macs)
                ->where('acctstarttime', '>=', $tresDiasAtras)
                ->whereNotNull('calledstationid')
                ->where('calledstationid', '!=', '')
                ->select('calledstationid', 'acctstarttime') 
                ->get();
                $acessos = $acessos->map(function ($acesso) {
                    return [
                        'calledstationid' => $acesso->calledstationid,
                        'acctstarttime' => $acesso->acctstarttime,
                    ];
                });
               // dd($hoje);   
            $acessadosHj = 0;
            $acessadosOntem = 0;
            $naoAcessados = 0;

            // Ajuste na contagem de acessos por período
            foreach ($radios as $radio) {
                $mac = $radio->mac;
                $imagem = 'http://maps.google.com/mapfiles/ms/icons/white-dot.png';
                
                // Filtrar os acessos deste rádio específico
                $acessosPorMac = $acessos->filter(function ($acesso) use ($mac) {
                    return $acesso['calledstationid'] === $mac;
                });
            
                // Filtrar os acessos para hoje
                $acessosHoje = $acessosPorMac->filter(function ($acesso) use ($hoje) {
                    return Carbon::parse($acesso['acctstarttime'])->isSameDay($hoje);
                })->count();

                  // Filtrar os acessos para ontem
                  $acessosOntem = $acessosPorMac->filter(function ($acesso) use ($ontem) {
                    return Carbon::parse($acesso['acctstarttime'])->isSameDay($ontem);
                })->count();

               // dd($acessosHoje);
                if ($acessosHoje > 0) {
                    $imagem = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                    $acessadosHj += $acessosHoje;
                } else if ($acessosOntem > 0) {
                    $imagem = 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png';
                        $acessadosOntem += $acessosOntem;
                }else  { 
                    $imagem = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
                    $naoAcessados++;
                   
                }
            
                $latLong = explode(',', $radio->geo);
                $latitudes[] = $latLong[0];
                $longitudes[] = $latLong[1];
            
                $newData[] = [
                    'id' => $radio->id,
                    'mac' => $radio->mac,
                    'acessos' => $acessosHoje,
                    'img' => $imagem,
                    'geo' => $radio->geo,
                ];
            }
            

    
            $latCenter = array_sum($latitudes) / count($latitudes);
            $lngCenter = array_sum($longitudes) / count($longitudes);
    
            return [
                'acessadoshj' => $acessadosHj,
                'acessadosontem' => $acessadosOntem,
                'naoacessados' => $naoAcessados ,
                'data' => $newData,
                'center' => [
                    'lat' => $latCenter,
                    'lng' => $lngCenter,
                ],
            ];
        });
    }
    
    
    
    

    public function updateMarker($id, $geo)
    {
        $radio = RadioDash::find($id);

        if ($radio) {
            $radio->geo = $geo;
            return $radio->save() ? true : false;
        }

        return false;
    }
   // Função para obter os dados paginados (para exibição na tela)
   public function getTrackedUsers($startDate, $endDate, $perPage = 10, $username = null)
   {
      
       $query = RadAcct::select('RadAcctId', 'UserName', 'AcctStartTime', 'AcctStopTime', 'acctsessiontime', 'acctinputoctets', 'acctoutputoctets', 'calledstationid', 'callingstationid');
   
       // Se o nome for fornecido, faz a busca por nome
       if (!empty($username)) {
           
           $query->where('UserName', 'LIKE', '%' . $username . '%');    
          
       } else {
          
           $query->whereBetween(DB::raw('DATE(AcctStartTime)'), [$startDate, $endDate]);
       }    
     
       $paginatedUsers = $query->paginate($perPage);    
       //\dd($paginatedUsers);
       foreach ($paginatedUsers->items() as $user) {
           $user->acctinputoctets = $this->convertBytes($user->acctinputoctets);
           $user->acctoutputoctets = $this->convertBytes($user->acctoutputoctets);
           $user->acctsessiontime = gmdate('H:i:s', $user->acctsessiontime);
           $user->acctstarttime = Carbon::parse($user->acctstarttime)->format('d/m/Y H:i:s');
           $user->acctstoptime = Carbon::parse($user->acctstoptime)->format('d/m/Y H:i:s');
       }
   
       
       return $paginatedUsers;
    }

   // Função para exportar todos os dados sem paginação
   public function getAllTrackedUsers($startDate, $endDate, $username = null, $mac = null, $region = null)
   {
       $query = RadAcct::select('RadAcctId', 'UserName', 'AcctStartTime', 'AcctStopTime', 'acctsessiontime', 'acctinputoctets', 'acctoutputoctets', 'calledstationid', 'callingstationid');

       // Filtros de busca
       if (!empty($username)) {
           $query->where('UserName', 'LIKE', '%' . $username . '%');
       }

       if (!empty($mac)) {
           $query->where('calledstationid', 'LIKE', '%' . $mac . '%');
       }

       if (!empty($region)) {
           $query->where('region_column', 'LIKE', '%' . $region . '%'); // Substitua 'region_column' pelo nome correto da coluna de região
       }

       $query->whereBetween(DB::raw('DATE(AcctStartTime)'), [$startDate, $endDate]);

       return $query->get(); // Sem paginação, retorna todos os dados
   }
    

    private function dateRange($startDate, $endDate)
    {
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
    
        
        $period = CarbonPeriod::create($start, $end);
    
        
        return $period->toArray();
    }

    private function convertBytes($bytes)
    {
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }
        public function getTotalRadios()
    {
        return RadioDash::count();
    }

    public function getTotalNovosUsersHoje()
    {
        return RadCheck::whereDate('data_criacao', Carbon::today())->count();
    }

    public function getTotalAcessosHoje()
    {
        return RadAcct::whereDate('acctstarttime', Carbon::today())->count();
    } 



    public function getRadiosInfo()
    {
        $totalRadios = $this->getTotalRadios();
        $totalNovosUsers = $this->getTotalNovosUsersHoje();
        $totalAcessos = $this->getTotalAcessosHoje();
        //$totalUso = $this->getStorageInfo();

        return [
            'totalRadios' => $totalRadios,
            'novosUsers' => $totalNovosUsers,
            'totalAcessos' => $totalAcessos,
            //'totalUso' => $totalUso,
        ];
    }


    
}