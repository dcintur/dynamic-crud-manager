<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\DynamicData;
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
        $user = $request->user();
        
        // Ottieni l'ID della pagina dalla route
        $pageId = null;
        
        // Caso 1: La route ha un parametro 'page'
        if ($request->route('page')) {
            $pageId = $request->route('page')->id;
        }
        // Caso 2: La route ha un parametro 'dynamicData'
        elseif ($request->route('dynamicData')) {
            $dynamicData = $request->route('dynamicData');
            $pageId = $dynamicData->dynamic_page_id;
        }
        // Caso 3: È una richiesta post e ha un dynamic_page_id
        elseif ($request->isMethod('post') && $request->has('dynamic_page_id')) {
            $pageId = $request->input('dynamic_page_id');
        }
       
        // Admin bypass
        if ($user->hasRole('Admin')) {
            return $next($request);
        }
       
        // No role or no page ID
        if (!$user->user_role_id || !$pageId) {
            return redirect()->route('home')->with('error', 'Non hai il permesso di accedere a questa pagina.');
        }
       
        // Check permission
        $permission = Permission::where('user_role_id', $user->user_role_id)
            ->where('dynamic_page_id', $pageId)
            ->first();
       
        if (!$permission) {
            return redirect()->route('home')->with('error', 'Non hai il permesso di accedere a questa pagina.');
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
            return redirect()->route('dynamic-data.page', $pageId)->with('error', 'Non hai il permesso di eseguire questa azione.');
        }
       
        return $next($request);
    }
}