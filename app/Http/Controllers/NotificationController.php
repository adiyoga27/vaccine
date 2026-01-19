<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Services\WahaService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $waha;

    public function __construct(WahaService $waha)
    {
        $this->waha = $waha;
    }

    public function configuration()
    {
        $session = $this->waha->getSession();
        // Stats for config page (simple count)
        $stats = [
             'sent' => NotificationLog::where('status', 'sent')->count(),
             'pending' => NotificationLog::where('status', 'pending')->count(),
             'failed' => NotificationLog::where('status', 'failed')->count(),
        ];
        return view('dashboard.admin.notifications.config', compact('stats', 'session'));
    }

    public function templates()
    {
        $templates = NotificationTemplate::all();
        return view('dashboard.admin.notifications.templates', compact('templates'));
    }

    public function updateTemplate(Request $request, $id)
    {
        $request->validate(['content' => 'required']);
        $template = NotificationTemplate::findOrFail($id);
        $template->update(['content' => $request->content]);
        return back()->with('success', 'Template berhasil diperbarui');
    }

    public function history(Request $request)
    {
        $query = NotificationLog::latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('to', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '' && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        return view('dashboard.admin.notifications.history', compact('logs'));
    }

    public function scan()
    {
        $qr = $this->waha->getQR();
        return response()->json(['qr' => $qr]);
    }

    public function status()
    {
        $session = $this->waha->getSession();
        return response()->json($session);
    }

    public function logout()
    {
        $this->waha->logout();
        return back()->with('success', 'Berhasil logout dari WhatsApp.');
    }
}
