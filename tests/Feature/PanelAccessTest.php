<?php

namespace Tests\Feature;

use App\Enums\ExamType;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Exams\ExamResource;
use App\Filament\Resources\Files\FileResource;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\SchoolClasses\SchoolClassResource;
use App\Filament\Resources\Subjects\SubjectResource;
use App\Filament\Resources\Tags\TagResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<class-string> */
    protected array $adminResources = [
        ArticleResource::class,
        CategoryResource::class,
        TagResource::class,
        GalleryResource::class,
        FileResource::class,
        PageResource::class,
        MenuItemResource::class,
        SchoolClassResource::class,
        SubjectResource::class,
        UserResource::class,
    ];

    public function test_admin_can_open_every_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach ([...$this->adminResources, ExamResource::class] as $resource) {
            $this->get($resource::getUrl('index'))->assertOk();
        }

        $this->get('/admin')->assertOk()->assertSee('Гимназија Зајечар');
        $this->get('/admin/profile')->assertOk()->assertSee('Подешавања налога');
    }

    public function test_professor_sees_only_the_exam_schedule_and_account(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(ExamResource::getUrl('index'))->assertOk();
        $this->get('/admin/profile')->assertOk();

        foreach ($this->adminResources as $resource) {
            $this->get($resource::getUrl('index'))->assertForbidden();
        }

        $this->get('/admin')->assertOk()->assertDontSee('Чланци');
    }

    public function test_inactive_user_cannot_enter_the_panel(): void
    {
        $this->actingAs(User::factory()->inactive()->create());

        $this->get('/admin')->assertForbidden();
    }

    public function test_professor_can_edit_only_own_exams(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $exam = Exam::create([
            'school_class_id' => SchoolClass::create(['grade' => 1, 'section' => 1])->id,
            'subject_id' => Subject::create(['name' => 'Физика'])->id,
            'user_id' => $owner->id,
            'type' => ExamType::Kontrolni,
            'date' => today()->next(Carbon::TUESDAY),
        ]);

        $this->actingAs($other)->get(ExamResource::getUrl('edit', ['record' => $exam]))->assertForbidden();
        $this->actingAs($owner)->get(ExamResource::getUrl('edit', ['record' => $exam]))->assertOk();
    }
}
