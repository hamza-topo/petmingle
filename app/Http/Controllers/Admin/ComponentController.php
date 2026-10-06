<?php

namespace App\Http\Controllers\Admin;

use App\Enums\App;
use App\Enums\Component;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Component\Store;
use App\Models\Component as ModelComponent;
use App\Repositories\ComponentRepository;
use App\Services\ComponentService;
use App\Traits\ImageTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ComponentController extends Controller
{
    use ImageTrait;

    public const COMPONENT_DIRECTORY = '/public/components/';

    public function __construct(
        protected ComponentRepository $componentRepository,
        protected ComponentService $componentService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $components = $this->componentRepository->all();
        $components = $this->componentService->format($components);

        return view('admin.components.index', \compact('components'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $components = $this->componentRepository->all();
        $avacomponents = array_keys($this->componentService->format($components)
            ->where('id', null)->toArray());
        $langs = App::LOCALES;

        return view('admin.components.create', \compact('avacomponents', 'langs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Store $request)
    {
        $component = $this->componentRepository->getByName($request->name);
        try {
            $request = $request->validated();
            if (! empty($request['media'])) {
                $request['media'] = $this->uploadAll([$request['media']])[0];
            }
            if (empty($component->id)) {
                $this->componentRepository->create($request);
            } else {
                $this->componentRepository->update($component->id, $request);
            }

            return redirect(route('admin.components.index'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(string $componentName)
    {
        if (! \in_array(
            $componentName,
            collect(Component::cases())->pluck('value')->toArray()
        )) {
            abort(404);
        }
        $component = $this->componentRepository->getByName($componentName) ?? new ModelComponent;
        $langs = App::LOCALES;

        return view('admin.components.elements.'.\strtolower($componentName), compact('component', 'componentName', 'langs'));
    }
}
