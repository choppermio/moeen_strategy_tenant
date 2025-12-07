<?php
use App\Models\Task;
use App\Models\Subtask;
use App\Models\Moashermkmf;
use App\Models\Mubadara;
use App\Models\Moasheradastrategy;
use App\Models\Hadafstrategy;
use App\Models\Organization;

if (!function_exists('current_organization')) {
    /**
     * Get the current organization
     * 
     * @return \App\Models\Organization|null
     */
    function current_organization()
    {
        if (!auth()->check()) {
            return null;
        }
        
        return auth()->user()->currentOrganization;
    }
}

if (!function_exists('current_organization_id')) {
    /**
     * Get the current organization ID
     * 
     * @return int|null
     */
    function current_organization_id()
    {
        if (!auth()->check()) {
            return session('current_organization_id');
        }
        
        return auth()->user()->getCurrentOrganizationId();
    }
}

if (!function_exists('user_organizations')) {
    /**
     * Get all organizations for the current user
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function user_organizations()
    {
        if (!auth()->check()) {
            return collect([]);
        }
        
        return auth()->user()->organizations;
    }
}

if (!function_exists('is_organization_admin')) {
    /**
     * Check if current user is admin in the current organization
     * 
     * @return bool
     */
    function is_organization_admin()
    {
        if (!auth()->check()) {
            return false;
        }
        
        return auth()->user()->isOrganizationAdmin();
    }
}

if (!function_exists('is_organization_manager')) {
    /**
     * Check if current user is manager in the current organization
     * 
     * @return bool
     */
    function is_organization_manager()
    {
        if (!auth()->check()) {
            return false;
        }
        
        return auth()->user()->isOrganizationManager();
    }
}



if (!function_exists('user_position')) {
    function user_position($id){
$current_user  = $id;
$employee_position = \App\Models\EmployeePosition::where('id',$current_user)->first();
return $employee_position;

}}

