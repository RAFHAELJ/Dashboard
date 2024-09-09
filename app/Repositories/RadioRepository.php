<?php
namespace App\Repositories;


use Carbon\Carbon;
use App\Models\Radio;
use App\Models\RadAcct;
use Illuminate\Http\Request;

class RadioRepository implements RadioRepositoryInterface {

    public function all() {
        
        return Radio::paginate();
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
        return Radio::findOrFail($id);
    }

    public function create(array $data) {
        // Criar e retornar o registro
        return Radio::create($data);
    }

    public function update($id, array $data) {
        // Encontrar e atualizar o registro
        $radio = Radio::findOrFail($id);
        $radio->update($data);
        return $radio;
    }

    public function delete($id) {
        // Verificar se o registro existe antes de tentar deletá-lo
        $radio = Radio::findOrFail($id);
        return $radio->delete();
    }

    public function search($term) {
        // Implementar uma busca com paginação
        return Radio::where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->paginate();
    }
   
    
    public function getGeoRadio()
    {
        $radios = Radio::all();
        
        if ($radios->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Nenhum dado'
            ];
        }
    
        $newData = [];
        $acessadosHj = 0;
        $acessadosOntem = 0;
        $naoAcessados = 0;
        $hoje = Carbon::today()->format('Y-m-d');
        $ontem = Carbon::yesterday()->format('Y-m-d');
    
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
    
            $newData[] = [
                'id' => $radio->id,
                'mac' => $radio->mac,
                'acessos' => $acessosHj,
                'img' => $imagem
            ];
        }
    
        return [
            'acessadoshj' => $acessadosHj,
            'acessadosontem' => $acessadosOntem,
            'naoacessados' => $naoAcessados,
            'data' => $newData
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
    
}