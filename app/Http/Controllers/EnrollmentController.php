<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentRequest;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Services\EnrollmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class EnrollmentController extends Controller
{
    /**
     * Renderiza a view da enturmação.
     *
     * @param LegacyRegistration $registration
     * @param LegacySchoolClass  $schoolClass
     * @param EnrollmentService  $enrollmentService
     *
     * @return View
     */
    public function viewEnroll(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $this->breadcrumb('Enturmar matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $enableCancelButton = $enrollmentService->isEnrolled($schoolClass, $registration);
        $anotherClassroomEnrollments = $enrollmentService->anotherClassroomEnrollments($schoolClass, $registration);

        return view('enrollments.enroll', [
            'registration' => $registration,
            'enrollments' => $registration->activeEnrollments()->get(),
            'schoolClass' => $schoolClass,
            'enableCancelButton' => $enableCancelButton,
            'anotherClassroomEnrollments' => $anotherClassroomEnrollments,
        ]);
    }

    /**
     * @param EnrollmentService  $enrollmentService
     * @param EnrollmentRequest  $request
     * @param LegacyRegistration $registration
     * @param LegacySchoolClass  $schoolClass
     *
     * @return RedirectResponse
     */
    public function enroll(
        EnrollmentService $enrollmentService,
        EnrollmentRequest $request,
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass
    ) {
        $date = Carbon::createFromFormat('d/m/Y', $request->input('enrollment_date'));

        if ($request->input('is_relocation') || $request->input('is_cancellation')) {
            $enrollmentFromId = $request->input('enrollment_from_id');
            $enrollment = $registration->enrollments()->whereKey($enrollmentFromId)->firstOrFail();

            try {
                $enrollmentService->cancelEnrollment($enrollment, $date);
            } catch (Throwable $throwable) {
                return redirect()->back()->with('error', $throwable->getMessage());
            }
        }

        if ($request->input('is_cancellation')) {
            return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->id)->with('success', 'Enturmação feita com sucesso.');
        }

        try {
            $enrollmentService->enroll($registration, $schoolClass, $date);
        } catch (Throwable $throwable) {
            return redirect()->back()->with('error', $throwable->getMessage());
        }

        return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->id)->with('success', 'Enturmação feita com sucesso.');
    }
}
