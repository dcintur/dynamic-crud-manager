<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $action): Response
    {

        // Temporaneamente bypass dei controlli
        //return $next($request);

        $user = $request->user();
        $pageId = $request->route('page') ? $request->route('page')->id : null;
        
        // Admin bypass
        if ($user->hasRole('Admin')) {
            return $next($request);
        }
        
        // No role or no page ID
        if (!$user->user_role_id || !$pageId) {
            return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
        }
        
        // Check permission
        $permission = Permission::where('user_role_id', $user->user_role_id)
            ->where('dynamic_page_id', $pageId)
            ->first();
        
        if (!$permission) {
            return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
        }
        
        // Check action permission
        $actionMap = [
            'view' => 'can_view',
            'create' => 'can_create',
            'edit' => 'can_edit',
            'delete' => 'can_delete',
            'export' => 'can_export',
            'import' => 'can_import'
        ];
        
        $permissionField = $actionMap[$action] ?? '';
        
        if (!$permissionField || !$permission->$permissionField) {
            return redirect()->route('dynamic-data.page', $pageId)->with('error', 'You do not have permission to perform this action.');
        }
        
        return $next($request);
    }
}