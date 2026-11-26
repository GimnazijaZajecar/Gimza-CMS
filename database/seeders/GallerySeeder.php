<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Подгалерије са странице „Галерија“ старог сајта. Фотографије су у спољним
 * албумима (Google Photos), па свака подгалерија има линк ка свом албуму.
 *
 * Датум подгалерије се узима из наслова (година или тачан датум); ако га
 * наслов нема, узима се датум претходног албума, јер је листа поређана од новијих ка старијим.
 */
class GallerySeeder extends Seeder
{
    /**
     * @var array<int, array{0: string, 1: string}>
     */
    public const ALBUMS = [
        ['Република Српска 2024', 'https://photos.app.goo.gl/MEGsFT3AQUuZdysA7'],
        ['Италија 2023', 'https://photos.app.goo.gl/pwz8hptThM87R89n8'],
        ['Београд 2023', 'https://photos.app.goo.gl/csSztSAi6BKVcMPbA'],
        ['Истанбул 2023', 'https://photos.app.goo.gl/5Z5NUt97d4BiBM3h6'],
        ['Париз 2023', 'https://photos.app.goo.gl/r6s8FtvS2ay9UTGt5'],
        ['Свети Сава 2024', 'https://photos.app.goo.gl/eF1rseMHYWMNsXy38'],
        ['Свети Сава 2023', 'https://photos.app.goo.gl/rPButp14R7BB1xbR7'],
        ['Франкофонија 2023', 'https://photos.app.goo.gl/TtieC7eTVNg3kEwA7'],
        ['Данска 2023', 'https://photos.app.goo.gl/AGcVuFSS3UoQnjWb7'],
        ['Додела диплома 2023', 'https://photos.app.goo.gl/F3w1tEEaT3aw64WP6'],
        ['Охрид 2023', 'https://photos.app.goo.gl/LSt1EfgLdTtd96P76'],
        ['Румунија 2022', 'https://photos.app.goo.gl/rPt8G2U8TGjWZpHn7'],
        ['Бугарска 2022', 'https://photos.app.goo.gl/6u3KanZ6saaCDFQE8'],
        ['Белгија 2022', 'https://photos.app.goo.gl/56qKsNkGbg2UXaXx7'],
        ['Литванија 2022', 'https://photos.app.goo.gl/ixdwSBUEjcDp3JKbA'],
        ['САД 2022', 'https://photos.app.goo.gl/QqBuhUU19BpvTdLJA'],
        ['Златибор и Мокра Гора 2022', 'https://photos.app.goo.gl/mCfAT6B4AG2FZMpx9'],
        ['Немачка 2022', 'https://photos.app.goo.gl/4Y4oRqpk1vHunVeg8'],
        ['Мађарска 2022', 'https://photos.app.goo.gl/dCMRQq6uCjYHgYxz5'],
        ['Грчка 2022', 'https://photos.app.goo.gl/xJeLHwRRCKTuLoVU8'],
        ['Пољска 2022', 'https://photos.app.goo.gl/rWc12DyYBEJsq2iG6'],
        ['Свети Сава 2022.', 'https://photos.app.goo.gl/oL3Gc8bbV91RATTu8'],
        ['Турска 2022', 'https://photos.app.goo.gl/TgiwYiAf169qNmBfA'],
        ['Република Српска 2022', 'https://photos.app.goo.gl/34j4J5ExGNPJ4Cq88'],
        ['Војводина 2022', 'https://photos.app.goo.gl/1k9FT3QTvKfQmKXcA'],
        ['Шпанија 2022', 'https://photos.app.goo.gl/Ewo73LcDjR5Ms2ST7'],
        ['Свети Сава 2021.', 'https://photos.app.goo.gl/kkBc8rd6Z2FbjXsY7'],
        ['Свети Сава 2020.', 'https://photos.app.goo.gl/WsVbYz7ySDp2iRiX7'],
        ['Русија 2020.', 'https://photos.app.goo.gl/zzoPPjSqWFvDKsEG8'],
        ['Турска 2020.', 'https://photos.app.goo.gl/Ja48ChDGGFZeQ4516'],
        ['Праг 2020.', 'https://photos.app.goo.gl/wPW8cw7L6V4UPhaW7'],
        ['„Популарна демократија“', 'https://photos.app.goo.gl/v8L3UpyeFtAFV33v6'],
        ['Шведска 2019.', 'https://photos.app.goo.gl/T7Uq7mvTA26Kbawx6'],
        ['Брисел 2019.', 'https://photos.app.goo.gl/ZTs9t2e1us1WW4yi9'],
        ['Ставрос 2019.', 'https://photos.app.goo.gl/MK4swW8hh3HA3s8y9'],
        ['Euroguidance 2019.', 'https://photos.app.goo.gl/ADmWe8DTtz2mkUPt9'],
        ['Играј за завичај', 'https://photos.app.goo.gl/iRT1U3p78NAhunKq7'],
        ['Рондо за две сестре', 'https://photos.app.goo.gl/KwFqf7BHoKmtTLVy6'],
        ['Perpetuum mobile', 'https://photos.app.goo.gl/ZzJL3DBt6NWNk1Ak9'],
        ['Потрага за благом', 'https://photos.app.goo.gl/RsRYZSeeCNvAJ3dR6'],
        ['Свети Сава 2019.', 'https://photos.app.goo.gl/fkJb8mXD2EZdjBBM6'],
        ['Интеркултура 10 година', 'https://photos.app.goo.gl/kgZJxawwNBjX1XcX8'],
        ['Euroguidance 2018.', 'https://photos.app.goo.gl/hQyFoqWSkJCc59cJ6'],
        ['Крос 2018.', 'https://photos.app.goo.gl/rzokv7ESNuy4x3UK6'],
        ['Центар за промоцију науке', 'https://photos.app.goo.gl/Jp3NSzk8vbiAx5To8'],
        ['Европски дан језика', 'https://photos.app.goo.gl/jKKhPsA4FDWtgGWs9'],
        ['Мокрањчеви дани 2018.', 'https://photos.app.goo.gl/fB9yKtYGsHJ4Ruoe9'],
        ['Церн 2018.', 'https://photos.app.goo.gl/z4YGBw1kdr1ux8xe6'],
        ['Крајова 2018.', 'https://photos.app.goo.gl/9azHLT8jwx3MpsxUA'],
        ['Ртањ 2018.', 'https://photos.app.goo.gl/x98fzPWL4L9qraLZ8'],
        ['Крушевац 2018.', 'https://photos.app.goo.gl/ZW1FJzpqi4zZVaQK6'],
        ['Свети Сава 2018.', 'https://photos.app.goo.gl/X5djRTsgNzfZwq89A'],
        ['Изложба цртежа и слика 23.5.2017.', 'https://photos.app.goo.gl/FjkXr6dFqXRPG42T8'],
        ['Свечана академија и сусрет генерација 19.5.2017.', 'https://photos.app.goo.gl/3z2RsqN66ZU7ej9u5'],
        ['Међународни спортски дан 27.4.2017.', 'https://photos.app.goo.gl/dsvmNhWA5Gx49NYS9'],
        ['Франкофонија 23.3.2017.', 'https://photos.app.goo.gl/U86ppCgNnNcQzGmF6'],
        ['„Мој град чаробан и млад“', 'https://photos.app.goo.gl/wwC4CPmrvKh974WUA'],
        ['Научни симпозијум', 'https://photos.app.goo.gl/3hKdERLxXSKyVDvKA'],
        ['Академија Св. Сава 2017.', 'https://photos.app.goo.gl/NFh9GkDgf7iV6HLR6'],
        ['Свети Сава 2017.', 'https://photos.app.goo.gl/mEUdCGfDUdjvC4N69'],
        ['Међународно такмичење из математике 19.11.2016.', 'https://photos.app.goo.gl/7xaALShFkBvzjewz5'],
        ['Вече страних језика 2016.', 'https://photos.app.goo.gl/3jRh18ec7KXU8UwE6'],
        ['Франкофонија 2016.', 'https://photos.app.goo.gl/UjHkkCddpPpoW2co6'],
        ['Доњи Милановац и Лепенски Вир 21.4.2016.', 'https://photos.app.goo.gl/kC8sZPKFWX3XYScbA'],
        ['Свети Сава 2016.', 'https://photos.app.goo.gl/JRiUSoiSM7BE2C3r7'],
        ['Франкофонија 2015.', 'https://goo.gl/photos/Bm7sQiPb1te5jS6a9'],
        ['Образујемо се (не)формално 2015.', 'https://goo.gl/photos/aUDucZtGpnJaAPtk8'],
        ['Италија 2015.', 'https://goo.gl/photos/LwNBdbxUVwq9BhXVA'],
        ['Маскенбал 2008.', 'https://photos.app.goo.gl/UERxzNYrnu5KNWTL9'],
        ['Гости из Румуније', 'https://photos.app.goo.gl/1yXCwCsLGysax5mi6'],
    ];

