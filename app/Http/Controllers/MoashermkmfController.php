<?php

namespace App\Http\Controllers;

use App\Models\Moashermkmf;
use App\Models\Mubadara;
use Illuminate\Http\Request;

class MoashermkmfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View('/moashermkmf/index', [
            'moashermkmfs' => Moashermkmf::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request  $request)
    {
        return View('/moashermkmf/create',[
            'mubadaras' => Mubadara::all(),
            

            


        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'percentage' => 0,
            'parent_id' => $request->input('mubadara'),
            'type' => $request->input('type'),
            'target' => $request->input('target'),
            'calculation_type' => $request->input('calculation_type'),
            'the_vari' => $request->input('the_vari'),
            'weight' => $request->input('weight'),
        ];
        
        // Only allow reached to be set if calculation_type is manual
        if ($request->input('calculation_type') === 'manual') {
            $data['reached'] = $request->input('reached');
        }
        
        // Only allow calculation_variable to be set if calculation_type is automatic
        if ($request->input('calculation_type') === 'automatic') {
            $data['calculation_variable'] = $request->input('calculation_variable');
        }
        
        $moashermkmf = Moashermkmf::create($data);
        
        // Sync moasheradastrategies if selected
        if ($request->has('moasheradastrategy_ids')) {
            $moashermkmf->moasheradastrategies()->sync($request->input('moasheradastrategy_ids'));
        }
        
        return redirect()->back()->with('success', 'تم إضافة الهدف بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Moashermkmf  $moashermkmf
     * @return \Illuminate\Http\Response
     */
    public function show(Moashermkmf $moashermkmf)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Moashermkmf  $moashermkmf
     * @return \Illuminate\Http\Response
     */
   // Edit method
public function edit($id)
{
    $moashermkmf = Moashermkmf::findOrFail($id);
    $mubadaras = Mubadara::all(); // Assuming you have a Mubadara model for the initiatives
    return view('moashermkmf.edit', compact('moashermkmf', 'mubadaras'));
}

// Update method
public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        // 'mubadara' => 'required|exists:mubadaras,id',
        'type' => 'required|in:mk,mf',
    ]);

    $moashermkmf = Moashermkmf::findOrFail($id);
    
    $updateData = [
        'name' => $validatedData['name'],
        // 'parent_id' => $validatedData['mubadara'], // Assuming 'mubadara_id' is the foreign key column
        'type' => $validatedData['type'],
        'target' => $request->input('target'),
        'calculation_type' => $request->input('calculation_type'),
        'the_vari' => $request->input('the_vari'),
        'weight' => $request->input('weight'),
    ];
    
    // Only allow reached to be updated if calculation_type is manual
    if ($request->input('calculation_type') === 'manual') {
        $updateData['reached'] = $request->input('reached');
    }
    
    // Only allow calculation_variable to be updated if calculation_type is automatic
    if ($request->input('calculation_type') === 'automatic') {
        $updateData['calculation_variable'] = $request->input('calculation_variable');
    }
    
    $moashermkmf->update($updateData);

    // Sync moasheradastrategies if selected
    if ($request->has('moasheradastrategy_ids')) {
        $moashermkmf->moasheradastrategies()->sync($request->input('moasheradastrategy_ids'));
    } else {
        $moashermkmf->moasheradastrategies()->detach();
    }

    return redirect()->route('moashermkmf.index')->with('success', 'Updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Moashermkmf  $moashermkmf
     * @return \Illuminate\Http\Response
     */
    public function destroy(Moashermkmf $moashermkmf)
    {
        //
    }
}