if (!function_exists('current_user_position')) {
    /**
     * Get the current user's employee position
     * This bypasses organization scope to ensure user can always access their position
     */
    function current_user_position(){
        $current_user = auth()->user()->id;
        // Use withoutGlobalScopes to bypass organization filtering
        // This ensures the user's position is always found regardless of current organization
        $employee_position = \App\Models\EmployeePosition::withoutGlobalScopes()
            ->where('user_id', $current_user)
            ->first();
        return $employee_position;
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if current user has a specific permission
     * System admins (ADMIN_ID) have all permissions in all organizations
     */
    function has_permission($permissionName)
    {
        if (!auth()->check()) {
            return false;
        }
        
        $position = current_user_position();
        if (!$position) {
            return false;
        }
        
        // System admins have all permissions in all organizations
        if (is_admin($position->id)) {
            return true;
        }
        
        return $position->hasPermission($permissionName);
    }
}

if (!function_exists('has_any_permission')) {
    /**
     * Check if current user has any of the given permissions
     * System admins (ADMIN_ID) have all permissions in all organizations
     */
    function has_any_permission($permissions)
    {
        if (!auth()->check()) {
            return false;
        }
        
        $position = current_user_position();
        if (!$position) {
            return false;
        }
        
        // System admins have all permissions in all organizations
        if (is_admin($position->id)) {
            return true;
        }
        
        return $position->hasAnyPermission($permissions);
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin (has user_id = 1 or is in ADMIN_ID env)
     */
    function is_admin()
    {
        if (!auth()->check()) {
            return false;
        }
        
        $adminIds = explode(',', env('ADMIN_ID', '1'));
        return in_array(auth()->user()->id, $adminIds);
    }
}

if (!function_exists('is_strategy')) {
    /**
     * Check if current user is strategy control
     */
    function is_strategy()
    {
        if (!auth()->check()) {
            return false;
        }
        
        $position = current_user_position();
        if (!$position) {
            return false;
        }
        
        $strategyIds = explode(',', env('STRATEGY_CONTROL_ID', ''));
        return in_array($position->id, $strategyIds);
    }
}

if (!function_exists('calculatePercentages')) {
    function calculatePercentages()
    {
    //    dd('here we go'); 
    //get all tasks 
    $tasks = Task::all();
    foreach($tasks as $task)
    {
        //get all subtasks where parent_id = task_id
        $subtasks = Subtask::where('parent_id', $task->id)->get();
        //count subtasks
        $subtasks_count = $subtasks->count();
        //get sum of percentages of subtasks
        $subtasks_sum = $subtasks->where('status','!=', 'strategy-pending-approval')->sum('percentage');
        //calculate average
        //echo $task->id.'  '.$subtasks_sum.'   '.$subtasks_count.'<br />';
        if($subtasks_count != 0 && $subtasks_sum != 0){
        $average = $subtasks_sum / $subtasks_count;
        $task->update(['percentage' => $average]);
    }
        // dd($average);
       
        //update task with average

    }

    //////////////////percentage of moashermkmfs/////////////////////
    //get all moashermkmfs
    $moashermkmfs = Moashermkmf::all();
    foreach($moashermkmfs as $moashermkmf)
    {
        $moashermkmf_tasks = $moashermkmf->tasks;
        $moashermkmf_tasks_count = $moashermkmf_tasks->count();
        $moashermkmf_tasks_sum = $moashermkmf_tasks->sum('percentage');
        if($moashermkmf_tasks_sum !=0){
        $moashermkmf_average = $moashermkmf_tasks_sum / $moashermkmf_tasks_count;
            
    }else{
        $moashermkmf_average = 0;
    }
        $moashermkmf->update(['percentage' => $moashermkmf_average]);


    }






    //////////////////percentage of mubadara/////////////////////
    //get all mubadara
    $mubadaras = Mubadara::all();
    foreach($mubadaras as $mubadara)
    {
        $mubadara_moashermkmfs_count = Moashermkmf::where('parent_id',$mubadara->id)->count();

        


        $ccount = 0;
        foreach(Moashermkmf::where('parent_id',$mubadara->id)->get() as $mm){
            if($mm->tasks->count() != 0){
                $ccount++;
            }
        }
        //echo 'count: '.$ccount.'<br>';



        $mubadara_moashermkmfs_sum = Moashermkmf::where('parent_id',$mubadara->id)->sum('percentage');
        if($mubadara_moashermkmfs_sum !=0){
        $mubadara_average = $mubadara_moashermkmfs_sum / $ccount;
        // $mubadara_average = $mubadara_moashermkmfs_sum / $mubadara_moashermkmfs_count;
        }else{
            $mubadara_average = 0;
        }
        $mubadara->update(['percentage' => $mubadara_average]);
    }


    //////////////////percentage of moasherstrategy/////////////////////
    //get all moasherstrategy
    $moasherstrategys = 	Moasheradastrategy::all();
    foreach($moasherstrategys as $moasherstrategy)
    {
        $moasherstrategy_mubadaras_count = $moasherstrategy->mobadaras->count();
        $moasherstrategy_mubadaras_sum = $moasherstrategy->mobadaras->sum('percentage');
        // dd($moasherstrategy_mubadaras_sum);
        if($moasherstrategy_mubadaras_sum !=0){
        $moasherstrategy_average = $moasherstrategy_mubadaras_sum / $moasherstrategy_mubadaras_count;
        }else{
            $moasherstrategy_average = 0;
        }
        $moasherstrategy->update(['percentage' => $moasherstrategy_average]);
    }





    //////////////////percentage of hadafstrategy/////////////////////
    //get all hadafstrategy
    $hadafstrategys = 	Hadafstrategy::all();
    foreach($hadafstrategys as $hadafstrategy)
    {
        $hadafstrategy_moasherstrategys_count = Moasheradastrategy::where('parent_id',$hadafstrategy->id)->count();
        //echo $hadafstrategy_moasherstrategys_count.'<br>';
        $hadafstrategy_moasherstrategys_sum = Moasheradastrategy::where('parent_id',$hadafstrategy->id)->sum('percentage');
        //echo $hadafstrategy_moasherstrategys_sum.'<br>';
        if($hadafstrategy_moasherstrategys_sum !=0){
        $hadafstrategy_average = $hadafstrategy_moasherstrategys_sum / $hadafstrategy_moasherstrategys_count;
        }else{
            $hadafstrategy_average = 0;
        }
        $hadafstrategy->update(['percentage' => $hadafstrategy_average]);
    }
        
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if the given employee ID is in the admin list
     * 
     * @param int|null $employee_id - The employee ID to check (defaults to current user)
     * @return bool
     */
    function is_admin($employee_id = null)
    {
        if ($employee_id === null) {
            $employee_id = current_user_position()->id ?? null;
        }
        
        $admin_ids = env('ADMIN_ID', '');
        $admin_list = array_map('trim', explode(',', $admin_ids));
        
        return in_array((string)$employee_id, $admin_list);
    }
}

if (!function_exists('is_strategy')) {
    /**
     * Check if the given employee ID is in the strategy list
     * 
     * @param int|null $employee_id - The employee ID to check (defaults to current user)
     * @return bool
     */
    function is_strategy($employee_id = null)
    {
        if ($employee_id === null) {
            $employee_id = current_user_position()->id ?? null;
        }
        
        $strategy_ids = env('STRATEGY_ID', '');
        $strategy_list = array_map('trim', explode(',', $strategy_ids));
        
        return in_array((string)$employee_id, $strategy_list);
    }
}

if (!function_exists('get_admin_ids')) {
    /**
     * Get all admin IDs as an array
     * 
     * @return array
     */
    function get_admin_ids()
    {
        $admin_ids = env('ADMIN_ID', '');
        return array_filter(array_map('trim', explode(',', $admin_ids)));
    }
}

if (!function_exists('get_strategy_ids')) {
    /**
     * Get all strategy IDs as an array
     * 
     * @return array
     */
    function get_strategy_ids()
    {
        $strategy_ids = env('STRATEGY_ID', '');
        return array_filter(array_map('trim', explode(',', $strategy_ids)));
    }
}