    public function run(): void
    {
        $previous = now();

        foreach (self::ALBUMS as $index => [$title, $url]) {
            $date = $this->dateFromTitle($title, $index) ?? $previous->copy()->subDay();

            $gallery = new Gallery([
                'title' => $title,
                'album_url' => $url,
                'is_active' => true,
            ]);

            $gallery->created_at = $date;
            $gallery->updated_at = $date;
            $gallery->save();

            $previous = $date;
        }
    }

    /**
     * Тачан датум (нпр. 19.5.2017.) или година (нпр. 2023) из наслова.
     * Када је позната само година, датум је приближан: Савиндан (27. јануар) за албуме
     * „Свети Сава“, а за остале средина године, тако да редослед из листе остане исти.
     */
    protected function dateFromTitle(string $title, int $index): ?Carbon
    {
        if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})\.?/', $title, $match)) {
            return Carbon::create((int) $match[3], (int) $match[2], (int) $match[1], 12);
        }

        if (! preg_match('/(20\d{2}|19\d{2})/', $title, $match)) {
            return null;
        }

        $year = (int) $match[1];

        if (str_contains($title, 'Сава')) {
            return Carbon::create($year, 1, 27, 12);
        }

        return Carbon::create($year, 7, 1, 12)->subDays($index);
    }
}
