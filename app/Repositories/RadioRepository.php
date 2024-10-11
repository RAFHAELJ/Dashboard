<?php
namespace App\Repositories;


use Carbon\Carbon;
use App\Models\Radio;
use App\Models\RadAcct;
use Carbon\CarbonPeriod;
use App\Models\RadioDash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RadioRepository  {

    public function all() {
        
        return RadioDash::with(['regiao','controladora'])->paginate();
    }

    public function radioRelatorio(Request $request)
    {
        
        $query = Radio::query();
    
        
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
        // Encontrar e atualizar o registro
        $radio = RadioDash::findOrFail($id);
        $radio->update($data);
        return $radio;
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
   
    
    public function getGeoRadio()
    {
        $radios = RadioDash::all(['id', 'mac', 'geo']);
        if ($radios->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Nenhum dado'
            ];
        }
   // \dd($radios);
        $newData = [];
        $acessadosHj = 0;
        $acessadosOntem = 0;
        $naoAcessados = 0;
        $hoje = Carbon::today()->format('Y-m-d');
        $ontem = Carbon::yesterday()->format('Y-m-d');
        $latitudes = [];
        $longitudes = [];
    
        foreach ($radios as $radio) {
            $imagem = 'http://maps.google.com/mapfiles/ms/icons/white-dot.png';
            $mac = $radio->mac;
            $acessosHj = RadAcct::where('calledstationid', $mac)
                ->where('acctstarttime', 'like', "$hoje%")
                ->count();
    
            if ($acessosHj > 0) {
                $imagem = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                $acessadosHj++;
            } else {
                $acessosOntem = RadAcct::where('calledstationid', $mac)
                    ->where('acctstarttime', 'like', "$ontem%")
                    ->count();
                if ($acessosOntem > 0) {
                    $imagem = 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png';
                    $acessadosOntem++;
                } else {
                    $imagem = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
                    $naoAcessados++;
                }
            }
    
            $latLong = explode(',', $radio->geo);
           // dd($latLong);
            $latitudes[] = $latLong[0];
            $longitudes[] = $latLong[1];
    
            $newData[] = [
                'id' => $radio->id,
                'mac' => $radio->mac,
                'acessos' => $acessosHj,
                'img' => $imagem,
                'geo' => $radio->geo,
            ];
        }
    
        // Centralize o mapa com base na média das coordenadas dos rádios
        $latCenter = array_sum($latitudes) / count($latitudes);
        $lngCenter = array_sum($longitudes) / count($longitudes);
    
        return [
            'acessadoshj' => $acessadosHj,
            'acessadosontem' => $acessadosOntem,
            'naoacessados' => $naoAcessados,
            'data' => $newData,
            'center' => [
                'lat' => $latCenter,
                'lng' => $lngCenter,
            ],
        ];
    }
    

    public function updateMarker($id, $geo)
    {
        $radio = Radio::find($id);

        if ($radio) {
            $radio->geo = $geo;
            return $radio->save() ? true : false;
        }

        return false;
    }
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
        
        foreach ($paginatedUsers->items() as $user) {
            $user->acctinputoctets = $this->convertBytes($user->acctinputoctets);
            $user->acctoutputoctets = $this->convertBytes($user->acctoutputoctets);
            $user->acctsessiontime = gmdate('H:i:s', $user->acctsessiontime);
            $user->acctstarttime = Carbon::parse($user->acctstarttime)->format('d/m/Y H:i:s');
            $user->acctstoptime = Carbon::parse($user->acctstoptime)->format('d/m/Y H:i:s');
        }
    
        
        return $paginatedUsers;
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
    
}