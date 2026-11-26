<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Enums\ExamType;
use App\Enums\MenuLocation;
use App\Filament\Pages\Auth\EditAccount;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Exams\ExamResource;
use App\Filament\Resources\Exams\Pages\CreateExam;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Article;
use App\Models\Category;
use App\Models\Exam;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFormsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    public function test_saving_an_article_returns_to_the_list_and_fills_author_and_dates(): void
    {
        $parent = Category::create(['name' => 'Такмичења']);
        $child = Category::create(['name' => 'Информатика', 'parent_id' => $parent->id]);

        Livewire::test(CreateArticle::class)
            ->fillForm([
                'title' => 'Државно такмичење из информатике',
                'description' => '<p>Текст вести.</p>',
                'type' => ArticleType::Uspeh->value,
                'status' => ArticleStatus::Objavljeno->value,
                'category_main' => $parent->id,
            ])
            ->set('data.category_sub', $child->id)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(ArticleResource::getUrl('index'));

        $article = Article::firstOrFail();

        $this->assertSame('drzavno-takmicenje-iz-informatike', $article->slug);
        $this->assertSame($child->id, $article->category_id);
        $this->assertTrue($article->creator->is($this->admin));
        $this->assertNotNull($article->published_at);
    }

    public function test_article_form_shows_the_category_and_subcategory_of_an_existing_article(): void
    {
        $parent = Category::create(['name' => 'Обавештења']);
        $child = Category::create(['name' => 'Упис', 'parent_id' => $parent->id]);

        $article = Article::create([
            'title' => 'Упис у први разред',
            'description' => '<p>Текст.</p>',
            'type' => ArticleType::Vest,
            'status' => ArticleStatus::Nacrt,
            'category_id' => $child->id,
        ]);

        Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->assertSchemaStateSet([
                'category_main' => $parent->id,
                'category_sub' => $child->id,
            ])
            ->fillForm(['title' => 'Упис у први разред 2027/28'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($article->fresh()->updater->is($this->admin));
        $this->assertSame($child->id, $article->fresh()->category_id);
    }

    public function test_status_must_be_one_of_the_offered_values(): void
    {
        Livewire::test(CreateArticle::class)
            ->fillForm([
                'title' => 'Вест',
                'description' => '<p>Текст.</p>',
                'type' => ArticleType::Vest->value,
                'status' => 'tip1',
            ])
            ->call('create')
            ->assertHasFormErrors(['status']);
    }

    public function test_category_hierarchy_has_two_levels_and_no_cycles(): void
    {
        $parent = Category::create(['name' => 'Такмичења']);
        $child = Category::create(['name' => 'Математика', 'parent_id' => $parent->id]);

        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => 'Окружно', 'parent_id' => $child->id])
            ->call('create')
            ->assertHasFormErrors(['parent_id']);

        Livewire::test(EditCategory::class, ['record' => $child->getRouteKey()])
            ->fillForm(['parent_id' => $child->id])
            ->call('save')
            ->assertHasFormErrors(['parent_id']);

        Livewire::test(EditCategory::class, ['record' => $parent->getRouteKey()])
            ->assertActionDoesNotExist('delete');
    }

    public function test_menu_item_switches_from_page_to_link(): void
    {
        $page = Page::create(['title' => 'О школи']);
        $item = MenuItem::create(['menu' => MenuLocation::Glavni, 'label' => 'О школи', 'page_id' => $page->id]);

        Livewire::test(EditMenuItem::class, ['record' => $item->getRouteKey()])
            ->assertSchemaStateSet(['link_type' => 'stranica'])
            ->fillForm(['link_type' => 'link', 'url' => '/o-nama'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($item->fresh()->page_id);
        $this->assertSame('/o-nama', $item->fresh()->href);
    }

    public function test_page_chooses_its_articles_in_order(): void
    {
        $page = Page::create(['title' => 'О школи']);
        $first = Article::create(['title' => 'Историјат', 'description' => '<p>…</p>', 'type' => ArticleType::Tekst, 'status' => ArticleStatus::Objavljeno]);
        $second = Article::create(['title' => 'Смерови', 'description' => '<p>…</p>', 'type' => ArticleType::Tekst, 'status' => ArticleStatus::Objavljeno]);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['pageArticles' => [
                ['article_id' => $second->id],
                ['article_id' => $first->id],
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([$second->id, $first->id], $page->articles()->pluck('articles.id')->all());
    }

    public function test_professor_schedules_an_exam_in_own_name(): void
    {
        $professor = User::factory()->create();
        $class = SchoolClass::create(['grade' => 2, 'section' => 3]);
        $subject = Subject::create(['name' => 'Хемија']);
        $date = today()->next(Carbon::WEDNESDAY);

        $this->actingAs($professor);

        Livewire::test(CreateExam::class)
            ->fillForm([
                'school_class_id' => $class->id,
                'subject_id' => $subject->id,
                'type' => ExamType::Pismeni->value,
                'date' => $date->format('Y-m-d'),
                'user_id' => $this->admin->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(ExamResource::getUrl('index'));

        $this->assertTrue(Exam::firstOrFail()->professor->is($professor));
    }

    public function test_exam_form_rejects_a_busy_day_and_a_past_day(): void
    {
        $class = SchoolClass::create(['grade' => 4, 'section' => 1]);
        $subject = Subject::create(['name' => 'Биологија']);
        $date = today()->next(Carbon::THURSDAY);

        Exam::create(['school_class_id' => $class->id, 'subject_id' => $subject->id, 'user_id' => $this->admin->id, 'type' => ExamType::Kontrolni, 'date' => $date]);

        Livewire::test(CreateExam::class)
            ->fillForm([
                'school_class_id' => $class->id,
                'subject_id' => $subject->id,
                'type' => ExamType::Pismeni->value,
                'date' => $date->format('Y-m-d'),
                'user_id' => $this->admin->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['date']);

        Livewire::test(CreateExam::class)
            ->fillForm([
                'school_class_id' => $class->id,
                'subject_id' => $subject->id,
                'type' => ExamType::Kontrolni->value,
                'date' => today()->subWeek()->format('Y-m-d'),
                'user_id' => $this->admin->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['date']);

        $this->assertSame(1, Exam::count());
    }

    public function test_account_settings_keep_the_email_and_have_no_cancel_button(): void
    {
        $this->get('/admin/profile')
            ->assertOk()
            ->assertSee('Сачувај измене')
            ->assertDontSee('Одустани');

        Livewire::test(EditAccount::class)
            ->assertFormFieldDisabled('email')
            ->fillForm(['username' => 'novo.ime', 'phone' => '+381 19 123 456'])
            ->call('save')
            ->assertHasNoFormErrors();

        // Ни подметнута вредност не мења имејл (поље се не шаље на сервер).
        Livewire::test(EditAccount::class)
            ->set('data.email', 'drugi@gimza.test')
            ->set('data.currentPassword', 'password')
            ->call('save');

        $this->admin->refresh();

        $this->assertSame('novo.ime', $this->admin->username);
        $this->assertSame('+381 19 123 456', $this->admin->phone);
        $this->assertNotSame('drugi@gimza.test', $this->admin->email);
    }
}
