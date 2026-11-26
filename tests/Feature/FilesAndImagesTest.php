<?php

namespace Tests\Feature;

use App\Enums\ExamType;
use App\Filament\Resources\Exams\Pages\ListExams;
use App\Filament\Resources\Galleries\Schemas\GalleryForm;
use App\Models\Exam;
use App\Models\File;
use App\Models\Gallery;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Support\ExamScheduleExport;
use App\Support\Slug;
use App\Support\WebpImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use ZipArchive;

class FilesAndImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_uploaded_images_are_stored_as_webp_and_resized(): void
    {
        $png = UploadedFile::fake()->image('slika.png', 2400, 1200);

        $path = WebpImage::store($png, 'galerija');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        $info = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame('image/webp', $info['mime']);
        $this->assertSame(WebpImage::MAX_WIDTH, $info[0]);
    }

    public function test_gallery_images_are_added_reordered_and_removed_with_their_files(): void
    {
        $gallery = Gallery::create(['title' => 'Дан школе']);
        $a = WebpImage::store(UploadedFile::fake()->image('a.jpg'), 'galerija');
        $b = WebpImage::store(UploadedFile::fake()->image('b.jpg'), 'galerija');

        GalleryForm::syncImages($gallery, [$a, $b]);
        GalleryForm::syncImages($gallery, [$b, $a]);
        $this->assertSame([$b, $a], $gallery->images()->pluck('path')->all());

        GalleryForm::syncImages($gallery, [$a]);
        Storage::disk('public')->assertMissing($b);

        $gallery->delete();
        Storage::disk('public')->assertMissing($a);
    }

    public function test_file_type_comes_from_the_extension_and_the_file_is_deleted_with_the_record(): void
    {
        Storage::disk('public')->put('dokumenta/pravilnik.pdf', '%PDF-1.4 test');

        $file = File::create(['title' => 'Правилник о оцењивању', 'path' => 'dokumenta/pravilnik.pdf', 'file_name' => 'pravilnik.pdf']);

        $this->assertSame('PDF', $file->extension);
        $this->assertSame('pravilnik-o-ocenjivanju', $file->slug);

        // Српско пресловљавање: „х“ постаје „h“, а не „x“.
        $this->assertSame('raspored-pismenih-i-kontrolnih-vezbi-hemija', Slug::make('Распоред писмених и контролних вежби – хемија'));
        $this->assertNotNull($file->uploadedBy);

        $file->delete();
        Storage::disk('public')->assertMissing('dokumenta/pravilnik.pdf');
    }

    public function test_exam_schedule_is_exported_to_excel_and_published_as_a_document(): void
    {
        $class = SchoolClass::create(['grade' => 1, 'section' => 2]);
        Exam::create([
            'school_class_id' => $class->id,
            'subject_id' => Subject::create(['name' => 'Математика'])->id,
            'user_id' => auth()->id(),
            'type' => ExamType::Pismeni,
            'date' => today()->next(Carbon::MONDAY),
        ]);

        $path = sys_get_temp_dir().'/test-raspored.xlsx';
        ExamScheduleExport::toXlsx(ExamScheduleExport::exams(today(), today()->addMonth()), $path);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path) === true);
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        $this->assertStringContainsString('Писмени задатак', $sheet);
        $this->assertStringContainsString('I-2', $sheet);

        $file = ExamScheduleExport::publish($path, 'Распоред писмених провера');
        $again = ExamScheduleExport::publish($path, 'Распоред писмених провера');

        $this->assertTrue($file->is($again));
        $this->assertSame(1, File::count());
        Storage::disk('public')->assertExists($again->fresh()->path);
        @unlink($path);
    }

    public function test_export_button_downloads_excel_and_publishes_it(): void
    {
        Exam::create([
            'school_class_id' => SchoolClass::create(['grade' => 2, 'section' => 1])->id,
            'subject_id' => Subject::create(['name' => 'Физика'])->id,
            'user_id' => auth()->id(),
            'type' => ExamType::Kontrolni,
            'date' => today()->next(Carbon::TUESDAY),
        ]);

        Livewire::test(ListExams::class)
            ->callAction('export', data: [
                'from' => today()->format('Y-m-d'),
                'to' => today()->addMonth()->format('Y-m-d'),
                'publish' => true,
            ])
            ->assertHasNoFormErrors()
            ->assertFileDownloaded('raspored-provera-'.now()->format('Y-m-d').'.xlsx');

        $this->assertTrue(File::query()->where('slug', ExamScheduleExport::PUBLISHED_SLUG)->exists());
    }
}
