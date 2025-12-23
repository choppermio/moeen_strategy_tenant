<?php

namespace App\Http\Controllers;

use App\Models\Moasheradastrategy;
use App\Models\Hadafstrategy;
use App\Models\Moashermkmf;
use Illuminate\Http\Request;

class MoasheradastrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View('/moasheradastrategy/index', [
            'moasheradastrategies' => Moasheradastrategy::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request  $request)
    {
        return View('/moasheradastrategy/create',[
            'hadafstrategies' => Hadafstrategy::all(),
            

            


        ]);
    }

    /**
     * 
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->hadafstrategy);
        $moasheradastrategy = Moasheradastrategy::create([
            'name' => $request->input('name'),
            'percentage' => 0,
            'weight' => $request->input('weight', 0),
            'parent_id' => $request->input('hadafstrategy'),
        ]);
        return redirect('/newstrategy?id='.$request->input('hadafstrategy'))->with('success', 'تم إضافة المؤشر بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Moasheradastrategy  $moasheradastrategy
     * @return \Illuminate\Http\Response
     */
    public function show(Moasheradastrategy $moasheradastrategy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Moasheradastrategy  $moasheradastrategy
     * @return \Illuminate\Http\Response
     */
    public function edit(Moasheradastrategy $moasheradastrategy)
    {
        $moasheradastrategy = MoasheradaStrategy::findOrFail($moasheradastrategy->id); // Find the indicator by ID or fail
        $hadafstrategies = HadafStrategy::all(); // Get all strategic goals for the dropdown
        // dd('aa');
        // Pass the indicator and goals to the edit view
        return view('moasheradastrategy.edit', compact('moasheradastrategy', 'hadafstrategies'));
    }
  
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Moasheradastrategy  $moasheradastrategy
     * @return \Illuminate\Http\Response
     */
 
public function update(Request $request, $id)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'name' => 'required|string|max:255', // Validate the name field
        'hadafstrategy' => 'required|exists:hadafstrategies,id', // Ensure the strategic goal exists
        'weight' => 'nullable|numeric|min:0', // Validate weight field
    ]);

    // Find the existing strategic indicator by ID
    $moasheradastrategy = MoasheradaStrategy::findOrFail($id);
// dd($validated['hadafstrategy']);
    // Update the indicator with validated data
    $moasheradastrategy->update([
        'name' => $validated['name'],
        'weight' => $validated['weight'] ?? $moasheradastrategy->weight,
        'parent_id' => $validated['hadafstrategy'], // Make sure to use the correct column name
    ]);

    // Redirect the user with a success message
    return redirect()->route('moasheradastrategy.index')->with('success', 'مؤشر استراتيجي تم تحديثه بنجاح!');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Moasheradastrategy  $moasheradastrategy
     * @return \Illuminate\Http\Response
     */
   public function destroy(Moasheradastrategy $moasheradastrategy)
{
    // Delete the Moasheradastrategy model instance
    $moasheradastrategy->delete();

    // Redirect the user back with a success message. Adjust the route as necessary.
    return redirect()->back()->with('success', 'Moasheradastrategy deleted successfully.');
}

    /**
     * Show the form for attaching moashermkmf to moasheradastrategy.
     */
    public function attach($id)
    {
        $moasheradastrategy = Moasheradastrategy::findOrFail($id);
        $moashermkmfs = Moashermkmf::all();
        $attached_ids = $moasheradastrategy->moashermkmfs->pluck('id')->toArray();
        
        return view('moasheradastrategy.attach', compact('moasheradastrategy', 'moashermkmfs', 'attached_ids'));
    }

    /**
     * Store the attachment of moashermkmf to moasheradastrategy.
     */
    public function storeAttach(Request $request, $id)
    {
        $request->validate([
            'moashermkmfs' => 'required|array',
            'moashermkmfs.*' => 'exists:moashermkmfs,id',
        ]);

        $moasheradastrategy = Moasheradastrategy::findOrFail($id);
        
        // Sync the selected moashermkmfs (this will remove unselected ones and add new ones)
        $moasheradastrategy->moashermkmfs()->sync($request->moashermkmfs);
        
        return redirect()->route('moasheradastrategy.index')->with('success', 'تم ربط المؤشرات بنجاح!');
    }

}
