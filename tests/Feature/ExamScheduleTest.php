<?php

namespace Tests\Feature;

use App\Enums\ExamType;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Support\ExamSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Правила распореда: највише 1 провера дневно и 3 недељно по одељењу, само радним данима.
 */
class ExamScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected SchoolClass $class;

    protected Subject $subject;

    protected User $professor;

    protected Carbon $monday;

    protected function setUp(): void
    {
        parent::setUp();

        $this->class = SchoolClass::create(['grade' => 3, 'section' => 2]);
        $this->subject = Subject::create(['name' => 'Математика']);
        $this->professor = User::factory()->create();
        $this->monday = today()->next(Carbon::MONDAY);
    }

    protected function exam(Carbon $date, ?SchoolClass $class = null, ExamType $type = ExamType::Kontrolni): Exam
    {
        return Exam::create([
            'school_class_id' => ($class ?? $this->class)->id,
            'subject_id' => $this->subject->id,
            'user_id' => $this->professor->id,
            'type' => $type,
            'date' => $date,
        ]);
    }

    public function test_class_name_is_built_from_grade_and_section(): void
    {
        $this->assertSame('III-2', $this->class->name);
    }

    public function test_only_one_exam_per_day_for_a_class(): void
    {
        $this->exam($this->monday);

        $this->assertNotNull(ExamSchedule::check($this->class->id, $this->monday));

        $this->expectException(ValidationException::class);
        $this->exam($this->monday, type: ExamType::Pismeni);
    }

    public function test_other_class_can_have_an_exam_on_the_same_day(): void
    {
        $this->exam($this->monday);
        $other = SchoolClass::create(['grade' => 1, 'section' => 1]);

        $this->assertNull(ExamSchedule::check($other->id, $this->monday));
        $this->exam($this->monday, $other);

        $this->assertSame(2, Exam::count());
    }

    public function test_at_most_three_exams_per_week(): void
    {
        $this->exam($this->monday);
        $this->exam($this->monday->copy()->addDay());
        $this->exam($this->monday->copy()->addDays(2));

        $message = ExamSchedule::check($this->class->id, $this->monday->copy()->addDays(3));
        $this->assertStringContainsString('три провере недељно', $message);

        // Следеће недеље поново може.
        $this->assertNull(ExamSchedule::check($this->class->id, $this->monday->copy()->addWeek()));
    }

    public function test_exams_cannot_be_on_weekends(): void
    {
        $saturday = $this->monday->copy()->addDays(5);

        $this->assertStringContainsString('радним даном', ExamSchedule::check($this->class->id, $saturday));
    }

    public function test_editing_an_exam_does_not_conflict_with_itself(): void
    {
        $exam = $this->exam($this->monday);

        $exam->update(['note' => 'Тригонометрија']);
        $exam->update(['date' => $this->monday->copy()->addDay()]);

        $this->assertTrue($exam->fresh()->date->isSameDay($this->monday->copy()->addDay()));
    }

    public function test_unavailable_dates_include_weekends_taken_days_and_full_weeks(): void
    {
        $this->exam($this->monday);
        $this->exam($this->monday->copy()->addDay());
        $this->exam($this->monday->copy()->addDays(2));

        $dates = ExamSchedule::unavailableDates($this->class->id, $this->monday, $this->monday->copy()->addDays(13));

        // Цела прва недеља је пуна (три провере), викенди никад нису доступни.
        foreach (range(0, 6) as $day) {
            $this->assertContains($this->monday->copy()->addDays($day)->format('Y-m-d'), $dates);
        }

        $this->assertNotContains($this->monday->copy()->addWeek()->format('Y-m-d'), $dates);
        $this->assertContains($this->monday->copy()->addWeek()->addDays(5)->format('Y-m-d'), $dates);
    }
}
