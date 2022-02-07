<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->input('id');
        $slug = $request->input('slug');
        $limit = $request->input('limit');
        $project = Project::with([]);

        if ($slug) {
            return response(
                $project->where('slug', $slug)->first(),
                200
            );
        }
        return response(
            $project->paginate($limit),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'bisnis_slug' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'sistem_pengelolaan' => ['required', 'string', 'max:255'],
            'skema_bisnis' => ['required', 'string', 'max:255'],
            'total_pendanaan' => ['required', 'integer'],
            'min_invest' => ['required', 'integer'],
            'harga_perlembar' => ['required', 'integer'],
            'min_dividen' => ['required', 'integer'],
            'max_dividen' => ['required', 'integer'],
            'dividen_periode' => ['required', 'string', 'max:255'],
            'saham_dibagikan' => ['required', 'integer'],
            'keuntungan_historis' => ['required', 'string', 'max:255'],
            'balik_modal' => ['required', 'string', 'max:255'],
            'keterangan' => ['required', 'string', 'max:255'],
            'img_url' => ['required', 'string', 'max:255'],
        ]);

        //  // Upload Selfie Image
        //  $img = $request->file('img_path');
        //  $imgname = $img->getClientOriginalName();
        //  $finalimg = date('His') . $imgname;
        //  $imgpath = $request->file('img_path')->storeAs('images', $finalimg, 'public');

        $project = Project::create([
            'project_name' => $request->project_name,
            'slug' => Str::slug($request->project_name . " " . $request->location),
            'bisnis_slug' => $request->bisnis_slug . rand(1, 100),
            'category' => $request->category,
            'penerbit' => $request->penerbit,
            'location' => $request->location,
            'sistem_pengelolaan' => $request->sistem_pengelolaan,
            'skema_bisnis' => $request->skema_bisnis,
            'total_perolehan' => $request->total_perolehan,
            'total_pendanaan' => $request->total_pendanaan,
            'min_invest' => $request->min_invest,
            'harga_perlembar' => $request->harga_perlembar,
            'min_dividen' => $request->min_dividen,
            'max_dividen' => $request->max_dividen,
            'dividen_periode' => $request->dividen_periode,
            'saham_dibagikan' => $request->saham_dibagikan,
            'keuntungan_historis' => $request->keuntungan_historis,
            'balik_modal' => $request->balik_modal,
            'keterangan' => $request->keterangan,
            'img_url' => $request->img_url,
            'map_link' => $request->map_link,
            'proposal_link' => $request->proposal_link,
        ]);

        if ($project) {
            return response()->json([
                'status' => 'success',
                'message' => 'Project berhasil ditambahkan'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
