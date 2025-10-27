<?php

namespace App\Http\Controllers;

use App\Domains\HealthProfile\CommandHandlers\DeleteHealthMetricCommandHandler;
use App\Domains\HealthProfile\CommandHandlers\StoreHealthMetricCommandHandler;
use App\Domains\HealthProfile\Commands\DeleteHealthMetricCommand;
use App\Enums\MetricType;
use App\Http\Requests\HealthProfileIndexRequest;
use App\Http\Requests\LogMedicationRequest;
use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\StoreMetricRequest;
use App\Domains\HealthProfile\Projections\Metric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\View\View;

class HealthProfileController extends Controller
{
    public function index(HealthProfileIndexRequest $request): View
    {
        $metrics = Metric::where('profile_uuid', Context::get('profile'))
            ->when($request->filled('metric'), function ($query) use ($request) {
                $query->where('type', $request->string('metric'));
            })
            ->orderByDesc('timestamp')
            ->paginate(15);

        return view('metrics.index', [
            'metrics' => $metrics,
        ]);
    }

    public function editMetricForm(Metric $metric): View
    {
        return view('metrics.edit', [
            'metric' => $metric,
        ]);
    }

    public function destroy(
        Metric $metric,
        DeleteHealthMetricCommandHandler $commandHandler,
    ): RedirectResponse {
        $commandHandler->handle(new DeleteHealthMetricCommand($metric->uuid));

        return redirect()->route('health.metrics.index')->with('success', 'Metric deleted successfully');
    }

    public function createMetricForm(Request $request): View
    {
        return view('metrics.create', [
            'metric' => MetricType::tryFrom($request->string('metric')),
        ]);
    }

    public function storeMetric(
        StoreMetricRequest $request,
        StoreHealthMetricCommandHandler $commandHandler,
        ?Metric $metric = null,
    ): RedirectResponse {
        $commandHandler->handle($request->getCommand());

        if ($metric) {
            return redirect()->route('health.metrics.index')->with('success', 'Metric updated successfully');
        }

        return redirect()->route('health.metrics.index')->with('success', 'Metric recorded successfully');
    }

    public function createMedicationForm()
    {
        return view('medications.schedule');
    }

    public function scheduleMedication(StoreMedicationRequest $request)
    {
        /* $validated = $request->validated(); */
        /* HealthProfileAggregate::retrieve($request->profile->uuid) */
        /*     ->scheduleMedication( */
        /*         $validated['name'], */
        /*         $validated['dosage'], */
        /*         $validated['frequency'], */
        /*         now() */
        /*     ) */
        /*     ->persist(); */

        return redirect()->route('health.medications.create')->with('success', 'Medication scheduled successfully');
    }

    public function createMedicationLogForm()
    {
        return view('medications.log');
    }

    public function logMedication(LogMedicationRequest $request)
    {
        /* $validated = $request->validated(); */
        /* HealthProfileAggregate::retrieve($request->profile->uuid) */
        /*     ->logMedicationAdherence( */
        /*         $validated['medication_id'], */
        /*         now(), */
        /*         $validated['taken'] */
        /*     ) */
        /*     ->persist(); */

        return redirect()->route('health.medications.log.create')->with('success', 'Medication adherence logged successfully');
    }

    public function createReportForm()
    {
        return view('reports.create');
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'metric_types' => 'nullable|array',
            'metric_types.*' => 'in:blood_pressure,heart_rate,blood_sugar,spo2,breastfeeding,weight,temperature',
        ]);

        $query = Metric::where('profile_uuid', $request->profile->uuid)
            ->whereBetween('timestamp', [$request->start_date, $request->end_date]);

        if ($request->metric_types) {
            $query->whereIn('type', $request->metric_types);
        }

        $metrics = $query->orderBy('timestamp')->get();

        // Calculate trends/averages
        $averages = $metrics->groupBy('type')->map(function ($group) {
            $values = $group->pluck('value')->map(function ($value) {
                return array_values((array) $value)[0]; // Extract first value (e.g., systolic, bpm)
            });

            return [
                'average' => $values->avg(),
                'min' => $values->min(),
                'max' => $values->max(),
            ];
        });

        return view('reports.index', [
            'metrics' => $metrics,
            'averages' => $averages,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'metric_types' => $request->metric_types ?? [],
        ]);
    }

    public function exportReportCsv(Request $request)
    {
        /* $request->validate([ */
        /*     'start_date' => 'required|date', */
        /*     'end_date' => 'required|date|after_or_equal:start_date', */
        /*     'metric_types' => 'nullable|array', */
        /*     'metric_types.*' => 'in:blood_pressure,heart_rate,blood_sugar,spo2,breastfeeding,weight,temperature', */
        /* ]); */

        /* $query = Metric::where('profile_uuid', $request->profile->uuid) */
        /*     ->whereBetween('timestamp', [$request->start_date, $request->end_date]); */

        /* if ($request->metric_types) { */
        /*     $query->whereIn('type', $request->metric_types); */
        /* } */

        /* $metrics = $query->orderBy('timestamp')->get(); */

        /* $csv = Writer::createFromString(); */
        /* $csv->insertOne(['Type', 'Value', 'Timestamp', 'Notes', 'Photo URL']); */

        /* foreach ($metrics as $metric) { */
        /*     $csv->insertOne([ */
        /*         $metric->type, */
        /*         json_encode($metric->value), */
        /*         $metric->timestamp->format('Y-m-d H:i:s'), */
        /*         $metric->notes ?? '', */
        /*         $metric->photo_url ?? '', */
        /*     ]); */
        /* } */

        /* return response($csv->toString(), 200, [ */
        /*     'Content-Type' => 'text/csv', */
        /*     'Content-Disposition' => 'attachment; filename="health-report-' . now()->format('Y-m-d') . '.csv"', */
        /* ]); */

        return back()->with('info', 'CSV export functionality is not implemented yet.');
    }
}
