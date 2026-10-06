<?php

namespace App\Http\Controllers\Admin;

use App\Enums\App;
use App\Http\Controllers\Controller;
use App\Repositories\BlogRepository;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Image Trait
     */
    use ImageTrait;

    public function __construct(
        protected BlogRepository $blogRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.blogs.index', ['blogs' => $this->blogRepository->paginate()]);
    }

    public function scheduled()
    {
        return view('admin.blogs.scheduled', ['blogs' => $this->blogRepository->getScheduled()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.blogs.create', ['langs' => App::LOCALES]);
    }

    /**
     * Upload Media associated to blog.
     *
     * @return Response
     */
    public function uploadMedia(Request $request)
    {

        return ['url' => asset('storage/'.$this->setFile($request->file('upload'))
            ->setName()
            ->upload())];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $active = 1;
        if ($request->get('active') === '1' || ! empty($request->get('publish_it_at'))) {
            $active = 0;
        }
        try {
            $request = $request->all();
            $request['active'] = $active;
            foreach ($request['slug'] as $lang => &$slug) {
                if (empty($slug) && ! empty($request['title'][$lang])) {
                    $slug = Str::slug($request['title'][$lang]);
                }
            }

            $request['user_id'] = auth()->user()->id;
            if (! empty($request['media'])) {
                $request['media'] = $this->uploadAll([$request['media']]);
            }

            $this->blogRepository->create($request);

            return redirect(route('admin.blogs.index'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $blog = $this->blogRepository->getById($id);

        return view('admin.blogs.edit', ['blog' => $blog, 'langs' => App::LOCALES]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $active = 1;
        if ($request->get('active') === '1' || ! empty($request->get('publish_it_at'))) {
            $active = 0;
        }
        try {
            $request = $request->all();
            $request['active'] = $active;

            foreach ($request['slug'] as $lang => &$slug) {
                if (empty($slug) && ! empty($request['title'][$lang])) {
                    $slug = Str::slug($request['title'][$lang]);
                }
            }

            $request['user_id'] = auth()->user()->id;
            if (! empty($request['media'])) {
                $request['media'] = $this->uploadAll([$request['media']]);
            }

            $this->blogRepository->update($id, $request);

            return redirect(route('admin.blogs.index'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $this->blogRepository->delete($id);

            return redirect(route('admin.blogs.index'))->with('success');
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return redirect()->back();
        }
    }
}
