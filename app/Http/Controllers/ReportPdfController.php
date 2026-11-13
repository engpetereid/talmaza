<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;

class ReportPdfController extends Controller
{
    public const METRIC_LABELS = [
        'attendance' => 'الحضور',
        'note' => 'النوتة',
        'mass' => 'القداس',
        'tasbeha' => 'التسبحة',
        'vespers' => 'عشية',
        'servants' => 'اجتماع خدام',
        'reading' => 'القراءة',
        'altar' => 'مذبح عائلي',
        'weekly_kholwa' => 'خلوة أسبوعية',
        'kholwa' => 'مشاركة خلوة',
        'training' => 'تدريب تلمذة',
        'sermon' => 'سماع العظة',
    ];

    /**
     * Download a weekly or monthly report as a PDF (Arabic / RTL).
     */
    public function __invoke(Request $request, Report $report)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            if ($user->family_id !== $report->family_id) {
                abort(403);
            }

            // Leaders must unlock their reports with the PIN first (same rule as the reports page).
            if (!$request->session()->has('reports_unlocked_' . $user->id)) {
                return redirect()->route('leader.reports');
            }
        }

        $report->load('family.members', 'family.leader');

        $html = view('pdf.report', [
            'report' => $report,
            'family' => $report->family,
            'sections' => $this->sections($report),
            'membersNotes' => $this->membersNotes($report),
            'metricLabels' => self::METRIC_LABELS,
        ])->render();

        $tempDir = storage_path('app/mpdf');
        File::ensureDirectoryExists($tempDir);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
            'margin_top' => 14,
            'margin_bottom' => 16,
            'margin_left' => 12,
            'margin_right' => 12,
            'tempDir' => $tempDir,
        ]);

        $mpdf->SetTitle($this->title($report));
        $mpdf->SetFooter('{PAGENO} / {nbpg}');
        $mpdf->WriteHTML($html);

        $filename = sprintf('report-%s-%d-%s.pdf', $report->type, $report->id, $report->report_date->format('Y-m-d'));

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function title(Report $report): string
    {
        $type = $report->type === 'weekly' ? 'التقرير الأسبوعي' : 'التقرير الشهري';

        return $type . ' - ' . ($report->family->name ?? '');
    }

    /**
     * Text sections of the report, each item normalized to ['text' => ..., 'replies' => [...]].
     */
    private function sections(Report $report): array
    {
        if ($report->type === 'weekly') {
            return [
                'timeline' => $this->items($report->timeline),
                'weekly_achievements' => $this->items($report->weekly_achievements),
                'priest_message' => $this->items($report->priest_message),
            ];
        }

        return [
            'monthly_summary' => $this->items($report->monthly_summary),
            'priest_message' => $this->items($report->priest_message),
        ];
    }

    /**
     * Normalize items, including the old single "reply" format.
     */
    private function items($items): array
    {
        if (!is_array($items)) {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $item = ['text' => $item];
            }
            if (!is_array($item)) {
                continue;
            }

            $replies = is_array($item['replies'] ?? null) ? $item['replies'] : [];
            if (empty($replies) && !empty($item['reply'])) {
                $replies[] = ['role' => 'admin', 'name' => 'الإدارة', 'text' => $item['reply'], 'date' => null];
            }

            $item['replies'] = $replies;
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Monthly member notes with the member's name, skipping empty ones.
     */
    private function membersNotes(Report $report): array
    {
        if ($report->type !== 'monthly' || !is_array($report->members_notes)) {
            return [];
        }

        $members = $report->family?->members->keyBy('id') ?? collect();
        $notes = [];

        foreach ($report->members_notes as $memberId => $note) {
            $note = is_string($note) ? ['text' => $note] : (is_array($note) ? $note : []);
            [$note] = $this->items([$note]) ?: [['text' => '', 'replies' => []]];

            if (trim($note['text'] ?? '') === '' && empty($note['replies'])) {
                continue;
            }

            $note['name'] = $members->get($memberId)?->name ?? 'مخدوم محذوف';
            $notes[] = $note;
        }

        return $notes;
    }
}
