<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\RadioRepository;

class RadioController extends Controller {
    protected $radioRepository;

    public function __construct(RadioRepository $radioRepository) {
        $this->radioRepository = $radioRepository;
    }

    public function index() {
        $radios = $this->radioRepository->all();
        return response()->json($radios);
    }

    public function store(Request $request) {
        $radio = $this->radioRepository->create($request->all());
        return response()->json($radio);
    }

    public function show($id) {
        $radio = $this->radioRepository->find($id);
        return response()->json($radio);
    }

    public function update(Request $request, $id) {
        $radio = $this->radioRepository->update($id, $request->all());
        return response()->json($radio);
    }

    public function destroy($id) {
        $this->radioRepository->delete($id);
        return response()->json(['success' => true]);
    }
}

