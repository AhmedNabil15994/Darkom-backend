<?php

namespace Modules\Slider\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\DataTable;
use Modules\Slider\Http\Requests\Dashboard\SliderRequest;
use Modules\Slider\Transformers\Dashboard\SliderResource;
use Modules\Slider\Repositories\Dashboard\SliderRepository as Slider;

class SliderController extends Controller
{

    function __construct(Slider $slider)
    {
        $this->slider = $slider;
    }

    public function index()
    {
        return view('slider::dashboard.index');
    }

    public function datatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->slider->QueryTable($request));

        $datatable['data'] = SliderResource::collection($datatable['data']);

        return Response()->json($datatable);
    }

    public function create()
    {
        $ads = $this->slider->getAdsToSlide();
        return view('slider::dashboard.create',compact("ads"));
    }

    public function store(SliderRequest $request)
    {
        try {
            $create = $this->slider->create($request);

            if ($create) {
                return Response()->json([true ,  __('apps::dashboard.messages.created') ]);
            }

            return Response()->json([false  , __('apps::dashboard.messages.failed')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function show($id)
    {
        return view('slider::dashboard.show');
    }

    public function edit($id)
    {
        $slider = $this->slider->findById($id);
        $ads = $this->slider->getAdsToSlide();

        return view('slider::dashboard.edit',compact('slider', "ads"));
    }

    public function update(SliderRequest $request, $id)
    {
        try {
            $update = $this->slider->update($request,$id);

            if ($update) {
                return Response()->json([true ,__('apps::dashboard.messages.updated')]);
            }

            return Response()->json([false  , __('apps::dashboard.messages.failed')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function destroy($id)
    {
        try {
            $delete = $this->slider->delete($id);

            if ($delete) {
                return Response()->json([true , __('apps::dashboard.messages.deleted') ]);
            }

            return Response()->json([false  , __('apps::dashboard.messages.failed')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function deletes(Request $request)
    {
        try {
            $deleteSelected = $this->slider->deleteSelected($request);

            if ($deleteSelected) {
                return Response()->json([true , __('apps::dashboard.messages.deleted')]);
            }

            return Response()->json([false  , __('apps::dashboard.messages.failed')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }
}
